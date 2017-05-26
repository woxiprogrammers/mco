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
    public function getCreateView() {
        try{
            return view('admin.material.create');
        }catch(\Exception $e){

        }
    }
    public function getEditView() {
        try{
            return view('admin.material.edit');
        }catch(\Exception $e){

        }
    }

}