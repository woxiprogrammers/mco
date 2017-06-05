<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientUser extends Model
{
    protected $table = 'categories';

    protected $fillable = ['user_id','client_id'];
}
