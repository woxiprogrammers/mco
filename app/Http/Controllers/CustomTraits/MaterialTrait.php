<?php
namespace App\Http\Controllers\CustomTraits;
use App\Category;
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
            $categories = Category::select('id','name')->orderBy('name','asc')->get()->toArray();
//            $units = Un
            return view('admin.material.create')->with(compact('categories'));
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