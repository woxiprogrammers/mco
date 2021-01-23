<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RentBill extends Model
{
    protected $table = 'rent_bills';
    protected $fillable = ['project_site_id', 'month', 'year', 'bill_number', 'total'];

    public function projectSite()
    {
        return $this->belongsTo('App\ProjectSite', 'project_site_id');
    }
}
