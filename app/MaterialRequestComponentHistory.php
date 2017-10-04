<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialRequestComponentHistory extends Model
{
    protected $table = 'material_request_component_history_table';

    protected $fillable = ['material_request_component_id','component_status_id','user_id','remark'];

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

    public function materialRequestComponent(){
        return $this->belongsTo('App\MaterialRequestComponents','material_request_component_id');
    }

    public function componentStatus(){
        return $this->belongsTo('App\PurchaseRequestComponentStatuses','component_status_id');
    }
}