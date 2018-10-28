<?php

namespace App\Http\Controllers\Subcontractor;

use App\Subcontractor;
use App\SubcontractorStructureType;
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
            $ScStrutureTypes = SubcontractorStructureType::whereIn('slug', ['itemwise'])->orderBy('id','asc')->get(['id','name','slug'])->toArray();
            return view('subcontractor.new_structure.create')->with(compact('subcontractors', 'ScStrutureTypes'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get subcontractor structure create view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
