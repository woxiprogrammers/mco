<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';
    protected $fillable = ['name','slug'];

        public function states(){
        return $this->hasMany('App\State','country_id');
    }
}
