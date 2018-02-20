<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialRequests extends Model
{
    protected $table = 'material_requests';

    protected $fillable = ['project_site_id','quotation_id','user_id','assigned_to','on_behalf_of','serial_no','format_id'];

    public function projectSite(){
        return $this->belongsTo('App\ProjectSite','project_site_id');
    }

    public function materialRequestComponents(){
        return $this->hasMany('App\MaterialRequestComponents','material_request_id');
    }

    public function onBehalfOf(){
        return $this->belongsTo('App\User','on_behalf_of');
    }

    public function assignedToUser(){
        return $this->belongsTo('App\User','assigned_to');
    }

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

    public function quotation(){
        return $this->belongsTo('App\Quotation','quotation_id');
    }
}
