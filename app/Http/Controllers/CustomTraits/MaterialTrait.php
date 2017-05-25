<?php
namespace App\Http\Controllers\CustomTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait MaterialTrait{
    public function getManageView() {
       try{
           return view('admin.material.manage');
       }catch(\Exception $e){

       }
    }

}