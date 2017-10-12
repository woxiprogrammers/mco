<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectSite extends Model
{
    protected $table = 'project_sites';

    protected $fillable = ['name','project_id','address','city_id'];

    public function project(){
        return $this->belongsTo('App\Project','project_id','id');
    }
    public function city(){
        return $this->belongsTo('App\City','city_id');
    }
}
