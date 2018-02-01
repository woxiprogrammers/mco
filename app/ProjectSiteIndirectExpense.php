<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectSiteIndirectExpense extends Model
{
    protected $table = 'project_site_indirect_expenses';

    protected $fillable = ['project_site_id','gst','tds'];

    public function projectSite(){
        return $this->belongsTo('App\ProjectSite','project_site_id');
    }
}
