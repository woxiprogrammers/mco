<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';

    protected $fillable = ['address','city_id', 'pincode','is_active'];

    public function cities(){
        return $this->belongsTo('App\City','city_id');
    }
}
