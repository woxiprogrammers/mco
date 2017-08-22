<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'vendors';

    protected $fillable = ['name', 'company', 'mobile', 'email', 'gstin', 'alternate_contact','city'];
}