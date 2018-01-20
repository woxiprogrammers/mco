<?php

namespace App\Http\Controllers\Subcontractor;

use App\DprMainCategory;
use App\Employee;
use App\Project;
use App\ProjectSite;
use App\Quotation;
use App\QuotationStatus;
use App\Subcontractor;
use App\SubcontractorBill;
use App\SubcontractorBillStatus;
use App\SubcontractorBillTax;
use App\SubcontractorDPRCategoryRelation;
use App\SubcontractorStructure;
use App\SubcontractorStructureType;
use App\Summary;
use App\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Client;
use Illuminate\Support\Facades\Session;

class SubcontractorController extends Controller
{
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
            return redirect('/subcontractor/create');
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
                $records['data'][$iterator] = [
                    $listingData[$pagination]['subcontractor_name'],
                    $listingData[$pagination]['company_name'],
                    $listingData[$pagination]['primary_cont_person_name'],
                    $listingData[$pagination]['primary_cont_person_mob_number'],
                    $listingData[$pagination]['escalation_cont_person_name'],
                    $listingData[$pagination]['escalation_cont_person_mob_number'],
                    $labourStatus,
                    '<div class="btn-group">
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
                    </div>'
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
            return view('subcontractor.edit')->with(compact('subcontractor'));
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
            return redirect('/subcontractor/edit/'.$subcontractor->id);
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
            switch($request['structure_type']){
                case 'amountwise' :
                        $subcontractorStructure = SubcontractorStructure::create([
                                                    'project_site_id' => $selectedGlobalProjectSiteID,
                                                    'subcontractor_id' => $request['subcontractor_id'],
                                                    'summary_id' => $request['summary_id'],
                                                    'sc_structure_type_id' => SubcontractorStructureType::where('slug',$request['structure_type'])->pluck('id')->first(),
                                                    'rate' => $request['rate'],
                                                    'total_work_area' => $request['total_work_area'],
                                                    'description' => $request['description'],
                                                ]);
                        foreach($request['bills'] as $key => $billData){
                            $subcontractorBill = SubcontractorBill::create([
                                'sc_structure_id' => $subcontractorStructure['id'],
                                'subcontractor_bill_status_id' => SubcontractorBillStatus::where('slug','draft')->pluck('id')->first(),
                                'qty' => $billData['quantity'],
                                'description' => $billData['description'],
                            ]);
                            if(array_key_exists('taxes',$billData)){
                                foreach ($billData['taxes'] as $taxID => $taxData){
                                    SubcontractorBillTax::create([
                                        'subcontractor_bills_id' => $subcontractorBill['id'],
                                        'tax_id' => $taxID,
                                        'percentage' => $taxData['percentage'],
                                    ]);
                                }
                            }
                        }
                    break;

                case 'sqft' :
                    $subcontractorStructure = SubcontractorStructure::create([
                                                'project_site_id' => $selectedGlobalProjectSiteID,
                                                'subcontractor_id' => $request['subcontractor_id'],
                                                'summary_id' => $request['summary_id'],
                                                'sc_structure_type_id' => SubcontractorStructureType::where('slug',$request['structure_type'])->pluck('id')->first(),
                                                'rate' => $request['rate'],
                                                'total_work_area' => $request['total_work_area'],
                                                'description' => $request['description'],
                                            ]);
            }
            $request->session()->flash('success', 'Subcontractor Structured Created successfully.');
            return redirect('/subcontractor/subcontractor-structure/create');
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

    public function subcontractorStructureListing(Request $request){
        try{
            $selectGlobalProjectSite = 0;
            if(Session::has('global_project_site')){
                $selectGlobalProjectSite = Session::get('global_project_site');
            }
            $listingData = SubcontractorStructure::where('project_site_id', $selectGlobalProjectSite)->get();
            $iTotalRecords = count($listingData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($listingData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($listingData); $iterator++,$pagination++ ){
                $action = '<a href="/subcontractor/subcontractor-bills/manage/'.$listingData[$pagination]['id'].'" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true">
                                        <i class="icon-docs"></i> Manage
                                    </a>
                                    <a href="/subcontractor/subcontractor-structure/view/'.$listingData[$pagination]['id'].'" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true">
                                         <i class="icon-docs"></i>View
                                    </a>';
                $total_amount = $listingData[$pagination]['rate'] * $listingData[$pagination]['total_work_area'];
                $records['data'][$iterator] = [
                    $listingData[$pagination]->subcontractor->subcontractor_name,
                    $listingData[$pagination]->summary->name,
                    $listingData[$pagination]->contractType->name,
                    $listingData[$pagination]['rate'],
                    $listingData[$pagination]['total_work_area'],
                    $total_amount,
                    date('d M Y',strtotime($listingData[$pagination]['created_at'])),
                    $action
                    // need to replece for edit functionality : <a href="/subcontractor/subcontractor-structure/edit/'.$listingData[$pagination]['id'].'">
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
            return redirect('/subcontractor/edit/'.$labour->id);
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
            $subcontractorStructureTypeSlug = SubcontractorStructure::join('subcontractor_structure_types','subcontractor_structure_types.id','=','subcontractor_structure.sc_structure_type_id')
                                                    ->where('subcontractor_structure.id',$subcontractorStructureId)
                                                    ->pluck('subcontractor_structure_types.slug')->first();
            return view('subcontractor.structure.bill.manage')->with(compact('taxes','subcontractorStructureId','subcontractorStructureTypeSlug'));
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
                $taxIds = SubcontractorBillTax::join('subcontractor_bills','subcontractor_bills.id','=','subcontractor_bill_taxes.subcontractor_bills_id')
                    ->join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                    ->join('taxes','subcontractor_bill_taxes.tax_id','=','taxes.id')
                    ->where('subcontractor_structure.id',$subcontractorStructureId)->distinct('subcontractor_bill_taxes.tax_id')
                    ->pluck('subcontractor_bill_taxes.tax_id');
                $taxes = Tax::whereIn('id',$taxIds)->distinct('id')->orderBy('id')->pluck('id');
                $taxesApplied = SubcontractorBillTax::where('subcontractor_bills_id',$listingData[$pagination]['id'])->select('tax_id','percentage')->get();
                $taxArray = array();
                $jIterator = 0;
                foreach ($taxes as $taxId){
                    $percentage = $taxesApplied->where('tax_id',$taxId)->pluck('percentage')->first();
                    $taxArray[$jIterator]['tax_id'] = $taxId;
                    $taxArray[$jIterator]['tax_percentage'] = ($percentage == null ) ? 0 : $percentage;
                    $jIterator++;
                }
                if($billStatusSlug == 'disapproved'){
                    $billNo = "-";
                }else{
                    $billNo = "R. A. - ".($billArrayNo);
                    $billArrayNo++;
                }
                $structureTypeSlug = SubcontractorStructureType::where('id',$listingData[$pagination]['sc_structure_type_id'])->pluck('slug')->first();
                if($structureTypeSlug == 'sqft'){
                    $rate = $listingData[$pagination]['rate'];
                    $subTotal = $rate * $listingData[$pagination]['qty'];
                }else{
                    $rate = $listingData[$pagination]['rate'] * $listingData[$pagination]['total_work_area'];
                    $subTotal = $rate * $listingData[$pagination]['qty'];
                }
                $records['data'][$iterator] = [
                    $billNo,
                ];
                foreach($taxArray as $taxAmount){
                    array_push($records['data'][$iterator],(($taxAmount['tax_percentage'] * $subTotal) / 100));
                }
                array_push($records['data'][$iterator],$billStatus);
                array_push($records['data'][$iterator],$action);
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
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
            return view('subcontractor.structure.bill.view')->with(compact('structureSlug','subcontractorBill','subcontractorStructure','noOfFloors','billName','rate','subcontractorBillTaxes','subTotal','finalTotal'));
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
                'description' => $request['description']
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

    public function getSubcontractorBillCreateView(Request $request,$subcontractorStructureId){
        try{
            $subcontractorStructure = SubcontractorStructure::where('id',$subcontractorStructureId)->first();
            $totalBillCount = $subcontractorStructure->subcontractorBill->count();
            $billName = "R.A. ".($totalBillCount + 1);
            $taxes = Tax::whereNotIn('slug',['vat'])->where('is_active',true)->where('is_special',false)->select('id','name','slug','base_percentage')->get();
            return view('subcontractor.structure.bill.create')->with(compact('subcontractorStructure','billName','taxes'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Bill Create View',
                'exception' => $e->getMessage(),
                'data' => $subcontractorStructureId
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

}
