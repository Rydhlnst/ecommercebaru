<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncStorageUploads extends Command
{
    protected $signature = 'storage:sync';
    protected $description = 'Sync uploaded files from storage/app/public to public/storage and public directories with proper web-readable permissions';

    public function handle(): int
    {
        $sourceDir = storage_path('app/public');
        $targetStorage = public_path('storage');
        $targetUploads = public_path('uploads');

        $this->info('Starting storage sync and permissions fix...');

        // If public/storage is a broken symlink or file, remove it
        if (is_link($targetStorage) && ! file_exists($targetStorage)) {
            @unlink($targetStorage);
            $this->info('Removed broken public/storage symlink.');
        }

        if (File::exists($sourceDir)) {
            File::ensureDirectoryExists($targetStorage, 0755);
            File::ensureDirectoryExists($targetUploads, 0755);

            try {
                File::copyDirectory($sourceDir, $targetStorage);
                $this->info('✓ Copied [storage/app/public] -> [public/storage]');
            } catch (\Throwable $e) {
                $this->error('Failed copying to public/storage: ' . $e->getMessage());
            }

            if (File::exists($sourceDir . '/uploads')) {
                try {
                    File::copyDirectory($sourceDir . '/uploads', $targetUploads);
                    $this->info('✓ Copied [storage/app/public/uploads] -> [public/uploads]');
                } catch (\Throwable $e) {
                    $this->error('Failed copying to public/uploads: ' . $e->getMessage());
                }
            }
        }

        // Recursively fix permissions on public/storage, public/uploads, and storage/app/public
        $this->fixDirectoryPermissions($targetStorage);
        $this->fixDirectoryPermissions($targetUploads);
        $this->fixDirectoryPermissions($sourceDir);

        $this->info('Storage sync and permissions update completed successfully.');
        return 0;
    }

    private function fixDirectoryPermissions(string $path): void
    {
        if (! File::exists($path)) {
            return;
        }

        @chmod($path, 0755);

        if (is_dir($path)) {
            $items = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($items as $item) {
                if ($item->isDir()) {
                    @chmod($item->getRealPath(), 0755);
                } else {
                    @chmod($item->getRealPath(), 0644);
                }
            }
        }
    }
}
