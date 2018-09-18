<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetInfo extends Model
{
    protected $table = 'asset_info';

    protected $fillable = ['in_date','out_date', 'in_quantity', 'out_quantity', 'in_reference_id','out_reference_id'];
}
