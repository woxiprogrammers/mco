<?php

namespace App\Http\Controllers\CheckList;

use App\ChecklistCategory;
use App\Http\Controllers\CustomTraits\CheckListTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ChecklistController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request)
    {
        try {
            return view('checklist.structure.manage');
        } catch (\Exception $e) {
            $data = [
                'action' => "Get Check List manage view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request)
    {
        try {
            $mainCategories = ChecklistCategory::whereNull('category_id')->where('is_active', true)->select('id','name')->get();
            return view('checklist.structure.create')->with(compact('mainCategories'));
        } catch (\Exception $e) {
            $data = [
                'action' => "Get check list create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getSubCategories(Request $request){
        try{
            $subCategories = ChecklistCategory::where('category_id',$request->category_id)->where('is_active', true)->select('id','name')->get();
            $response = '<option value="">-- Select Sub Category --</option>';
            foreach($subCategories as $subCategory){
                $response .= '<option value="'.$subCategory['id'].'">'.$subCategory['name'].'</option>';
            }
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get checklist subcategories',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_decode($data));
            $status = 500;
            $response = '';
        }
        return response()->json($response,$status);
    }

    public function createStructure(Request $request){
        try {
            dd($request->all());
        } catch (\Exception $e) {
            $data = [
                'action' => "Create Checklist Structure",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}


