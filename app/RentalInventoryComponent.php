<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RentalInventoryComponent extends Model
{
    protected $table = 'rental_inventory_components';
    protected $fillable = ['inventory_component_id', 'month', 'year', 'opening_stock', 'closing_stock'];

    public function inventoryComponent()
    {
        return $this->belongsTo('App\InventoryComponent', 'inventory_component_id');
    }
}
