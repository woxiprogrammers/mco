<?php
namespace App\Http\Controllers\CustomTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait ProfitMarginTrait{
    public function getManageView() {
        try{
            return view('admin.profitMargin.manage');
        }catch(\Exception $e){

        }
    }

}