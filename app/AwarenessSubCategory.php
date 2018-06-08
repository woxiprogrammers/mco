<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AwarenessSubCategory extends Model
{
    protected $table = 'awareness_sub_category';

    protected $fillable = ['awareness_main_category_id','name','is_active'];

    public function awarenessMainCategory(){
        return $this->belongsTo('App\AwarenessMainCategory','awareness_main_category_id');
    }
}
