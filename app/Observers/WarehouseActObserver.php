<?php

namespace App\Observers;

use App\Models\WarehouseAct;
use App\Jobs\SendNotificationsAboutAdmission;

class WarehouseActObserver
{
    /**
     * Handle the WarehouseAct "created" event.
     */
    public function created(WarehouseAct $warehouseAct): void
    {
        if ($warehouseAct->type=='receipt') SendNotificationsAboutAdmission::dispatch(array_column($warehouseAct->act, 'offer_id'));
    }

    /**
     * Handle the WarehouseAct "updated" event.
     */
    public function updated(WarehouseAct $warehouseAct): void
    {
        //
    }

    /**
     * Handle the WarehouseAct "deleted" event.
     */
    public function deleted(WarehouseAct $warehouseAct): void
    {
        //
    }

    /**
     * Handle the WarehouseAct "restored" event.
     */
    public function restored(WarehouseAct $warehouseAct): void
    {
        //
    }

    /**
     * Handle the WarehouseAct "force deleted" event.
     */
    public function forceDeleted(WarehouseAct $warehouseAct): void
    {
        //
    }
}
