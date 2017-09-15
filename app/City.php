<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';
    protected $fillable = ['name','slug','state_id'];

    public function state(){
        return $this->belongsTo('App\State','state_id');
    }

    public function project_sites(){

        return $this->belongsTo('App\');

    }


}
