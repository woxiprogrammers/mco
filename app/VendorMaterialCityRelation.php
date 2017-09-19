<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorMaterialCityRelation extends Model
{
    protected $table = 'vendor_material_city_relation';

    protected $fillable = ['vendor_city_relation_id','vendor_material_relation_id'];

    public function vendorMaterial(){
        return $this->belongsTo('App\VendorMaterialRelation','vendor_material_relation_id');
    }

    public function vendorCity(){
        return $this->belongsTo('App\VendorCityRelation','vendor_city_relation_id');
    }
}
