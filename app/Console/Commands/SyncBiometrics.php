<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncBiometrics extends Command
{
    protected $signature = 'sync:biometrics {--test : Test connections and exit}';

    protected $description = 'Sync biometric data from MSSQL to MySQL using Change Tracking';

    // Matches your Python TABLES_CONFIG
    protected $tablesConfig = [
        'checkinout' => [
            'pk' => 'Logid',
            'columns' => ['Logid', 'Userid', 'CheckTime', 'CheckType'],
        ],
        'userinfo' => [
            'pk' => 'Userid',
            'columns' => ['Userid', 'Name', 'Deptid'],
        ],
        'dept' => [
            'pk' => 'Deptid',
            'columns' => ['Deptid', 'Deptname'],
        ],
    ];

    public function handle()
    {
        $this->info('Biometric Sync Started...');

        try {
            $msConn = DB::connection('sqlsrv');
            $msConn->getPdo(); // Try to connect
            $this->info('Successfully connected to MSSQL.');

            $myConn = DB::connection('mysql');
            $myConn->getPdo(); // Try to connect
            $this->info('Successfully connected to MySQL.');

            if ($this->option('test')) {
                $this->info('Test mode completed successfully.');

                return 0;
            }

            // 1. Ensure metadata table exists
            $this->ensureMetadataTableExists();

            // 2. Get current MSSQL version
            try {
                $currentVersion = $msConn->selectOne('SELECT CHANGE_TRACKING_CURRENT_VERSION() as ver')->ver;
            } catch (\Exception $e) {
                $this->error('MSSQL Change Tracking Error: '.$e->getMessage());
                $this->comment('Please ensure Change Tracking is enabled on the MSSQL database.');

                return 1;
            }

            foreach ($this->tablesConfig as $tableName => $config) {
                $pk = $config['pk'];
                $cols = array_merge([$pk], array_diff($config['columns'], [$pk]));

                $lastVersion = $this->getLastVersion($tableName);

                // --- INITIAL FULL SYNC ---
                if ($lastVersion == 0) {
                    $this->info("[$tableName] First run detected. Performing full sync...");
                    $rows = $msConn->table($tableName)->select($cols)->get();

                    foreach ($rows->chunk(500) as $chunk) {
                        $data = collect($chunk)->map(fn ($row) => (array) $row)->toArray();
                        $myConn->table($tableName)->upsert($data, [$pk], array_diff($cols, [$pk]));
                    }

                    $this->updateLastVersion($tableName, $currentVersion);

                    continue;
                }

                // --- CHANGE TRACKING LOGIC ---
                if ($currentVersion == $lastVersion) {
                    continue;
                }

                $tCols = collect($cols)->filter(fn ($c) => $c != $pk)->map(fn ($c) => "T.$c")->toArray();
                $selectCols = "CT.$pk, CT.SYS_CHANGE_OPERATION".(count($tCols) ? ', '.implode(', ', $tCols) : '');

                $changes = $msConn->select("
                    SELECT $selectCols 
                    FROM CHANGETABLE(CHANGES $tableName, ?) AS CT
                    LEFT JOIN $tableName AS T ON T.$pk = CT.$pk
                ", [$lastVersion]);

                if (empty($changes)) {
                    $this->updateLastVersion($tableName, $currentVersion);

                    continue;
                }

                foreach ($changes as $change) {
                    $rowArray = (array) $change;
                    $op = $rowArray['SYS_CHANGE_OPERATION'];
                    unset($rowArray['SYS_CHANGE_OPERATION']); // Remove op meta-data

                    if ($op === 'I' || $op === 'U') {
                        $myConn->table($tableName)->upsert([$rowArray], [$pk], array_keys($rowArray));
                    } elseif ($op === 'D') {
                        $myConn->table($tableName)->where($pk, $rowArray[$pk])->delete();
                    }
                }

                $this->updateLastVersion($tableName, $currentVersion);
                $this->info("[$tableName] Processed ".count($changes).' changes.');
            }
        } catch (\Exception $e) {
            $this->error('Error during sync: '.$e->getMessage());

            return 1; // FAILURE
        }

        return 0; // SUCCESS
    }

    private function ensureMetadataTableExists()
    {
        if (! Schema::connection('mysql')->hasTable('sync_metadata')) {
            Schema::connection('mysql')->create('sync_metadata', function ($table) {
                $table->string('table_name')->primary();
                $table->bigInteger('last_version');
            });
        }
    }

    private function getLastVersion($tableName)
    {
        $row = DB::connection('mysql')->table('sync_metadata')
            ->where('table_name', $tableName)
            ->first();

        return $row ? $row->last_version : 0;
    }

    private function updateLastVersion($tableName, $version)
    {
        DB::connection('mysql')->table('sync_metadata')->updateOrInsert(
            ['table_name' => $tableName],
            ['last_version' => $version]
        );
    }
}
