<?php

namespace App\Http\Controllers\Subcontractor;

use App\ExtraItem;
use App\Subcontractor;
use App\SubcontractorStructureType;
use App\Summary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class SubcontractorStructureController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request){
        try{
            return view('subcontractor.new_structure.manage');
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Subcontractor structure manage view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request){
        try{
            $subcontractors = Subcontractor::where('is_active',true)->orderBy('id','asc')->get(['id','subcontractor_name'])->toArray();
            $ScStrutureTypes = SubcontractorStructureType::/*whereIn('slug', ['itemwise'])->*/orderBy('id','asc')->get(['id','name','slug'])->toArray();
            $summaries = Summary::where('is_active', true)->select('id', 'name')->get()->toArray();
            $extraItems = ExtraItem::where('is_active', true)->select('id','name','rate')->orderBy('name','asc')->get();
            return view('subcontractor.new_structure.create')->with(compact('subcontractors', 'ScStrutureTypes', 'summaries', 'extraItems'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get subcontractor structure create view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createStructure(Request $request){
        try{
            dd($request->all());
        }catch (\Exception $e){
            $data = [
                'action' => 'subcontractor structure create',
                'params' => json_encode($request->all()),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
