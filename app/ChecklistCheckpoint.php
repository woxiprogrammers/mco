<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChecklistCheckpoint extends Model
{
    protected $table = 'checklist_checkpoints';

    protected $fillable = ['checklist_category_id','description','is_remark_required'];

    public function checklistCategory(){
        return $this->belongsTo('App\ChecklistCategory','checklist_category_id');
    }

    public function checklistCheckpointsImages(){
        return $this->hasMany('App\ChecklistCheckpointImages','checklist_checkpoint_id');
    }

}
