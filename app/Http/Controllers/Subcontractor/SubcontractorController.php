<?php

namespace App\Http\Controllers\Subcontractor;

use App\BankInfo;
use App\DprMainCategory;
use App\Employee;
use App\Http\Controllers\CustomTraits\PeticashTrait;
use App\PaymentType;
use App\Project;
use App\ProjectSite;
use App\Quotation;
use App\QuotationStatus;
use App\Subcontractor;
use App\SubcontractorAdvancePayment;
use App\SubcontractorBill;
use App\SubcontractorBillReconcileTransaction;
use App\SubcontractorBillStatus;
use App\SubcontractorBillTax;
use App\SubcontractorBillTransaction;
use App\SubcontractorDPRCategoryRelation;
use App\SubcontractorStructure;
use App\SubcontractorStructureType;
use App\Summary;
use App\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Client;
use Illuminate\Support\Facades\Session;

class SubcontractorController extends Controller
{
    use PeticashTrait;
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getCreateView(Request $request){
        try{
            return view('subcontractor.create');
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Create View',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createSubcontractor(Request $request){
        try{
            $scData = $request->except('_token');
            $scData['is_active'] = (boolean)false;
            Subcontractor::create($scData);
            $request->session()->flash('success', 'Subcontractor Created successfully.');
            return redirect('/subcontractor/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Subcontractor',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageView(Request $request){
        try{
            return view('subcontractor.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Manage view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function subcontractorListing(Request $request){
        try{
            $user = Auth::user();
            $listingData = Subcontractor::get();
            $iTotalRecords = count($listingData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($listingData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($listingData); $iterator++,$pagination++ ){
                if( $listingData[$pagination]['is_active'] == true){
                    $labourStatus = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $labourStatus = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-manage-user')){
                    $actionButton = '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/subcontractor/edit/'.$listingData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a href="/subcontractor/change-status/'.$listingData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> '.$status.' </a>
                            </li>
                        </ul>
                    </div>';
                }else{
                    $actionButton = '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/subcontractor/edit/'.$listingData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                        </ul>
                    </div>';
                }
                $records['data'][$iterator] = [
                    $listingData[$pagination]['subcontractor_name'],
                    $listingData[$pagination]['company_name'],
                    $listingData[$pagination]['primary_cont_person_name'],
                    $listingData[$pagination]['primary_cont_person_mob_number'],
                    $listingData[$pagination]['escalation_cont_person_name'],
                    $listingData[$pagination]['escalation_cont_person_mob_number'],
                    $labourStatus,
                    $actionButton
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Subcontractor Listing',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,200);
    }

    public function changeSubcontractorStatus(Request $request, $labour){
        try{
            $newStatus = (boolean)!$labour->is_active;
            $labour->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Subcontractor Status changed successfully.');
            return redirect('/subcontractor/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change Subcontractor status',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request, $subcontractor){
        try{
            $transaction_types = PaymentType::get();
            return view('subcontractor.edit')->with(compact('subcontractor','transaction_types'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get role edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editSubcontractor(Request $request,$subcontractor){
        try{
            $updateLabourData = $request->except('_token');
            Subcontractor::where('id',$subcontractor['id'])->update($updateLabourData);
            $request->session()->flash('success', 'Subcontractor Edited successfully.');
            return redirect('/subcontractor/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Subcontractor',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageStructureView(Request $request) {
        try{
            $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
            $projectSiteIds = Quotation::where('quotation_status_id',$approvedQuotationStatus['id'])->pluck('project_site_id')->toArray();
            $projectIds = ProjectSite::whereIn('id',$projectSiteIds)->pluck('project_id')->toArray();
            $clientIds = Project::whereIn('id',$projectIds)->pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$clientIds)->where('is_active',true)->orderBy('id','asc')->get()->toArray();
            return view('subcontractor.structure.manage')->with(compact('clients'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Structure Manage view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getSubcontractorStructureCreateView(Request $request) {
        try{
            $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
            $projectSiteIds = Quotation::where('quotation_status_id',$approvedQuotationStatus['id'])->pluck('project_site_id')->toArray();
            $projectIds = ProjectSite::whereIn('id',$projectSiteIds)->pluck('project_id')->toArray();
            $clientIds = Project::whereIn('id',$projectIds)->pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$clientIds)->where('is_active',true)->orderBy('id','asc')->get()->toArray();
            $projectSites = ProjectSite::select('id','name')->get()->toArray();
            $subcontractor = Subcontractor::where('is_active',true)->orderBy('id','asc')->get(['id','subcontractor_name'])->toArray();
            $summary = Summary::where('is_active',true)->orderBy('id','asc')->get(['id','name'])->toArray();
            $ScStrutureTypes = SubcontractorStructureType::orderBy('id','asc')->get(['id','name','slug'])->toArray();
            $taxes = Tax::whereNotIn('slug',['vat'])->where('is_active',true)->where('is_special',false)->select('id','name','slug','base_percentage')->get();
            return view('subcontractor.structure.create')->with(compact('projectSites','clients','subcontractor','summary','ScStrutureTypes','taxes'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Structure Create View',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createSubcontractorStructure(Request $request) {
        try{
            if(Session::has('global_project_site')){
                $selectedGlobalProjectSiteID = Session::get('global_project_site');
            }else{
                $selectedGlobalProjectSiteID = 0;
            }
            $subcontractorStructure = SubcontractorStructure::create([
                'project_site_id' => $selectedGlobalProjectSiteID,
                'subcontractor_id' => $request['subcontractor_id'],
                'summary_id' => $request['summary_id'],
                'sc_structure_type_id' => SubcontractorStructureType::where('slug',$request['structure_type'])->pluck('id')->first(),
                'rate' => $request['rate'],
                'total_work_area' => $request['total_work_area'],
                'description' => $request['description'],
            ]);
            $request->session()->flash('success', 'Subcontractor Structured Created successfully.');
            return redirect('/subcontractor/subcontractor-structure/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Subcontractor Structure',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function subcontractorStructureListing(Request $request){
        try{
            $user = Auth::user();
            $filterFlag = true;
            $subcontractor_name = null;
            $project_name = null;
            if ($request->has('subcontractor_name')) {
                $subcontractor_name = $request['subcontractor_name'];
            }
            $ids = SubcontractorStructure::pluck('id');

            if($request->has('project_name') && $filterFlag == true){
                $projectSites = Project::join('project_sites','project_sites.project_id','=','projects.id')->where('projects.name','ilike','%'.$request['project_name'].'%')->select('project_sites.id')->get()->toArray();
                $ids = SubcontractorStructure::where('project_site_id','!=', 0)->whereIn('project_site_id',$projectSites)->orderBy('created_at','desc')->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if($request->has('subcontractor_name') && $filterFlag == true){
                $subContractorid = Subcontractor::where('company_name','ilike','%'.$request['subcontractor_name'].'%')->select('id')->get()->toArray();
                $ids = SubcontractorStructure::whereIn('subcontractor_id',$subContractorid)->orderBy('created_at','desc')->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            $listingData = array();
            if ($filterFlag) {
                $listingData = SubcontractorStructure::whereIn('id',$ids)->get();
            }
            $total = 0;
            $billTotals = 0;
            $billPaidAmount = 0;
            if ($request->has('get_total')) {
                if ($filterFlag) {
                    foreach($listingData as $subcontractorStruct) {
                        $total = $total + ($subcontractorStruct['rate']*$subcontractorStruct['total_work_area']);
                        $subcontractorBillIdsArray = $subcontractorStruct->subcontractorBill->where('subcontractor_bill_status_id',SubcontractorBillStatus::where('slug','approved')->pluck('id')->first())->pluck('id');
                        foreach ($subcontractorBillIdsArray as $subBillids) {
                            $subcontractorBill = SubcontractorBill::where('id',$subBillids)->first();
                            $subcontractorStructure = $subcontractorBill->subcontractorStructure;
                            $subcontractorBillTaxes = $subcontractorBill->subcontractorBillTaxes;
                            $taxTotal = 0;
                            $structureSlug = $subcontractorStructure->contractType->slug;
                            if($structureSlug == 'sqft'){
                                $rate = $subcontractorStructure['rate'];
                                $subTotal = $subcontractorBill['qty'] * $rate;
                                foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                                    $taxTotal += ($subcontractorBillTaxData['percentage'] * $subTotal) / 100;
                                }
                                $finalTotal = $subTotal + $taxTotal;
                            }else{
                                $rate = $subcontractorStructure['rate'] * $subcontractorStructure['total_work_area'];
                                $subTotal = $subcontractorBill['qty'] * $rate;
                                foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                                    $taxTotal += ($subcontractorBillTaxData['percentage'] * $subTotal) / 100;
                                }
                                $finalTotal = $subTotal + $taxTotal;
                            }
                            $billTotals += $finalTotal;
                            $billPaidAmount += SubcontractorBillTransaction::where('subcontractor_bills_id',$subBillids)->sum('total');
                        }
                    }
                }
                $records['total'] = $total;
                $records['billtotal'] = $billTotals;
                $records['paidtotal'] = $billPaidAmount;
                $records['balancetotal'] = $billTotals - $billPaidAmount;
            } else {
                $iTotalRecords = count($listingData);
                $records = array();
                $records['data'] = array();
                $end = $request->length < 0 ? count($listingData) : $request->length;
                for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($listingData); $iterator++,$pagination++ ){
                    $subcontractorBillIds = $listingData[$pagination]->subcontractorBill->where('subcontractor_bill_status_id',SubcontractorBillStatus::where('slug','approved')->pluck('id')->first())->pluck('id');
                    $billTotals = 0;
                    $billPaidAmount = 0;
                    foreach ($subcontractorBillIds as $subcontractorStructureBillId){
                        $subcontractorBill = SubcontractorBill::where('id',$subcontractorStructureBillId)->first();
                        $subcontractorStructure = $subcontractorBill->subcontractorStructure;
                        $subcontractorBillTaxes = $subcontractorBill->subcontractorBillTaxes;
                        $taxTotal = 0;
                        $structureSlug = $subcontractorStructure->contractType->slug;
                        if($structureSlug == 'sqft'){
                            $rate = $subcontractorStructure['rate'];
                            $subTotal = $subcontractorBill['qty'] * $rate;
                            foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                                $taxTotal += ($subcontractorBillTaxData['percentage'] * $subTotal) / 100;
                            }
                            $finalTotal = $subTotal + $taxTotal;
                        }else{
                            $rate = $subcontractorStructure['rate'] * $subcontractorStructure['total_work_area'];
                            $subTotal = $subcontractorBill['qty'] * $rate;
                            foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                                $taxTotal += ($subcontractorBillTaxData['percentage'] * $subTotal) / 100;
                            }
                            $finalTotal = $subTotal + $taxTotal;
                        }
                        $billTotals += $finalTotal;
                        $billPaidAmount += SubcontractorBillTransaction::where('subcontractor_bills_id',$subcontractorStructureBillId)->sum('total');
                    }
                    $action = '';
                    if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-subcontractor-billing') || $user->customHasPermission('edit-subcontractor-billing') || $user->customHasPermission('view-subcontractor-billing') || $user->customHasPermission('approve-subcontractor-billing')){
                        $action .= '<a href="/subcontractor/subcontractor-bills/manage/'.$listingData[$pagination]['id'].'" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true">
                                            <i class="icon-docs"></i> Manage
                                        </a>';
                    }
                    if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-subcontractor-structure') || $user->customHasPermission('view-subcontractor-structure')){
                        $action .= '<a href="/subcontractor/subcontractor-structure/view/'.$listingData[$pagination]['id'].'" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true">
                                             <i class="icon-docs"></i>View
                                        </a>';
                    }
                    $total_amount = $listingData[$pagination]['rate'] * $listingData[$pagination]['total_work_area'];
                    $records['data'][$iterator] = [
                        $listingData[$pagination]->subcontractor->subcontractor_name,
                        $listingData[$pagination]->projectSite->project->name,
                        $listingData[$pagination]->summary->name,
                        $listingData[$pagination]->contractType->name,
                        $listingData[$pagination]['rate'],
                        $listingData[$pagination]['total_work_area'],
                        $total_amount,
                        $billTotals,
                        $billPaidAmount,
                        $billTotals-$billPaidAmount,
                        date('d M Y',strtotime($listingData[$pagination]['created_at'])),
                        $action
                    ];
                }
                $records["draw"] = intval($request->draw);
                $records["recordsTotal"] = $iTotalRecords;
                $records["recordsFiltered"] = $iTotalRecords;
            }
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Subcontractor Listing',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,200);
    }

    public function getSubcontractorStructureEditView(Request $request, $subcontractor_struct){
        try{
            $subcontractor = Subcontractor::where('is_active',true)->where('id',$subcontractor_struct->subcontractor_id)->orderBy('id','asc')->get(['id','subcontractor_name'])->toArray();
            $summary = Summary::where('is_active',true)->where('id',$subcontractor_struct->summary_id)->orderBy('id','asc')->get(['id','name'])->toArray();
            $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
            $projectSiteIds = Quotation::where('quotation_status_id',$approvedQuotationStatus['id'])->pluck('project_site_id')->toArray();
            $projectIds = ProjectSite::whereIn('id',$projectSiteIds)->pluck('project_id')->toArray();
            $clientIds = Project::whereIn('id',$projectIds)->pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$clientIds)->where('is_active',true)->orderBy('id','asc')->get()->toArray();
            $projectSites = ProjectSite::select('id','name')->get()->toArray();
            return view('subcontractor.structure.edit')->with(compact('summary','subcontractor_struct','projectSites','clients','subcontractor'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get role edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editSubcontractorStructure(Request $request,$labour){
        try{
            if($request['project_site_id'] != -1){
                $updateLabourData = $request->except('_token');
            }else{
                $updateLabourData = $request->except('_token','project_site_id');
            }
            $updateLabourData['employee_type_id'] = EmployeeType::where('slug','labour')->pluck('id')->first();
            Employee::where('id',$labour['id'])->update($updateLabourData);
            $request->session()->flash('success', 'Subcontractor Edited successfully.');
            return redirect('/subcontractor/subcontractor-structure/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Subcontractor',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getProjects(Request $request, $client){
        try{
            $status = 200;
            if ($client == 0) {
                $projectOptions[] = '<option value="0">ALL</option>';
            } else {
                $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
                $projectSiteIds = Quotation::where('quotation_status_id',$approvedQuotationStatus['id'])->pluck('project_site_id')->toArray();
                $projectIds = ProjectSite::whereIn('id',$projectSiteIds)->pluck('project_id')->toArray();
                $projects = Project::where('client_id',$client)->whereIn('id',$projectIds)->get()->toArray();
                $projectOptions = array();
                for($i = 0 ; $i < count($projects); $i++){
                    $projectOptions[] = '<option value="'.$projects[$i]['id'].'"> '.$projects[$i]['name'].' </option>';
                }
            }
        }catch (\Exception $e){
            $projectOptions = array();
            $status = 500;
            $data = [
                'actions' => 'Get Project from client',
                'params' => $request->all(),
                'exception' => $e->getMessage(),
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($projectOptions,$status);
    }

    public function getProjectSites(Request $request,$project){
        try{
            $status = 200;
            if ($project == 0) {
                $projectSitesOptions[] = '<option value="0">ALL</option>';
            } else {
                $projectSites = ProjectSite::where('project_id', $project)->get()->toArray();
                $projectSitesOptions = array();
                for($i = 0 ; $i < count($projectSites); $i++){
                    $projectSitesOptions[] = '<option value="'.$projectSites[$i]['id'].'"> '.$projectSites[$i]['name'].' </option>';
                }
            }
        }catch (\Exception $e){
            $projectSitesOptions = array();
            $status = 500;
            $data = [
                'actions' => 'Get Project Site',
                'params' => $request->all(),
                'exception' => $e->getMessage(),
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($projectSitesOptions,$status);
    }

    public function dprAutoSuggest(Request $request,$keyword){
        try{
            $status = 200;
            $response = array();
            $dprCategories = DprMainCategory::where('name','ilike','%'.$keyword.'%')->where('status', true)->get();
            $iterator = 0;
            foreach ($dprCategories as $dprCategory){
                $response[$iterator]['dpr_category_id'] = $dprCategory['id'];
                $response[$iterator]['dpr_category_name'] = $dprCategory['name'];
                $iterator++;
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Dpr category auto suggest',
                'keyword' => $keyword,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = array();
        }
        return response()->json($response,$status);
    }

    public function assignDprCategories(Request $request,$subcontractor){
        try{
            SubcontractorDPRCategoryRelation::where('subcontractor_id',$subcontractor->id)->whereNotIn('dpr_main_category_id',$request->dpr_categories)->delete();
            foreach ($request->dpr_categories as $dprCateogoryId){
                $subcontractorDprCategoryRelation = SubcontractorDPRCategoryRelation::where('subcontractor_id',$subcontractor->id)->where('dpr_main_category_id',$dprCateogoryId)->first();
                if($subcontractorDprCategoryRelation == null){
                    $subcontractorDprCategoryRelationData = [
                        'subcontractor_id' => $subcontractor->id,
                        'dpr_main_category_id' => $dprCateogoryId
                    ];
                    SubcontractorDPRCategoryRelation::create($subcontractorDprCategoryRelationData);
                }
            }
            $request->session()->flash('success','DPR categories assinged to subcontractor');
            return redirect('/subcontractor/edit/'.$subcontractor->id);
        }catch (\Exception $e){
            $data = [
                'action' => 'Assign DPR categories to subcontractor',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getBillManageView(Request $request,$subcontractorStructureId) {
        try{
            $taxData= SubcontractorBillTax::join('subcontractor_bills','subcontractor_bills.id','=','subcontractor_bill_taxes.subcontractor_bills_id')
                ->join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                ->join('taxes','subcontractor_bill_taxes.tax_id','=','taxes.id')
                ->where('subcontractor_structure.id',$subcontractorStructureId)->distinct('taxes.id')
                ->orderBy('taxes.id')->select('taxes.id','taxes.name')->get()->toArray();
            $taxes = array_column($taxData,'name');
            return view('subcontractor.structure.bill.manage')->with(compact('taxes','subcontractorStructureId'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Structure Bill Manage view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getBillListing(Request $request,$subcontractorStructureId,$billStatusSlug){
        try{
            $billArrayNo = 1;
            if($billStatusSlug == "disapproved"){
                $listingData = SubcontractorBill::join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                    ->where('subcontractor_structure.id',$subcontractorStructureId)
                    ->where('subcontractor_bill_status_id',SubcontractorBillStatus::where('slug','disapproved')->pluck('id')->first())
                    ->orderBy('subcontractor_bills.id','asc')
                    ->select('subcontractor_bills.id','subcontractor_bills.qty','subcontractor_bills.subcontractor_bill_status_id','subcontractor_structure.sc_structure_type_id','subcontractor_structure.rate','subcontractor_structure.total_work_area')->get();
            }else{
                $listingData = SubcontractorBill::join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                    ->where('subcontractor_structure.id',$subcontractorStructureId)
                    ->whereIn('subcontractor_bill_status_id',SubcontractorBillStatus::whereIn('slug',['approved','draft'])->pluck('id'))
                    ->orderBy('subcontractor_bills.id','asc')
                    ->select('subcontractor_bills.id','subcontractor_bills.qty','subcontractor_bills.subcontractor_bill_status_id','subcontractor_structure.sc_structure_type_id','subcontractor_structure.rate','subcontractor_structure.total_work_area')->get();
            }
            if ($request->has('get_total')) {
                $finalAmount = $paidAmount = 0;
                foreach($listingData as $data){
                    $structureTypeSlug = SubcontractorStructureType::where('id',$data['sc_structure_type_id'])->pluck('slug')->first();
                    if($structureTypeSlug == 'sqft'){
                        $rate = $data['rate'];
                        $basicAmount = $rate * $data['qty'];
                    }else{
                        $rate = $data['rate'] * $data['total_work_area'];
                        $basicAmount = $rate * $data['qty'];
                    }
                    $taxesApplied = SubcontractorBillTax::where('subcontractor_bills_id',$data['id'])->sum('percentage');
                    $taxAmount = $basicAmount * ($taxesApplied / 100);
                    $finalAmount += $basicAmount + $taxAmount;
                    $paidAmount += SubcontractorBillTransaction::where('subcontractor_bills_id', $data['id'])->sum('total');
                }
                $records['final_amount'] = $finalAmount;
                $records['paid_amount'] = $paidAmount;
                $records['pending_amount'] = $finalAmount - $paidAmount;
            }else{
                $iTotalRecords = count($listingData);
                $records = array();
                $records['data'] = array();
                $end = $request->length < 0 ? count($listingData) : $request->length;
                for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($listingData); $iterator++,$pagination++ ){
                    $action = '<div class="btn btn-xs green">
                        <a href="/subcontractor/subcontractor-bills/view/'.$listingData[$pagination]->id.'" style="color: white">
                             View Bill
                        </a>
                    </div>';
                    $billStatus = $listingData[$pagination]->subcontractorBillStatus->name;
                    $structureTypeSlug = SubcontractorStructureType::where('id',$listingData[$pagination]['sc_structure_type_id'])->pluck('slug')->first();
                    if($structureTypeSlug == 'sqft'){
                        $rate = $listingData[$pagination]['rate'];
                        $basicAmount = $rate * $listingData[$pagination]['qty'];
                    }else{
                        $rate = $listingData[$pagination]['rate'] * $listingData[$pagination]['total_work_area'];
                        $basicAmount = $rate * $listingData[$pagination]['qty'];
                    }
                    if($billStatusSlug == 'disapproved'){
                        $billNo = "-";
                    }else{
                        $billNo = "R. A. - ".($billArrayNo);
                        $billArrayNo++;
                    }
                    $taxesApplied = SubcontractorBillTax::where('subcontractor_bills_id',$listingData[$pagination]['id'])->sum('percentage');
                    $taxAmount = $basicAmount * ($taxesApplied / 100);
                    $finalAmount = $basicAmount + $taxAmount;
                    $paidAmount = SubcontractorBillTransaction::where('subcontractor_bills_id', $listingData[$pagination]['id'])->sum('total');
                    $records['data'][$iterator] = [
                        $billNo,
                        $basicAmount,
                        $taxAmount,
                        $finalAmount,
                        $paidAmount,
                        $finalAmount - $paidAmount,
                        $billStatus,
                        $action
                    ];
                }
                $records["draw"] = intval($request->draw);
                $records["recordsTotal"] = $iTotalRecords;
                $records["recordsFiltered"] = $iTotalRecords;
            }

        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Subcontractor Bill Listing',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,200);
    }

    public function getSubcontractorStructureBillView(Request $request,$subcontractorStructureBillId){
        try{
            $subcontractorBill = SubcontractorBill::where('id',$subcontractorStructureBillId)->first();
            $subcontractorStructure = $subcontractorBill->subcontractorStructure;
            $subcontractorBillTaxes = $subcontractorBill->subcontractorBillTaxes;
            $totalBills = $subcontractorStructure->subcontractorBill->sortBy('id')->pluck('id');
            $taxTotal = 0;
            $structureSlug = $subcontractorStructure->contractType->slug;
            if($structureSlug == 'sqft'){
                $rate = $subcontractorStructure['rate'];
                $subTotal = $subcontractorBill['qty'] * $rate;
                foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                    $taxTotal += ($subcontractorBillTaxData['percentage'] * $subTotal) / 100;
                }
                $finalTotal = $subTotal + $taxTotal;
            }else{
                $rate = $subcontractorStructure['rate'] * $subcontractorStructure['total_work_area'];
                $subTotal = $subcontractorBill['qty'] * $rate;
                foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                    $taxTotal += ($subcontractorBillTaxData['percentage'] * $subTotal) / 100;
                }
                $finalTotal = $subTotal + $taxTotal;
            }
            $billNo = 1;
            foreach($totalBills as $billId){
                $status = SubcontractorBill::join('subcontractor_bill_status','subcontractor_bill_status.id','=','subcontractor_bills.subcontractor_bill_status_id')
                                ->where('subcontractor_bills.id',$billId)->pluck('subcontractor_bill_status.slug')->first();
                if($status != 'disapproved'){
                    if($billId == $subcontractorStructureBillId){
                        $billName = "R.A. ".$billNo;
                        break;
                    }
                }else{
                    $billName = '-';
                }
                $billNo++;
            }
            $noOfFloors = $totalBills->count();
            $BillTransactionTotals = SubcontractorBillTransaction::where('subcontractor_bills_id',$subcontractorBill->id)->pluck('total')->toArray();
            $remainingAmount = $finalTotal - array_sum($BillTransactionTotals);
            $paymentTypes = PaymentType::whereIn('slug',['cheque','neft','rtgs','internet-banking'])->orderBy('id')->get();
            $totalBillHoldAmount = SubcontractorBillTransaction::where('subcontractor_bills_id',$subcontractorStructureBillId)->sum('hold');
            $reconciledHoldAmount = SubcontractorBillReconcileTransaction::where('subcontractor_bill_id',$subcontractorStructureBillId)->where('transaction_slug','hold')->sum('amount');
            $remainingHoldAmount = $reconciledHoldAmount - $totalBillHoldAmount;
            $totalBillRetentionAmount = SubcontractorBillTransaction::where('subcontractor_bills_id',$subcontractorStructureBillId)->sum('retention_amount');
            $reconciledRetentionAmount = SubcontractorBillReconcileTransaction::where('subcontractor_bill_id',$subcontractorStructureBillId)->where('transaction_slug','retention')->sum('amount');
            $remainingRetentionAmount = $reconciledRetentionAmount - $totalBillRetentionAmount;
            $paidAmount = SubcontractorBillTransaction::where('subcontractor_bills_id', $subcontractorBill->id)->sum('total');
            $pendingAmount = $finalTotal - $paidAmount;
            $banks = BankInfo::where('is_active',true)->select('id','bank_name','balance_amount')->get();
            $statistics = $this->getSiteWiseStatistics();
            $cashAllowedLimit = ($statistics['remainingAmount'] > 0) ? $statistics['remainingAmount'] : 0 ;
            return view('subcontractor.structure.bill.view')->with(compact('structureSlug','subcontractorBill','subcontractorStructure','noOfFloors','billName','rate','subcontractorBillTaxes','subTotal','finalTotal','remainingAmount','paymentTypes','remainingHoldAmount','remainingRetentionAmount','pendingAmount','banks','cashAllowedLimit'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Bill View',
                'exception' => $e->getMessage(),
                'data' => $subcontractorStructureBillId
            ];
            Log::Critical(json_encode($data));
            abort(500);
        }
    }

    public function getSubcontractorStructureBillEditView(Request $request,$subcontractorStructureBillId){
        try{
            $subcontractorBill = SubcontractorBill::where('id',$subcontractorStructureBillId)->first();
            $subcontractorStructure = $subcontractorBill->subcontractorStructure;
            $subcontractorBillTaxes = $subcontractorBill->subcontractorBillTaxes;
            $totalBills = $subcontractorStructure->subcontractorBill->sortBy('id')->pluck('id');
            $taxTotal = 0;
            $structureSlug = $subcontractorStructure->contractType->slug;
            if($structureSlug == 'sqft'){
                $rate = $subcontractorStructure['rate'];
                $subTotal = $subcontractorBill['qty'] * $rate;
                foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                    $taxTotal += ($subcontractorBillTaxData['percentage'] * $subTotal) / 100;
                }
                $finalTotal = $subTotal + $taxTotal;
            }else{
                $rate = $subcontractorStructure['rate'] * $subcontractorStructure['total_work_area'];
                $subTotal = $subcontractorBill['qty'] * $rate;
                foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                    $taxTotal += ($subcontractorBillTaxData['percentage'] * $subTotal) / 100;
                }
                $finalTotal = $subTotal + $taxTotal;
            }
            $billNo = 1;
            foreach($totalBills as $billId){
                $status = SubcontractorBill::join('subcontractor_bill_status','subcontractor_bill_status.id','=','subcontractor_bills.subcontractor_bill_status_id')
                    ->where('subcontractor_bills.id',$billId)->pluck('subcontractor_bill_status.slug')->first();
                if($status != 'disapproved'){
                    if($billId == $subcontractorStructureBillId){
                        $billName = "R.A. ".$billNo;
                        break;
                    }
                }else{
                    $billName = '-';
                }
                $billNo++;
            }
            $noOfFloors = $totalBills->count();
            return view('subcontractor.structure.bill.edit')->with(compact('subcontractorBill','subcontractorStructure','noOfFloors','billName','rate','subcontractorBillTaxes','subTotal','finalTotal'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Bill View',
                'exception' => $e->getMessage(),
                'data' => $subcontractorStructureBillId
            ];
            Log::Critical(json_encode($data));
            abort(500);
        }
    }

    public function getSubcontractorStructureView(Request $request,$subcontractorStructureId){
        try{
            $subcontractorStructure = SubcontractorStructure::where('id',$subcontractorStructureId)->first();
            $noOfFloors = $subcontractorStructure->subcontractorBill->count();
            return view('subcontractor.structure.view')->with(compact('subcontractorStructure','noOfFloors'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Structure View',
                'exception' => $e->getMessage(),
                'data' => $subcontractorStructureId
            ];
            Log::Critical(json_encode($data));
            abort(500);
        }
    }

    public function changeBillStatus(Request $request,$statusSlug,$subcontractorStructureBillId){
        try{
            $subcontractorBill = SubcontractorBill::where('id',$subcontractorStructureBillId)->update([
                'subcontractor_bill_status_id' => SubcontractorBillStatus::where('slug',$statusSlug)->pluck('id')->first()
            ]);
            $request->session()->flash('success', 'Bill Status changed successfully.');
            return redirect('/subcontractor/subcontractor-bills/view/'.$subcontractorStructureBillId);
        }catch(\Exception $e){
            $data = [
                'action' => 'Change Subcontractor Structure Bill View',
                'exception' => $e->getMessage(),
                'data' => $subcontractorStructureBillId
            ];
            Log::Critical(json_encode($data));
            abort(500);
        }
    }

    public function editSubcontractorStructureBill(Request $request,$subcontractorStructureBillId){
        try{
            SubcontractorBill::where('id',$subcontractorStructureBillId)->update([
                'description' => $request['description'],
                'number_of_floors' => $request->number_of_floors
            ]);
            foreach ($request['taxes'] as $taxId => $taxPercentage){
                SubcontractorBillTax::where('id',$taxId)->where('subcontractor_bills_id',$subcontractorStructureBillId)->update([
                    'percentage' => $taxPercentage
                ]);
            }
            $request->session()->flash('success', 'Bill Edited successfully.');
            return redirect('/subcontractor/subcontractor-bills/edit/'.$subcontractorStructureBillId);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Subcontractor Bill',
                'exception' => $e->getMessage(),
                'data' => $subcontractorStructureBillId
            ];
            Log::Critical(json_encode($data));
            abort(500);
        }
    }

    public function getSubcontractorBillCreateView(Request $request,$subcontractorStructure){
        try{
            $quantitySum = SubcontractorBill::join('subcontractor_structure','subcontractor_structure.id','=','sc_structure_id')
                                            ->join('subcontractor_bill_status','subcontractor_bill_status.id','=','subcontractor_bills.subcontractor_bill_status_id')
                                            ->where('subcontractor_structure.id', $subcontractorStructure->id)
                                            ->where('subcontractor_bill_status.slug','!=','disapproved')
                                            ->sum('qty');
            if($subcontractorStructure->contractType->slug == 'amountwise'){
                $allowedQuantity = 1 - $quantitySum;
            }else{
                $allowedQuantity = $subcontractorStructure->total_work_area - $quantitySum;
            }
            $totalBillCount = $subcontractorStructure->subcontractorBill->count();
            $billName = "R.A. ".($totalBillCount + 1);
            $taxes = Tax::whereNotIn('slug',['vat'])->where('is_active',true)->where('is_special',false)->select('id','name','slug','base_percentage')->get();
            return view('subcontractor.structure.bill.create')->with(compact('subcontractorStructure','billName','taxes','allowedQuantity'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Bill Create View',
                'exception' => $e->getMessage(),
                'data' => $subcontractorStructure->id
            ];
            Log::Critical(json_encode($data));
            abort(500);
        }
    }

    public function createSubcontractorBill(Request $request,$subcontractorStructureId){
        try{
            $subcontractorBill = SubcontractorBill::create([
                'sc_structure_id' => $subcontractorStructureId,
                'subcontractor_bill_status_id' => SubcontractorBillStatus::where('slug','draft')->pluck('id')->first(),
                'qty' => $request['qty'],
                'description' => $request['description'],
                'number_of_floors' => $request->number_of_floors
            ]);
            if($request->has('taxes')){
                foreach ($request['taxes'] as $taxID => $taxPercentage){
                    SubcontractorBillTax::create([
                        'subcontractor_bills_id' => $subcontractorBill['id'],
                        'tax_id' => $taxID,
                        'percentage' => $taxPercentage,
                    ]);
                }
            }
            $request->session()->flash('success', 'Bill Created successfully.');
            return redirect('/subcontractor/subcontractor-bills/view/'.$subcontractorBill['id']);
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Subcontractor Bill',
                'exception' => $e->getMessage(),
                'data' => $subcontractorStructureId
            ];
            Log::Critical(json_encode($data));
            abort(500);
        }
    }

    public function createTransaction(Request $request){
        try{
            $subcontractorBillTransactionData = $request->except('_token','remainingTotal','bank_id','payment_id');
            if($request->has('is_advance')){
                $subcontractorBillTransactionData['is_advance'] = true;
                $subcontractorBillTransaction = SubcontractorBillTransaction::create($subcontractorBillTransactionData);
                $subcontractor = $subcontractorBillTransaction->subcontractorBill->subcontractorStructure->subcontractor;
                $balanceAdvanceAmount = $subcontractor->balance_advance_amount;
                $subcontractor->update(['balance_advance_amount' => $balanceAdvanceAmount - $subcontractorBillTransaction->total]);
            }else{
                $bank = BankInfo::where('id',$request['bank_id'])->first();
                if($request['total'] <= $bank['balance_amount']){
                    $subcontractorBillTransactionData['is_advance'] = false;
                    $subcontractorBillTransactionData['bank_id'] = $request['bank_id'];
                    $subcontractorBillTransactionData['payment_type_id'] = $request['payment_id'];
                    $subcontractorBillTransaction = SubcontractorBillTransaction::create($subcontractorBillTransactionData);
                    $bankData['balance_amount'] = $bank['balance_amount'] - $subcontractorBillTransaction['subtotal'];
                    $bank->update($bankData);
                }else{
                    $request->session()->flash('success','Bank Balance Amount is insufficient for this transaction');
                    return redirect('/subcontractor/subcontractor-bills/view/'.$request['subcontractor_bills_id']);
                }
            }

            if($subcontractorBillTransaction != null){
                $request->session()->flash('success','Transaction created successfully');
            }else{
                $request->session()->flash('error','Cannot create transaction');
            }
            return redirect('/subcontractor/subcontractor-bills/view/'.$request['subcontractor_bills_id']);
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Subcontractor Bill Transaction',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::Critical(json_encode($data));
            abort(500);
        }
    }

    public function getTransactionListing(Request $request,$subcontractorBillId){
        try{

            $listingData = SubcontractorBillTransaction::where('subcontractor_bills_id', $subcontractorBillId)->get();
            $iTotalRecords = count($listingData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($listingData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($listingData); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $iterator+1,
                    $listingData[$pagination]['subtotal'],
                    $listingData[$pagination]['debit'],
                    $listingData[$pagination]['hold'],
                    $listingData[$pagination]['retention_amount'],
                    $listingData[$pagination]['tds_amount'],
                    $listingData[$pagination]['other_recovery'],
                    $listingData[$pagination]['total'],
                    date('d M Y',strtotime($listingData[$pagination]['created_at'])),
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Subcontractor Listing',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,200);
    }

    public function addReconcileTransaction(Request $request){
        try{
            $reconcileTransactionData = $request->except('_token');
            if($request['paid_from_slug'] == 'cash'){
                $statistics = $this->getSiteWiseStatistics();
                $cashAllowedLimit = ($statistics['remainingAmount'] > 0) ? $statistics['remainingAmount'] : 0 ;
                if($request['amount'] <= $cashAllowedLimit){
                    $billReconcileTransaction = SubcontractorBillReconcileTransaction::create($reconcileTransactionData);
                    $request->session()->flash('success','Bill Reconcile Transaction saved Successfully.');
                }else{
                    $request->session()->flash('success','Cash Amount is insufficient for this transaction');
                }
            }else{
                $bank = BankInfo::where('id',$request['bank_id'])->first();
                if($request['amount'] <= $bank['balance_amount']){
                    $billReconcileTransaction = SubcontractorBillReconcileTransaction::create($reconcileTransactionData);
                    $request->session()->flash('success','Bill Reconcile Transaction saved Successfully.');
                    $bankData['balance_amount'] = $bank['balance_amount'] - $billReconcileTransaction['amount'];
                    $bank->update($bankData);
                }else{
                    $request->session()->flash('success','Bank Balance Amount is insufficient for this transaction');
                }
            }


            return redirect('/subcontractor/subcontractor-bills/view/'.$request->subcontractor_bill_id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Add Subcontractor Reconcile Transactions',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getHoldReconcileListing(Request $request){
        try{
            $status = 200;
            $billReconcileTransaction = SubcontractorBillReconcileTransaction::where('subcontractor_bill_id',$request->bill_id)->where('transaction_slug','hold')->orderBy('created_at','desc')->get();
            $iTotalRecords = count($billReconcileTransaction);
            $records = array();
            $records['data'] = array();
            if($request->length == -1){
                $length = $iTotalRecords;
            }else{
                $length = $request->length;
            }
            for($iterator = 0,$pagination = $request->start; $iterator < $length && $iterator < count($billReconcileTransaction); $iterator++,$pagination++ ){
                $records['data'][] = [
                    date('d M Y',strtotime($billReconcileTransaction[$pagination]['created_at'])),
                    $billReconcileTransaction[$pagination]['amount'],
                    ($billReconcileTransaction[$pagination]->paymentType != null) ? ucfirst($billReconcileTransaction[$pagination]->paid_from_slug).' - '.$billReconcileTransaction[$pagination]->paymentType->name : ucfirst($billReconcileTransaction[$pagination]->paid_from_slug),
                    $billReconcileTransaction[$pagination]['reference_number']
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Subcontractor Hold Reconcile Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 200;
        }
        return response()->json($records,$status);
    }

    public function getRetentionReconcileListing(Request $request){
        try{
            $records = array();
            $status = 200;
            $billReconcileTransaction = SubcontractorBillReconcileTransaction::where('subcontractor_bill_id',$request->bill_id)->where('transaction_slug','retention')->orderBy('created_at','desc')->get();
            $iTotalRecords = count($billReconcileTransaction);
            $records = array();
            $records['data'] = array();
            if($request->length == -1){
                $length = $iTotalRecords;
            }else{
                $length = $request->length;
            }
            for($iterator = 0,$pagination = $request->start; $iterator < $length && $iterator < count($billReconcileTransaction); $iterator++,$pagination++ ){
                $records['data'][] = [
                    date('d M Y',strtotime($billReconcileTransaction[$pagination]['created_at'])),
                    $billReconcileTransaction[$pagination]['amount'],
                    ($billReconcileTransaction[$pagination]->paymentType != null) ? ucfirst($billReconcileTransaction[$pagination]->paid_from_slug).' - '.$billReconcileTransaction[$pagination]->paymentType->name : ucfirst($billReconcileTransaction[$pagination]->paid_from_slug),
                    $billReconcileTransaction[$pagination]['reference_number']
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Subcontractor Retention Reconcile Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
        }
        return response()->json($records,$status);
    }

    public function addAdvancePayment(Request $request){
        try{
            $advancePaymentData = $request->all();
            $subcontractorAdvanceAmount = SubcontractorAdvancePayment::create($advancePaymentData);
            $subcontractor = Subcontractor::findOrFail($request->subcontractor_id);
            if(!isset($subcontractor->total_advance_amount)){
                $newBalanceadvanceAmount = $newTotaladvanceAmount = $request->amount;
            }else{
                $newBalanceadvanceAmount = $subcontractor->balance_advance_amount + $request->amount;
                $newTotaladvanceAmount = $subcontractor->total_advance_amount + $request->amount;
            }
            $subcontractor->update([
                'total_advance_amount' => $newTotaladvanceAmount,
                'balance_advance_amount' => $newBalanceadvanceAmount
            ]);
            $request->session()->flash('success','Advance Amount added successfully.');
            return redirect('/subcontractor/edit/'.$request->subcontractor_id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Add subcontractor advance payment',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function advancePaymentListing(Request $request){
        try{
            $status = 200;
            $paymentData = SubcontractorAdvancePayment::where('subcontractor_id',$request->subcontractor_id)->orderBy('created_at','desc')->get();
            $iTotalRecords = count($paymentData);
            $records = array();
            $records['data'] = array();
            if($request->length == -1){
                $length = $iTotalRecords;
            }else{
                $length = $request->length;
            }
            for($iterator = 0,$pagination = $request->start; $iterator < $length && $iterator < count($paymentData); $iterator++,$pagination++ ){
                $records['data'][] = [
                    date('d M Y',strtotime($paymentData[$pagination]['created_at'])),
                    $paymentData[$pagination]['amount'],
                    $paymentData[$pagination]->paymentType->name,
                    $paymentData[$pagination]['reference_number']
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Advance Payment Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $records = [];
        }
        return response()->json($records,$status);
    }

}
