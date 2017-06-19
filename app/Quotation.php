<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = 'quotations';

    protected $fillable = ['project_site_id','quotation_status_id','remark','is_tax_applied','is_summary_applied','discount','built_up_area','carpet_area'];
}
