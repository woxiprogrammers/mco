<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectSite extends Model
{
    protected $table = 'project_sites';

    protected $fillable = ['name','project_id','address','city_id','advanced_amount','advanced_balance',
        'distributed_salary_amount','distributed_purchase_peticash_amount','asset_rent_opening_expense'
    ];

    public function project(){
        return $this->belongsTo('App\Project','project_id','id');
    }

    public function city(){
        return $this->belongsTo('App\City','city_id');
    }

    public function peticashSiteTransfer(){
        return $this->hasMany('App\PeticashSiteTransfer','project_site_id');
    }

    public function peticashSalaryTransfer(){
        return $this->hasMany('App\PeticashSalaryTransaction','project_site_id');
    }
}
