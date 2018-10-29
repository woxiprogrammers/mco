<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetRentMonthlyExpenses extends Model
{
    protected $table = "asset_rent_monthly_expenses";

    protected $fillable = ['project_site_id','asset_id','year_id','january','february','march','april','may'
    ,'june','july','august','september','october','november','december'];
}
