<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequestComponentVendorRelation extends Model
{
    protected $table = 'purchase_request_component_vendor_relation';

    protected $fillable = ['purchase_request_component_id','vendor_id','is_email_sent'];

    public function mailInfo(){
        return $this->hasMany('App\PurchaseRequestComponentVendorMailInfo','purchase_request_component_vendor_relation_id');
    }

    public function vendor(){
        return $this->belongsTo('App\Vendor','vendor_id');
    }
}
