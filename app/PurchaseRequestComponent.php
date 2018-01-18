<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequestComponent extends Model
{
    protected $table = 'purchase_request_components';

    protected $fillable = ['purchase_request_id','material_request_component_id'];

    public function purchaseRequest(){
        return $this->belongsTo('App\PurchaseRequest','purchase_request_id');
    }

    public function materialRequestComponent(){
        return $this->belongsTo('App\MaterialRequestComponents','material_request_component_id');
    }

    public function vendorRelations(){
        return $this->hasMany('App\PurchaseRequestComponentVendorRelation','purchase_request_component_id');
    }
}
