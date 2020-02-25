<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillTypes extends Model
{
    protected $table = 'bill_types';

    protected $fillable = ['name', 'slug'];
}
