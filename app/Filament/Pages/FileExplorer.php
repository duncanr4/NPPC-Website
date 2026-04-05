<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Livewire\WithFileUploads;

class FileExplorer extends Page {
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'File Explorer';
    protected static ?string $navigationGroup = 'Developer Tools';
    protected static ?int $navigationSort = 201;
    protected static string $view = 'filament.pages.file-explorer';

    public string $currentPath = '';
    public ?string $fileContent = null;
    public ?string $viewingFile = null;
    public string $searchQuery = '';
    public array $searchResults = [];
    public bool $isSearching = false;
    public string $newFolderName = '';
    public $uploadedFiles = [];

    public function mount(): void {
        $this->currentPath = '';
    }

    public function navigateTo(string $path): void {
        $this->currentPath = $path;
        $this->fileContent = null;
        $this->viewingFile = null;
        $this->clearSearch();
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

        if ($size > 512000) {
            $this->fileContent = "(File too large to display: ".number_format($size / 1024, 1)." KB)";
            $this->viewingFile = $path;

            return;
        }

        if ($this->isBinaryFile($fullPath)) {
            $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                $this->fileContent = '__IMAGE__';
            } else {
                $this->fileContent = "(Binary file: ".number_format($size / 1024, 1)." KB)";
            }
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

    // --- Search ---

    public function search(): void {
        $query = trim($this->searchQuery);
        if (! $query) {
            $this->clearSearch();

            return;
        }

        $this->isSearching = true;
        $this->searchResults = [];
        $basePath = config('claude.repo_path', base_path());

        $this->searchDirectory($basePath, '', $query, 0);
    }

    public function clearSearch(): void {
        $this->searchQuery = '';
        $this->searchResults = [];
        $this->isSearching = false;
    }

    private function searchDirectory(string $basePath, string $relativePath, string $query, int $depth): void {
        if ($depth > 8 || count($this->searchResults) >= 100) {
            return;
        }

        $fullPath = $relativePath ? $basePath.DIRECTORY_SEPARATOR.$relativePath : $basePath;

        if (! is_dir($fullPath) || ! is_readable($fullPath)) {
            return;
        }

        $entries = @scandir($fullPath);
        if (! $entries) {
            return;
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            if (in_array($entry, ['.git', 'node_modules', 'vendor'])) {
                continue;
            }

            $entryPath = $relativePath ? $relativePath.DIRECTORY_SEPARATOR.$entry : $entry;
            $entryFullPath = $basePath.DIRECTORY_SEPARATOR.$entryPath;
            $isDir = is_dir($entryFullPath);

            // Match by name or extension
            $matches = false;
            if (str_starts_with($query, '.')) {
                // Extension search: ".png" matches all png files
                $ext = '.'.strtolower(pathinfo($entry, PATHINFO_EXTENSION));
                $matches = ! $isDir && $ext === strtolower($query);
            } else {
                // Name search: case-insensitive substring
                $matches = stripos($entry, $query) !== false;
            }

            if ($matches) {
                $this->searchResults[] = [
                    'name'     => $entry,
                    'path'     => $entryPath,
                    'dir'      => $relativePath ?: '/',
                    'is_dir'   => $isDir,
                    'size'     => $isDir ? null : @filesize($entryFullPath),
                    'ext'      => $isDir ? null : strtolower(pathinfo($entry, PATHINFO_EXTENSION)),
                ];

                if (count($this->searchResults) >= 100) {
                    return;
                }
            }

            if ($isDir) {
                $this->searchDirectory($basePath, $entryPath, $query, $depth + 1);
            }
        }
    }

    // --- Create Folder ---

    public function createFolder(): void {
        $name = trim($this->newFolderName);
        if (! $name || ! preg_match('/^[a-zA-Z0-9_\-. ]+$/', $name)) {
            return;
        }

        $basePath = config('claude.repo_path', base_path());
        $targetPath = $this->currentPath
            ? $basePath.DIRECTORY_SEPARATOR.$this->currentPath.DIRECTORY_SEPARATOR.$name
            : $basePath.DIRECTORY_SEPARATOR.$name;

        if (! is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
        }

        $this->newFolderName = '';
    }

    // --- Upload Files ---

    public function uploadFiles(): void {
        if (empty($this->uploadedFiles)) {
            return;
        }

        $basePath = config('claude.repo_path', base_path());
        $targetDir = $this->currentPath
            ? $basePath.DIRECTORY_SEPARATOR.$this->currentPath
            : $basePath;

        foreach ($this->uploadedFiles as $file) {
            $file->storeAs(
                '',
                $file->getClientOriginalName(),
                ['disk' => 'local', 'root' => $targetDir]
            );
        }

        $this->uploadedFiles = [];
    }

    // --- View Data ---

    public function getViewData(): array {
        $basePath = config('claude.repo_path', base_path());
        $fullPath = $this->currentPath ? $basePath.DIRECTORY_SEPARATOR.$this->currentPath : $basePath;

        $items = [];

        if (is_dir($fullPath)) {
            $entries = scandir($fullPath);

            foreach ($entries as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }

                if ($this->currentPath === '' && in_array($entry, ['node_modules', '.git'])) {
                    continue;
                }

                $entryPath = $this->currentPath ? $this->currentPath.DIRECTORY_SEPARATOR.$entry : $entry;
                $entryFullPath = $basePath.DIRECTORY_SEPARATOR.$entryPath;

                $isDir = is_dir($entryFullPath);
                $size = $isDir ? null : @filesize($entryFullPath);
                $modified = @filemtime($entryFullPath);

                $items[] = [
                    'name'     => $entry,
                    'path'     => $entryPath,
                    'is_dir'   => $isDir,
                    'size'     => $size,
                    'modified' => $modified,
                    'ext'      => $isDir ? null : strtolower(pathinfo($entry, PATHINFO_EXTENSION)),
                ];
            }

            usort($items, function ($a, $b) {
                if ($a['is_dir'] && ! $b['is_dir']) {
                    return -1;
                }
                if (! $a['is_dir'] && $b['is_dir']) {
                    return 1;
                }

                return strcasecmp($a['name'], $b['name']);
            });
        }

        $breadcrumbs = [['name' => 'Project Root', 'path' => '']];
        if ($this->currentPath) {
            $parts = explode(DIRECTORY_SEPARATOR, $this->currentPath);
            $accumulated = '';
            foreach ($parts as $part) {
                $accumulated = $accumulated ? $accumulated.DIRECTORY_SEPARATOR.$part : $part;
                $breadcrumbs[] = ['name' => $part, 'path' => $accumulated];
            }
        }

        // Get image URL for preview
        $imageUrl = null;
        if ($this->fileContent === '__IMAGE__' && $this->viewingFile) {
            $storagePrefix = 'storage/app/public/';
            if (str_starts_with($this->viewingFile, $storagePrefix)) {
                $imageUrl = asset('storage/'.substr($this->viewingFile, strlen($storagePrefix)));
            } else {
                $imageUrl = asset($this->viewingFile);
            }
        }

        return [
            'items'       => $items,
            'breadcrumbs' => $breadcrumbs,
            'imageUrl'    => $imageUrl,
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
            'php'                                       => 'php',
            'js', 'ts'                                  => 'js',
            'vue'                                       => 'vue',
            'css', 'scss', 'sass'                       => 'css',
            'html'                                      => 'html',
            'json'                                      => 'json',
            'md'                                        => 'md',
            'yml', 'yaml'                               => 'yaml',
            'env'                                       => 'env',
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg' => 'img',
            'sql', 'sqlite'                             => 'db',
            'mp4', 'webm', 'mov'                        => 'video',
            'pdf'                                       => 'pdf',
            default                                     => 'file',
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
