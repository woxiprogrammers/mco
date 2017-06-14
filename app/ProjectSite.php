<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectSite extends Model
{
    protected $table = 'project_sites';

    protected $fillable = ['name','project_id','address'];
}
