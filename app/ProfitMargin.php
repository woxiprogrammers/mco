<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class ProfitMargin extends Model
{
    protected $table = 'profit_margins';

    protected $fillable = ['name','slug','base_percentage','is_active'];
    use Sluggable;
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
