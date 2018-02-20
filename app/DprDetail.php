<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DprDetail extends Model
{
    protected $table = 'dpr_details';

    protected $fillable = ['project_site_id','number_of_users','subcontractor_dpr_category_relation_id'];

    public function projectSite(){
        return $this->belongsTo('App\ProjectSite','project_site_id');
    }

    public function subcontractorDprCategoryRelation(){
        return $this->belongsTo('App\SubcontractorDPRCategoryRelation','subcontractor_dpr_category_relation_id');
    }
}
