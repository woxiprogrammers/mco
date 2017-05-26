<?php
namespace App\Http\Controllers\CustomTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait SummaryTrait{
    public function getManageView() {
        try{
            return view('admin.summary.manage');
        }catch(\Exception $e){

        }
    }

}