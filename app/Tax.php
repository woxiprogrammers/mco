<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = 'taxes';

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

    public function quotation_tax_version(){
        return $this->hasMany('App\QuotationTaxVersion','tax_id');
    }
}
