<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductVersion extends Model
{
    protected $table = 'product_versions';

    protected $fillable = ['product_id','rate_per_unit'];
}
