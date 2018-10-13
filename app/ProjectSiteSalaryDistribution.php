<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectSiteSalaryDistribution extends Model
{
    protected $table = 'project_site_salary_distributions';

    protected $fillable = ['project_site_id','month_id','year_id','distributed_amount'];
}
