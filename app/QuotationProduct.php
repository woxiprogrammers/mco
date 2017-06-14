<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotationProduct extends Model
{
    protected $table = 'quotation_products';

    protected $fillable = ['project_site_id'];
}
