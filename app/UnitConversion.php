<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitConversion extends Model
{
    protected $table = 'unit_conversions';

    protected $fillable = ['unit_1_id','unit_2_id','unit_1_value','unit_2_value'];

    public function fromUnit(){
        $this->belongsTo('App\Unit','unit_1_id');
    }

    public function toUnit(){
        $this->belongsTo('App\Unit','unit_2_id');
    }

}
