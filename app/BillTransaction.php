<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillTransaction extends Model
{
    protected $table = 'bill_transactions';
//dd();
    protected $fillable = ['bill_id','subtotal','total','remark'];

    public function bill(){
        return $this->belongsTo('App\Bill','bill_id');
    }
}
