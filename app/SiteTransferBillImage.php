<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteTransferBillImage extends Model
{
    protected $table = 'site_transfer_bill_images';

    protected $fillable = ['site_transfer_bill_id','name'];

    public function siteTransferBill(){
        return $this->belongsTo('App\SiteTransferBill','site_transfer_bill_id');
    }
}
