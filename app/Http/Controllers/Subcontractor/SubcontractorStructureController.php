<?php

namespace App\Http\Controllers\Subcontractor;

use App\ExtraItem;
use App\Subcontractor;
use App\SubcontractorStructure;
use App\SubcontractorStructureExtraItem;
use App\SubcontractorStructureSummary;
use App\SubcontractorStructureType;
use App\Summary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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
            $subcontractorStructureData = [
                'subcontractor_id' => (int)$request->subcontractor_id,
                'sc_structure_type_id' => SubcontractorStructureType::where('slug', $request->structure_type)->pluck('id')->first(),
            ];
            if($projectSiteId = Session::get('global_project_site')){
                $subcontractorStructureData['project_site_id'] = (int) $projectSiteId;
            } elseif ($projectSiteId = $request->project_site_id){
                $subcontractorStructureData['project_site_id'] = (int) $projectSiteId;
            } else {
                $request->session()->flash('error', 'Project site is not selected');
                return redirect('/subcontractor/structure/create');
            }
            $subcontractorStructure = SubcontractorStructure::create($subcontractorStructureData);
            $structureSummaryData = [
                'subcontractor_structure_id' => $subcontractorStructure->id
            ];
            foreach($request->summaries as $summaryId){
                $structureSummaryData['summary_id'] = (int) $summaryId;
                $structureSummaryData['rate'] = (float) $request->rate[$summaryId];
                $structureSummaryData['description'] = $request->description[$summaryId];
                $structureSummaryData['total_work_area'] = (float)$request->total_work_area[$summaryId];
                $structureSummary = SubcontractorStructureSummary::create($structureSummaryData);
            }
            $structureExtraItemData = [
                'subcontractor_structure_id' => $subcontractorStructure->id
            ];
            foreach($request->extra_items as $extraItemId => $rate){
                $structureExtraItemData['extra_item_id'] = $extraItemId;
                $structureExtraItemData['rate'] = (double)$rate;
                $structureExtraItem = SubcontractorStructureExtraItem::create($structureExtraItemData);
            }
            $request->session()->flash('success', 'Subcontractor structure created successfully.');
            return redirect('/subcontractor/structure/manage');
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
