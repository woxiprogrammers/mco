<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';

    protected $fillable = ['name','client_id','is_active','hsn_code_id','city_id','cc_mail'];

    public function project_site(){
        return $this->hasMany('App\ProjectSite','project_id');
    }

    public function client(){
        return $this->belongsTo('App\Client','client_id');
    }

    public function hsn_code(){
        return $this->belongsTo('App\HsnCode','hsn_code_id');
    }
    public function cities(){
        return $this->hasOne('App\City','state_id');
    }
}
