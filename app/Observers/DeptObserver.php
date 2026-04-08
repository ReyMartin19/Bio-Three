<?php

namespace App\Observers;

use App\Jobs\SyncDeptToExternalDb;
use App\Models\Dept;

class DeptObserver
{
    /**
     * Handle the Dept "created" event.
     */
    public function created(Dept $dept): void
    {
        SyncDeptToExternalDb::dispatch($dept->toArray(), 'create');
    }

    /**
     * Handle the Dept "updated" event.
     */
    public function updated(Dept $dept): void
    {
        SyncDeptToExternalDb::dispatch($dept->toArray(), 'update');
    }

    /**
     * Handle the Dept "deleted" event.
     */
    public function deleted(Dept $dept): void
    {
        SyncDeptToExternalDb::dispatch($dept->toArray(), 'delete');
    }
}
