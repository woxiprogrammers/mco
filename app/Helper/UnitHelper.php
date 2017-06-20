<?php
/**
 * Created by Ameya Joshi.
 * Date: 19/6/17
 * Time: 3:00 PM
 */
namespace App\Helper;

use App\UnitConversion;

class UnitHelper{

    public static function unitConversion($fromUnit,$toUnit, $rate){
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
            }
        }
        return $materialRateTo;
    }
}