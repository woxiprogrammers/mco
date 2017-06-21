<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';

    protected $fillable = ['name','client_id','is_active'];

    public function project_site(){
        return $this->hasMany('App\ProjectSite','project_id');
    }

    public function client(){
        return $this->belongsTo('App\Client','client_id');
    }
}
