<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryCart extends Model
{

    protected $table = 'inventory_cart';

    protected $fillable = [
        'inventory_component_id', 'project_site_id', 'unit_id', 'quantity'
    ];

    public function projectSite()
    {
        return $this->belongsTo('App\ProjectSite', 'project_site_id');
    }

    public function inventoryComponent()
    {
        return $this->belongsTo('App\InventoryComponent', 'inventory_component_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Unit', 'unit_id');
    }
}
