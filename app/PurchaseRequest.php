<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    protected $table = 'purchase_requests';

    protected $fillable = ['quotation_id','project_site_id','user_id','purchase_component_status_id','behalf_of_user_id','assigned_to','format_id'];

    public function quotation(){
        return $this->belongsTo('App\Quotation','quotation_id');
    }

    public function projectSite(){
        return $this->belongsTo('App\ProjectSite','project_site_id');
    }

    public function status(){
        return $this->belongsTo('App\PurchaseRequestComponentStatuses','purchase_component_status_id');
    }

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

    public function onBehalfOfUser(){
        return $this->belongsTo('App\User','behalf_of_user_id');
    }
}
