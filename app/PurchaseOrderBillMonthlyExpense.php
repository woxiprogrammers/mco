<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderBillMonthlyExpense extends Model
{
    protected $table = 'purchase_order_bill_monthly_expenses';

    protected $fillable = ['project_site_id','month_id','year_id','purchase_expense','site_transfer_expense','site_transfer_bill_expense','asset_maintenance_expense','total_expense'];

    public function projectSite(){
        $this->belongsTo('App\ProjectSite','project_site_id');
    }

    public function month(){
        $this->belongsTo('App\Month','month_id');
    }

    public function year(){
        $this->belongsTo('App\Year','year_id');
    }
}
