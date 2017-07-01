<?php
/**
 * Created by Ameya Joshi.
 * Date: 19/6/17
 * Time: 3:00 PM
 */

namespace App\Helper;

use App\Unit;
use App\UnitConversion;
use Illuminate\Support\Facades\Log;

class UnitHelper{

    public static function unitConversion($fromUnit,$toUnit, $rate){
        Log::info('from ');
        Log::info($fromUnit);
        Log::info('to');
        Log::info($toUnit);
        $conversion = UnitConversion::where('unit_1_id',$fromUnit)->where('unit_2_id',$toUnit)->first();
        if($conversion != null){
            $materialRateFrom = $conversion->unit_1_value / $conversion->unit_2_value;
            $materialRateTo = $rate * $materialRateFrom;
        }else{
            $conversion = UnitConversion::where('unit_2_id',$fromUnit)->where('unit_1_id',$toUnit)->first();
            if($conversion != null){
                $materialRateFrom = $conversion->unit_2_value / $conversion->unit_1_value;
                $materialRateTo = $rate * $materialRateFrom;
            }else{
                $materialRateTo['unit'] = $fromUnit;
                $materialRateTo['rate'] = $rate;
                $unit1 = Unit::where('id',$fromUnit)->pluck('name')->first();
                $unit2 = Unit::where('id',$toUnit)->pluck('name')->first();
                $materialRateTo['message'] = "$unit1-$unit2 conversion is not present.";
            }
        }
        return $materialRateTo;
    }
}