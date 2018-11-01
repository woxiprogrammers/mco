<?php

namespace App\Http\Controllers\Subcontractor;

use App\ExtraItem;
use App\Project;
use App\Subcontractor;
use App\SubcontractorBill;
use App\SubcontractorBillStatus;
use App\SubcontractorBillTransaction;
use App\SubcontractorStructure;
use App\SubcontractorStructureExtraItem;
use App\SubcontractorStructureSummary;
use App\SubcontractorStructureType;
use App\Summary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

    public function structureListing(Request $request){
        try{
            $user = Auth::user();
            $filterFlag = true;
            $subcontractor_name = null;
            $project_name = null;
            $ids = SubcontractorStructure::whereNull('summary_id')->pluck('id');
            if($request->has('project_name') && $filterFlag == true){
                $projectSites = Project::join('project_sites','project_sites.project_id','=','projects.id')->where('projects.name','ilike','%'.$request['project_name'].'%')->select('project_sites.id')->get()->toArray();
                $ids = SubcontractorStructure::where('project_site_id','!=', 0)
                    ->whereIn('id',$ids)
                    ->whereIn('project_site_id',$projectSites)
                    ->orderBy('created_at','desc')->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if($request->has('subcontractor_name') && $filterFlag == true){
                $subContractorid = Subcontractor::where('company_name','ilike','%'.$request['subcontractor_name'].'%')->select('id')->get()->toArray();
                $ids = SubcontractorStructure::whereIn('subcontractor_id',$subContractorid)
                    ->whereIn('id',$ids)->orderBy('created_at','desc')->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            $listingData = array();
            if ($filterFlag) {
                $listingData = SubcontractorStructure::whereIn('id',$ids)->get();
            }
            $billTotals = 0;
            $billPaidAmount = 0;
            if ($request->has('get_total')) {
                if ($filterFlag) {
                    foreach($listingData as $subcontractorStruct) {
                        $subcontractorBillIdsArray = $subcontractorStruct->subcontractorBill->where('subcontractor_bill_status_id',SubcontractorBillStatus::where('slug','approved')->pluck('id')->first())->pluck('id');
                        foreach ($subcontractorBillIdsArray as $subBillids) {
                            $subcontractorBill = SubcontractorBill::where('id',$subBillids)->first();
                            $subcontractorStructure = $subcontractorBill->subcontractorStructure;
                            $subcontractorBillTaxes = $subcontractorBill->subcontractorBillTaxes;
                            $taxTotal = 0;
                            $structureSlug = $subcontractorStructure->contractType->slug;
                            if($structureSlug == 'sqft' || $structureSlug == 'itemwise'){
                                $rate = $subcontractorStructure['rate'];
                                $subTotal = round(($subcontractorBill['qty'] * $rate),3);
                                foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                                    $taxTotal += round((($subcontractorBillTaxData['percentage'] * $subTotal) / 100),3);
                                }
                                $finalTotal = round(($subTotal + $taxTotal),3);
                            }else{
                                $rate = round(($subcontractorStructure['rate'] * $subcontractorStructure['total_work_area']),3);
                                $subTotal = round(($subcontractorBill['qty'] * $rate),3);
                                foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                                    $taxTotal += round((($subcontractorBillTaxData['percentage'] * $subTotal) / 100),3);
                                }
                                $finalTotal = round(($subTotal + $taxTotal),3);
                            }
                            $billTotals += round(($finalTotal),3);
                            $billPaidAmount += round((SubcontractorBillTransaction::where('subcontractor_bills_id',$subBillids)->sum('total')),3);
                        }
                    }
                }
                $records['billtotal'] = round($billTotals,3);
                $records['paidtotal'] = round($billPaidAmount,3);
                $records['balancetotal'] = round(($billTotals - $billPaidAmount),3);
            } else {
                $iTotalRecords = count($listingData);
                $records = array();
                $records['data'] = array();
                $end = $request->length < 0 ? count($listingData) : $request->length;
                for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($listingData); $iterator++, $pagination++) {
                    $subcontractorBillIds = $listingData[$pagination]->subcontractorBill->where('subcontractor_bill_status_id', SubcontractorBillStatus::where('slug', 'approved')->pluck('id')->first())->pluck('id');
                    $billTotals = 0;
                    $billPaidAmount = 0;
                    foreach ($subcontractorBillIds as $subcontractorStructureBillId) {
                        $subcontractorBill = SubcontractorBill::where('id', $subcontractorStructureBillId)->first();
                        $subcontractorStructure = $subcontractorBill->subcontractorStructure;
                        $subcontractorBillTaxes = $subcontractorBill->subcontractorBillTaxes;
                        $taxTotal = 0;
                        $structureSlug = $subcontractorStructure->contractType->slug;
                        if ($structureSlug == 'sqft' || $structureSlug == 'itemwise') {
                            $rate = $subcontractorStructure['rate'];
                            $subTotal = round(($subcontractorBill['qty'] * $rate), 3);
                            foreach ($subcontractorBillTaxes as $key => $subcontractorBillTaxData) {
                                $taxTotal += round((($subcontractorBillTaxData['percentage'] * $subTotal) / 100), 3);
                            }
                            $finalTotal = round(($subTotal + $taxTotal), 3);
                        } else {
                            $rate = round(($subcontractorStructure['rate'] * $subcontractorStructure['total_work_area']), 3);
                            $subTotal = round(($subcontractorBill['qty'] * $rate), 3);
                            foreach ($subcontractorBillTaxes as $key => $subcontractorBillTaxData) {
                                $taxTotal += round((($subcontractorBillTaxData['percentage'] * $subTotal) / 100), 3);
                            }
                            $finalTotal = round(($subTotal + $taxTotal), 3);
                        }
                        $billTotals += round($finalTotal, 3);
                        $billPaidAmount += round((SubcontractorBillTransaction::where('subcontractor_bills_id', $subcontractorStructureBillId)->sum('total')), 3);
                    }
                    $action = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">';
                    if ($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-subcontractor-structure') || $user->customHasPermission('view-subcontractor-structure')) {
                        $action .= '<li>
                                        <a href="/subcontractor/structure/edit/'.$listingData[$pagination]->id.'">
                                             <i class="icon-docs"></i>Edit
                                        </a>
                                     </li>
                                     <li>
                                        <a href="javascript:void(0);" onclick="getSummaries('.$listingData[$pagination]->id.')">
                                             <i class="icon-docs"></i>View
                                        </a>
                                     </li>';
                    }
                    if ($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-subcontractor-billing') || $user->customHasPermission('edit-subcontractor-billing') || $user->customHasPermission('view-subcontractor-billing') || $user->customHasPermission('approve-subcontractor-billing')) {
                        $action .= '<li><a href="javascript:void(0);">
                                            <i class="icon-docs"></i> Manage
                                        </a></li>';
                    }
                    $action .= '</ul>
                        </div>';
                    $records['data'][$iterator] = [
                        $listingData[$pagination]->subcontractor->subcontractor_name,
                        $listingData[$pagination]->projectSite->project->name,
                        $listingData[$pagination]->contractType->name,
                        $billTotals,
                        $billPaidAmount,
                        round(($billTotals - $billPaidAmount), 3),
                        date('d M Y', strtotime($listingData[$pagination]['created_at'])),
                        $action
                    ];
                }
                $records["draw"] = intval($request->draw);
                $records["recordsTotal"] = $iTotalRecords;
                $records["recordsFiltered"] = $iTotalRecords;
            }
            $status = 200;
        }catch (\Exception $e){
            $records = [];
            $status = 500;
            $data = [
                'action' => 'subcontractor structure listing',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
        return response()->json($records, $status);
    }

    public function getStructureDetails(Request $request){
        try{
            $subcontractorStructure = SubcontractorStructure::findOrFail($request->subcontractor_structure_id);
            return view('subcontractor.new_structure.details_partial')->with(compact('subcontractorStructure'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get subcontractor structure Detail view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json(['message' => 'Something went wrong.'], 500);
        }
    }

    public function getEditView(Request $request, $subcontractorStructure){
        try{
            $summaries = Summary::where('is_active', true)->select('id', 'name')->get()->toArray();
            return view('subcontractor.new_structure.edit')->with(compact('subcontractorStructure', 'summaries'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get subcontractor structure Detail view',
                'subcontractor_structure' => $subcontractorStructure,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
