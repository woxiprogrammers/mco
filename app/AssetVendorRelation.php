<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetVendorRelation extends Model
{
    protected $table = "asset_vendor_relation";

    protected $fillable = ['asset_id','vendor_id'];

    public function vendor(){
        return $this->belongsTo('App/Vendor','vendor_id');
    }

    public function asset(){
        return $this->belongsTo('App/Asset','asset_id');
    }
}
