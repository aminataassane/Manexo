<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;

class BackupService
{
    public function getBackupDirectory(): string
    {
        return storage_path('app/backups');
    }

    /**
     * Resolve the full path to a PostgreSQL binary (pg_dump, psql).
     * Returns the absolute path without quotes (for use with Process::command array).
     */
    protected function pgBinPath(string $binary): string
    {
        // Search common PostgreSQL install directories on Windows
        if (PHP_OS_FAMILY === 'Windows') {
            $dirs = glob('C:\\Program Files\\PostgreSQL\\*\\bin');
            if ($dirs) {
                rsort($dirs); // Prefer latest version
                foreach ($dirs as $dir) {
                    $path = $dir . '\\' . $binary . '.exe';
                    if (file_exists($path)) {
                        return $path;
                    }
                }
            }
        }

        return $binary;
    }

    /**
     * Create a temporary .pgpass file for password authentication.
     * Format: hostname:port:database:username:password
     */
    protected function createPgPassFile(string $host, string $port, string $database, string $username, string $password): string
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'pgpass_');
        $line = implode(':', [
            str_replace(['\\', ':'], ['\\\\', '\\:'], $host),
            $port,
            str_replace(['\\', ':'], ['\\\\', '\\:'], $database),
            str_replace(['\\', ':'], ['\\\\', '\\:'], $username),
            str_replace(['\\', ':'], ['\\\\', '\\:'], $password),
        ]);
        file_put_contents($tmpFile, $line . PHP_EOL);
        chmod($tmpFile, 0600);

        return $tmpFile;
    }

    /**
     * Remove the temporary pgpass file.
     */
    protected function removePgPassFile(string $path): void
    {
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    public function listBackups(): array
    {
        $dir = $this->getBackupDirectory();

        if (! is_dir($dir)) {
            return [];
        }

        $files = glob($dir . '/*.sql') ?: [];
        $gzFiles = glob($dir . '/*.sql.gz') ?: [];
        $encFiles = glob($dir . '/*.sql.enc') ?: [];
        $allFiles = array_merge($files, $gzFiles, $encFiles);

        $backups = [];
        foreach ($allFiles as $file) {
            $size = filesize($file);
            $backups[] = [
                'name' => basename($file),
                'size' => $size,
                'size_human' => $this->humanSize($size),
                'created_at' => date('Y-m-d H:i:s', filemtime($file)),
                'encrypted' => str_ends_with($file, '.enc'),
            ];
        }

        usort($backups, fn ($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return $backups;
    }

    public function createBackup(bool $encrypt = false): array
    {
        $dir = $this->getBackupDirectory();

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'backup_' . date('Y-m-d_His') . '.sql';
        $filepath = $dir . DIRECTORY_SEPARATOR . $filename;

        $host = config('database.connections.pgsql.host', '127.0.0.1');
        $port = config('database.connections.pgsql.port', '5432');
        $database = config('database.connections.pgsql.database');
        $username = config('database.connections.pgsql.username');
        $password = config('database.connections.pgsql.password', '');

        $pgDump = $this->pgBinPath('pg_dump');
        $pgPassFile = $this->createPgPassFile($host, $port, $database, $username, $password);

        try {
            $command = [
                $pgDump,
                '-h', $host,
                '-p', (string) $port,
                '-U', $username,
                '--no-password',
                '-f', $filepath,
                $database,
            ];

            $env = ['PGPASSFILE' => $pgPassFile];
            if (PHP_OS_FAMILY !== 'Windows') {
                $env['PGPASSWORD'] = $password;
            }

            $result = Process::timeout(300)
                ->env($env)
                ->command($command)
                ->run();

            $this->removePgPassFile($pgPassFile);

            if (! $result->successful()) {
                return ['success' => false, 'filename' => null, 'error' => $result->errorOutput()];
            }

            // Encrypt if requested
            if ($encrypt) {
                $encryptedPath = $filepath . '.enc';
                $encKey = config('app.backup_encryption_key', config('app.key'));

                $encCommand = sprintf(
                    'openssl enc -aes-256-cbc -salt -pbkdf2 -in %s -out %s -pass pass:%s',
                    escapeshellarg($filepath),
                    escapeshellarg($encryptedPath),
                    escapeshellarg($encKey),
                );
                $encResult = Process::timeout(120)->run($encCommand);

                if ($encResult->successful()) {
                    @unlink($filepath); // Remove unencrypted file
                    $filename .= '.enc';
                }
            }

            return ['success' => true, 'filename' => $filename, 'error' => null];
        } catch (\Throwable $e) {
            $this->removePgPassFile($pgPassFile);

            return ['success' => false, 'filename' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Rotate backups, keeping only the most recent $keep files.
     */
    public function rotateBackups(int $keep = 10): int
    {
        $backups = $this->listBackups(); // Already sorted newest first
        $deleted = 0;

        if (count($backups) <= $keep) {
            return $deleted;
        }

        $dir = $this->getBackupDirectory();
        $toDelete = array_slice($backups, $keep);

        foreach ($toDelete as $backup) {
            $path = $dir . '/' . $backup['name'];
            if (file_exists($path) && @unlink($path)) {
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Restore a backup by filename.
     */
    public function restoreBackup(string $filename): array
    {
        $dir = $this->getBackupDirectory();
        $filepath = $dir . '/' . basename($filename);

        if (! file_exists($filepath)) {
            return ['success' => false, 'error' => 'Backup file not found.'];
        }

        $host = config('database.connections.pgsql.host', '127.0.0.1');
        $port = config('database.connections.pgsql.port', '5432');
        $database = config('database.connections.pgsql.database');
        $username = config('database.connections.pgsql.username');
        $password = config('database.connections.pgsql.password', '');

        $sqlFile = $filepath;

        // Decrypt if encrypted
        if (str_ends_with($filename, '.enc')) {
            $decryptedPath = $dir . '/restore_tmp_' . date('YmdHis') . '.sql';
            $encKey = config('app.backup_encryption_key', config('app.key'));

            $decResult = Process::timeout(120)->run(sprintf(
                'openssl enc -d -aes-256-cbc -pbkdf2 -in %s -out %s -pass pass:%s',
                escapeshellarg($filepath),
                escapeshellarg($decryptedPath),
                escapeshellarg($encKey),
            ));

            if (! $decResult->successful()) {
                return ['success' => false, 'error' => 'Decryption failed: ' . $decResult->errorOutput()];
            }

            $sqlFile = $decryptedPath;
        }

        try {
            $psql = $this->pgBinPath('psql');
            $pgPassFile = $this->createPgPassFile($host, $port, $database, $username, $password);

            $command = [
                $psql,
                '-h', $host,
                '-p', (string) $port,
                '-U', $username,
                '--no-password',
                '-d', $database,
                '-f', $sqlFile,
            ];

            $env = ['PGPASSFILE' => $pgPassFile];
            if (PHP_OS_FAMILY !== 'Windows') {
                $env['PGPASSWORD'] = $password;
            }

            $result = Process::timeout(600)
                ->env($env)
                ->command($command)
                ->run();

            $this->removePgPassFile($pgPassFile);

            // Clean up temporary decrypted file
            if ($sqlFile !== $filepath && file_exists($sqlFile)) {
                @unlink($sqlFile);
            }

            if ($result->successful()) {
                return ['success' => true, 'error' => null];
            }

            return ['success' => false, 'error' => $result->errorOutput()];
        } catch (\Throwable $e) {
            $this->removePgPassFile($pgPassFile);

            // Clean up temporary decrypted file
            if ($sqlFile !== $filepath && file_exists($sqlFile)) {
                @unlink($sqlFile);
            }

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function humanSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' Go';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' Mo';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' Ko';
        }

        return $bytes . ' o';
    }
}
