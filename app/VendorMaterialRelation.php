<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorMaterialRelation extends Model
{
    protected $table = 'vendor_material_relation';

    protected $fillable = ['vendor_id','material_id'];

    public function vendor(){
        return $this->belongsTo('App\Vendor','vendor_id');
    }

    public function material(){
        return $this->belongsTo('App\Material','material_id');
    }
}
