<?php

namespace App\Jobs;

use App\Models\ClaudeSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;

class RunClaudeCode implements ShouldQueue {
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600; // 10 minutes max
    public int $tries   = 1;

    public function __construct(
        public string $sessionId,
    ) {}

    public function handle(): void {
        $session = ClaudeSession::findOrFail($this->sessionId);

        $repoPath = config('claude.repo_path', base_path());
        $worktreeBase = config('claude.worktree_base', '/tmp/claude-worktrees');
        $claudeBinary = config('claude.binary', 'claude');

        $branchName = 'claude/session-'.substr($session->id, 0, 8).'-'.time();
        $worktreePath = $worktreeBase.'/'.$branchName;

        $session->update([
            'status'        => 'running',
            'branch_name'   => $branchName,
            'worktree_path' => $worktreePath,
        ]);

        try {
            // Ensure worktree base directory exists
            if (! is_dir($worktreeBase)) {
                mkdir($worktreeBase, 0755, true);
            }

            // Create a new branch from current HEAD
            $result = Process::path($repoPath)
                ->timeout(30)
                ->run("git worktree add -b {$branchName} {$worktreePath} HEAD");

            if (! $result->successful()) {
                throw new \RuntimeException('Failed to create worktree: '.$result->errorOutput());
            }

            // Run Claude Code in the worktree
            $escapedPrompt = str_replace("'", "'\\''", $session->prompt);

            $result = Process::path($worktreePath)
                ->timeout($this->timeout - 60) // leave buffer
                ->env([
                    'HOME' => env('HOME', '/root'),
                    'PATH' => env('PATH', '/usr/local/bin:/usr/bin:/bin'),
                ])
                ->run("{$claudeBinary} -p '{$escapedPrompt}' --no-input 2>&1");

            $output = $result->output();

            // Capture the diff
            $diffResult = Process::path($worktreePath)
                ->timeout(30)
                ->run('git diff HEAD');

            $diff = $diffResult->output();

            // Get list of changed files
            $statusResult = Process::path($worktreePath)
                ->timeout(30)
                ->run('git diff HEAD --name-only');

            $filesChanged = array_filter(explode("\n", trim($statusResult->output())));

            // Also check for untracked files
            $untrackedResult = Process::path($worktreePath)
                ->timeout(30)
                ->run('git ls-files --others --exclude-standard');

            $untrackedFiles = array_filter(explode("\n", trim($untrackedResult->output())));

            if (! empty($untrackedFiles)) {
                // Stage untracked files so they show in diff
                Process::path($worktreePath)
                    ->timeout(30)
                    ->run('git add '.implode(' ', array_map('escapeshellarg', $untrackedFiles)));

                // Re-capture diff with staged changes
                $diffResult = Process::path($worktreePath)
                    ->timeout(30)
                    ->run('git diff HEAD');

                $diff = $diffResult->output();
                $filesChanged = array_merge($filesChanged, $untrackedFiles);
            }

            $session->update([
                'status'        => 'completed',
                'output'        => $output,
                'diff'          => $diff,
                'files_changed' => array_values(array_unique($filesChanged)),
            ]);
        } catch (\Throwable $e) {
            $session->update([
                'status' => 'failed',
                'output' => ($session->output ?? '')."\\n\\nERROR: ".$e->getMessage(),
            ]);

            // Clean up worktree on failure
            $this->cleanupWorktree($repoPath, $worktreePath, $branchName);
        }
    }

    private function cleanupWorktree(string $repoPath, string $worktreePath, string $branchName): void {
        if (is_dir($worktreePath)) {
            Process::path($repoPath)->timeout(30)->run("git worktree remove --force {$worktreePath}");
        }
        Process::path($repoPath)->timeout(30)->run("git branch -D {$branchName}");
    }
}
