<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectSiteReceiptPayment extends Model
{
    protected $table = 'project_site_receipt_payments';

    protected $fillable = ['project_site_id','payment_id','amount','reference_number','bank_id','paid_from_slug','adv_receipt_date'];

    public function paymentType(){
        return $this->belongsTo('App\PaymentType','payment_id');
    }

    public function projectSite(){
        return $this->belongsTo('App\ProjectSite','project_site_id');
    }

    public function bank(){
        return $this->belongsTo('App\BankInfo','bank_id');
    }
}
