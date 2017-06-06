<?php

namespace App\Http\Controllers\CustomTraits;
use App\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait BillTrait{

    public function getCreateView(Request $request){
        try{
            //dd(123);
            $clients = Category::get()->toArray();
            return view('admin.bill.create')->with(compact('clients'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get bill create view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

}