<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class FileExplorer extends Page {
    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'File Explorer';
    protected static ?string $navigationGroup = 'Developer Tools';
    protected static ?int $navigationSort = 201;
    protected static string $view = 'filament.pages.file-explorer';

    public string $currentPath = '';
    public ?string $fileContent = null;
    public ?string $viewingFile = null;

    public function mount(): void {
        $this->currentPath = '';
    }

    public function navigateTo(string $path): void {
        $this->currentPath = $path;
        $this->fileContent = null;
        $this->viewingFile = null;
    }

    public function goUp(): void {
        $this->currentPath = dirname($this->currentPath);
        if ($this->currentPath === '.') {
            $this->currentPath = '';
        }
        $this->fileContent = null;
        $this->viewingFile = null;
    }

    public function viewFile(string $path): void {
        $fullPath = $this->getFullPath($path);

        if (! is_file($fullPath) || ! is_readable($fullPath)) {
            $this->fileContent = '(Cannot read file)';
            $this->viewingFile = $path;

            return;
        }

        $size = filesize($fullPath);

        // Don't try to display binary or huge files
        if ($size > 512000) { // 500KB
            $this->fileContent = "(File too large to display: ".number_format($size / 1024, 1)." KB)";
            $this->viewingFile = $path;

            return;
        }

        if ($this->isBinaryFile($fullPath)) {
            $this->fileContent = "(Binary file: ".number_format($size / 1024, 1)." KB)";
            $this->viewingFile = $path;

            return;
        }

        $this->fileContent = file_get_contents($fullPath);
        $this->viewingFile = $path;
    }

    public function closeFile(): void {
        $this->fileContent = null;
        $this->viewingFile = null;
    }

    public function getViewData(): array {
        $basePath = config('claude.repo_path', base_path());
        $fullPath = $this->currentPath ? $basePath.DIRECTORY_SEPARATOR.$this->currentPath : $basePath;

        $items = [];

        if (! is_dir($fullPath)) {
            return ['items' => [], 'breadcrumbs' => []];
        }

        $entries = scandir($fullPath);

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            // Skip common non-useful directories
            if ($this->currentPath === '' && in_array($entry, ['node_modules', '.git'])) {
                continue;
            }

            $entryPath = $this->currentPath ? $this->currentPath.DIRECTORY_SEPARATOR.$entry : $entry;
            $entryFullPath = $basePath.DIRECTORY_SEPARATOR.$entryPath;

            $isDir = is_dir($entryFullPath);
            $size = $isDir ? null : filesize($entryFullPath);
            $modified = filemtime($entryFullPath);

            $items[] = [
                'name'     => $entry,
                'path'     => $entryPath,
                'is_dir'   => $isDir,
                'size'     => $size,
                'modified' => $modified,
                'ext'      => $isDir ? null : strtolower(pathinfo($entry, PATHINFO_EXTENSION)),
            ];
        }

        // Sort: directories first, then alphabetical
        usort($items, function ($a, $b) {
            if ($a['is_dir'] && ! $b['is_dir']) {
                return -1;
            }
            if (! $a['is_dir'] && $b['is_dir']) {
                return 1;
            }

            return strcasecmp($a['name'], $b['name']);
        });

        // Build breadcrumbs
        $breadcrumbs = [['name' => 'Project Root', 'path' => '']];
        if ($this->currentPath) {
            $parts = explode(DIRECTORY_SEPARATOR, $this->currentPath);
            $accumulated = '';
            foreach ($parts as $part) {
                $accumulated = $accumulated ? $accumulated.DIRECTORY_SEPARATOR.$part : $part;
                $breadcrumbs[] = ['name' => $part, 'path' => $accumulated];
            }
        }

        return [
            'items'       => $items,
            'breadcrumbs' => $breadcrumbs,
        ];
    }

    public function formatSize(?int $bytes): string {
        if ($bytes === null) {
            return '-';
        }
        if ($bytes < 1024) {
            return $bytes.' B';
        }
        if ($bytes < 1048576) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return number_format($bytes / 1048576, 1).' MB';
    }

    public function getFileIcon(string $ext): string {
        return match ($ext) {
            'php'                    => 'php',
            'js', 'ts'               => 'js',
            'vue'                    => 'vue',
            'css', 'scss', 'sass'    => 'css',
            'html', 'blade.php'      => 'html',
            'json'                   => 'json',
            'md'                     => 'md',
            'yml', 'yaml'            => 'yaml',
            'env'                    => 'env',
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg' => 'img',
            'sql', 'sqlite'          => 'db',
            default                  => 'file',
        };
    }

    private function getFullPath(string $path): string {
        $basePath = config('claude.repo_path', base_path());

        return $basePath.DIRECTORY_SEPARATOR.$path;
    }

    private function isBinaryFile(string $path): bool {
        $content = file_get_contents($path, false, null, 0, 8192);

        return str_contains($content, "\0");
    }
}
