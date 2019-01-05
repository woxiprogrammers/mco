<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotationSummary extends Model
{
    protected $table = 'quotation_summaries';

    protected $fillable = ['quotation_id','summary_id','rate_per_sqft'];

    public function summary(){
        return $this->belongsTo('App\Summary','summary_id');
    }
}
