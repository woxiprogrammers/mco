<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductMaterialRelation extends Model
{
    protected $table = 'product_material_relation';

    protected $fillable = ['product_version_id','material_version_id','material_quantity'];
}
