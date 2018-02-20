<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChecklistCheckpointImages extends Model
{
    protected $table = 'checklist_checkpoint_images';

    protected $fillable = ['checklist_checkpoint_id','caption','is_required'];

    public function checklistCheckpoint(){
        return $this->belongsTo('App\ChecklistCheckpoint','checklist_checkpoint_id');
    }
}
