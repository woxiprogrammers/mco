<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubcontractorStructureType extends Model
{
    protected $table = 'subcontractor_structure_types';

    protected $fillable = ['name','slug','created_at','updated_at'];
}
