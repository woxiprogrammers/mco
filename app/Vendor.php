<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'vendor';

    protected $fillable = ['name', 'company', 'mobile', 'email', 'gstin', 'alternate_contact','city','is_active'];
}