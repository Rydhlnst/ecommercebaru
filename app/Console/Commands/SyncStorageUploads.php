<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncStorageUploads extends Command
{
    protected $signature = 'storage:sync';
    protected $description = 'Sync uploaded files from storage/app/public to public/storage and public directories for cPanel environments';

    public function handle(): int
    {
        $sourceDir = storage_path('app/public');
        $targetDir1 = public_path('storage');
        $targetDir2 = public_path('uploads');

        $this->info('Starting storage sync...');

        if (! File::exists($sourceDir)) {
            $this->warn("Source directory [{$sourceDir}] does not exist.");
            return 0;
        }

        File::ensureDirectoryExists($targetDir1, 0777);
        File::ensureDirectoryExists($targetDir2, 0777);

        try {
            File::copyDirectory($sourceDir, $targetDir1);
            $this->info("✓ Copied [storage/app/public] -> [public/storage]");
        } catch (\Throwable $e) {
            $this->error("Failed copying to public/storage: " . $e->getMessage());
        }

        if (File::exists($sourceDir . '/uploads')) {
            try {
                File::copyDirectory($sourceDir . '/uploads', $targetDir2);
                $this->info("✓ Copied [storage/app/public/uploads] -> [public/uploads]");
            } catch (\Throwable $e) {
                $this->error("Failed copying to public/uploads: " . $e->getMessage());
            }
        }

        $this->info('Storage sync completed successfully.');
        return 0;
    }
}
