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
    public function getCreateView() {
        try{
            return view('admin.units.create');
        }catch(\Exception $e){

        }
    }
    public function getEditView() {
        try{
            return view('admin.units.edit');
        }catch(\Exception $e){

        }
    }

}