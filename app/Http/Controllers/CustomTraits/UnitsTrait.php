<?php
namespace App\Http\Controllers\CustomTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait UnitsTrait{
    public function getManageView() {
        try{
            return view('admin.units.manage');
        }catch(\Exception $e){

        }
    }

}