<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillImage extends Model
{
    protected $table = 'bill_images';

    protected $fillable = ['bill_id','image'];

    public function bill(){
        return $this->belongsTo('App\Bill','bill_id');
    }
}
