<?php

namespace App\Http\Controllers\CustomTraits;
use App\Bill;
use App\BillImage;
use App\BillQuotationExtraItem;
use App\BillQuotationProducts;
use App\BillStatus;
use App\BillTax;
use App\BillTransaction;
use App\Category;
use App\Client;
use App\Helper\NumberHelper;
use App\Product;
use App\ProductDescription;
use App\Project;
use App\ProjectSite;
use App\Quotation;
use App\QuotationExtraItem;
use App\QuotationProduct;
use App\QuotationStatus;
use App\Tax;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

trait BillTrait{

    public function getCreateView(Request $request,$project_site){
        try{
            $quotation = Quotation::where('project_site_id',$project_site['id'])->first()->toArray();
            $cancelBillStatusId = BillStatus::where('slug','cancelled')->pluck('id')->first();
            $bills = Bill::where('quotation_id',$quotation['id'])->where('bill_status_id','!=',$cancelBillStatusId)->get()->toArray();
            $quotationProducts = QuotationProduct::where('quotation_id',$quotation['id'])->get()->toArray();
            $extraItems = QuotationExtraItem::where('quotation_id',$quotation['id'])->get();
            if($bills != null){
                foreach ($extraItems as $key => $extraItem){
                    $extraItem['previous_rate'] = BillQuotationExtraItem::whereIn('bill_id',array_column($bills,'id'))->where('quotation_extra_item_id',$extraItem->id)->sum('rate');
                }
                for($i = 0 ; $i < count($quotationProducts) ; $i++){
                    $quotationProducts[$i]['previous_quantity'] = 0;
                    for($j = 0; $j < count($bills) ; $j++ ){
                        $quotationProducts[$i]['product_detail'] = Product::where('id',$quotationProducts[$i]['product_id'])->first()->toArray();
                        $quotationProducts[$i]['category_name'] = Category::where('id',$quotationProducts[$i]['product_detail']['category_id'])->pluck('name')->first();
                        $quotationProducts[$i]['unit'] = Unit::where('id',$quotationProducts[$i]['product_detail']['unit_id'])->pluck('name')->first();
                        if($quotation['discount'] != 0){
                            $quotationProducts[$i]['rate'] = round(($quotationProducts[$i]['rate_per_unit'] - ($quotationProducts[$i]['rate_per_unit'] * ($quotation['discount'] / 100))),3);
                        }else{
                            $quotationProducts[$i]['rate'] = round(($quotationProducts[$i]['rate_per_unit']),3);
                        }
                        $bill_products = BillQuotationProducts::where('bill_id',$bills[$j]['id'])->where('quotation_product_id',$quotationProducts[$i]['id'])->get()->toArray();
                        for($k = 0 ; $k < count($bill_products) ; $k++ ){
                            if($bill_products[$k]['quotation_product_id'] == $quotationProducts[$i]['id']){
                                $quotationProducts[$i]['previous_quantity'] = $quotationProducts[$i]['previous_quantity'] + $bill_products[$k]['quantity'];
                            }
                        }
                    }
                }
            }else{
                foreach ($extraItems as $key => $extraItem){
                    $extraItem['previous_rate'] = 0;
                }
                for($i=0 ; $i < count($quotationProducts) ; $i++){
                    $quotationProducts[$i]['product_detail'] = Product::where('id',$quotationProducts[$i]['product_id'])->first()->toArray();
                    $quotationProducts[$i]['category_name'] = Category::where('id',$quotationProducts[$i]['product_detail']['category_id'])->pluck('name')->first();
                    $quotationProducts[$i]['unit'] = Unit::where('id',$quotationProducts[$i]['product_detail']['unit_id'])->pluck('name')->first();
                    $quotationProducts[$i]['previous_quantity'] = 0;
                    if($quotation['discount'] != 0){
                        $quotationProducts[$i]['rate'] = round(($quotationProducts[$i]['rate_per_unit'] - ($quotationProducts[$i]['rate_per_unit'] * ($quotation['discount'] / 100))),3);
                    }else{
                        $quotationProducts[$i]['rate'] = round(($quotationProducts[$i]['rate_per_unit']),3);
                    }
                }
            }
            $taxes = Tax::where('is_active',true)->where('is_special',false)->get()->toArray();
            $specialTaxes = Tax::where('is_active', true)->where('is_special',true)->get();

            return view('admin.bill.create')->with(compact('extraItems','quotation','bills','project_site','quotationProducts','taxes','specialTaxes'));
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
                $bills = Bill::where('quotation_id',$quotation->id)->where('bill_status_id',$statusId)->get();
            }else{
                $statusId = BillStatus::whereIn('slug',['approved','draft'])->get()->toArray();
                $bills = Bill::where('quotation_id',$quotation->id)->whereIn('bill_status_id',array_column($statusId,'id'))->get();
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
                foreach($bill->bill_quotation_product as $key1 => $product){
                    $rate = round(($product->quotation_products->rate_per_unit - ($product->quotation_products->rate_per_unit * ($product->quotation_products->quotation->discount / 100))),3);
                    $total_amount = $total_amount + ($product->quantity * $rate) ;
                }
                $listingData[$iterator]['subTotal'] = $total_amount;
                $thisBillTax = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                                        ->where('bill_taxes.bill_id',$bill->id)
                                        ->where('taxes.is_special', false)
                                        ->pluck('bill_taxes.tax_id')
                                        ->toArray();
                $otherTaxes = array_values(array_diff($taxesAppliedToBills,$thisBillTax));
                if($thisBillTax != null){
                    $currentTaxes = Tax::whereIn('id',$otherTaxes)->where('is_active',true)->where('is_special', false)->select('id as tax_id','name')->get();
                }
                if($currentTaxes != null){
                    $thisBillTaxInfo = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                        ->where('bill_taxes.bill_id',$bill->id)
                        ->where('taxes.is_special', false)
                        ->select('bill_taxes.percentage as percentage','bill_taxes.tax_id as tax_id')
                        ->get()
                        ->toArray();
                    $currentTaxes = array_merge($thisBillTaxInfo,$currentTaxes->toArray());
                    usort($currentTaxes, function($a, $b) {
                        return $a['tax_id'] > $b['tax_id'];
                    });
                }else{
                    $currentTaxes = Tax::where('is_active',true)->where('is_special', false)->select('id as tax_id')->get();
                }
                $listingData[$iterator]['final_total'] = $total_amount;
                foreach($currentTaxes as $key2 => $tax){
                    if(array_key_exists('percentage',$tax)){
                        $listingData[$iterator]['tax'][$tax['tax_id']] = $total_amount * ($tax['percentage'] / 100);
                    }else{
                        $listingData[$iterator]['tax'][$tax['tax_id']] = 0;
                    }
                    $listingData[$iterator]['final_total'] = round($listingData[$iterator]['final_total'] + $listingData[$iterator]['tax'][$tax['tax_id']]);
                    $i++;
                }
                $thisBillSpecialTax = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                                        ->where('bill_taxes.bill_id',$bill->id)
                                        ->where('taxes.is_special', true)
                                        ->pluck('bill_taxes.tax_id')
                                        ->toArray();
                $otherSpecialTaxes = array_values(array_diff($specialTaxesAppliedToBills,$thisBillSpecialTax));
                if($thisBillSpecialTax != null){
                    $currentSpecialTaxes = Tax::whereIn('id',$otherSpecialTaxes)->where('is_active',true)->where('is_special', true)->select('id as tax_id','name')->get();
                }else{
                    $currentSpecialTaxes = Tax::where('is_active',true)->where('is_special', true)->select('id as tax_id','name')->get();

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
                    $currentSpecialTaxes = Tax::where('is_active',true)->where('is_special', true)->select('id as tax_id')->get();
                }
                foreach($currentSpecialTaxes as $key2 => $tax){
                    $appliedOnTaxes = json_decode($tax['applied_on']);
                    $taxAmount = 0;
                    foreach($appliedOnTaxes as $appliedTaxId){
                        if($appliedTaxId == 0){                 // On Subtotal
                            $taxAmount += $total_amount * ($tax['percentage'] / 100);
                        }else{
                            $taxAmount += $listingData[$iterator]['tax'][$appliedTaxId] * ($tax['percentage'] / 100);
                        }
                    }
                    $listingData[$iterator]['tax'][$tax['tax_id']] = $taxAmount;
                    $listingData[$iterator]['final_total'] = round($listingData[$iterator]['final_total'] + $listingData[$iterator]['tax'][$tax['tax_id']]);
                }
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
                    $listingData[$pagination]['bill_no_format'],
                    $listingData[$pagination]['subTotal'],
                ];
                foreach($listingData[$pagination]['tax'] as $taxAmount){
                    array_push($records['data'][$iterator],round($taxAmount,3));
                }
                array_push($records['data'][$iterator],$listingData[$iterator]['final_total']);
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
                        $listingData[$iterator]['company'] = $clientData[$j]['company'];
                        $listingData[$iterator]['project_name'] = $projectData[$j]['name'];
                        $listingData[$iterator]['project_site_id'] = $projectSiteData[$i]['id'];
                        $listingData[$iterator]['project_site_name'] = $projectSiteData[$i]['name'];
                        $iterator++;
                    }
                }
            }
            $iTotalRecords = count($listingData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($listingData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($listingData); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $listingData[$pagination]['company'],
                    $listingData[$pagination]['project_name'],
                    $listingData[$pagination]['project_site_name'],
                    '<div class="btn-group">
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
                    </div>'
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
            $selectedBillId = $bill['id'];
            $cancelBillStatusId = BillStatus::where('slug','cancelled')->pluck('id')->first();
            $bills = Bill::where('quotation_id',$bill['quotation_id'])->where('bill_status_id','!=',$cancelBillStatusId)->orderBy('created_at','asc')->get()->toArray();
            $billQuotationProducts = BillQuotationProducts::where('bill_id',$bill['id'])->get()->toArray();
            $total['previous_bill_amount'] = $total['current_bill_amount'] = $total['cumulative_bill_amount'] = $total_extra_item =  0;
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
                $billQuotationProducts[$iterator]['current_bill_amount'] = round(($billQuotationProducts[$iterator]['quantity'] * $billQuotationProducts[$iterator]['rate']),3);
                $billWithoutCancelStatus = Bill::where('id','<',$bill['id'])->where('bill_status_id','!=',$cancelBillStatusId)->pluck('id')->toArray();
                $previousBills = BillQuotationProducts::whereIn('bill_id',$billWithoutCancelStatus)->get();
                foreach($previousBills as $key => $previousBill){
                    if($billQuotationProducts[$iterator]['quotation_product_id'] == $previousBill['quotation_product_id']){
                        $billQuotationProducts[$iterator]['previous_quantity'] = $billQuotationProducts[$iterator]['previous_quantity'] +  $previousBill['quantity'];
                    }
                }
                $billQuotationProducts[$iterator]['cumulative_quantity'] = round(($billQuotationProducts[$iterator]['quantity'] + $billQuotationProducts[$iterator]['previous_quantity']),3);
                $total['current_bill_amount'] = round(($total['current_bill_amount'] + $billQuotationProducts[$iterator]['current_bill_amount']),3);
            }
            $extraItems = BillQuotationExtraItem::where('bill_id',$bill->id)->get();
            if(count($extraItems) > 0){
                $total_extra_item = 0;
                foreach($extraItems as $key => $extraItem){
                    $extraItem['previous_rate'] = BillQuotationExtraItem::whereIn('bill_id',array_column($bills,'id'))->where('bill_id','!=',$bill->id)->where('quotation_extra_item_id',$extraItem->quotation_extra_item_id)->sum('rate');
                    $total_extra_item = $total_extra_item + $extraItem['rate'];
                }
                $total['current_bill_amount'] = round(($total['current_bill_amount'] + $total_extra_item),3);
            }

            $final['current_bill_amount'] = $total_rounded['current_bill_amount'] = round($total['current_bill_amount']);
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
                $final['current_bill_amount'] = round($final['current_bill_amount'] + $taxes[$billTaxes[$j]['tax_id']]['current_bill_amount']);
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
                    $final['current_bill_gross_total_amount'] = round(($final['current_bill_amount'] + $specialTaxAmount));
                }
            }else{
                $final['current_bill_gross_total_amount'] = round($final['current_bill_amount']);
            }

            $BillTransactionTotals = BillTransaction::where('bill_id',$bill->id)->pluck('total')->toArray();
            $remainingAmount = $final['current_bill_gross_total_amount'] - array_sum($BillTransactionTotals);
            return view('admin.bill.view')->with(compact('extraItems','bill','selectedBillId','total','total_rounded','final','total_current_bill_amount','bills','billQuotationProducts','taxes','specialTaxes','remainingAmount'));
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
            $bill['quotation_id'] = $request['quotation_id'];
            $bill['bill_status_id'] = BillStatus::where('slug','draft')->pluck('id')->first();
            $bill['date'] = $request->date;
            $bill_created = Bill::create($bill);
            foreach($request['quotation_product_id'] as $key => $value){
                $bill_quotation_product['bill_id'] = $bill_created['id'];
                $bill_quotation_product['quotation_product_id'] = $key;
                $bill_quotation_product['quantity'] = $value['current_quantity'];
                $bill_quotation_product['product_description_id'] = $value['product_description_id'];
                BillQuotationProducts::create($bill_quotation_product);
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

    public function generateCurrentBill(Request $request,$bill){
        try{
            $data = array();
            $invoiceData = $taxData = array();
            if($bill->quotation->project_site->project->hsn_code == null){
                $data['hsnCode'] = '';
            }else{
                $data['hsnCode'] = $bill->quotation->project_site->project->hsn_code->code;
            }
            $allBillIds = Bill::where('quotation_id',$bill['quotation_id'])->pluck('id')->toArray();
            $data['company_name'] = $bill->quotation->project_site->project->client->company;
            $data['billData'] = $bill;
            $data['currentBillID'] = 1;
            foreach($allBillIds as $key => $billId){
                 if($billId == $bill['id']){
                     $data['currentBillID'] = $key+1;
                 }
             }
            $data['billDate'] = date('d/m/Y',strtotime($bill['date']));
            $data['projectSiteName'] = ProjectSite::where('id',$bill->quotation->project_site_id)->pluck('name')->first();
            $data['clientCompany'] = Client::where('id',$bill->quotation->project_site->project->client_id)->pluck('company')->first();
            $billQuotationProducts = BillQuotationProducts::where('bill_id',$bill['id'])->get();
            $i = $j = $data['productSubTotal'] = $data['grossTotal'] = 0;
            foreach($billQuotationProducts as $key => $billQuotationProduct){
                    $invoiceData[$i]['product_name'] = $billQuotationProduct->quotation_products->product->name;
                    $invoiceData[$i]['description'] = $billQuotationProduct->description;
                    $invoiceData[$i]['quantity'] = round(($billQuotationProduct->quantity),3);
                    $invoiceData[$i]['unit'] = $billQuotationProduct->quotation_products->product->unit->name;
                    $invoiceData[$i]['rate'] = round(($billQuotationProduct->quotation_products->rate_per_unit - ($billQuotationProduct->quotation_products->rate_per_unit * ($billQuotationProduct->quotation_products->quotation->discount / 100))),3);
                    $invoiceData[$i]['amount'] = round(($invoiceData[$i]['quantity'] * $invoiceData[$i]['rate']), 3);
                    $data['productSubTotal'] = round(($data['productSubTotal'] + $invoiceData[$i]['amount']),3);
                $i++;
            }
            $data['extraItems'] = BillQuotationExtraItem::where('bill_id',$bill->id)->get();
            if(count($data['extraItems']) > 0){
                $total['extra_item'] = 0;
                foreach($data['extraItems'] as $key => $extraItem){
                    $extraItem['previous_rate'] = BillQuotationExtraItem::whereIn('bill_id',$allBillIds)->where('bill_id','!=',$bill->id)->where('quotation_extra_item_id',$extraItem->quotation_extra_item_id)->sum('rate');
                    $total['extra_item'] = $total['extra_item'] + $extraItem['rate'];
                }
            }else{
                $total['extra_item'] = 0;
            }
            $data['subTotal'] = $data['productSubTotal'] + $total['extra_item'];
            $data['invoiceData'] = $invoiceData;
            $taxes = BillTax::where('bill_id',$bill['id'])->get();
            foreach($taxes as $key => $tax){
                $taxData[$j]['name'] = $tax->taxes->name;
                $taxData[$j]['percentage'] = abs($tax->percentage);
                $taxData[$j]['tax_amount'] = round($data['subTotal'] * ($tax->percentage / 100) , 3);
                $data['grossTotal'] = $data['grossTotal'] + $taxData[$j]['tax_amount'];
                $j++;
            }
            $data['taxData'] = $taxData;
            $data['grossTotal'] = round($data['grossTotal'] + $data['subTotal']);
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
            $data = array();
            $data['currentBillID'] = 1;
            $data['projectSiteName'] = ProjectSite::where('id',$bill->quotation->project_site_id)->pluck('name')->first();
            $data['clientCompany'] = Client::where('id',$bill->quotation->project_site->project->client_id)->pluck('company')->first();
            $cancelBillStatusId = BillStatus::where('slug','cancelled')->pluck('id')->first();
            $previousBillIds = Bill::where('quotation_id',$bill['quotation_id'])->where('bill_status_id','!=',$cancelBillStatusId)->where('id','<',$bill['id'])->pluck('id');
            $billProducts = BillQuotationProducts::whereIn('bill_id',$previousBillIds)->get()->toArray();
            $currentBillProducts = BillQuotationProducts::where('bill_id',$bill['id'])->get()->toArray();
            $allBillIds = Bill::where('quotation_id',$bill['quotation_id'])->where('bill_status_id','!=',$cancelBillStatusId)->where('id','<=',$bill['id'])->pluck('id');
            foreach($allBillIds as $key => $billId){
                if($billId == $bill['id']){
                    $data['currentBillID'] = $key+1;
                    break;
                }
            }
            $distinctProducts = BillQuotationProducts::whereIn('bill_id',$allBillIds)->distinct('quotation_product_id')->orderBy('quotation_product_id')->select('quotation_product_id')->get();
            $invoiceData = $total = array();
            $i = $total['previous_quantity'] = $total['current_quantity'] = $total['cumulative_quantity'] = $total['rate'] = $total['product_previous_bill_amount'] = $total['product_current_bill_amount'] = $total['product_cumulative_bill_amount'] = 0;
            foreach($distinctProducts as $key => $distinctProduct){
                $invoiceData[$i]['product_name'] = $distinctProduct->quotation_products->product->name;
                $invoiceData[$i]['unit'] = $distinctProduct->quotation_products->product->unit->name;
                $invoiceData[$i]['rate'] = round(($distinctProduct->quotation_products->rate_per_unit - ($distinctProduct->quotation_products->rate_per_unit * ($distinctProduct->quotation_products->discount / 100))),3);
                $invoiceData[$i]['quotation_product_id'] = $distinctProduct['quotation_product_id'];
                $invoiceData[$i]['previous_quantity'] = 0;
                foreach($billProducts as $k => $billProduct){
                    if($distinctProduct['quotation_product_id'] == $billProduct['quotation_product_id']){
                        $invoiceData[$i]['previous_quantity'] = round(($invoiceData[$i]['previous_quantity'] + $billProduct['quantity']),3);
                        $invoiceData[$i]['current_quantity'] = 0;
                    }
                }
                foreach($currentBillProducts as $j => $currentBillProduct){
                    if($distinctProduct['quotation_product_id'] == $currentBillProduct['quotation_product_id']){
                        $invoiceData[$i]['current_quantity'] = round(($currentBillProduct['quantity']),3);
                    }
                }
                $invoiceData[$i]['cumulative_quantity'] = round(($invoiceData[$i]['previous_quantity'] + $invoiceData[$i]['current_quantity']),3);
                $invoiceData[$i]['previous_bill_amount'] = round(($invoiceData[$i]['previous_quantity'] * $invoiceData[$i]['rate']),3);
                $invoiceData[$i]['current_bill_amount'] = round(($invoiceData[$i]['current_quantity'] * $invoiceData[$i]['rate']),3);
                $invoiceData[$i]['cumulative_bill_amount'] = round(($invoiceData[$i]['cumulative_quantity'] * $invoiceData[$i]['rate']),3);
                $total['previous_quantity'] = round(($total['previous_quantity'] + $invoiceData[$i]['previous_quantity']),3);
                $total['current_quantity'] = round(($total['current_quantity'] + $invoiceData[$i]['current_quantity']),3);
                $total['cumulative_quantity'] = round(($total['cumulative_quantity'] + $invoiceData[$i]['cumulative_quantity']),3);
                $total['rate'] = round(($total['rate'] + $invoiceData[$i]['rate']),3);
                $total['product_previous_bill_amount'] = round(($total['product_previous_bill_amount'] + $invoiceData[$i]['previous_bill_amount']),3);
                $total['product_current_bill_amount'] = round(($total['product_current_bill_amount'] + $invoiceData[$i]['current_bill_amount']),3);
                $total['product_cumulative_bill_amount'] = round(($total['product_cumulative_bill_amount']  + $invoiceData[$i]['cumulative_bill_amount']),3);
                $i++;
            }
            $data['extraItems'] = BillQuotationExtraItem::where('bill_id',$bill->id)->get();
            if(count($data['extraItems']) > 0){
                $total['extra_item_previous_bill_amount'] = $total['extra_item_current_bill_amount'] = $total['extra_item_cumulative_bill_amount'] = 0;
                foreach($data['extraItems'] as $key => $extraItem){
                    $extraItem['previous_rate'] = BillQuotationExtraItem::whereIn('bill_id',$allBillIds)->where('bill_id','!=',$bill->id)->where('quotation_extra_item_id',$extraItem->quotation_extra_item_id)->sum('rate');
                    $total['extra_item_previous_bill_amount'] = $total['extra_item_previous_bill_amount'] + $extraItem['previous_rate'];
                    $total['extra_item_current_bill_amount'] = $total['extra_item_current_bill_amount'] + $extraItem['rate'];
                    $total['extra_item_cumulative_bill_amount'] = $total['extra_item_previous_bill_amount'] + $total['extra_item_current_bill_amount'];
                }
            }else{
                $total['extra_item_previous_bill_amount'] = $total['extra_item_current_bill_amount'] = $total['extra_item_cumulative_bill_amount'] = 0;
            }
            $total['previous_bill_amount'] = $total['product_previous_bill_amount'] + $total['extra_item_previous_bill_amount'];
            $total['current_bill_amount'] = $total['product_current_bill_amount'] + $total['extra_item_current_bill_amount'];
            $total['cumulative_bill_amount'] = $total['product_cumulative_bill_amount'] + $total['extra_item_cumulative_bill_amount'];
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

    public function editBillView(Request $request,$bill){
        try{
            $i = 0;
            $quotationProducts = $bill->quotation->quotation_products;
            $cancelBillStatusId = BillStatus::where('slug','cancelled')->pluck('id')->first();
            $allBillIDs = Bill::where('id','<=',$bill->id)->where('quotation_id',$bill->quotation_id)->where('bill_status_id','!=',$cancelBillStatusId)->pluck('id')->toArray();
            $billQuotationProducts = BillQuotationProducts::whereIn('bill_id',$allBillIDs)->get();
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
                    }
                }
            }
            $quotationExtraItems = QuotationExtraItem::where('quotation_id',$bill->quotation->id)->get();
            $billExtraItems = BillQuotationExtraItem::where('bill_id',$bill->id)->get();
            foreach($quotationExtraItems as $key => $quotationExtraItem){
                $quotationExtraItem['prev_amount'] = 0;
                $quotationExtraItem['prev_amount'] = BillQuotationExtraItem::whereIn('bill_id',$allBillIDs)->where('bill_id','!=',$bill->id)->where('quotation_extra_item_id',$quotationExtraItem['id'])->sum('rate');
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
                $currentTaxes = Tax::whereNotIn('id',$billTaxes)->where('is_active',true)->where('is_special', false)->get();
            }
            $billTaxInfo = BillTax::where('bill_id',$bill->id)->whereIn('tax_id',$billTaxes)->get()->toArray();
            $currentTaxes = array_merge($billTaxInfo,$currentTaxes->toArray());
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
            return view('admin.bill.edit')->with(compact('bill','quotationProducts','taxes','specialTaxes','quotationExtraItems'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Bill',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editBill(Request $request, $bill){
        try{
            Bill::where('id',$bill->id)->update(['date' => $request->date]);
            $products = $request->quotation_product_id;
            $alreadyExistQuotationProductIds = BillQuotationProducts::where('bill_id',$bill->id)->pluck('quotation_product_id')->toArray();
            $editQuotationProductIds = array_keys($products);
            $deletedQuotationProductIds = array_values(array_diff($alreadyExistQuotationProductIds,$editQuotationProductIds));
            foreach($deletedQuotationProductIds as $productId){
                BillQuotationProducts::where('bill_id',$bill->id)->where('quotation_product_id',$productId)->delete();
            }
            foreach($products as $key => $product){
                $alreadyExistProduct = BillQuotationProducts::where('bill_id',$bill->id)->where('quotation_product_id',$key)->first();
                if($alreadyExistProduct != null){
                    $billQuotationProduct = array();
                    if($key == $alreadyExistProduct->quotation_product_id){
                        if($product['current_quantity'] != $alreadyExistProduct->quantity){
                            $billQuotationProduct['quantity'] = $product['current_quantity'];
                        }
                        $billQuotationProduct['product_description_id'] = $product['product_description_id'];
                        BillQuotationProducts::where('bill_id',$bill->id)->where('quotation_product_id',$key)->update($billQuotationProduct);
                    }else{
                        $billQuotationProduct['bill_id'] = $bill->id;
                        $billQuotationProduct['quotation_product_id'] = $key;
                        $billQuotationProduct['quantity'] = $product['current_quantity'];
                        $billQuotationProduct['product_description_id'] = $product['product_description_id'];
                        BillQuotationProducts::create($billQuotationProduct);
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
            if(count($alreadyPresent) == 0){
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
                $description = BillQuotationProducts::whereIn('bill_id',array_column($data['tillThisBill']->toArray(),'id'))->where('quotation_product_id',$billQuotationProduct->quotation_product_id)->distinct('product_description_id')->select('product_description_id')->get();
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
                        $totalBillQuantity += $productArray[$i]['description'][$description_id->product_description->id]['bills'][$iterator]['quantity'];
                        $totalBillAmount += $productArray[$i]['description'][$description_id->product_description->id]['bills'][$iterator]['amount'];
                        $iterator++;
                    }

                    $productArray[$i]['description'][$description_id->product_description->id]['bills']['total_quantity'] = $totalBillQuantity;
                    $productArray[$i]['description'][$description_id->product_description->id]['bills']['total_amount'] = $totalBillAmount;
                      $j++;
                }
                $i++;
            }
//dd($productArray);
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
                        $total_rate = $total_rate + $billQuotationExtraItems[$iterator]['bills'][$iteratorJ]['current_rate'];
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
                    $isAppliedTax = BillTax::where('tax_id',$taxId['tax_id'])->where('bill_id',$billId)->first();
                    if(count($isAppliedTax) == 0){
                        $taxInfo[$taxId['tax_id']]['bills'][$billId]['percentage'] = 0;
                    }else{
                        $taxInfo[$taxId['tax_id']]['bills'][$billId]['percentage'] = $isAppliedTax['percentage'];
                    }
                    $taxInfo[$taxId['tax_id']]['bills'][$billId]['tax_amount'] = round(($subTotal['subtotal'] * ($taxInfo[$taxId['tax_id']]['bills'][$billId]['percentage']/100)),3);
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
                                $taxAmount += round(($subTotal['subtotal'] * ($taxInfo[$taxId['tax_id']]['bills'][$billId]['percentage']/100)),3);
                            }else{
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
                        //$productRow++;
                        $amountColumn = $totalAmountColumn;
                        $amountColumn++;
                        $sheet->getCell('B'.($productRow))->setValue($extraItem['name']);
                        $sheet->getCell('E'.($productRow))->setValue($extraItem['quotation_rate']);
                        $next_column = 'G';
                        foreach($extraItem['bills'] as $billIterator => $thisBill ){
                            $current_column = $next_column++;
                            $sheet->getCell($current_column.($productRow))->setValue($thisBill['current_rate']);
                            $next_column++;
                        }
                        $sheet->getCell(($amountColumn).($productRow))->setValue($extraItem['total_rate']);
                        $productRow = $productRow + 1;
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
                        $sheet->getCell($columnForTotal.($productRow))->setValue("=SUM($columnForTotal$rowForSubtotal:$columnForTotal$beforeTotalRowNumber)");
                        $columnForTotal++;
                        $columnForTotal++;
                    }
                    $sheet->getCell($columnForTotal.($productRow))->setValue("=SUM($columnForTotal$rowForSubtotal:$columnForTotal$beforeTotalRowNumber)");

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
            $response['subtotal'] = round($subtotal,3);
            foreach($taxValues as $taxId => $value){
                $response['taxes'][$iterator]['tax_id'] = $taxId;
                $response['taxes'][$iterator]['tax_amount'] = round(($subtotal * $value),3);
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
            $transactionData = $request->only('bill_id','total','subtotal','remark');
            $status = 200;
            $response['message'] = "Transaction Saved Successfully";
            BillTransaction::create($transactionData);
        }catch (\Exception $e){
            $status = 500;
            $data = [
                'action' => 'Save Transaction Details',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            $response['message'] = "Something went Wrong";
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($response,$status);
    }

    public function billTransactionListing(Request $request, $billId){
        try{
            $status = 200;
            $transactionDetails = BillTransaction::where('bill_id', $billId)->get();
            $taxes = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                            ->where('taxes.is_special','=', false)
                            ->where('bill_taxes.bill_id','=', $billId)
                            ->orderBy('taxes.id','asc')
                            ->select('bill_taxes.percentage as percentage','taxes.name as tax_name','taxes.id as id','bill_taxes.applied_on as applied_on')
                            ->get()->toArray();
            $specialTaxes = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                ->where('taxes.is_special','=', true)
                ->where('bill_taxes.bill_id','=', $billId)
                ->orderBy('taxes.id','asc')
                ->select('bill_taxes.percentage as percentage','taxes.name as tax_name','taxes.id as id','bill_taxes.applied_on as applied_on')
                ->get()->toArray();
            $records = array();
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = count($transactionDetails);
            $records["recordsFiltered"] = count($transactionDetails);
            $records['data'] = array();
            $end = $request->length < 0 ? count($transactionDetails) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($transactionDetails); $iterator++,$pagination++ ){
                $taxAmounts = array();
                foreach($taxes as $tax){
                    $taxAmounts[$tax['id']] = round(($transactionDetails[$pagination]['subtotal'] * ($tax['percentage'] / 100)),3);
                }
                foreach($specialTaxes as $specialTax){
                    $taxAmount = 0;
                    $appliedOn = json_decode($specialTax['applied_on']);
                    foreach($appliedOn as $onTaxId){
                        if($onTaxId == 0){                        // On subtotal
                            $taxAmount = round(($taxAmount + ($transactionDetails[$pagination]['subtotal'] * ($specialTax['percentage'] / 100))),3);
                        }else{
                            $taxAmount = round(($taxAmount + ($taxAmounts[$onTaxId] * ($specialTax['percentage'] / 100))),3);
                        }
                    }
                    $taxAmounts[$specialTax['id']] = $taxAmount;
                }
                $records['data'][$iterator][] = $pagination + 1;
                $records['data'][$iterator][] = $transactionDetails[$pagination]['subtotal'];
                foreach($taxAmounts as $taxAmnt){
                    $records['data'][$iterator][] = $taxAmnt;
                }
                $records['data'][$iterator][] = round($transactionDetails[$pagination]['subtotal'] + array_sum($taxAmounts));
                $records['data'][$iterator][] = "<a class=\"btn btn-xs green dropdown-toggle transaction-details\" onclick=\"getTransactionDetails(".$transactionDetails[$pagination]['id'].")\">Detail</a>";
            }
        }catch(\Exception $e){
            $status = 500;
            $records = array();
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
}



