<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpeningBalanceSite extends Model
{
    protected $table = 'opening_balance_site';
    protected $fillable = ['id','quotation_id,','opening_balance_label,','opening_balance_value'];
}
