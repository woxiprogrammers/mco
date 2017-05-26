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
    public function getCreateView() {
        try{
            return view('admin.summary.create');
        }catch(\Exception $e){

        }
    }
    public function getEditView() {
        try{
            return view('admin.summary.edit');
        }catch(\Exception $e){

        }
    }

}