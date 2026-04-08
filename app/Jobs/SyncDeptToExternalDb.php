<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncDeptToExternalDb implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     *
     * @param  array  $data  The department data to sync.
     * @param  string  $operation  The operation type: 'create', 'update', 'delete'.
     */
    public function __construct(
        protected array $data,
        protected string $operation
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $msConn = DB::connection('sqlsrv');
        $tableName = 'Dept';
        $pk = 'Deptid';
        $deptId = $this->data[$pk];
        $deptName = $this->data['DeptName'];
        $supDeptId = $this->data['SupDeptid'] ?? 0;

        if ($this->operation === 'delete') {
            $msConn->table($tableName)->where($pk, $deptId)->delete();
            Log::info("Synced delete for Dept ID: {$deptId} to MSSQL.");

            return;
        }

        $exists = $msConn->table($tableName)->where($pk, $deptId)->exists();

        if ($exists) {
            // Record already exists in MSSQL — just update it
            $msConn->table($tableName)->where($pk, $deptId)->update([
                'DeptName' => $deptName,
                'SupDeptid' => $supDeptId,
            ]);
            Log::info("Synced update for Dept ID: {$deptId} to MSSQL.");
        } else {
            // Use a raw SQL INSERT with IDENTITY_INSERT toggled in the same batch
            // to guarantee they run in the same session scope on SQL Server.
            Log::info("Inserting new Dept ID: {$deptId} into MSSQL with IDENTITY_INSERT ON.");
            $msConn->unprepared("
                SET IDENTITY_INSERT {$tableName} ON;
                INSERT INTO {$tableName} (Deptid, DeptName, SupDeptid)
                VALUES ({$deptId}, N'{$deptName}', {$supDeptId});
                SET IDENTITY_INSERT {$tableName} OFF;
            ");
            Log::info("Synced create for Dept ID: {$deptId} to MSSQL.");
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SyncDeptToExternalDb job failed for Dept ID: {$this->data['Deptid']}. Error: {$exception->getMessage()}");
    }
}
