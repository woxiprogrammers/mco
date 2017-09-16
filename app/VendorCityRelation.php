<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorCityRelation extends Model
{
    protected $table = 'vendor_city_relation';

    protected $fillable = ['vendor_id','city_id'];

    public function vendor(){
        return $this->belongsTo('App\Vendor','vendor_id');
    }

    public function city(){
        return $this->belongsTo('App\City','city_id');
    }
}
