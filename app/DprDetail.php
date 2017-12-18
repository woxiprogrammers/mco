<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DprDetail extends Model
{
    protected $table = 'dpr_details';
    protected $fillable = ['project_site_id','subcontractor_id','dpr_main_category_id','number_of_users'];

}
