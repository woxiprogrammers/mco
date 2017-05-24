<?php
/**
 * Created by Ameya Joshi
 * Date: 23/5/17
 */


namespace App\Http\Controllers\CustomTraits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait CategoryTrait{

    public function getCreateView(Request $request){
        try{
            return view('admin.category.create');
        }catch(\Exception $e){
            $data = [
                'action' => "Get category create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }


    public function getEditView(Request $request){
        try{
            return view('admin.category.edit');
        }catch(\Exception $e){
            $data = [
                'action' => "Get category edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}