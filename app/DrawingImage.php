<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrawingImage extends Model
{
    protected $table = 'drawing_images';
    protected $fillable = ['random_string','drawing_category_site_relation_id'];

    public function drawingImageVersions(){
        return $this->hasMany('App\DrawingImageVersion','drawing_image_id');
    }

    public function drawingCategorySiteRelation(){
        return $this->belongsTo('App\DrawingCategorySiteRelation','drawing_category_site_relation_id');
    }
}
