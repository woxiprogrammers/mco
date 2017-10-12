<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequestComponentVendorMailInfo extends Model
{
    protected $table = 'purchase_request_component_vendor_mail_info';

    protected $fillable = ['purchase_request_component_vendor_relation_id','user_id','created_at','updated_at'];

    public function componentVendorRelation(){
        return $this->belongsTo('App\PurchaseRequestComponentVendorRelation','purchase_request_component_vendor_relation_id');
    }
}
