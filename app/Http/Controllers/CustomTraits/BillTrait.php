<?php

namespace App\Http\Controllers\CustomTraits;
use App\BankInfo;
use App\Bill;
use App\BillImage;
use App\BillQuotationExtraItem;
use App\BillQuotationProducts;
use App\BillQuotationSummary;
use App\BillReconcileTransaction;
use App\BillStatus;
use App\BillTax;
use App\BillTransaction;
use App\Category;
use App\Client;
use App\Helper\MaterialProductHelper;
use App\Helper\NumberHelper;
use App\PaymentType;
use App\Product;
use App\ProductDescription;
use App\Project;
use App\ProjectSite;
use App\Quotation;
use App\QuotationBankInfo;
use App\QuotationExtraItem;
use App\QuotationProduct;
use App\QuotationStatus;
use App\QuotationSummary;
use App\Summary;
use App\Tax;
use App\TransactionStatus;
use App\Unit;
use Cron\Tests\DayOfMonthFieldTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

trait BillTrait{

    public function getCreateView(Request $request,$project_site){
        try{
            $quotationModel = new Quotation();
            $billStatusModel = new BillStatus();
            $billModel = new Bill();
            $quotationExtraItemModel = new QuotationExtraItem();
            $quotationProductModel = new QuotationProduct();
            $billQuotationExtraItem = new BillQuotationExtraItem();
            $quotationBankInfo = new QuotationBankInfo();
            $tax = new Tax();
            $unitModel = new Unit();
            $quotation = $quotationModel->where('project_site_id',$project_site['id'])->first();
            $cancelBillStatusId = $billStatusModel->where('slug','cancelled')->pluck('id')->first();
            $bills = $billModel->where('quotation_id',$quotation['id'])->where('bill_status_id','!=',$cancelBillStatusId)->orderBy('created_at','asc')->get()->toArray();
            $extraItems = $quotationExtraItemModel->where('quotation_id',$quotation['id'])->get();
            $banksAssigned = $quotationBankInfo->where('quotation_id',$quotation['id'])->select('bank_info_id')->get();
            $taxes = $tax->where('is_active',true)->where('is_special',false)->get()->toArray();
            $specialTaxes = $tax->where('is_active', true)->where('is_special',true)->get();
            if($bills != null){
                foreach ($extraItems as $key => $extraItem){
                    $extraItem['previous_rate'] = $billQuotationExtraItem->whereIn('bill_id',array_column($bills,'id'))->where('quotation_extra_item_id',$extraItem->id)->sum('rate');
                }
            }else{
                foreach ($extraItems as $key => $extraItem){
                    $extraItem['previous_rate'] = 0;
                }
            }
            if($quotation->billType->slug == 'itemwise'){
                $productModel = new Product();
                $categoryModel = new Category();

                $billQuotationProductsModel = new BillQuotationProducts();
                $quotationProducts = $quotationProductModel->where('quotation_id',$quotation['id'])->get()->toArray();
                if($bills != null){
                    for($i = 0 ; $i < count($quotationProducts) ; $i++){
                        $quotationProducts[$i]['previous_quantity'] = 0;
                        for($j = 0; $j < count($bills) ; $j++ ){
                            $quotationProducts[$i]['product_detail'] = $productModel->where('id',$quotationProducts[$i]['product_id'])->first()->toArray();
                            $quotationProducts[$i]['category_name'] = $categoryModel->where('id',$quotationProducts[$i]['product_detail']['category_id'])->pluck('name')->first();
                            $quotationProducts[$i]['unit'] = $unitModel->where('id',$quotationProducts[$i]['product_detail']['unit_id'])->pluck('name')->first();
                            $quotationProducts[$i]['rate'] = ($quotation['discount'] != 0)
                                                                ? round(($quotationProducts[$i]['rate_per_unit'] - ($quotationProducts[$i]['rate_per_unit'] * ($quotation['discount'] / 100))),3)
                                                                : round(($quotationProducts[$i]['rate_per_unit']),3);
                            $bill_products = $billQuotationProductsModel->where('bill_id',$bills[$j]['id'])->where('quotation_product_id',$quotationProducts[$i]['id'])->get()->toArray();
                            for($k = 0 ; $k < count($bill_products) ; $k++ ){
                                if($bill_products[$k]['quotation_product_id'] == $quotationProducts[$i]['id']){
                                    $quotationProducts[$i]['previous_quantity'] = $quotationProducts[$i]['previous_quantity'] + $bill_products[$k]['quantity'];
                                }
                            }
                        }
                    }
                }else{
                    for($i=0 ; $i < count($quotationProducts) ; $i++){
                        $quotationProducts[$i]['product_detail'] = $productModel->where('id',$quotationProducts[$i]['product_id'])->first()->toArray();
                        $quotationProducts[$i]['category_name'] = $categoryModel->where('id',$quotationProducts[$i]['product_detail']['category_id'])->pluck('name')->first();
                        $quotationProducts[$i]['unit'] = $unitModel->where('id',$quotationProducts[$i]['product_detail']['unit_id'])->pluck('name')->first();
                        $quotationProducts[$i]['previous_quantity'] = 0;
                        if($quotation['discount'] != 0){
                            $quotationProducts[$i]['rate'] = round(($quotationProducts[$i]['rate_per_unit'] - ($quotationProducts[$i]['rate_per_unit'] * ($quotation['discount'] / 100))),3);
                        }else{
                            $quotationProducts[$i]['rate'] = round(($quotationProducts[$i]['rate_per_unit']),3);
                        }
                    }
                }
                return view('admin.bill.item-wise.create')->with(compact('banksAssigned','extraItems','quotation','bills','project_site','quotationProducts','taxes','specialTaxes'));
            }else{
                $quotationSummaryModel = new QuotationSummary();
                $sQFTUnitName = $unitModel->where('slug','sqft')->pluck('name')->first();
                $quotationSummaries = $quotationSummaryModel->where('quotation_id',$quotation['id'])->get();
                if($bills != null){
                    $billQuotationSummaryModel = new BillQuotationSummary();
                    $previousBillIds = array_column($bills,'id');
                    for($i = 0 ; $i < count($quotationSummaries) ; $i++){
                        $quotationSummaries[$i]['summary_name'] = $quotationSummaries[$i]->summary->name;
                        $quotationSummaries[$i]['previous_quantity'] = 0;
                        $quotationSummaries[$i]['quantity'] = $quotation['built_up_area'];
                        $quotationSummaries[$i]['unit'] = $sQFTUnitName;
                        $quotationSummaries[$i]['previous_quantity'] = $billQuotationSummaryModel->whereIn('bill_id',$previousBillIds)
                                            ->where('quotation_summary_id',$quotationSummaries[$i]['id'])->sum('quantity');
                        $quotationSummaries[$i]['allowed_quantity'] = ($quotation->billType->slug == 'amountwise') ?
                                                                  1 - $quotationSummaries[$i]['previous_quantity']
                                                : $quotationSummaries[$i]['quantity'] - $quotationSummaries[$i]['previous_quantity'];
                    }
                }else{
                    for($i=0 ; $i < count($quotationSummaries) ; $i++){
                        $quotationSummaries[$i]['summary_name'] = $quotationSummaries[$i]->summary->name;
                        $quotationSummaries[$i]['previous_quantity'] = 0;
                        $quotationSummaries[$i]['quantity'] = $quotation['built_up_area'];
                        $quotationSummaries[$i]['unit'] = $sQFTUnitName;
                        $quotationSummaries[$i]['allowed_quantity'] = ($quotation->billType->slug == 'amountwise') ?
                            1 - $quotationSummaries[$i]['previous_quantity']
                            : $quotationSummaries[$i]['quantity'] - $quotationSummaries[$i]['previous_quantity'];
                    }
                }
                return view('admin.bill.create')->with(compact('banksAssigned','extraItems','quotation','bills','project_site','quotationSummaries','taxes','specialTaxes'));
            }


        }catch(\Exception $e){
            $data = [
                'action' => 'Get existing bill create view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateNewBillView(Request $request){
        try{
            $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
            $projectSiteIds = Quotation::where('quotation_status_id',$approvedQuotationStatus['id'])->pluck('project_site_id')->toArray();
            $projectIds = ProjectSite::whereIn('id',$projectSiteIds)->pluck('project_id')->toArray();
            $clientIds = Project::whereIn('id',$projectIds)->pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$clientIds)->where('is_active',true)->orderBy('id','asc')->get()->toArray();
            return view('admin.bill.create-new')->with(compact('clients'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get new bill create view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getProjects(Request $request,$client){
        try{
            $status = 200;
            $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
            $projectSiteIds = Quotation::where('quotation_status_id',$approvedQuotationStatus['id'])->pluck('project_site_id')->toArray();
            $projectIds = ProjectSite::whereIn('id',$projectSiteIds)->pluck('project_id')->toArray();
            $projects = Project::where('client_id',$client['id'])->whereIn('id',$projectIds)->get()->toArray();
            $projectOptions = array();
            for($i = 0 ; $i < count($projects); $i++){
                $projectOptions[] = '<option value="'.$projects[$i]['id'].'"> '.$projects[$i]['name'].' </option>';
            }
        }catch (\Exception $e){
            $projectOptions = array();
            $status = 500;
            $data = [
                'actions' => 'Create New Bill',
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
            $projectSites = ProjectSite::where('project_id',$project['id'])->get()->toArray();
            $projectSitesOptions = array();
            for($i = 0 ; $i < count($projectSites); $i++){
                $projectSitesOptions[] = '<option value="'.$projectSites[$i]['id'].'"> '.$projectSites[$i]['name'].' </option>';
            }
        }catch (\Exception $e){
            $projectSitesOptions = array();
            $status = 500;
            $data = [
                'actions' => 'Create New Bill',
                'params' => $request->all(),
                'exception' => $e->getMessage(),
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($projectSitesOptions,$status);
    }

    public function getProjectSiteManageView(Request $request){
        try{
            return view('admin.bill.manage');
        }catch (\Exception $e){
            $data = [
                'action' => 'Get project site manage view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageView(Request $request,$project_site){
        try{
            $quotation = Quotation::where('project_site_id',$project_site->id)->first();
            $billIds = Bill::where('quotation_id',$quotation->id)->pluck('id')->toArray();
            $taxes_applied = BillTax::whereIn('bill_id',$billIds)->distinct('tax_id')->orderBy('tax_id')->select('tax_id')->get()->toArray();
            $taxes = Tax::whereIn('id',$taxes_applied)->orderBy('id')->get();
            $bill_statuses = BillStatus::whereIn('slug',['draft','approved','cancelled'])->get()->toArray();
            return view('admin.bill.manage-bill')->with(compact('taxes','project_site','bill_statuses'));
        }catch(\Exception $e){
            $data = [
              'action' => 'Get bill manage view',
               'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function billListing(Request $request,$project_site,$status){
        try{
            $listingData = $currentTaxes = array();
            $iterator = $i = 0;
            $array_no = 1;
            $quotation = Quotation::where('project_site_id',$project_site->id)->first();
            $allBills = Bill::where('quotation_id',$quotation->id)->get();
            if($status == "cancelled"){
                $statusId = BillStatus::where('slug',$status)->pluck('id')->first();
                $bills = Bill::where('quotation_id',$quotation->id)->where('bill_status_id',$statusId)->orderBy('created_at','asc')->get();
            }else{
                $statusId = BillStatus::whereIn('slug',['approved','draft'])->get()->toArray();
                $bills = Bill::where('quotation_id',$quotation->id)->whereIn('bill_status_id',array_column($statusId,'id'))->orderBy('created_at','asc')->get();
            }
            $cancelBillStatusId = BillStatus::where('slug','cancelled')->pluck('id')->first();
            $taxesAppliedToBills = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                                    ->whereIn('bill_taxes.bill_id',array_column($allBills->toArray(),'id'))
                                    ->where('taxes.is_special', false)
                                    ->distinct('bill_taxes.tax_id')
                                    ->orderBy('bill_taxes.tax_id')
                                    ->pluck('bill_taxes.tax_id')
                                    ->toArray();
            $specialTaxesAppliedToBills = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                                    ->whereIn('bill_taxes.bill_id',array_column($allBills->toArray(),'id'))
                                    ->where('taxes.is_special', true)
                                    ->distinct('bill_taxes.tax_id')
                                    ->orderBy('bill_taxes.tax_id')
                                    ->pluck('bill_taxes.tax_id')
                                    ->toArray();
            foreach($bills as $key => $bill){
                $listingData[$iterator]['status'] = $bill->bill_status->slug ;
                $listingData[$iterator]['bill_id'] = $bill->id;
                if($bill->bill_status_id != $cancelBillStatusId){
                    $listingData[$iterator]['array_no'] = "RA Bill - ".$array_no;
                    $array_no++;
                }else{
                    $listingData[$iterator]['array_no'] = '-';
                }
                $listingData[$iterator]['bill_no_format'] = "B-".strtoupper(date('M',strtotime($bill['created_at'])))."-".$bill->id."/".date('y',strtotime($bill['created_at']));
                $total_amount = 0;
                if($bill->quotation->billType->slug == 'sqft' || $bill->quotation->billType->slug == 'amountwise'){
                    $billQuotationSummaryData = $bill->billQuotationSummary->where('is_deleted',false);
                    foreach($billQuotationSummaryData as $key1 => $billQuotationSummary){
                        $rate = $billQuotationSummary['rate_per_sqft'];
                        $total_amount = round(($total_amount + ($billQuotationSummary['quantity'] * $rate)),3) ;
                    }
                }else{
                    foreach($bill->bill_quotation_product as $key1 => $product){
                        $rate = round(($product->quotation_products->rate_per_unit - ($product->quotation_products->rate_per_unit * ($product->quotation_products->quotation->discount / 100))),3);
                        $total_amount = round(($total_amount + ($product->quantity * $rate)),3) ;
                    }
                }
                if(count($bill->bill_quotation_extraItems) > 0){
                    $extraItemsTotal = round($bill->bill_quotation_extraItems->sum('rate'),3);
                }else{
                    $extraItemsTotal = 0;
                }
                $total_amount = ($total_amount + $extraItemsTotal) - $bill->discount_amount;
                $listingData[$iterator]['subTotal'] = $total_amount;
                $thisBillTax = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                                        ->where('bill_taxes.bill_id',$bill->id)
                                        ->where('taxes.is_special', false)
                                        ->pluck('bill_taxes.tax_id')
                                        ->toArray();
                //$otherTaxes = array_values(array_diff($taxesAppliedToBills,$thisBillTax));
                $otherTaxes = $thisBillTax;
                if($thisBillTax != null){
                    $currentTaxes = Tax::whereIn('id',$otherTaxes)->where('is_active',true)->where('is_special', false)->select('id as tax_id','name')->get()->toArray();
                }
                if($currentTaxes != null){
                    $thisBillTaxInfo = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                        ->where('bill_taxes.bill_id',$bill->id)
                        ->where('taxes.is_special', false)
                        ->select('bill_taxes.percentage as percentage','bill_taxes.tax_id as tax_id')
                        ->get()
                        ->toArray();
                    //$currentTaxes = array_merge($thisBillTaxInfo,$currentTaxes);
                    $currentTaxes = $thisBillTaxInfo;
                    usort($currentTaxes, function($a, $b) {
                        return $a['tax_id'] > $b['tax_id'];
                    });
                }else{
                    $currentTaxes = Tax::where('is_active',true)->where('is_special', false)->select('id as tax_id')->get();
                }
                $listingData[$iterator]['final_total'] = $total_amount;
                foreach($currentTaxes as $key2 => $tax){
                    if(array_key_exists('percentage',$tax)){
                        $listingData[$iterator]['tax'][$tax['tax_id']] = round(($total_amount * ($tax['percentage'] / 100)),3);
                    }else{
                        $listingData[$iterator]['tax'][$tax['tax_id']] = 0;
                    }
                    $listingData[$iterator]['final_total'] = round(($listingData[$iterator]['final_total'] + $listingData[$iterator]['tax'][$tax['tax_id']]),3);
                    $i++;
                }
                $thisBillSpecialTax = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                                        ->where('bill_taxes.bill_id',$bill->id)
                                        ->where('taxes.is_special', true)
                                        ->pluck('bill_taxes.tax_id')
                                        ->toArray();
                $otherSpecialTaxes = array_values(array_diff($specialTaxesAppliedToBills,$thisBillSpecialTax));
                if($thisBillSpecialTax != null){
                    $currentSpecialTaxes = Tax::whereIn('id',$otherSpecialTaxes)->where('is_active',true)->where('is_special', true)->select('id as tax_id','name','base_percentage as percentage')->get();
                }else{
                    $currentSpecialTaxes = Tax::where('is_active',true)->where('is_special', true)->select('id as tax_id','name','base_percentage as percentage')->get();

                }
                if($currentSpecialTaxes != null){
                    $thisBillSpecialTaxInfo = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                                            ->where('bill_taxes.bill_id',$bill->id)
                                            ->where('taxes.is_special', true)
                                            ->select('bill_taxes.percentage as percentage','bill_taxes.applied_on as applied_on','bill_taxes.tax_id as tax_id')
                                            ->get()
                                            ->toArray();
                    if(!is_array($currentSpecialTaxes)){
                        $currentSpecialTaxes = $currentSpecialTaxes->toArray();
                    }
                    $currentSpecialTaxes = array_merge($thisBillSpecialTaxInfo,$currentSpecialTaxes);
                    usort($currentSpecialTaxes, function($a, $b) {
                        return $a['tax_id'] > $b['tax_id'];
                    });
                }else{
                    $currentSpecialTaxes = Tax::where('is_active',true)->where('is_special', true)->select('id as tax_id','base_percentage as percentage')->get();
                }
                foreach($currentSpecialTaxes as $key2 => $tax){
                    $taxAmount = 0;
                    if(array_key_exists('applied_on',$tax)){
                        $appliedOnTaxes = json_decode($tax['applied_on']);
                        foreach($appliedOnTaxes as $appliedTaxId){
                            if($appliedTaxId == 0){                 // On Subtotal
                                $taxAmount += round(($total_amount * ($tax['percentage'] / 100)),3);
                            }else{
                                $taxAmount += round(($listingData[$iterator]['tax'][$appliedTaxId] * ($tax['percentage'] / 100)),3);
                            }
                        }
                    }else{
                        $taxAmount += round(($total_amount * ($tax['percentage'] / 100)),3);
                    }

                    $listingData[$iterator]['tax'][$tax['tax_id']] = $taxAmount;
                    $listingData[$iterator]['final_total'] = round(($listingData[$iterator]['final_total'] + $listingData[$iterator]['tax'][$tax['tax_id']]),3);
                }
                $listingData[$iterator]['final_total'] = $listingData[$iterator]['final_total'] + $bill['rounded_amount_by'];
                $listingData[$iterator]['paid_amount'] = BillTransaction::where('bill_id',$bill->id)->sum('total');
                $listingData[$iterator]['balance_amount'] = $listingData[$iterator]['final_total'] - $listingData[$iterator]['paid_amount'];
                $listingData[$iterator]['rounded_amount_by'] = $bill['rounded_amount_by'];
                $iterator++;
            }
            $iTotalRecords = count($listingData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($listingData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($listingData); $iterator++,$pagination++ ){
                switch($listingData[$iterator]['status']){
                    case "draft" :
                        $billStatus = '<td><span class="btn btn-xs btn-warning"> Draft </span></td>';
                    break;

                    case "approved" :
                        $billStatus = '<td><span class="btn btn-xs green-meadow"> Approve </span></td>';
                        break;

                    case "cancelled" :
                        $billStatus = '<td><span class="btn btn-xs btn-danger"> Cancelled </span></td>';
                        break;
                }
                $records['data'][$iterator] = [
                    $iterator+1,
                    $listingData[$pagination]['array_no'],
                    $listingData[$pagination]['subTotal'],
                ];
                $totalTaxAmount = 0;

                if(array_key_exists('tax',$listingData[$pagination])){
                    foreach($listingData[$pagination]['tax'] as $taxAmount){
                        $totalTaxAmount += round($taxAmount,3);
                    }
                }
                array_push($records['data'][$iterator],round($totalTaxAmount,3));
                array_push($records['data'][$iterator],$listingData[$iterator]['rounded_amount_by']);
                array_push($records['data'][$iterator],$listingData[$iterator]['final_total']);
                array_push($records['data'][$iterator],$listingData[$iterator]['paid_amount']);
                array_push($records['data'][$iterator],$listingData[$iterator]['balance_amount']);
                array_push($records['data'][$iterator],$billStatus);
                if($listingData[$iterator]['status'] == "approved"){
                    array_push($records['data'][$iterator],'<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/bill/view/'.$listingData[$pagination]['bill_id'].'">
                                    <i class="icon-docs"></i> View </a>
                            </li>
                        </ul>
                    </div>');
                }elseif($listingData[$iterator]['status'] == "cancelled"){
                    array_push($records['data'][$iterator],'<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="javascript:void(0);">
                                    <i class="icon-docs"></i> View </a>
                            </li>
                        </ul>
                    </div>');
                }else{
                    array_push($records['data'][$iterator],'<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/bill/view/'.$listingData[$pagination]['bill_id'].'">
                                    <i class="icon-docs"></i> View </a>
                            </li>
                        </ul>
                    </div>');
                }

            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Bill Detail Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,200);
    }

    public function ProjectSiteListing(Request $request){
        try{
            $user = Auth::user();
            $iterator = 0;
            $listingData = array();
            $quotationIds = Bill::groupBy('quotation_id')->pluck('quotation_id')->toArray();
            $projectSiteIds = Quotation::whereIn('id',$quotationIds)->pluck('project_site_id')->toArray();
            $projectSiteData = ProjectSite::orderBy('updated_at','desc')->whereIn('id',$projectSiteIds)->get()->toArray();
            for($i = 0 ; $i < count($projectSiteData) ; $i++){
                $projectData = Project::where('id',$projectSiteData[$i]['project_id'])->get()->toArray();
                for($j = 0 ; $j < count($projectData) ; $j++){
                    $clientData = Client::where('id',$projectData[$j]['client_id'])->get()->toArray();
                    for($k = 0 ; $k < count($clientData); $k++){
                        $billType = Quotation::join('subcontractor_structure_types','subcontractor_structure_types.id','=','quotations.bill_type_id')
                                        ->where('quotations.project_site_id',$projectSiteData[$i]['id'])
                                        ->pluck('subcontractor_structure_types.name')->first();
                        $listingData[$iterator]['company'] = $clientData[$j]['company'];
                        $listingData[$iterator]['project_name'] = $projectData[$j]['name'];
                        $listingData[$iterator]['project_site_id'] = $projectSiteData[$i]['id'];
                        $listingData[$iterator]['project_site_name'] = $projectSiteData[$i]['name'];
                        $listingData[$iterator]['bill_type'] = $billType;
                        $iterator++;
                    }
                }
            }
            $iTotalRecords = count($listingData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($listingData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($listingData); $iterator++,$pagination++ ){
                if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-billing')){
                    $button = '<div class="btn-group">
                                <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                    Actions
                                    <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-left" role="menu">
                                    <li>
                                        <a href="/bill/create/'.$listingData[$pagination]['project_site_id'].'">
                                            <i class="icon-docs"></i> Create </a>
                                    </li>
                                    <li>
                                        <a href="/bill/manage/'.$listingData[$pagination]['project_site_id'].'">
                                            <i class="icon-docs"></i> Manage </a>
                                    </li>
                                </ul>
                             </div>';
                }else{
                    $button = '<div class="btn-group">
                                <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                    Actions
                                    <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-left" role="menu">
                                    <li>
                                        <a href="/bill/manage/'.$listingData[$pagination]['project_site_id'].'">
                                            <i class="icon-docs"></i> Manage </a>
                                    </li>
                                </ul>
                             </div>';
                }
                $records['data'][$iterator] = [
                    $listingData[$pagination]['company'],
                    $listingData[$pagination]['project_name'],
                    $listingData[$pagination]['project_site_name'],
                    $listingData[$pagination]['bill_type'],
                    $button
                ];

            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Product Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,200);
    }

    public function viewBill(Request $request,$bill){
        try{
            $quotation = $bill->quotation;
            $selectedBillId = $bill['id'];
            $billStatusModel = new BillStatus();
            $billModel = new Bill();
            $cancelBillStatusId = $billStatusModel->where('slug','cancelled')->pluck('id')->first();
            $bills = $billModel->where('quotation_id',$bill['quotation_id'])->where('bill_status_id','!=',$cancelBillStatusId)->orderBy('created_at','asc')->get()->toArray();
            $total['previous_bill_amount'] = $total['current_bill_subtotal'] = $total['cumulative_bill_amount'] = $total_extra_item =  0;
            if($bill->quotation->billType->slug == 'sqft' || $bill->quotation->billType->slug == 'amountwise'){
                $billQuotationSummaryModel = new BillQuotationSummary();
                $unitModel = new Unit();
                $sQFTUnitName = $unitModel->where('slug','sqft')->pluck('name')->first();
                $billQuotationSummaries = $billQuotationSummaryModel->where('is_deleted',false)->where('bill_id',$bill['id'])->get();

                for($iterator = 0 ; $iterator < count($billQuotationSummaries) ; $iterator++){
                    $summaryData = $billQuotationSummaries[$iterator]->quotationSummary->summary;
                    $billQuotationSummaries[$iterator]['summaryDetail'] = $summaryData;
                    $billQuotationSummaries[$iterator]['product_description'] = $billQuotationSummaries[$iterator]->productDescription
                                                                                    ->where('id',$billQuotationSummaries[$iterator]['product_description_id'])
                                                                                    ->where('quotation_id',$bill['quotation_id'])->first();
                    $billQuotationSummaries[$iterator]['unit'] = $sQFTUnitName;
                    $billQuotationSummaries[$iterator]['current_bill_subtotal'] = round(($billQuotationSummaries[$iterator]['quantity'] * $billQuotationSummaries[$iterator]['rate_per_sqft']),3);
                    $previousBillIdsWithoutCancelStatus = $billModel->where('quotation_id',$bill->quotation->id)
                                                                ->where('id','<',$bill['id'])
                                                                ->where('bill_status_id','!=',$cancelBillStatusId)
                                                                ->pluck('id')->toArray();
                    if(count($previousBillIdsWithoutCancelStatus) > 0){
                        $billQuotationSummaries[$iterator]['previous_quantity'] = $billQuotationSummaryModel->whereIn('bill_id',$previousBillIdsWithoutCancelStatus)
                                            ->where('quotation_summary_id',$billQuotationSummaries[$iterator]['quotation_summary_id'])
                                            ->where('is_deleted',false)->sum('quantity');
                    }else{
                        $billQuotationSummaries[$iterator]['previous_quantity'] = 0;
                    }
                    $billQuotationSummaries[$iterator]['cumulative_quantity'] = round(($billQuotationSummaries[$iterator]['quantity'] + $billQuotationSummaries[$iterator]['previous_quantity']),3);
                    $total['current_bill_subtotal'] = round(($total['current_bill_subtotal'] + $billQuotationSummaries[$iterator]['current_bill_subtotal']),3);
                }
                $extraItems = BillQuotationExtraItem::where('bill_id',$bill->id)->get();
                if(count($extraItems) > 0){
                    $total_extra_item = 0;
                    foreach($extraItems as $key => $extraItem){
                        $extraItem['previous_rate'] = BillQuotationExtraItem::whereIn('bill_id',array_column($bills,'id'))->where('bill_id','!=',$bill->id)->where('quotation_extra_item_id',$extraItem->quotation_extra_item_id)->sum('rate');
                        $total_extra_item = $total_extra_item + $extraItem['rate'];
                    }
                    $total['current_bill_subtotal'] = round(($total['current_bill_subtotal'] + $total_extra_item),3);
                }
                $total_rounded['current_bill_subtotal'] = round($total['current_bill_subtotal'],3);
                $final['current_bill_amount'] = $total_rounded['current_bill_amount'] = $total['current_bill_amount'] = round(($total['current_bill_subtotal'] - $bill['discount_amount']),3);
                $billTaxes = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                    ->where('bill_taxes.bill_id','=',$bill['id'])
                    ->where('taxes.is_special','=', false)
                    ->select('bill_taxes.id as id','bill_taxes.percentage as percentage','taxes.id as tax_id','taxes.name as tax_name','bill_taxes.applied_on as applied_on','taxes.slug as tax_slug')
                    ->get();
                $taxes = array();
                if($billTaxes != null){
                    $billTaxes = $billTaxes->toArray();
                }
                $tdsRetentionTaxAmount = 0;
                for($j = 0 ; $j < count($billTaxes) ; $j++){
                    $taxes[$billTaxes[$j]['tax_id']] = $billTaxes[$j];
                    $taxes[$billTaxes[$j]['tax_id']]['current_bill_amount'] = round(($total['current_bill_amount'] * ($taxes[$billTaxes[$j]['tax_id']]['percentage'] / 100)) , 3);
                    $final['current_bill_amount'] = round(($final['current_bill_amount'] + $taxes[$billTaxes[$j]['tax_id']]['current_bill_amount']),3);
                    if($billTaxes[$j]['tax_slug'] == 'retention' || $billTaxes[$j]['tax_slug'] == 'tds'){
                        $tdsRetentionTaxAmount = $tdsRetentionTaxAmount + round(($total['current_bill_amount'] * ($taxes[$billTaxes[$j]['tax_id']]['percentage'] / 100)) , 3);
                    }
                }
                $specialTaxes= BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                    ->where('bill_taxes.bill_id','=',$bill['id'])
                    ->where('taxes.is_special','=', true)
                    ->select('bill_taxes.id as id','bill_taxes.percentage as percentage','taxes.id as tax_id','taxes.name as tax_name','bill_taxes.applied_on as applied_on')
                    ->get();
                if($specialTaxes != null){
                    $specialTaxes = $specialTaxes->toArray();
                }else{
                    $specialTaxes = array();
                }
                if(count($specialTaxes) > 0){
                    for($j = 0 ; $j < count($specialTaxes) ; $j++){
                        $specialTaxes[$j]['applied_on'] = json_decode($specialTaxes[$j]['applied_on']);
                        $specialTaxAmount = 0;
                        foreach($specialTaxes[$j]['applied_on'] as $appliedOnTaxId){
                            if($appliedOnTaxId == 0){
                                $specialTaxAmount = $specialTaxAmount + ($total['current_bill_amount'] * ($specialTaxes[$j]['percentage'] / 100));
                            }else{
                                $specialTaxAmount = $specialTaxAmount + ($taxes[$appliedOnTaxId]['current_bill_amount'] * ($specialTaxes[$j]['percentage'] / 100));
                            }
                        }
                        $specialTaxes[$j]['current_bill_amount'] = round($specialTaxAmount , 3);
                        $final['current_bill_gross_total_amount'] = round(($final['current_bill_amount'] + $specialTaxAmount),3) + $bill['rounded_amount_by'];
                    }
                }else{
                    $final['current_bill_gross_total_amount'] = round($final['current_bill_amount'],3) + $bill['rounded_amount_by'];
                }
            }else{
                $billQuotationProducts = BillQuotationProducts::where('bill_id',$bill['id'])->get()->toArray();
                for($iterator = 0 ; $iterator < count($billQuotationProducts) ; $iterator++){
                    $billQuotationProducts[$iterator]['previous_quantity'] = 0;
                    $billQuotationProducts[$iterator]['quotationProducts'] = QuotationProduct::where('id',$billQuotationProducts[$iterator]['quotation_product_id'])->where('quotation_id',$bill['quotation_id'])->first();
                    $billQuotationProducts[$iterator]['productDetail'] = Product::where('id',$billQuotationProducts[$iterator]['quotationProducts']['product_id'])->first();
                    $billQuotationProducts[$iterator]['product_description'] = ProductDescription::where('id',$billQuotationProducts[$iterator]['product_description_id'])->where('quotation_id',$bill['quotation_id'])->first();
                    $billQuotationProducts[$iterator]['unit'] = Unit::where('id',$billQuotationProducts[$iterator]['productDetail']['unit_id'])->pluck('name')->first();
                    $quotation_id = Bill::where('id',$billQuotationProducts[$iterator]['bill_id'])->pluck('quotation_id')->first();
                    $discount = Quotation::where('id',$quotation_id)->pluck('discount')->first();
                    $rate_per_unit = QuotationProduct::where('id',$billQuotationProducts[$iterator]['quotation_product_id'])->pluck('rate_per_unit')->first();
                    $billQuotationProducts[$iterator]['rate'] = round(($rate_per_unit - ($rate_per_unit * ($discount / 100))),3);
                    $billQuotationProducts[$iterator]['current_bill_subtotal'] = round(($billQuotationProducts[$iterator]['quantity'] * $billQuotationProducts[$iterator]['rate']),3);
                    $billWithoutCancelStatus = Bill::where('id','<',$bill['id'])->where('bill_status_id','!=',$cancelBillStatusId)->pluck('id')->toArray();
                    $previousBills = BillQuotationProducts::whereIn('bill_id',$billWithoutCancelStatus)->get();
                    foreach($previousBills as $key => $previousBill){
                        if($billQuotationProducts[$iterator]['quotation_product_id'] == $previousBill['quotation_product_id']){
                            $billQuotationProducts[$iterator]['previous_quantity'] = $billQuotationProducts[$iterator]['previous_quantity'] +  $previousBill['quantity'];
                        }
                    }
                    $billQuotationProducts[$iterator]['cumulative_quantity'] = round(($billQuotationProducts[$iterator]['quantity'] + $billQuotationProducts[$iterator]['previous_quantity']),3);
                    $total['current_bill_subtotal'] = round(($total['current_bill_subtotal'] + $billQuotationProducts[$iterator]['current_bill_subtotal']),3);
                }
                $extraItems = BillQuotationExtraItem::where('bill_id',$bill->id)->get();
                if(count($extraItems) > 0){
                    $total_extra_item = 0;
                    foreach($extraItems as $key => $extraItem){
                        $extraItem['previous_rate'] = BillQuotationExtraItem::whereIn('bill_id',array_column($bills,'id'))->where('bill_id','!=',$bill->id)->where('quotation_extra_item_id',$extraItem->quotation_extra_item_id)->sum('rate');
                        $total_extra_item = $total_extra_item + $extraItem['rate'];
                    }
                    $total['current_bill_subtotal'] = round(($total['current_bill_subtotal'] + $total_extra_item),3);
                }
                $total_rounded['current_bill_subtotal'] = round($total['current_bill_subtotal'],3);
                $final['current_bill_amount'] = $total_rounded['current_bill_amount'] = $total['current_bill_amount'] = round(($total['current_bill_subtotal'] - $bill['discount_amount']),3);
                $billTaxes = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                    ->where('bill_taxes.bill_id','=',$bill['id'])
                    ->where('taxes.is_special','=', false)
                    ->select('bill_taxes.id as id','bill_taxes.percentage as percentage','taxes.id as tax_id','taxes.name as tax_name','bill_taxes.applied_on as applied_on','taxes.slug as tax_slug')
                    ->get();
                $taxes = array();
                if($billTaxes != null){
                    $billTaxes = $billTaxes->toArray();
                }
                $tdsRetentionTaxAmount = 0;
                for($j = 0 ; $j < count($billTaxes) ; $j++){
                    $taxes[$billTaxes[$j]['tax_id']] = $billTaxes[$j];
                    $taxes[$billTaxes[$j]['tax_id']]['current_bill_amount'] = round(($total['current_bill_amount'] * ($taxes[$billTaxes[$j]['tax_id']]['percentage'] / 100)) , 3);
                    $final['current_bill_amount'] = round(($final['current_bill_amount'] + $taxes[$billTaxes[$j]['tax_id']]['current_bill_amount']),3);
                    if($billTaxes[$j]['tax_slug'] == 'retention' || $billTaxes[$j]['tax_slug'] == 'tds'){
                        $tdsRetentionTaxAmount = $tdsRetentionTaxAmount + round(($total['current_bill_amount'] * ($taxes[$billTaxes[$j]['tax_id']]['percentage'] / 100)) , 3);
                    }
                }
                $specialTaxes= BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                    ->where('bill_taxes.bill_id','=',$bill['id'])
                    ->where('taxes.is_special','=', true)
                    ->select('bill_taxes.id as id','bill_taxes.percentage as percentage','taxes.id as tax_id','taxes.name as tax_name','bill_taxes.applied_on as applied_on')
                    ->get();
                if($specialTaxes != null){
                    $specialTaxes = $specialTaxes->toArray();
                }else{
                    $specialTaxes = array();
                }
                if(count($specialTaxes) > 0){
                    for($j = 0 ; $j < count($specialTaxes) ; $j++){
                        $specialTaxes[$j]['applied_on'] = json_decode($specialTaxes[$j]['applied_on']);
                        $specialTaxAmount = 0;
                        foreach($specialTaxes[$j]['applied_on'] as $appliedOnTaxId){
                            if($appliedOnTaxId == 0){
                                $specialTaxAmount = $specialTaxAmount + ($total['current_bill_amount'] * ($specialTaxes[$j]['percentage'] / 100));
                            }else{
                                $specialTaxAmount = $specialTaxAmount + ($taxes[$appliedOnTaxId]['current_bill_amount'] * ($specialTaxes[$j]['percentage'] / 100));
                            }
                        }
                        $specialTaxes[$j]['current_bill_amount'] = round($specialTaxAmount , 3);
                        $final['current_bill_gross_total_amount'] = round(($final['current_bill_amount'] + $specialTaxAmount),3) + $bill['rounded_amount_by'];
                    }
                }else{
                    $final['current_bill_gross_total_amount'] = round($final['current_bill_amount'],3) + $bill['rounded_amount_by'];
                }
            }

            $BillTransactionTotals = BillTransaction::where('bill_id',$bill->id)->pluck('total')->toArray();
            $remainingAmount = ($final['current_bill_gross_total_amount'] + abs($tdsRetentionTaxAmount)) - array_sum($BillTransactionTotals);
            $paymentTypes = PaymentType::orderBy('id')->whereIn('slug',['cheque','neft','rtgs','internet-banking'])->get();
            $totalBillHoldAmount = BillTransaction::where('bill_id',$selectedBillId)->sum('hold');
            $reconciledHoldAmount = BillReconcileTransaction::where('bill_id',$selectedBillId)->where('transaction_slug','hold')->sum('amount');
            $remainingHoldAmount = $reconciledHoldAmount - $totalBillHoldAmount;
            $totalBillRetentionAmount = BillTransaction::where('bill_id',$selectedBillId)->sum('retention_amount');
            $reconciledRetentionAmount = BillReconcileTransaction::where('bill_id',$selectedBillId)->where('transaction_slug','retention')->sum('amount');
            $remainingRetentionAmount = $reconciledRetentionAmount - $totalBillRetentionAmount;
            $balanceCancelledTransactionAmount = $bill->quotation->cancelled_bill_transaction_balance_amount;
            $banks = BankInfo::where('is_active',true)->select('id','bank_name','balance_amount')->get();
            $bill['rounded_amount_by'] = ($bill['rounded_amount_by'] == null) ? 0 : $bill['rounded_amount_by'];
            return view('admin.bill.view')->with(compact('balanceCancelledTransactionAmount','quotation','billQuotationSummaries','extraItems','bill','selectedBillId','total','total_rounded','final','total_current_bill_amount','bills','billQuotationProducts','taxes','specialTaxes','remainingAmount','paymentTypes','remainingHoldAmount','remainingRetentionAmount','banks'));
        }catch (\Exception $e){
            $data = [
                'action' => 'get view of bills',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createBill(Request $request){
        try{
            $projectSiteId = $request['project_site_id'];
            $bill_quotation_product = array();
            $quotationModel = new Quotation();
            $billStatusModel = new BillStatus();
            $billModel = new Bill();


            $quotationData = $quotationModel->where('id',$request['quotation_id'])->first();
            $bill['quotation_id'] = $quotationData['id'];
            $bill['bill_status_id'] = $billStatusModel->where('slug','draft')->pluck('id')->first();
            $bill['date'] = $request->date;
            $bill['performa_invoice_date'] = $request->performa_invoice_date;
            $bill['discount_amount'] = $request->discount_amount;
            $bill['sub_total'] = $request['sub_total'];
            $bill['with_tax_amount'] = $request['with_tax_amount'];
            $bill['rounded_amount_by'] = $request['round_amount_by'];
            $bill['gross_total'] = $request['grand_total'];
            if($request->assign_bank != 'default') {
                $bill['bank_info_id'] = $request->assign_bank;
            }
            $bill['discount_description'] = $request->discount_description;
            $bill_created = $billModel->create($bill);
            if($request['bill_type_slug'] == 'itemwise'){
                $billQuotationProductsModel = new BillQuotationProducts();
                foreach($request['quotation_product_id'] as $key => $value){
                    $bill_quotation_product['bill_id'] = $bill_created['id'];
                    $bill_quotation_product['quotation_product_id'] = $key;
                    $bill_quotation_product['quantity'] = $value['current_quantity'];
                    $bill_quotation_product['product_description_id'] = $value['product_description_id'];
                    $billQuotationProductsModel->create($bill_quotation_product);
                }
            }else{
                $billQuotationSummaryModel = new BillQuotationSummary();
                foreach($request['quotation_summary_id'] as $key => $value){
                    $bill_quotation_summary['bill_id'] = $bill_created['id'];
                    $bill_quotation_summary['quotation_summary_id'] = $key;
                    $bill_quotation_summary['rate_per_sqft'] = $value['rate'];
                    $bill_quotation_summary['built_up_area'] = $quotationData['built_up_area'];
                    $bill_quotation_summary['quantity'] = $value['current_quantity'];
                    $bill_quotation_summary['is_deleted'] = false;
                    $bill_quotation_summary['product_description_id'] = $value['product_description_id'];
                    $billQuotationSummaryModel->create($bill_quotation_summary);
                }
            }

            if($request->has('extra_item')){
                foreach($request['extra_item'] as $quotationExtraItemId => $extraItemData){
                    $bill_quotation_extra_item['bill_id'] = $bill_created['id'];
                    $bill_quotation_extra_item['quotation_extra_item_id'] = $quotationExtraItemId;
                    $bill_quotation_extra_item['description'] = $extraItemData['description'];
                    $bill_quotation_extra_item['rate'] = $extraItemData['rate'];
                    BillQuotationExtraItem::create($bill_quotation_extra_item);
                }
            }

            if($request->has('tax_percentage')){
                foreach($request['tax_percentage'] as $key => $value){
                    if($value != 0){
                        $bill_taxes['tax_id'] = $key;
                        $bill_taxes['bill_id'] = $bill_created['id'];
                        $bill_taxes['percentage'] = $value;
                        $bill_taxes['applied_on'] = json_encode([0]);
                        BillTax::create($bill_taxes);
                    }
                }
            }

            if($request->has('applied_on')){
                foreach($request->applied_on as $taxId => $specialTax){
                    if(array_key_exists('on',$specialTax)){
                        $bill_taxes['tax_id'] = $taxId;
                        $bill_taxes['bill_id'] = $bill_created['id'];
                        $bill_taxes['percentage'] = $specialTax['percentage'];
                        $bill_taxes['applied_on'] = json_encode($specialTax['on']);
                        BillTax::create($bill_taxes);
                    }
                }
            }

            $request->session()->flash('success','Bill Created Successfully');
            return redirect('/bill/create/'.$projectSiteId);
        }catch (\Exception $e){
            $data = [
                'action' => 'Create New bill',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function approveBill(Request $request){
        try{
            $paidStatusId = BillStatus::where('slug','approved')->pluck('id')->first();
            if($request->has('remark')){
                Bill::where('id',$request->bill_id)->update(['remark' => $request->remark , 'bill_status_id' => $paidStatusId]);
            }else{
                Bill::where('id',$request->bill_id)->update(['bill_status_id' => $paidStatusId]);
            }
            if($request->has('bill_images')){
                $imagesUploaded = $this->uploadPaidBillImages($request->bill_images,$request->bill_id);
            }else{
                $imagesUploaded = true;
            }
            if($imagesUploaded == true){
                $request->session()->flash('success','Bill approved Successfully');
            }else{
                $request->session()->flash('error','Something went wrong');
            }
            return redirect('/bill/view/'.$request->bill_id);
        }catch (\Exception $e){
            $data = [
                'action' => 'Change bill status to PAID',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function uploadTempBillImages(Request $request,$billId){
        try{
            $billDirectoryName = sha1($billId);
            $tempUploadPath = public_path().env('BILL_TEMP_IMAGE_UPLOAD');
            $tempImageUploadPath = $tempUploadPath.DIRECTORY_SEPARATOR.$billDirectoryName;
            /* Create Upload Directory If Not Exists */
            if (!file_exists($tempImageUploadPath)) {
                File::makeDirectory($tempImageUploadPath, $mode = 0777, true, true);
            }
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
            $request->file('file')->move($tempImageUploadPath,$filename);
            $path = env('BILL_TEMP_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$billDirectoryName.DIRECTORY_SEPARATOR.$filename;
            $response = [
                'jsonrpc' => '2.0',
                'result' => 'OK',
                'path' => $path
            ];
        }catch (\Exception $e){
            $response = [
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => 101,
                    'message' => 'Failed to open input stream.',
                ],
                'id' => 'id'
            ];
        }
        return response()->json($response);
    }

    public function removeTempImage(Request $request){
        try{
            $sellerUploadPath = public_path().$request->path;
            File::delete($sellerUploadPath);
            return response(200);
        }catch(\Exception $e){
            return response(500);
        }
    }

    public function displayBillImages(Request $request){
        try{
            $path = $request->path;
            $count = $request->count;
            $random = mt_rand(1,10000000000);
        }catch (\Exception $e){
            $path = null;
            $count = null;
        }
        return view('partials.bill.bill-images')->with(compact('path','count','random'));
    }

    public function uploadPaidBillImages($images,$billId){
        try{
            $billDirectoryName = sha1($billId);
            $tempImageUploadPath = public_path().env('BILL_TEMP_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$billDirectoryName;
            $imageUploadPath = public_path().env('BILL_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$billDirectoryName;
            $billImagesData = array();
            $billImagesData['bill_id'] = $billId;
            foreach($images as $image){
                $imageName = basename($image['image_name']);
                $newTempImageUploadPath = $tempImageUploadPath.'/'.$imageName;
                $billImagesData['image'] = $imageName;
                BillImage::create($billImagesData);
                if (!file_exists($imageUploadPath)) {
                    File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
                }
                if(File::exists($newTempImageUploadPath)){
                    $imageUploadNewPath = $imageUploadPath.DIRECTORY_SEPARATOR.$imageName;
                    File::move($newTempImageUploadPath,$imageUploadNewPath);
                }
            }
            if(count(scandir($tempImageUploadPath)) <= 2){
                rmdir($tempImageUploadPath);
            }
            return true;
        }catch (\Exception $e){
            $data = [
                'action' => 'Upload Work Order Images',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return false;
        }
    }

    public function generateCurrentBill(Request $request,$slug,$bill){
        try{
            $data = array();
            $data['slug'] = $slug;
            $data['bankData'] = ($bill->bankInfo != null) ? $bill->bankInfo : null;
            $data['discount_description'] = $bill->discount_description;
            $invoiceData = $taxData = array();
            if($bill->quotation->project_site->project->hsn_code == null){
                $data['hsnCode'] = '';
            }else{
                $data['hsnCode'] = $bill->quotation->project_site->project->hsn_code->code;
            }
            $allBillIds = Bill::where('quotation_id',$bill['quotation_id'])->orderBy('id')->pluck('id')->toArray();
            $data['company_name'] = $bill->quotation->project_site->project->client->company;
            $data['gstin']= $bill->quotation->project_site->project->client->gstin;
            $data['address']= $bill->quotation->project_site->project->client->address;
            $data['billData'] = $bill;
            $data['currentBillID'] = 1;
            $billIterator = 0;
            $cancelBillStatusId = BillStatus::where('slug','cancelled')->pluck('id')->first();
            foreach($allBillIds as $key => $billId){
                $billStatusId = Bill::where('id',$billId)->pluck('bill_status_id')->first();
                    if($billStatusId != $cancelBillStatusId){
                        if($billId == $bill['id']){
                            $data['currentBillID'] = $billIterator+1;
                        }
                        $billIterator++;
                    }
            }
            if($slug == "performa-invoice"){
                 $data['billDate'] = date('d/m/Y',strtotime($bill['performa_invoice_date']));
            }else{
                 $data['billDate'] = date('d/m/Y',strtotime($bill['date']));
            }
            $quotation = $bill->quotation;
            $projectSiteData = ProjectSite::where('id',$quotation->project_site_id)->first();
            $data['projectSiteName'] = $projectSiteData->name;
            $data['projectSiteAddress'] = $projectSiteData->address;
            $data['clientCompany'] = Client::where('id',$bill->quotation->project_site->project->client_id)->pluck('company')->first();

            $i = $j = $data['productSubTotal'] = $data['grossTotal'] = 0;
            if($quotation->billType->slug == 'sqft' || $quotation->billType->slug == 'amountwise'){
                $unitModel = new Unit();
                $billQuotationSummaryModel = new BillQuotationSummary();
                $billQuotationSummaries = $billQuotationSummaryModel->where('bill_id',$bill['id'])
                                            ->where('is_deleted',false)->get();
                $sQFTUnitName = $unitModel->where('slug','sqft')->pluck('name')->first();
                foreach($billQuotationSummaries as $key => $billQuotationSummary){
                    $invoiceData[$i]['product_name'] = $billQuotationSummary->quotationSummary->summary->name;
                    $invoiceData[$i]['description'] = $billQuotationSummary->productDescription->description;
                    $invoiceData[$i]['quantity'] = $billQuotationSummary->quantity;
                    $invoiceData[$i]['unit'] = $sQFTUnitName;
                    $invoiceData[$i]['rate'] = $billQuotationSummary->rate_per_sqft;
                    $invoiceData[$i]['amount'] = round(($invoiceData[$i]['quantity'] * $invoiceData[$i]['rate']), 3);
                    $data['productSubTotal'] = round(($data['productSubTotal'] + $invoiceData[$i]['amount']),3);
                    $i++;
                }
            }else{
                $billQuotationProducts = BillQuotationProducts::where('bill_id',$bill['id'])->get();
                foreach($billQuotationProducts as $key => $billQuotationProduct){
                    $invoiceData[$i]['product_name'] = $billQuotationProduct->quotation_products->product->name;
                    $invoiceData[$i]['description'] = $billQuotationProduct->product_description->description;
                    $invoiceData[$i]['quantity'] = (($billQuotationProduct->quantity));
                    $invoiceData[$i]['unit'] = $billQuotationProduct->quotation_products->product->unit->name;
                    $invoiceData[$i]['rate'] = round(($billQuotationProduct->quotation_products->rate_per_unit - ($billQuotationProduct->quotation_products->rate_per_unit * ($billQuotationProduct->quotation_products->quotation->discount / 100))),3);
                    $invoiceData[$i]['amount'] = round(($invoiceData[$i]['quantity'] * $invoiceData[$i]['rate']), 3);
                    $data['productSubTotal'] = round(($data['productSubTotal'] + $invoiceData[$i]['amount']),3);
                    $i++;
                }
            }

            $data['extraItems'] = BillQuotationExtraItem::where('bill_id',$bill->id)->get();
            if(count($data['extraItems']) > 0){
                $total['extra_item'] = 0;
                foreach($data['extraItems'] as $key => $extraItem){
                    $extraItem['previous_rate'] = BillQuotationExtraItem::whereIn('bill_id',$allBillIds)->where('bill_id','!=',$bill->id)->where('quotation_extra_item_id',$extraItem->quotation_extra_item_id)->sum('rate');
                    $total['extra_item'] = round(($total['extra_item'] + $extraItem['rate']),3);
                }
            }else{
                $total['extra_item'] = 0;
            }
            $data['sub_total_before_discount'] = round(($data['productSubTotal'] + $total['extra_item']),3);
            $data['discount_amount'] = $bill['discount_amount'];
            $data['subTotal'] = round(($data['sub_total_before_discount'] - $data['discount_amount']),3);
            $data['invoiceData'] = $invoiceData;
            $taxes = BillTax::where('bill_id',$bill['id'])->get();
            foreach($taxes as $key => $tax){
                $taxData[$j]['name'] = $tax->taxes->name;
                $taxData[$j]['percentage'] = abs($tax->percentage);
                $taxData[$j]['tax_amount'] = round($data['subTotal'] * ($tax->percentage / 100) , 3);
                $data['grossTotal'] = round(($data['grossTotal'] + $taxData[$j]['tax_amount']),3);
                $j++;
            }
            $data['taxData'] = $taxData;
            $data['totalAfterTax'] = round(($data['grossTotal'] + $data['subTotal']),3);
            $data['roundedBy'] = $bill['rounded_amount_by'];
            $data['grossTotal'] = $data['totalAfterTax'] + $data['roundedBy'];
            $data['amountInWords'] = ucwords(NumberHelper::getIndianCurrency($data['grossTotal']));
            $data['invoice_no'] = "B-".strtoupper(date('M',strtotime($bill['created_at'])))."-".$bill->id."/".date('y',strtotime($bill['created_at']));
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML(view('admin.bill.pdf.invoice',$data));
            return $pdf->stream();
        }catch(\Exception $e){
            $data = [
                'actions' => 'Generate current Bill',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500,$e->getMessage());
        }
    }

    public function generateCumulativeInvoice(Request $request,$bill){
        try{
            $projectSiteModel = new ProjectSite();
            $clientModel = new Client();
            $billStatusModel = new BillStatus();
            $billModel = new Bill();

            $data = array();
            $data['currentBillID'] = 1;
            $data['projectSiteName'] = $projectSiteModel->where('id',$bill->quotation->project_site_id)->pluck('name')->first();
            $data['clientCompany'] = $clientModel->where('id',$bill->quotation->project_site->project->client_id)->pluck('company')->first();
            $cancelBillStatusId = $billStatusModel->where('slug','cancelled')->pluck('id')->first();
            $previousBillIds = $billModel->where('quotation_id',$bill['quotation_id'])->where('bill_status_id','!=',$cancelBillStatusId)->where('id','<',$bill['id'])->pluck('id');
            if($bill->quotation->billType->slug == 'sqft' || $bill->quotation->billType->slug == 'amountwise'){
                $billQuotationSummaryModel = new BillQuotationSummary();
                $quotationSummaryModel = new QuotationSummary();
            $allBillIds = $billModel->where('quotation_id',$bill['quotation_id'])->where('bill_status_id','!=',$cancelBillStatusId)->where('id','<=',$bill['id'])->pluck('id');
                foreach($allBillIds as $key => $billId){
                    if($billId == $bill['id']){
                        $data['currentBillID'] = $key+1;
                        break;
                    }
                }
                $distinctQuotationSummaryIds = $billQuotationSummaryModel->whereIn('bill_id',$allBillIds)
                                        ->distinct('quotation_summary_id')->orderBy('quotation_summary_id')
                                        ->pluck('quotation_summary_id');
                $invoiceData = $total = array();
                $i = $total['previous_quantity'] = $total['current_quantity'] = $total['cumulative_quantity'] = $total['rate'] = $total['product_previous_bill_amount'] = $total['product_current_bill_amount'] = $total['product_cumulative_bill_amount'] = 0;
                $unitModel = new Unit();
                $sQFTUnitName = $unitModel->where('slug','sqft')->pluck('name')->first();
                foreach($distinctQuotationSummaryIds as $key => $quotationSummaryId){
                    $quotationSummary = $quotationSummaryModel->where('id',$quotationSummaryId)->first();
                    $invoiceData[$i]['product_name'] = $quotationSummary->summary->name;
                    $invoiceData[$i]['unit'] = $sQFTUnitName;
                    $invoiceData[$i]['rate'] = $quotationSummary['rate_per_sqft'];
                    $invoiceData[$i]['quotation_product_id'] = $quotationSummaryId;
                    $invoiceData[$i]['previous_quantity'] = $invoiceData[$i]['previous_bill_amount'] = 0;
                    $invoiceData[$i]['current_quantity'] = $invoiceData[$i]['current_bill_amount'] = 0;
                    if(count($previousBillIds) > 0){
                        $previousBillQuotationSummaryData = $billQuotationSummaryModel->whereIn('bill_id',$previousBillIds)
                            ->where('quotation_summary_id',$quotationSummaryId)
                            ->where('is_deleted',false)->get();
                        $invoiceData[$i]['previous_quantity'] = $previousBillQuotationSummaryData->sum('quantity');
                        $invoiceData[$i]['previous_bill_amount'] += $previousBillQuotationSummaryData->sum(function($previousBillQuotationSummary){
                            return round(($previousBillQuotationSummary['quantity'] * $previousBillQuotationSummary['rate_per_sqft']),3);
                        });
                    }
                    $currentBillSummaryData = $billQuotationSummaryModel->where('bill_id',$bill['id'])
                        ->where('quotation_summary_id',$quotationSummaryId)
                        ->where('is_deleted',false)->first();
                    if($currentBillSummaryData != null){
                        $invoiceData[$i]['current_quantity'] = $currentBillSummaryData['quantity'];
                        $invoiceData[$i]['current_bill_amount'] = round(($currentBillSummaryData['quantity'] * $currentBillSummaryData['rate_per_sqft']),3);
                    }
                    $invoiceData[$i]['cumulative_quantity'] = (($invoiceData[$i]['previous_quantity'] + $invoiceData[$i]['current_quantity']));

                    $invoiceData[$i]['cumulative_bill_amount'] = $invoiceData[$i]['current_bill_amount'] + $invoiceData[$i]['previous_bill_amount'];
                    $total['previous_quantity'] = (($total['previous_quantity'] + $invoiceData[$i]['previous_quantity']));
                    $total['current_quantity'] = (($total['current_quantity'] + $invoiceData[$i]['current_quantity']));
                    $total['cumulative_quantity'] = (($total['cumulative_quantity'] + $invoiceData[$i]['cumulative_quantity']));
                    $total['rate'] = round(($total['rate'] + $invoiceData[$i]['rate']),3);
                    $total['product_previous_bill_amount'] = round(($total['product_previous_bill_amount'] + $invoiceData[$i]['previous_bill_amount']),3);
                    $total['product_current_bill_amount'] = round(($total['product_current_bill_amount'] + $invoiceData[$i]['current_bill_amount']),3);
                    $total['product_cumulative_bill_amount'] = round(($total['product_cumulative_bill_amount']  + $invoiceData[$i]['cumulative_bill_amount']),3);
                    $i++;
                }
            }else{
                $billQuotationProductModel = new BillQuotationProducts();
                $billProducts = $billQuotationProductModel->whereIn('bill_id',$previousBillIds)->get()->toArray();
                $currentBillProducts = $billQuotationProductModel->where('bill_id',$bill['id'])->get()->toArray();
                $allBillIds = $billModel->where('quotation_id',$bill['quotation_id'])->where('bill_status_id','!=',$cancelBillStatusId)->where('id','<=',$bill['id'])->pluck('id');
                foreach($allBillIds as $key => $billId){
                    if($billId == $bill['id']){
                        $data['currentBillID'] = $key+1;
                        break;
                    }
                }
                $distinctProducts = $billQuotationProductModel->whereIn('bill_id',$allBillIds)->distinct('quotation_product_id')->orderBy('quotation_product_id')->select('quotation_product_id')->get();
                $invoiceData = $total = array();
                $i = $total['previous_quantity'] = $total['current_quantity'] = $total['cumulative_quantity'] = $total['rate'] = $total['product_previous_bill_amount'] = $total['product_current_bill_amount'] = $total['product_cumulative_bill_amount'] = 0;
                foreach($distinctProducts as $key => $distinctProduct){
                    $invoiceData[$i]['product_name'] = $distinctProduct->quotation_products->product->name;
                    $invoiceData[$i]['unit'] = $distinctProduct->quotation_products->product->unit->name;
                    $invoiceData[$i]['rate'] = round(($distinctProduct->quotation_products->rate_per_unit - ($distinctProduct->quotation_products->rate_per_unit * ($bill->quotation->discount / 100))),3);
                    $invoiceData[$i]['quotation_product_id'] = $distinctProduct['quotation_product_id'];
                    $invoiceData[$i]['previous_quantity'] = 0;
                    foreach($billProducts as $k => $billProduct){
                        if($distinctProduct['quotation_product_id'] == $billProduct['quotation_product_id']){
                            $invoiceData[$i]['previous_quantity'] = (($invoiceData[$i]['previous_quantity'] + $billProduct['quantity']));
                            $invoiceData[$i]['current_quantity'] = 0;
                        }
                    }
                    foreach($currentBillProducts as $j => $currentBillProduct){
                        if($distinctProduct['quotation_product_id'] == $currentBillProduct['quotation_product_id']){
                            $invoiceData[$i]['current_quantity'] = (($currentBillProduct['quantity']));
                        }
                    }
                    $invoiceData[$i]['cumulative_quantity'] = (($invoiceData[$i]['previous_quantity'] + $invoiceData[$i]['current_quantity']));
                    $invoiceData[$i]['previous_bill_amount'] = round(($invoiceData[$i]['previous_quantity'] * $invoiceData[$i]['rate']),3);
                    $invoiceData[$i]['current_bill_amount'] = round(($invoiceData[$i]['current_quantity'] * $invoiceData[$i]['rate']),3);
                    $invoiceData[$i]['cumulative_bill_amount'] = round(($invoiceData[$i]['cumulative_quantity'] * $invoiceData[$i]['rate']),3);
                    $total['previous_quantity'] = (($total['previous_quantity'] + $invoiceData[$i]['previous_quantity']));
                    $total['current_quantity'] = (($total['current_quantity'] + $invoiceData[$i]['current_quantity']));
                    $total['cumulative_quantity'] = (($total['cumulative_quantity'] + $invoiceData[$i]['cumulative_quantity']));
                    $total['rate'] = round(($total['rate'] + $invoiceData[$i]['rate']),3);
                    $total['product_previous_bill_amount'] = round(($total['product_previous_bill_amount'] + $invoiceData[$i]['previous_bill_amount']),3);
                    $total['product_current_bill_amount'] = round(($total['product_current_bill_amount'] + $invoiceData[$i]['current_bill_amount']),3);
                    $total['product_cumulative_bill_amount'] = round(($total['product_cumulative_bill_amount']  + $invoiceData[$i]['cumulative_bill_amount']),3);
                    $i++;
                }
            }

            $extraItems = BillQuotationExtraItem::whereIn('bill_id',$allBillIds)->get();
            $data['extraItems'] = array();
            if(count($extraItems) > 0){
                $total['extra_item_previous_bill_amount'] = $total['extra_item_current_bill_amount'] = $total['extra_item_cumulative_bill_amount'] = 0;
                foreach($extraItems as $key => $extraItem){
                    if(!array_key_exists($extraItem->quotationExtraItems->extraItem->id,$data['extraItems'])){
                        $data['extraItems'][$extraItem->quotationExtraItems->extraItem->id] = $extraItem;
                    }
                    $data['extraItems'][$extraItem->quotationExtraItems->extraItem->id]['previous_rate'] = BillQuotationExtraItem::whereIn('bill_id',$allBillIds)->where('bill_id','!=',$bill->id)->where('quotation_extra_item_id',$extraItem->quotation_extra_item_id)->sum('rate');
                    $total['extra_item_previous_bill_amount'] = round(($total['extra_item_previous_bill_amount'] + $extraItem['previous_rate']),3);
                    $total['extra_item_current_bill_amount'] = round(($total['extra_item_current_bill_amount'] + $extraItem['rate']),3);
                    $total['extra_item_cumulative_bill_amount'] = round(($total['extra_item_previous_bill_amount'] + $total['extra_item_current_bill_amount']),3);
                }
            }else{
                $total['extra_item_previous_bill_amount'] = $total['extra_item_current_bill_amount'] = $total['extra_item_cumulative_bill_amount'] = 0;
            }
            $data['extraItems'] = array_values($data['extraItems']);
            $total['previous_bill_amount'] = round(($total['product_previous_bill_amount'] + $total['extra_item_previous_bill_amount']),3);
            $total['current_bill_amount'] = round(($total['product_current_bill_amount'] + $total['extra_item_current_bill_amount']),3);
            $total['cumulative_bill_amount'] = round(($total['product_cumulative_bill_amount'] + $total['extra_item_cumulative_bill_amount']),3);
            $data['total'] = $total;
            $data['invoiceData'] = $invoiceData;
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML(view('admin.bill.pdf.cumulative',$data));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream();
        }catch(\Exception $e){
            $data = [
                'actions' => 'Generate Cumulative Bill',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500,$e->getMessage());
        }
    }

    public function editBillView(Request $request, $bill){
        try{
            $billModel = new Bill();
            $billStatusModel = new BillStatus();
            $i = 0;
            $quotation = $bill->quotation;
            $quotationProducts = $quotation->quotation_products;
            $cancelBillStatusId = $billStatusModel->where('slug','cancelled')->pluck('id')->first();
            $allBills = $billModel->where('quotation_id',$bill['quotation_id'])->where('bill_status_id','!=',$cancelBillStatusId)->orderBy('created_at','asc')->get()->toArray();
            $approvedBillIds = $billModel->where('quotation_id',$bill['quotation_id'])
                ->where('bill_status_id',$billStatusModel->where('slug','approved')->pluck('id')->first())
                ->pluck('id');
            $allBillIDsTillThisBill = $billModel->where('id','<=',$bill->id)->where('quotation_id',$bill->quotation_id)->where('bill_status_id','!=',$cancelBillStatusId)->pluck('id')->toArray();
            if($quotation->billType->slug == 'sqft' || $quotation->billType->slug == 'amountwise'){
                $quotationSummaryModel = new QuotationSummary();
                $billQuotationSummaryModel = new BillQuotationSummary();
                $unitModel = new Unit();
                $sQFTUnitName = $unitModel->where('slug','sqft')->pluck('name')->first();
                $quotationSummaries = $quotationSummaryModel->where('quotation_id',$bill['quotation_id'])->get();
                foreach($quotationSummaries as $key => $quotationSummary){
                    $quotationSummary['used_quantity'] = $billQuotationSummaryModel->whereIn('bill_id',$approvedBillIds)
                        ->where('bill_id','!=',$bill['id'])
                        ->where('quotation_summary_id',$quotationSummary['id'])->sum('quantity');
                    $quotationSummary['previous_quantity'] = $billQuotationSummaryModel->whereIn('bill_id',$allBillIDsTillThisBill)
                        ->where('bill_id','!=',$bill['id'])
                        ->where('quotation_summary_id',$quotationSummary['id'])->sum('quantity');
                    $billQuotationSummary = $billQuotationSummaryModel->where('bill_id',$bill['id'])
                        ->where('quotation_summary_id',$quotationSummary['id'])->where('is_deleted',false)->first();
                    $quotationSummary['quantity'] = $quotation['built_up_area'];
                    $quotationSummary['allowed_quantity'] = ($quotation->billType->slug == 'amountwise')
                                        ? 1 - $quotationSummary['previous_quantity']
                                        : $quotation['built_up_area'] - $quotationSummary['previous_quantity'];
                    if($billQuotationSummary != null){
                        if(($billQuotationSummary['product_description_id'] != null)){
                            $quotationSummary['bill_description'] = $billQuotationSummary->productDescription
                                ->where('id',$billQuotationSummary['product_description_id'])
                                ->where('quotation_id',$bill['quotation_id'])->pluck('description')->first();
                            $quotationSummary['bill_product_description_id'] = $billQuotationSummary->product_description_id;
                        }else{
                            $quotationSummary['bill_description'] = '';
                            $quotationSummary['bill_product_description_id'] = null;
                        }
                        $quotationSummary['current_quantity'] = $billQuotationSummary->quantity;
                        $quotationSummary['rate_per_sqft'] = $billQuotationSummary['rate_per_sqft'];
                    }
                }
            }else{
                $billQuotationProducts = BillQuotationProducts::whereIn('bill_id',$allBillIDsTillThisBill)->get();
                foreach($quotationProducts as $key => $quotationProduct){
                    $quotationProduct['previous_quantity'] = 0;
                    foreach($billQuotationProducts as $key1 => $billQuotationProduct){
                        $quotationProduct['discounted_rate'] = round(($quotationProduct['rate_per_unit'] - ($quotationProduct['rate_per_unit'] * ($quotationProduct->quotation->discount / 100))),3);
                        if($billQuotationProduct->quotation_product_id == $quotationProduct->id){
                            $quotationProduct['previous_quantity'] = $quotationProduct['previous_quantity'] + $billQuotationProduct->quantity;
                            if($billQuotationProduct->bill_id == $bill->id){
                                $quotationProduct['previous_quantity'] = $quotationProduct['previous_quantity'] - $billQuotationProduct->quantity;
                                $quotationProduct['bill_description'] = ($billQuotationProduct->product_description_id != null) ? $billQuotationProduct->product_description->description : '';
                                $quotationProduct['bill_product_description_id'] = ($billQuotationProduct->product_description_id != null) ? $billQuotationProduct->product_description_id : null;
                                $quotationProduct['current_quantity'] = $billQuotationProduct->quantity;
                            }
                            $quotationProduct['allowed_quantity'] = $quotationProduct['quantity'] - $quotationProduct['previous_quantity'];
                        }
                    }
                }
            }

            $quotationExtraItems = QuotationExtraItem::where('quotation_id',$bill->quotation->id)->get();
            $billExtraItems = BillQuotationExtraItem::where('bill_id',$bill->id)->get();
            foreach($quotationExtraItems as $key => $quotationExtraItem){
                $quotationExtraItem['prev_amount'] = 0;
                $quotationExtraItem['prev_amount'] = BillQuotationExtraItem::whereIn('bill_id',array_column($allBills,'id'))->where('bill_id','!=',$bill->id)->where('quotation_extra_item_id',$quotationExtraItem['id'])->sum('rate');
                foreach($billExtraItems as $key1 => $billExtraItem){
                    if($billExtraItem['quotation_extra_item_id'] == $quotationExtraItem['id']){
                        $quotationExtraItem['prev_amount'] = $quotationExtraItem['prev_amount'] + $billExtraItem['rate'];
                        if($billExtraItem->bill_id == $bill->id){
                            $quotationExtraItem['current_rate'] = $billExtraItem['rate'];
                            $quotationExtraItem['description'] = $billExtraItem['description'];
                            $quotationExtraItem['prev_amount'] = $quotationExtraItem['prev_amount'] - $billExtraItem['rate'];
                        }
                    }
                }
            }
            $billTaxes = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                                ->where('bill_taxes.bill_id','=',$bill->id)
                                ->where('taxes.is_special','=', false)
                                ->pluck('bill_taxes.tax_id')
                                ->toArray();
            $taxes = $currentTaxes =  array();
            if($billTaxes != null){
                $currentTaxes = Tax::whereNotIn('id',$billTaxes)
                    ->where('is_active',true)->where('is_special', false)->get()->toArray();
            }else{
                $currentTaxes = Tax::where('is_active',true)->where('is_special', false)->get()->toArray();
            }

            $billTaxInfo = BillTax::where('bill_id',$bill->id)
                ->whereIn('tax_id',$billTaxes)->get()->toArray();
            $currentTaxes = array_merge($billTaxInfo, $currentTaxes);
            foreach($currentTaxes as $key => $tax){
                if(!(array_key_exists('name',$tax))){
                    $taxes[$i] = Tax::where('id',$tax['tax_id'])->select('id','name','slug')->first()->toArray();
                    $taxes[$i]['percentage'] = $tax['percentage'];
                    $taxes[$i]['already_applied'] = 1;
                }else{
                    $taxes[$i]['id'] = $tax['id'];
                    $taxes[$i]['name'] = $tax['name'];
                    $taxes[$i]['slug'] = $tax['slug'];
                    $taxes[$i]['percentage'] = $tax['base_percentage'];
                    $taxes[$i]['already_applied'] = 0;
                }
                $i++;
            }
            $billSpecialTaxes = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                ->where('bill_taxes.bill_id','=',$bill->id)
                ->where('taxes.is_special','=', true)
                ->pluck('bill_taxes.tax_id')
                ->toArray();
            $specialTaxes = $currentSpecialTaxes =  array();
            $currentSpecialTaxes = Tax::whereNotIn('id',$billSpecialTaxes)->where('is_active',true)->where('is_special', true)->get()->toArray();
            $billTaxInfo = BillTax::where('bill_id',$bill->id)->whereIn('tax_id',$billSpecialTaxes)->get()->toArray();
            $currentTaxes = array_merge($billTaxInfo,$currentSpecialTaxes);
            $i = 0;
            foreach($currentTaxes as $key => $tax){
                if(!(array_key_exists('name',$tax))){
                    $specialTaxes[$i] = Tax::where('id',$tax['tax_id'])->select('id','name','slug')->first()->toArray();
                    $specialTaxes[$i]['percentage'] = $tax['percentage'];
                    $specialTaxes[$i]['already_applied'] = 1;
                    $specialTaxes[$i]['applied_on'] = json_decode($tax['applied_on']);
                }else{
                    $specialTaxes[$i]['id'] = $tax['id'];
                    $specialTaxes[$i]['name'] = $tax['name'];
                    $specialTaxes[$i]['slug'] = $tax['slug'];
                    $specialTaxes[$i]['percentage'] = $tax['base_percentage'];
                    $specialTaxes[$i]['already_applied'] = 0;
                    $specialTaxes[$i]['applied_on'] = [];
                }
                $i++;
            }
            $allbankInfoIds = QuotationBankInfo::where('quotation_id',$bill->quotation_id)->select('bank_info_id')->get();
            if($bill->quotation->billType->slug == 'sqft' || $bill->quotation->billType->slug == 'amountwise'){
                return view('admin.bill.edit')->with(compact('sQFTUnitName','bill','quotationSummaries','taxes','specialTaxes','quotationExtraItems','allbankInfoIds'));
            }else{
                return view('admin.bill.item-wise.edit')->with(compact('bill','quotationProducts','taxes','specialTaxes','quotationExtraItems','allbankInfoIds'));
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Bill view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editBill(Request $request, $bill){
        try{
            $billModel = new Bill();
            $approvedTrasanctionStatusId = TransactionStatus::where('slug','approved')->pluck('id')->first();
            $billTransactionAmount = $bill->transactions
                ->where('transaction_status_id',$approvedTrasanctionStatusId)->sum('total');
            if($billTransactionAmount > $request['grand_total']){
                $request->session()->flash('error', 'Cannot Edit the bill as Transaction amount is greater than the Bill amount you edited.');
                return redirect('/bill/view/'.$bill->id);
            }
            $billData = $request->only('date','performa_invoice_date','discount_amount','discount_description','grand_total');
            $billData['bank_info_id'] = $request['assign_bank'];
            $billData['rounded_amount_by'] = $request['round_amount_by'];
            if($request['assign_bank'] != 'default') {
                $billData['bank_info_id'] = $request['assign_bank'];
            }
            $bill->update($billData);
            $quotation = $bill->quotation;
            if($quotation->billType->slug == 'sqft' || $quotation->billType->slug == 'amountwise'){
                $billQuotationSummaryModel = new BillQuotationSummary();
                $quotationSummaryRequestData = $request['quotation_summary_id'];
                foreach ($quotationSummaryRequestData as $quotationSummaryId => $quotationSummaryRequest){
                    $alreadyExistSummary = $billQuotationSummaryModel->where('bill_id',$bill->id)
                        ->where('quotation_summary_id',$quotationSummaryId)->first();
                    if($alreadyExistSummary != null){
                        $billQuotationSummary = array();
                        if(array_key_exists('current_quantity',$quotationSummaryRequest)){
                            if($quotationSummaryRequest['current_quantity'] !== $alreadyExistSummary['quantity']){
                                $billQuotationSummary['quantity'] = $quotationSummaryRequest['current_quantity'];
                            }
                            if($quotationSummaryRequest['rate'] !== $alreadyExistSummary['rate_per_sqft']){
                                $billQuotationSummary['rate_per_sqft'] = $quotationSummaryRequest['rate'];
                            }
                            $billQuotationSummary['is_deleted'] = false;
                            $billQuotationSummary['product_description_id'] = $quotationSummaryRequest['product_description_id'];
                            $alreadyExistSummary->update($billQuotationSummary);
                        }else{
                            $alreadyExistSummary->update([
                                'is_deleted' => true
                            ]);
                        }
                    }else{
                        if(array_key_exists('current_quantity',$quotationSummaryRequest)){
                            $billQuotationSummary['bill_id'] = $bill->id;
                            $billQuotationSummary['quotation_summary_id'] = $quotationSummaryId;
                            $billQuotationSummary['rate_per_sqft'] = $quotationSummaryRequest['rate'];
                            $billQuotationSummary['built_up_area'] = $quotation['built_up_area'];
                            $billQuotationSummary['quantity'] = $quotationSummaryRequest['current_quantity'];
                            $billQuotationSummary['is_deleted'] = false;
                            $billQuotationSummary['product_description_id'] = $quotationSummaryRequest['product_description_id'];
                            $billQuotationSummaryModel->create($billQuotationSummary);
                        }
                    }
                }
            }else{
                $billQuotationProductModel = new BillQuotationProducts();
                $products = $request->quotation_product_id;
                $alreadyExistQuotationProductIds = $billQuotationProductModel->where('bill_id',$bill->id)
                    ->pluck('quotation_product_id')->toArray();
                $editQuotationProductIds = array_keys($products);
                $deletedQuotationProductIds = array_values(array_diff($alreadyExistQuotationProductIds,$editQuotationProductIds));
                foreach($products as $key => $product){
                    $alreadyExistProduct = $billQuotationProductModel->where('bill_id',$bill->id)
                        ->where('quotation_product_id',$key)->first();
                    if($alreadyExistProduct != null){
                        $billQuotationProduct = array();
                        if(array_key_exists('current_quantity',$product)){
                            if($product['current_quantity'] != $alreadyExistProduct->quantity){
                                $billQuotationProduct['quantity'] = $product['current_quantity'];
                            }
                            $billQuotationProduct['product_description_id'] = $product['product_description_id'];
                            $billQuotationProductModel->where('bill_id',$bill->id)
                                ->where('quotation_product_id',$key)->update($billQuotationProduct);
                        }else{
                            $billQuotationProductModel->where('bill_id',$bill->id)
                                ->where('quotation_product_id',$key)->delete();
                        }
                    }else{
                        if(array_key_exists('current_quantity',$product)){
                            $billQuotationProduct['bill_id'] = $bill->id;
                            $billQuotationProduct['quotation_product_id'] = $key;
                            $billQuotationProduct['quantity'] = $product['current_quantity'];
                            $billQuotationProduct['product_description_id'] = $product['product_description_id'];
                            $billQuotationProductModel->create($billQuotationProduct);
                        }
                    }
                }
            }

            if($request->has('extra_item')){
                $extraItems = $request->extra_item;
                $alreadyExistQuotationExtraItemIds = BillQuotationExtraItem::where('bill_id',$bill->id)->pluck('quotation_extra_item_id')->toArray();
                $editQuotationExtraItemIds = array_keys($extraItems);
                $deletedQuotationExtraItemIds = array_values(array_diff($alreadyExistQuotationExtraItemIds,$editQuotationExtraItemIds));
                if(count($deletedQuotationExtraItemIds) > 0){
                    foreach($deletedQuotationExtraItemIds as $extraItemId){
                        BillQuotationExtraItem::where('bill_id',$bill->id)->where('quotation_extra_item_id',$extraItemId)->delete();
                    }
                }
                foreach($extraItems as $extraItemId => $extraItem){
                    $alreadyExistExtraItem = BillQuotationExtraItem::where('bill_id',$bill->id)->where('quotation_extra_item_id',$extraItemId)->first();
                    if($alreadyExistExtraItem != null){
                        $billQuotationExtraItem = array();
                        if($extraItemId == $alreadyExistExtraItem->quotation_extra_item_id){
                            if($extraItem['rate'] != $alreadyExistExtraItem->rate){
                                $billQuotationExtraItem['rate'] = $extraItem['rate'];
                            }
                            $billQuotationExtraItem['description'] = $extraItem['description'];
                            BillQuotationExtraItem::where('bill_id',$bill->id)->where('quotation_extra_item_id',$extraItemId)->update($billQuotationExtraItem);
                        }
                    }else{
                        $billQuotationExtraItem['bill_id'] = $bill->id;
                        $billQuotationExtraItem['quotation_extra_item_id'] = $extraItemId;
                        $billQuotationExtraItem['rate'] = $extraItem['rate'];
                        $billQuotationExtraItem['description'] = $extraItem['description'];
                        BillQuotationExtraItem::create($billQuotationExtraItem);
                    }
                }
            }else{
                BillQuotationExtraItem::where('bill_id',$bill->id)->delete();
            }
            $tax_applied = $request->tax_data;
            if ($tax_applied != null) {
                foreach($tax_applied as $taxId => $tax){
                    if($tax['is_already_applied'] == true){
                        $alreadyPresentTax = BillTax::where('tax_id',$taxId)->where('bill_id',$bill->id)->pluck('percentage');
                        if($alreadyPresentTax != $tax['percentage']){
                            BillTax::where('tax_id',$taxId)->where('bill_id',$bill->id)->update(['percentage' => $tax['percentage']]);
                        }
                    }else{
                        if($tax['percentage'] != 0){
                            $taxData['bill_id'] = $bill->id;
                            $taxData['tax_id'] = $taxId;
                            $taxData['percentage'] = $tax['percentage'];
                            BillTax::create($taxData);
                        }
                    }
                }
            }

            if($request->has('applied_on')){
                foreach($request->applied_on as $taxId => $specialTax){
                    if(!array_key_exists('on',$specialTax) && $specialTax['is_already_applied'] == true){
                        BillTax::where('tax_id',$taxId)->where('bill_id',$bill->id)->delete();
                    }elseif(array_key_exists('on',$specialTax) && $specialTax['is_already_applied'] == true){
                        BillTax::where('tax_id',$taxId)->where('bill_id',$bill->id)->update([
                            'percentage' => $specialTax['percentage'],
                            'applied_on' => json_encode($specialTax['on'])
                        ]);
                    }elseif(array_key_exists('on',$specialTax) && $specialTax['is_already_applied'] == false){
                        if($specialTax['percentage'] != 0){
                            $bill_taxes['tax_id'] = $taxId;
                            $bill_taxes['bill_id'] = $bill['id'];
                            $bill_taxes['percentage'] = $specialTax['percentage'];
                            $bill_taxes['applied_on'] = json_encode($specialTax['on']);
                            BillTax::create($bill_taxes);
                        }
                    }
                }
            }
            return redirect('/bill/view/'.$bill->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit bill',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function cancelBill(Request $request,$bill){
        try{
            $cancelBillStatusId = BillStatus::where('slug','cancelled')->pluck('id')->first();
            $bill->update(['bill_status_id' => $cancelBillStatusId , 'remark' => $request->remark]);
            return redirect('/bill/manage/project-site');
        }catch(\Exception $e){
            $data = [
                'action' => 'Cancel bill status',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createProductDescription(Request $request){
        try{
            $status = 200;
            $product_description = array();
            $alreadyPresent = ProductDescription::where('quotation_id',$request->quotation_id)->where('description',$request->description)->first();
            if($alreadyPresent == null){
                $product_description = ProductDescription::create(['description' => $request->description , 'quotation_id' => $request->quotation_id]);
            }else{
                $product_description = $alreadyPresent;
            }
        }catch(\Exception $e){
            $status = 500;
            $product_description = array();
            $data = [
                'action' => 'Create Product Description',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($product_description,$status);
    }

    public function updateProductDescription(Request $request){
        try{
            $status = 200;
            $data = $request->all();
            ProductDescription::where('id',$data['description_id'])->update(['description' => $data['description']]);
            $response['message'] = "Description edited successfully";
        }catch(\Exception $e){
            $response['message'] = 'Something went wrong';
            $data = [
                'action' => 'Update Product Description',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            $status = 500;
            Log::critical(json_encode($data));
        }
        return response()->json($response,$status);
    }

    public function getProductDescription(Request $request,$quotation_id,$keyword){
        try{
            $keyword = trim($keyword);
            if($keyword == "" || $keyword == null){
                $descriptions = ProductDescription::where('quotation_id',$quotation_id)->select('id','description')->get();
            }else{
                $descriptions = ProductDescription::where('quotation_id',$quotation_id)->where('description','ILIKE','%'.$keyword.'%')->select('id','description')->get();
            }
            $response = array();
            if(count($descriptions) > 0){
                $iterator = 0;
                foreach($descriptions as $description){
                    $response[$iterator]['id'] = $description['id'];
                    $response[$iterator]['description'] = $description['description'];
                    $iterator++;
                }
            }
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Product Description',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            $status = 500;
            $response = array();
            Log::critical(json_encode($data));
        }

        return response()->json($response,$status);
    }

    public function generateCumulativeExcelSheet(Request $request,$bill){
        try{
            $data = array();
            $data['cancelledBillStatus'] = BillStatus::where('slug','cancelled')->first();
            $data['tillThisBill'] = Bill::where('quotation_id',$bill->quotation_id)->where('id','<=',$bill->id)->where('bill_status_id','!=',$data['cancelledBillStatus']->id)->orderBy('id','asc')->get();
            $data['bill'] = $bill;
            $billQuotationProducts = BillQuotationProducts::whereIn('bill_id',array_column($data['tillThisBill']->toArray(),'id'))->distinct('quotation_product_id')->select('quotation_product_id')->get();
            $i = 0;
            $productArray = array();
            $billSubTotal = array();
            foreach($billQuotationProducts as $key => $billQuotationProduct){
                $currrentProductId = $billQuotationProduct['quotation_product_id'];
                $productArray[$i]['name'] = $billQuotationProduct->quotation_products->product->name;
                $productArray[$i]['quotation_product_id'] = $billQuotationProduct->quotation_product_id;
                $productArray[$i]['discounted_rate'] = round(($billQuotationProduct->quotation_products->rate_per_unit - ($billQuotationProduct->quotation_products->rate_per_unit * ($bill->quotation->discount / 100))),3);
                $productArray[$i]['BOQ'] = $billQuotationProduct->quotation_products->quantity;
                $productArray[$i]['WO_amount'] = $productArray[$i]['discounted_rate'] * $productArray[$i]['BOQ'];
                $description = BillQuotationProducts::whereIn('bill_id',array_column($data['tillThisBill']->toArray(),'id'))->where('quotation_product_id',$billQuotationProduct->quotation_product_id)->orderBy('product_description_id','asc')->distinct('product_description_id')->select('product_description_id')->get();
                $j = 0;
                $productArray[$i]['description'] = array();
                foreach($description as $key1 => $description_id){
                    $productArray[$i]['description'][$description_id->product_description->id]['description'] = $description_id->product_description->description;
                    $productArray[$i]['description'][$description_id->product_description->id]['bills'] =array();
                    $iterator = 0;
                    $totalBillQuantity = 0;
                    $totalBillAmount = 0;
                    foreach($data['tillThisBill'] as $key2 => $thisBill){
                        $currentProductQuantity = BillQuotationProducts::where('bill_id',$thisBill->id)->where('quotation_product_id',$currrentProductId)->where('product_description_id',$description_id->product_description_id)->pluck('quantity')->first();
                        if($currentProductQuantity != null){
                            $productArray[$i]['description'][$description_id->product_description->id]['bills'][$iterator]['quantity'] = $currentProductQuantity;
                            $productArray[$i]['description'][$description_id->product_description->id]['bills'][$iterator]['amount'] = round(($currentProductQuantity *  $productArray[$i]['discounted_rate']),3);
                        }else{
                            $productArray[$i]['description'][$description_id->product_description->id]['bills'][$iterator]['quantity'] = 0;
                            $productArray[$i]['description'][$description_id->product_description->id]['bills'][$iterator]['amount'] = 0;
                        }
                        if(array_key_exists($thisBill->id,$billSubTotal)){
                            $billSubTotal[$thisBill->id]['subtotal'] +=  $productArray[$i]['description'][$description_id->product_description->id]['bills'][$iterator]['amount'];
                        }else{
                            $billSubTotal[$thisBill->id]['subtotal'] = array();
                            $billSubTotal[$thisBill->id]['subtotal'] =  $productArray[$i]['description'][$description_id->product_description->id]['bills'][$iterator]['amount'];
                        }
                        $billSubTotal[$thisBill->id]['discounted_total'] = round(($billSubTotal[$thisBill->id]['subtotal'] - $thisBill->discount_amount),3);
                        $billSubTotal[$thisBill->id]['discount'] = $thisBill->discount_amount;
                        $totalBillQuantity += $productArray[$i]['description'][$description_id->product_description->id]['bills'][$iterator]['quantity'];
                        $totalBillAmount += $productArray[$i]['description'][$description_id->product_description->id]['bills'][$iterator]['amount'];
                        $iterator++;
                    }

                    $productArray[$i]['description'][$description_id->product_description->id]['bills']['total_quantity'] = $totalBillQuantity;
                    $productArray[$i]['description'][$description_id->product_description->id]['bills']['total_amount'] = round($totalBillAmount,3);
                      $j++;
                }
                $i++;
            }
            $billQuotationExtraItemCollection = BillQuotationExtraItem::whereIn('bill_id',array_column($data['tillThisBill']->toArray(),'id'))->distinct('quotation_extra_item_id')->select('quotation_extra_item_id')->get();
            $billQuotationExtraItems = $billQuotationExtraItemCollection->toArray();
            $iterator = 0;
            foreach($billQuotationExtraItems as $key => $billQuotationExtraItem){
                $billQuotationExtraItems[$iterator]['name'] = $billQuotationExtraItemCollection[$iterator]->quotationExtraItems->extraItem->name;
                $billQuotationExtraItems[$iterator]['quotation_rate'] = $billQuotationExtraItemCollection[$iterator]->quotationExtraItems->rate;
                $billQuotationExtraItems[$iterator]['bills'] = array();
                $iteratorJ = 0;
                $total_rate = 0;
                foreach($data['tillThisBill'] as $key2 => $thisBill){
                    $extraItemRate = BillQuotationExtraItem::where('quotation_extra_item_id',$billQuotationExtraItems[$iterator]['quotation_extra_item_id'])->where('bill_id',$thisBill->id)->pluck('rate')->first();
                    $billQuotationExtraItems[$iterator]['bills'][$iteratorJ]['current_rate'] = ($extraItemRate != null) ? $extraItemRate : 0;
                    $total_rate = round(($total_rate + $billQuotationExtraItems[$iterator]['bills'][$iteratorJ]['current_rate']),3);
                    if(array_key_exists($thisBill->id,$billSubTotal)){
                        $billSubTotal[$thisBill->id]['subtotal'] +=  $billQuotationExtraItems[$iterator]['bills'][$iteratorJ]['current_rate'];
                    }else{
                        $billSubTotal[$thisBill->id]['subtotal'] = array();
                        $billSubTotal[$thisBill->id]['subtotal'] =  $billQuotationExtraItems[$iterator]['bills'][$iteratorJ]['current_rate'];
                    }
                    $billSubTotal[$thisBill->id]['discounted_total'] = $billSubTotal[$thisBill->id]['subtotal'] - $thisBill->discount_amount;
                    $billSubTotal[$thisBill->id]['discount'] = $thisBill->discount_amount;
                    $iteratorJ++;
                }
                $billQuotationExtraItems[$iterator]['total_rate'] = $total_rate;
                $iterator++;
                }
            $TaxIdTillBillWithoutSpecialTax = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                ->whereIn('bill_taxes.bill_id',array_column($data['tillThisBill']->toArray(),'id'))
                ->where('taxes.is_special','!=', true)
                ->distinct('bill_taxes.tax_id')
                ->select('bill_taxes.tax_id')
                ->get();
            $taxInfo = array();
            foreach($TaxIdTillBillWithoutSpecialTax as $key => $taxId){
                $taxInfo[$taxId['tax_id']]['name'] = Tax::where('id',$taxId['tax_id'])->pluck('name')->first();
                $taxInfo[$taxId['tax_id']]['total'] = 0;
                foreach($billSubTotal as $billId => $subTotal){
                    $taxInfo[$taxId['tax_id']]['bills'][$billId]['bill_id'] = $billId;
                    $taxInfo[$taxId['tax_id']]['bills'][$billId]['bill_subtotal'] = $subTotal['subtotal'];
                    $taxInfo[$taxId['tax_id']]['bills'][$billId]['bill_discountedTotal'] = $subTotal['discounted_total'];
                    $isAppliedTax = BillTax::where('tax_id',$taxId['tax_id'])->where('bill_id',$billId)->first();
                    if(count($isAppliedTax) == 0){
                        $taxInfo[$taxId['tax_id']]['bills'][$billId]['percentage'] = 0;
                    }else{
                        $taxInfo[$taxId['tax_id']]['bills'][$billId]['percentage'] = $isAppliedTax['percentage'];
                    }
                    $taxInfo[$taxId['tax_id']]['bills'][$billId]['tax_amount'] = round(($subTotal['discounted_total'] * ($taxInfo[$taxId['tax_id']]['bills'][$billId]['percentage']/100)),3);
                    $taxInfo[$taxId['tax_id']]['total'] += $taxInfo[$taxId['tax_id']]['bills'][$billId]['tax_amount'];
                }
            }
            $TaxIdTillBillWithSpecialTax = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                ->whereIn('bill_taxes.bill_id',array_column($data['tillThisBill']->toArray(),'id'))
                ->where('taxes.is_special','=', true)
                ->distinct('bill_taxes.tax_id')
                ->select('bill_taxes.tax_id')
                ->get();
            foreach($TaxIdTillBillWithSpecialTax as $key => $taxId){
                $taxInfo[$taxId['tax_id']]['name'] = Tax::where('id',$taxId['tax_id'])->pluck('name')->first();
                $taxInfo[$taxId['tax_id']]['total'] = 0;
                foreach($billSubTotal as $billId => $subTotal){
                    $taxInfo[$taxId['tax_id']]['bills'][$billId]['bill_id'] = $billId;
                    $taxInfo[$taxId['tax_id']]['bills'][$billId]['bill_subtotal'] = $subTotal['subtotal'];
                    $taxInfo[$taxId['tax_id']]['bills'][$billId]['bill_discountedTotal'] = $subTotal['discounted_total'];
                    $isAppliedTax = BillTax::where('tax_id',$taxId['tax_id'])->where('bill_id',$billId)->first();
                    if(count($isAppliedTax) == 0){
                        $taxInfo[$taxId['tax_id']]['bills'][$billId]['percentage'] = 0;
                        $taxAmount = 0;
                    }else{
                        $taxInfo[$taxId['tax_id']]['bills'][$billId]['percentage'] = $isAppliedTax['percentage'];
                        $specialTaxAppliedID  = json_decode($isAppliedTax['applied_on']);
                        $taxAmount = 0;
                        foreach($specialTaxAppliedID as $appliedOnId){
                            if($appliedOnId == 0){
                                //$taxAmount += MaterialProductHelper::customRound(($subTotal['discounted_total'] * ($taxInfo[$taxId['tax_id']]['bills'][$billId]['percentage']/100)),3);
                                $taxAmount += round(($subTotal['discounted_total'] * ($taxInfo[$taxId['tax_id']]['bills'][$billId]['percentage']/100)),3);
                            }else{
                                //$taxAmount += MaterialProductHelper::customRound(($taxInfo[$appliedOnId]['bills'][$billId]['tax_amount'] * ($taxInfo[$taxId['tax_id']]['bills'][$billId]['percentage']/100)),3);
                                $taxAmount += round(($taxInfo[$appliedOnId]['bills'][$billId]['tax_amount'] * ($taxInfo[$taxId['tax_id']]['bills'][$billId]['percentage']/100)),3);
                            }
                        }
                    }
                    $taxInfo[$taxId['tax_id']]['bills'][$billId]['tax_amount'] = $taxAmount;
                    $taxInfo[$taxId['tax_id']]['total'] += $taxAmount;
                }
            }
                $now = date('j_M_Y_His');
                Excel::create('Cummulative_Bill_'.$now, function($excel) use($data,$productArray,$billSubTotal,$taxInfo,$billQuotationExtraItems) {
                $excel->sheet('Sheetname', function($sheet) use($data,$productArray,$billSubTotal,$taxInfo,$billQuotationExtraItems) {
                    $sheet->row(1, array('SRN','Product with description','Rate','BOQ','W.O.Amount'));
                    $next_column = 'F';
                    $row = 1;
                    for($iterator = 0 ; $iterator < count($data['tillThisBill']); $iterator++,$next_column++){
                        $current_column = $next_column++;
                        $sheet->getCell($current_column.($row+1))->setValue('Quantity');
                        $sheet->getCell(($next_column).($row+1))->setValue('Amount');
                        $sheet->mergeCells($current_column.$row.':'.$next_column.$row);
                        $sheet->getCell($current_column.$row)->setValue("RA Bill".($iterator+1));

                    }
                    $totalAmountColumn = $next_column;
                    $next_column_data = array('Total Quantity','Total Amount');
                    for($iterator = 0 ; $iterator < count($next_column_data) ; $iterator++,$next_column++){
                        $columnData = $next_column_data[$iterator];
                        $sheet->cell($next_column.$row, function($cell) use($columnData) {
                            $cell->setAlignment('center')->setValignment('center');
                            $cell->setValue($columnData);
                        });
                    }
                    $serialNumber = 1;
                    $productRow = 4;
                    foreach($productArray as $product){
                        $sheet->row($productRow, array($serialNumber,$product['name'],$product['discounted_rate'],$product['BOQ'],$product['WO_amount']));
                        foreach($product['description'] as $description){
                            $amountColumn = $totalAmountColumn;
                            $next_column = 'F';
                            $productRow++;
                            $sheet->getCell('B'.($productRow))->setValue($description['description']);
                            foreach($description['bills'] as $bill => $thisBill ){
                                $current_column = $next_column++;
                                $sheet->getCell($current_column.($productRow))->setValue($thisBill['quantity']);
                                $sheet->getCell(($next_column).($productRow))->setValue($thisBill['amount']);
                                $next_column++;
                            }
                            $sheet->getCell(($amountColumn).($productRow))->setValue($description['bills']['total_quantity']);
                            $amountColumn++;
                            $sheet->getCell(($amountColumn).($productRow))->setValue($description['bills']['total_amount']);
                        }
                        $productRow = $productRow + 1;
                        $productRow++;
                    $serialNumber++;
                    }
                    foreach ($billQuotationExtraItems as $key => $extraItem){
                        $amountColumn = $totalAmountColumn;
                        $amountColumn++;
                        $sheet->getCell('A'.($productRow))->setValue($serialNumber);
                        $sheet->getCell('B'.($productRow))->setValue("Extra Item : ".$extraItem['name']);
                        $sheet->getCell('E'.($productRow))->setValue($extraItem['quotation_rate']);
                        $next_column = 'G';
                        foreach($extraItem['bills'] as $billIterator => $thisBill ){
                            $current_column = $next_column++;
                            $sheet->getCell($current_column.($productRow))->setValue($thisBill['current_rate']);
                            $next_column++;
                        }
                        $sheet->getCell(($amountColumn).($productRow))->setValue($extraItem['total_rate']);
                        $productRow = $productRow + 1;
                        $serialNumber++;
                    }
                    $sheet->getCell('B'.($productRow))->setValue('SubTotal');
                    $columnForSubTotal = 'G';
                    $rowForSubtotal = $productRow;
                    $totalSubTotal = 0;
                    foreach($billSubTotal as $subTotal){
                        $totalSubTotal += $subTotal['subtotal'];
                        $sheet->getCell($columnForSubTotal.($productRow))->setValue($subTotal['subtotal']);
                        $columnForSubTotal++;
                        $columnForSubTotal++;
                    }
                    $sheet->getCell($columnForSubTotal.($productRow))->setValue($totalSubTotal);
                    $productRow++;

                    $sheet->getCell('B'.($productRow))->setValue('Discount');
                    $columnForDiscount = 'G';
                    $rowForDiscount = $productRow;
                    $totalDiscount = 0;
                    foreach($billSubTotal as $subTotal){
                        $totalDiscount += $subTotal['discount'];
                        $sheet->getCell($columnForDiscount.($productRow))->setValue($subTotal['discount']);
                        $columnForDiscount++;
                        $columnForDiscount++;
                    }
                    $sheet->getCell($columnForDiscount.($productRow))->setValue($totalDiscount);
                    $productRow++;

                    $sheet->getCell('B'.($productRow))->setValue('Discount Total');
                    $columnForDiscountSubTotal = 'G';
                    $rowForDiscountSubtotal = $productRow;
                    $totalDiscountSubTotal = 0;
                    foreach($billSubTotal as $subTotal){
                        $totalDiscountSubTotal += $subTotal['discounted_total'];
                        $sheet->getCell($columnForDiscountSubTotal.($productRow))->setValue($subTotal['discounted_total']);
                        $columnForDiscountSubTotal++;
                        $columnForDiscountSubTotal++;
                    }
                    $sheet->getCell($columnForDiscountSubTotal.($productRow))->setValue($totalDiscountSubTotal);

                    $productRow++;
                    foreach($taxInfo as $tax){
                        $sheet->getCell('B'.($productRow))->setValue($tax['name']);
                        $next_column = 'F';
                        foreach($tax['bills'] as $bill) {
                            $current_column = $next_column++;
                            $sheet->getCell($current_column . ($productRow))->setValue($bill['percentage']);
                            $sheet->getCell($next_column . ($productRow))->setValue($bill['tax_amount']);
                            $next_column++;
                        }
                        $next_column++;
                        $sheet->getCell($next_column . ($productRow))->setValue($tax['total']);
                        $productRow++;
                    }
                    $columnForTotal = 'G';
                    $productRow++;
                    $beforeTotalRowNumber = $productRow - 1;
                    $sheet->getCell('B'.($productRow))->setValue('Total');
                    foreach($data['tillThisBill'] as $bill){
                        $sheet->getCell($columnForTotal.($productRow))->setValue("=SUM($columnForTotal$rowForDiscountSubtotal:$columnForTotal$beforeTotalRowNumber)");
                        $columnForTotal++;
                        $columnForTotal++;
                    }
                    $sheet->getCell($columnForTotal.($productRow))->setValue("=SUM($columnForTotal$rowForDiscountSubtotal:$columnForTotal$beforeTotalRowNumber)");

                });
            })->download('xlsx'); //->export('xls');
        }catch(\Exception $e){
            $data = [
                'action' => 'Generate excel sheet cumulative bill',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function calculateTaxAmounts(Request $request){
        try{
            $data = $request->except('_token');
            $taxes = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                            ->where('taxes.is_special','=',false)
                            ->where('bill_taxes.bill_id','=', $request->bill_id)
                            ->orderBy('bill_taxes.tax_id','asc')
                            ->select('bill_taxes.percentage as percentage','bill_taxes.tax_id as tax_id','bill_taxes.applied_on as applied_on')
                            ->get();
            $specialTaxes = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                ->where('taxes.is_special','=', true)
                ->where('bill_taxes.bill_id','=', $request->bill_id)
                ->orderBy('bill_taxes.tax_id','asc')
                ->select('bill_taxes.percentage as percentage','bill_taxes.tax_id as tax_id','bill_taxes.applied_on as applied_on')
                ->get();
            $response = array();
            $iterator = 0;
            $taxValues = array();
            foreach($taxes as $tax){
                $taxValues[$tax['tax_id']] = ($tax['percentage'] / 100);
            }
            foreach($specialTaxes as $specialTax){
                $taxValue = 0;
                $appliedOn = json_decode($specialTax['applied_on']);
                foreach($appliedOn as $appliedTaxId){
                    if($appliedTaxId == 0){                        // On subtotal
                        $taxValue = $taxValue + ($specialTax['percentage'] / 100);
                    }else{
                        $taxValue = $taxValue + ($taxValues[$appliedTaxId] * ($specialTax['percentage'] / 100));
                    }
                }
                $taxValues[$specialTax['tax_id']] = $taxValue;
            }
            $subtotal = $data['total'] / (1 + (array_sum($taxValues)));
            $response['subtotal'] = MaterialProductHelper::customRound($subtotal,3);
            foreach($taxValues as $taxId => $value){
                $response['taxes'][$iterator]['tax_id'] = $taxId;
                $response['taxes'][$iterator]['tax_amount'] = MaterialProductHelper::customRound(($subtotal * $value),3);
                $iterator++;
            }
            $status = 200;
        }catch(\Exception $e){
            $status = 500;
            $data = [
                'action' => 'Calculate Tax Amounts',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            $response = ['message' => 'Something went wrong.'];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($response,$status);
    }

    public function saveTransactionDetails(Request $request){
        try{
            $cancelBillStatusId = BillStatus::where('slug','cancelled')->pluck('id')->first();
            $transactionData = $request->except('_token','bank_id','payment_type_id');
            $projectSiteId = Quotation::join('bills','bills.quotation_id','=','quotations.id')
                                ->where('bills.id',$request->bill_id)
                                ->pluck('quotations.project_site_id')->first();
            $projectSite = ProjectSite::findOrFail($projectSiteId);
            $bill = Bill::findOrFail($request->bill_id);
            $bills = Bill::where('quotation_id',$bill['quotation_id'])->where('bill_status_id','!=',$cancelBillStatusId)->orderBy('created_at','asc')->get()->toArray();
            $total['previous_bill_amount'] = $total['current_bill_subtotal'] = $total['cumulative_bill_amount'] = $total_extra_item =  0;
            if($bill->quotation->billType->slug == 'sqft' || $bill->quotation->billType->slug == 'amountwise'){
                $billQuotationSummaryModel = new BillQuotationSummary();
                $billModel = new Bill();
                $billQuotationSummaries = $billQuotationSummaryModel->where('is_deleted',false)->where('bill_id',$bill['id'])->get();
                for($iterator = 0 ; $iterator < count($billQuotationSummaries) ; $iterator++){
                    $billQuotationSummaries[$iterator]['current_bill_subtotal'] = round(($billQuotationSummaries[$iterator]['quantity'] * $billQuotationSummaries[$iterator]['rate_per_sqft']),3);
                    $previousBillIdsWithoutCancelStatus = $billModel->where('quotation_id',$bill->quotation->id)
                        ->where('id','<',$bill['id'])
                        ->where('bill_status_id','!=',$cancelBillStatusId)
                        ->pluck('id')->toArray();
                    if(count($previousBillIdsWithoutCancelStatus) > 0){
                        $billQuotationSummaries[$iterator]['previous_quantity'] = $billQuotationSummaryModel->whereIn('bill_id',$previousBillIdsWithoutCancelStatus)
                            ->where('quotation_summary_id',$billQuotationSummaries[$iterator]['quotation_summary_id'])
                            ->where('is_deleted',false)->sum('quantity');
                    }else{
                        $billQuotationSummaries[$iterator]['previous_quantity'] = 0;
                    }
                    $billQuotationSummaries[$iterator]['cumulative_quantity'] = round(($billQuotationSummaries[$iterator]['quantity'] + $billQuotationSummaries[$iterator]['previous_quantity']),3);
                    $total['current_bill_subtotal'] = round(($total['current_bill_subtotal'] + $billQuotationSummaries[$iterator]['current_bill_subtotal']),3);
                }
            }else{
                $billQuotationProducts = BillQuotationProducts::where('bill_id',$bill['id'])->get()->toArray();
                for($iterator = 0 ; $iterator < count($billQuotationProducts) ; $iterator++){
                    $billQuotationProducts[$iterator]['previous_quantity'] = 0;
                    $quotation_id = Bill::where('id',$billQuotationProducts[$iterator]['bill_id'])->pluck('quotation_id')->first();
                    $discount = Quotation::where('id',$quotation_id)->pluck('discount')->first();
                    $rate_per_unit = QuotationProduct::where('id',$billQuotationProducts[$iterator]['quotation_product_id'])->pluck('rate_per_unit')->first();
                    $billQuotationProducts[$iterator]['rate'] = round(($rate_per_unit - ($rate_per_unit * ($discount / 100))),3);
                    $billQuotationProducts[$iterator]['current_bill_subtotal'] = round(($billQuotationProducts[$iterator]['quantity'] * $billQuotationProducts[$iterator]['rate']),3);
                    $billWithoutCancelStatus = Bill::where('id','<',$bill['id'])->where('bill_status_id','!=',$cancelBillStatusId)->pluck('id')->toArray();
                    $previousBills = BillQuotationProducts::whereIn('bill_id',$billWithoutCancelStatus)->get();
                    foreach($previousBills as $key => $previousBill){
                        if($billQuotationProducts[$iterator]['quotation_product_id'] == $previousBill['quotation_product_id']){
                            $billQuotationProducts[$iterator]['previous_quantity'] = $billQuotationProducts[$iterator]['previous_quantity'] +  $previousBill['quantity'];
                        }
                    }
                    $billQuotationProducts[$iterator]['cumulative_quantity'] = round(($billQuotationProducts[$iterator]['quantity'] + $billQuotationProducts[$iterator]['previous_quantity']),3);
                    $total['current_bill_subtotal'] = round(($total['current_bill_subtotal'] + $billQuotationProducts[$iterator]['current_bill_subtotal']),3);
                }
            }

            $extraItems = BillQuotationExtraItem::where('bill_id',$bill->id)->get();
            if(count($extraItems) > 0){
                $total_extra_item = 0;
                foreach($extraItems as $key => $extraItem){
                    $extraItem['previous_rate'] = BillQuotationExtraItem::whereIn('bill_id',array_column($bills,'id'))->where('bill_id','!=',$bill->id)->where('quotation_extra_item_id',$extraItem->quotation_extra_item_id)->sum('rate');
                    $total_extra_item = $total_extra_item + $extraItem['rate'];
                }
                $total['current_bill_subtotal'] = round(($total['current_bill_subtotal'] + $total_extra_item),3);
            }
            $total_rounded['current_bill_subtotal'] = round($total['current_bill_subtotal']);
            $final['current_bill_amount'] = $total_rounded['current_bill_amount'] = $total['current_bill_amount'] = round(($total['current_bill_subtotal'] - $bill['discount_amount']),3);
            $billTaxes = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                ->where('bill_taxes.bill_id','=',$bill['id'])
                ->where('taxes.is_special','=', false)
                ->select('bill_taxes.id as id','bill_taxes.percentage as percentage','taxes.id as tax_id','taxes.name as tax_name','bill_taxes.applied_on as applied_on')
                ->get();
            $taxes = array();
            if($billTaxes != null){
                $billTaxes = $billTaxes->toArray();
            }
            for($j = 0 ; $j < count($billTaxes) ; $j++){
                $taxes[$billTaxes[$j]['tax_id']] = $billTaxes[$j];
                $taxes[$billTaxes[$j]['tax_id']]['current_bill_amount'] = round($total['current_bill_amount'] * ($taxes[$billTaxes[$j]['tax_id']]['percentage'] / 100) , 3);
                $final['current_bill_amount'] = round(($final['current_bill_amount'] + $taxes[$billTaxes[$j]['tax_id']]['current_bill_amount']),3);
            }
            $specialTaxes= BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                ->where('bill_taxes.bill_id','=',$bill['id'])
                ->where('taxes.is_special','=', true)
                ->select('bill_taxes.id as id','bill_taxes.percentage as percentage','taxes.id as tax_id','taxes.name as tax_name','bill_taxes.applied_on as applied_on')
                ->get();
            if($specialTaxes != null){
                $specialTaxes = $specialTaxes->toArray();
            }else{
                $specialTaxes = array();
            }
            if(count($specialTaxes) > 0){
                for($j = 0 ; $j < count($specialTaxes) ; $j++){
                    $specialTaxes[$j]['applied_on'] = json_decode($specialTaxes[$j]['applied_on']);
                    $specialTaxAmount = 0;
                    foreach($specialTaxes[$j]['applied_on'] as $appliedOnTaxId){
                        if($appliedOnTaxId == 0){
                            $specialTaxAmount = $specialTaxAmount + ($total['current_bill_amount'] * ($specialTaxes[$j]['percentage'] / 100));
                        }else{
                            $specialTaxAmount = $specialTaxAmount + ($taxes[$appliedOnTaxId]['current_bill_amount'] * ($specialTaxes[$j]['percentage'] / 100));
                        }
                    }
                    $specialTaxes[$j]['current_bill_amount'] = round($specialTaxAmount , 3);
                    $final['current_bill_gross_total_amount'] = round(($final['current_bill_amount'] + $specialTaxAmount),3) + $bill['rounded_amount_by'];
                }
            }else{
                $final['current_bill_gross_total_amount'] = round($final['current_bill_amount'],3) + $bill['rounded_amount_by'];
            }
            $approvedBillStatusId = TransactionStatus::where('slug','approved')->pluck('id')->first();
            $totalTransactionAmount = BillTransaction::where('bill_id')->where('transaction_status_id',$approvedBillStatusId)->sum('amount');
            if(($totalTransactionAmount + $request->amount) > $final['current_bill_gross_total_amount']){
                $request->session()->flash('error','Total Payment amount is greater than total bill amount');
                return redirect('/bill/view/'.$request->bill_id);
            }else{
                $transactionData['transaction_status_id'] = $approvedBillStatusId;
                if($transactionData['paid_from_advanced'] == 'advance'){
                    $transactionData['paid_from_advanced'] = true;
                    $advanceBalanceAmount = ($projectSite->advanced_balance != null) ? $projectSite->advanced_balance : 0 ;

                    if($advanceBalanceAmount < $request->amount){
                        $request->session()->flash('error','Transaction amount is greater that advance balance amount. Advance balance amount is '.$advanceBalanceAmount);
                        return redirect('/bill/view/'.$request->bill_id);
                    }else{
                        BillTransaction::create($transactionData);
                        $newAdvanceBalanceAmount = $advanceBalanceAmount - $request->amount;
                        $projectSite->update(['advanced_balance' => $newAdvanceBalanceAmount]);
                    }
                }elseif($transactionData['paid_from_advanced'] == 'bank'){
                    $transactionData['paid_from_advanced'] = false;
                    $transactionData['paid_from_slug'] = 'bank';
                    $transactionData['payment_type_id'] = $request['payment_type_id'];
                    $transactionData['bank_id'] = $request['bank_id'];
                    BillTransaction::create($transactionData);
                    $bank = BankInfo::where('id',$request['bank_id'])->first();
                    $bankData['balance_amount'] = $bank['balance_amount'] + $request['total'];
                    $bankData['total_amount'] = $bank['total_amount'] + $request['total'];
                    $bank->update($bankData);

                }elseif($transactionData['paid_from_advanced'] == 'cancelled_bill_advance'){
                    $quotation = $bill->quotation;
                    if($quotation['cancelled_bill_transaction_balance_amount'] < $request->amount){
                        $request->session()->flash('error','Transaction amount is greater that cancel bill advance balance amount. Cancel Bill Advance balance amount is '.$quotation['cancelled_bill_transaction_balance_amount']);
                        return redirect('/bill/view/'.$request->bill_id);
                    }else{
                        $transactionData['paid_from_advanced'] = false;
                        $transactionData['paid_from_slug'] = 'cancelled_bill_advance';
                        $transactionData['payment_type_id'] = $request['payment_type_id'];
                        BillTransaction::create($transactionData);
                        $quotation->update(['cancelled_bill_transaction_balance_amount' => $quotation['cancelled_bill_transaction_balance_amount'] - $request->amount]);
                    }
                }else {
                    $transactionData['paid_from_advanced'] = false;
                    $transactionData['paid_from_slug'] = 'cash';
                    BillTransaction::create($transactionData);
                }

                $request->session()->flash('success','Bill Transaction added successfully.');
            }
            return redirect('/bill/view/'.$request->bill_id);
        }catch (\Exception $e){
            $status = 500;
            $data = [
                'action' => 'Save Transaction Details',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function billTransactionListing(Request $request, $billId){
        try{
            $status = 200;
            $transactionDetails = BillTransaction::where('bill_id', $billId)->orderBy('id','desc')->get();
            $records = array();
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = count($transactionDetails);
            $records["recordsFiltered"] = count($transactionDetails);
            $records['data'] = array();
            $end = $request->length < 0 ? count($transactionDetails) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($transactionDetails); $iterator++,$pagination++ ){
                if($transactionDetails[$pagination]['paid_from_advanced'] == true){
                    $paidFrom = 'Advance Payment';
                }elseif($transactionDetails[$pagination]->payment_type_id != null){
                    $paidFrom = ucfirst($transactionDetails[$pagination]->paid_from_slug).' - '.$transactionDetails[$pagination]->paymentType->name;
                }else{
                    $paidFrom = ucfirst($transactionDetails[$pagination]->paid_from_slug);
                }
                if($transactionDetails[$pagination]->transactionStatus->slug == 'cancelled'){
                    $balanceAmountAfterChangeStatus = $transactionDetails[$pagination]->bill->quotation->cancelled_bill_transaction_balance_amount - $transactionDetails[$pagination]->total;
                    if($balanceAmountAfterChangeStatus >= 0){
                        $changeStatusButton = '<a href="javascript:void(0);" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true" onclick="openDetails(\'approved\','.$transactionDetails[$pagination]['id'].')">
                                        Approve
                                    </a><a href="javascript:void(0);" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true" onclick="openDetails(\'deleted\','.$transactionDetails[$pagination]['id'].')">
                                        Delete
                                    </a>';
                    }else{
                        $changeStatusButton = '-';
                    }

                }elseif($transactionDetails[$pagination]->transactionStatus->slug == 'approved'){
                    $changeStatusButton = '<a href="javascript:void(0);" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true" onclick="openDetails(\'cancelled\','.$transactionDetails[$pagination]['id'].')">
                                        Cancel
 -                                   </a><a href="javascript:void(0);" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true" onclick="openDetails(\'deleted\','.$transactionDetails[$pagination]['id'].')">
                                        Delete
                                    </a>';
                }else{
                    $changeStatusButton = '<a href="javascript:void(0);" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true" onclick="openDetails(\'approved\','.$transactionDetails[$pagination]['id'].')">
                                        Approve
                                    </a><a href="javascript:void(0);" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true" onclick="openDetails(\'cancelled\','.$transactionDetails[$pagination]['id'].')">
                                        Cancel
                                    </a>';
                }

                $records['data'][] = [
                    $pagination+1,
                    date('j M Y',strtotime($transactionDetails[$pagination]['created_at'])),
                    $paidFrom,
                    $transactionDetails[$pagination]['amount'],
                    $transactionDetails[$pagination]['debit'],
                    $transactionDetails[$pagination]['hold'],
                    $transactionDetails[$pagination]['retention_amount'],
                    $transactionDetails[$pagination]['tds_amount'],
                    $transactionDetails[$pagination]['other_recovery_value'],
                    $transactionDetails[$pagination]['total'],
                    $transactionDetails[$pagination]->transactionStatus->name,
                    $changeStatusButton
                ];
            }
        }catch(\Exception $e){
            $status = 500;
            $data = [
                'action' => 'Transaction listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $records = array();
        }
        return response()->json($records,$status);
    }

    public function changeBillTransactionStatus(Request $request){
        try{
            $billTransaction = new BillTransaction();
            $transactionStatus = new TransactionStatus();
            $billTransactionData = $billTransaction->where('id',$request['bill_transaction_id'])->first();
            $bill = $billTransactionData->bill;
            $quotation = $bill->quotation;
            if($request['status-slug'] == 'cancelled'){
                $quotation->update([
                    'cancelled_bill_transaction_total_amount' => $quotation['cancelled_bill_transaction_total_amount'] + $billTransactionData['total'],
                    'cancelled_bill_transaction_balance_amount' => $quotation['cancelled_bill_transaction_balance_amount'] + $billTransactionData['total']
                ]);
            }elseif($billTransactionData->transactionStatus->slug == 'cancelled'){
                $quotation->update([
                    'cancelled_bill_transaction_total_amount' => $quotation['cancelled_bill_transaction_total_amount'] - $billTransactionData['total'],
                    'cancelled_bill_transaction_balance_amount' => $quotation['cancelled_bill_transaction_balance_amount'] - $billTransactionData['total']
                ]);
            }
            $billTransactionData->update([
                'transaction_status_id' => $transactionStatus->where('slug',$request['status-slug'])->pluck('id')->first(),
                'status_remark' => $request['remark']
            ]);

            return redirect('/bill/view/'.$bill->id);
        }catch (\Exception $e){
            $status = 500;
            $data = [
                'action' => 'Transaction listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function billTransactionDetail(Request $request,$transaction){
        try{
            $transactionData = [
                'subtotal' => $transaction->subtotal,
                'total' => $transaction->total,
                'remark' => $transaction->remark
            ];
            $taxes = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                ->where('taxes.is_special','=',false)
                ->where('bill_taxes.bill_id','=', $transaction->bill_id)
                ->orderBy('bill_taxes.tax_id','asc')
                ->select('bill_taxes.percentage as percentage','taxes.name as name','taxes.id as id')
                ->get();
            $specialTaxes = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                ->where('taxes.is_special','=', true)
                ->where('bill_taxes.bill_id','=', $transaction->bill_id)
                ->orderBy('bill_taxes.tax_id','asc')
                ->select('bill_taxes.percentage as percentage','taxes.name as name','bill_taxes.applied_on as applied_on')
                ->get();
            $transactionData['taxes'] = array();
            foreach($taxes as $tax){
                $transactionData['taxes'][$tax['id']]['name'] = $tax['name'];
                $transactionData['taxes'][$tax['id']]['percentage'] = $tax['percentage'];
                $transactionData['taxes'][$tax['id']]['amount'] = $transactionData['subtotal'] * ($tax['percentage'] / 100);
            }
            foreach($specialTaxes as $specialTax){
                $appliedOnTaxes = json_decode($specialTax['applied_on']);
                $taxAmount = 0;
                foreach ($appliedOnTaxes as $appliedOnTaxId){
                    if($appliedOnTaxId == 0){                  // On subtotal
                        $taxAmount += $transactionData['subtotal'] * ($specialTax['percentage'] / 100);
                    }else{
                        $taxAmount += $transactionData['taxes'][$appliedOnTaxId]['amount'] * ($specialTax['percentage'] / 100);
                    }
                }
                $transactionData['taxes'][$specialTax['id']]['name'] = $specialTax['name'];
                $transactionData['taxes'][$specialTax['id']]['percentage'] = $specialTax['percentage'];
                $transactionData['taxes'][$specialTax['id']]['amount'] = $taxAmount;
            }
            return view('partials.bill.transaction-detail')->with(compact('transactionData'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Transaction listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function addReconcileTransaction(Request $request){
        try{
            $reconcileTransactionData = $request->except('_token');
            $billReconcileTransaction = BillReconcileTransaction::create($reconcileTransactionData);
            if($request['paid_from_slug'] == 'bank'){
                $bank = BankInfo::where('id',$request['bank_id'])->first();
                $bankData['balance_amount'] = $bank['balance_amount'] + $request['amount'];
                $bankData['total_amount'] = $bank['total_amount'] + $request['amount'];
                $bank->update($bankData);
            }
            $request->session()->flash('success','Bill Reconcile Transaction saved Successfully.');

            return redirect('/bill/view/'.$request->bill_id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Add Reconcile Transactions',
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
            $paymentData = BillReconcileTransaction::where('bill_id',$request->bill_id)->where('transaction_slug','hold')->orderBy('created_at','desc')->get();
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
                    ($paymentData[$pagination]->paymentType != null) ? ucfirst($paymentData[$pagination]->paid_from_slug).' - '.$paymentData[$pagination]->paymentType->name : ucfirst($paymentData[$pagination]->paid_from_slug),
                    $paymentData[$pagination]['reference_number']
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Retention Reconcile Listing',
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
            $paymentData = BillReconcileTransaction::where('bill_id',$request->bill_id)->where('transaction_slug','retention')->orderBy('created_at','desc')->get();
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
                    ($paymentData[$pagination]->paymentType != null) ? ucfirst($paymentData[$pagination]->paid_from_slug).' - '.$paymentData[$pagination]->paymentType->name : ucfirst($paymentData[$pagination]->paid_from_slug),
                    $paymentData[$pagination]['reference_number']
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Retention Reconcile Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
        }
        return response()->json($records,$status);
    }
}



