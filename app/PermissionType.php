<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PermissionType extends Model
{
    protected $table = 'permission_types';

    protected $fillable = ['name','slug'];
}
