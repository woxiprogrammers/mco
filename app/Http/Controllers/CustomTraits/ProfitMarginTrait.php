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
    public function getCreateView() {
        try{
            return view('admin.profitMargin.create');
        }catch(\Exception $e){

        }
    }
    public function getEditView() {
        try{
            return view('admin.profitMargin.edit');
        }catch(\Exception $e){

        }
    }

}