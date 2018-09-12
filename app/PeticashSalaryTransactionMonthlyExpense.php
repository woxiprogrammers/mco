<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeticashSalaryTransactionMonthlyExpense extends Model
{
    protected $table = 'peticash_salary_transaction_monthly_expenses';

    protected $fillable = ['project_site_id','month_id','year_id','total_expense'];

    public function projectSite(){
        $this->belongsTo('App\ProjectSite','project_site_id');
    }

    public function month(){
        $this->belongsTo('App\Month','month_id');
    }

    public function year(){
        $this->belongsTo('App\Year','year_id');
    }
}
