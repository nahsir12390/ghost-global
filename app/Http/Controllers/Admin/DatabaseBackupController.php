<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DatabaseBackupController extends Controller
{
    public function download(Request $request): BinaryFileResponse
    {
        abort_if($request->user()->isVendor(), 403);

        $connection = DB::connection();
        $driver = $connection->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            return $this->downloadMysqlBackup($connection);
        }

        if ($driver === 'sqlite') {
            return $this->downloadSqliteBackup($connection);
        }

        abort(422, 'Database backup is only available for MySQL, MariaDB, and SQLite connections.');
    }

    protected function downloadMysqlBackup(Connection $connection): BinaryFileResponse
    {
        $pdo = $connection->getPdo();
        $backupDirectory = storage_path('app/backups');

        if (!is_dir($backupDirectory)) {
            mkdir($backupDirectory, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $fileName = "database_backup_{$timestamp}.sql";
        $filePath = $backupDirectory . DIRECTORY_SEPARATOR . $fileName;
        $handle = fopen($filePath, 'wb');

        if ($handle === false) {
            abort(500, 'Unable to create the backup file.');
        }

        fwrite($handle, "-- Database backup generated on {$timestamp}\n");
        fwrite($handle, "-- Database: {$connection->getDatabaseName()}\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        $tables = array_map(function ($row) {
            return array_values((array) $row)[0];
        }, $connection->select('SHOW TABLES'));

        foreach ($tables as $table) {
            $tableName = str_replace('`', '``', $table);
            $createTableRow = (array) $connection->selectOne("SHOW CREATE TABLE `{$tableName}`");
            $createStatement = $createTableRow['Create Table'] ?? array_values($createTableRow)[1] ?? null;

            if (!$createStatement) {
                continue;
            }

            fwrite($handle, "-- Table structure for `{$table}`\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
            fwrite($handle, $createStatement . ";\n\n");

            $columns = Schema::connection($connection->getName())->getColumnListing($table);
            $columnList = implode(', ', array_map(fn ($column) => "`{$column}`", $columns));

            foreach ($connection->table($table)->cursor() as $row) {
                $values = [];

                foreach ($columns as $column) {
                    $values[] = $this->toSqlValue($pdo, $row->{$column} ?? null);
                }

                fwrite(
                    $handle,
                    sprintf(
                        "INSERT INTO `%s` (%s) VALUES (%s);\n",
                        $table,
                        $columnList,
                        implode(', ', $values)
                    )
                );
            }

            fwrite($handle, "\n");
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    protected function downloadSqliteBackup(Connection $connection): BinaryFileResponse
    {
        $databasePath = $connection->getDatabaseName();

        if (!$databasePath || !file_exists($databasePath)) {
            abort(404, 'SQLite database file could not be found.');
        }

        $fileName = 'database_backup_' . now()->format('Y-m-d_H-i-s') . '.sqlite';

        return response()->download($databasePath, $fileName);
    }

    protected function toSqlValue(\PDO $pdo, mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return $pdo->quote((string) $value);
    }
}
