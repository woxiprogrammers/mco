<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HsnCode extends Model
{
    protected $table = 'hsn_codes';

    protected $fillable = ['code','description'];

    public function projects(){
        return $this->hasMany('App\Project','hsn_code_id');
    }
}
