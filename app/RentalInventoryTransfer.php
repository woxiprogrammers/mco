<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RentalInventoryTransfer extends Model
{
    protected $table = 'rental_inventory_transfers';
    protected $fillable = ['inventory_component_transfer_id', 'quantity', 'rent_per_day', 'rent_start_date'];

    public function inventoryComponentTransfer()
    {
        return $this->belongsTo('App\InventoryComponentTransfers', 'inventory_component_transfer_id');
    }
}
