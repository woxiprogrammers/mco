<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Labour extends Model
{
    protected $table = 'labours';

    protected $fillable = ['name','mobile','per_day_wages','project_site_id','labour_id','is_active'];

    public function projectSite(){
        return $this->belongsTo('App\ProjectSite','project_site_id');
    }
}
