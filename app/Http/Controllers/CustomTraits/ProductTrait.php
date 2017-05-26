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
    public function getCreateView() {
        try{
            return view('admin.product.create');
        }catch(\Exception $e){

        }
    }
    public function getEditView() {
        try{
            return view('admin.product.edit');
        }catch(\Exception $e){

        }
    }

}