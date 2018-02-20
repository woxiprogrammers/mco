<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetMaintenanceBillImage extends Model
{
    protected $table = 'asset_maintenance_bill_images';

    protected $fillable = ['name','asset_maintenance_bill_id'];
}
