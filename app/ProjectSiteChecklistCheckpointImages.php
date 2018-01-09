<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectSiteChecklistCheckpointImages extends Model
{
    protected $table = 'project_site_checklist_checkpoint_images';

    protected $fillable = ['project_site_checklist_checkpoint_id','caption','is_required'];

    public function projectSiteChecklistCheckpoint(){
        return $this->belongsTo('App\ProjectSiteChecklistCheckpoint','project_site_checklist_checkpoint_id');
    }
}
