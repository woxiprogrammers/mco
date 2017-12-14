<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class ChecklistCategory extends Model
{
    protected $table = 'checklist_categories';

    protected $fillable = ['name','slug','is_active','category_id'];

    use Sluggable;
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function mainCategory(){
        return $this->belongsTo('App\ChecklistCategory','category_id');
    }

    public function checkpoints(){
        return $this->hasMany('App\ChecklistCheckpoint','checklist_category_id');
    }
}
