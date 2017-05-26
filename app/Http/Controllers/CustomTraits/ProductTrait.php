<?php
namespace App\Http\Controllers\CustomTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait ProductTrait{
    public function getManageView() {
        try{
            return view('admin.product.manage');
        }catch(\Exception $e){

        }
    }

}