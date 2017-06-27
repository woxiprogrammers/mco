<?php

namespace App\Http\Controllers\CustomTraits;
use App\Bill;
use App\BillQuotationProducts;
use App\BillStatus;
use App\BillTax;
use App\Category;
use App\Client;
use App\Helper\NumberHelper;
use App\Product;
use App\Project;
use App\ProjectSite;
use App\Quotation;
use App\QuotationProduct;
use App\QuotationStatus;
use App\Tax;
use App\Unit;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;

trait BillTrait{

    public function getCreateView(Request $request,$project_site){
        try{
            $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
            $quotation = Quotation::where('project_site_id',$project_site['id'])->first()->toArray();
            $bills = Bill::where('quotation_id',$quotation['id'])->get()->toArray();
            $quotationProducts = QuotationProduct::where('quotation_id',$quotation['id'])->get()->toArray();
            if($bills != null){
                for($i = 0 ; $i < count($quotationProducts) ; $i++){
                    $quotationProducts[$i]['previous_quantity'] = 0;
                    for($j = 0; $j < count($bills) ; $j++ ){
                        $quotationProducts[$i]['product_detail'] = Product::where('id',$quotationProducts[$i]['product_id'])->first()->toArray();
                        $quotationProducts[$i]['category_name'] = Category::where('id',$quotationProducts[$i]['product_detail']['category_id'])->pluck('name')->first();
                        $quotationProducts[$i]['unit'] = Unit::where('id',$quotationProducts[$i]['product_detail']['unit_id'])->pluck('name')->first();
                        if($quotation['discount'] != 0){
                            $quotationProducts[$i]['rate'] = $quotationProducts[$i]['rate_per_unit'] - ($quotationProducts[$i]['rate_per_unit'] * ($quotation['discount'] / 100));
                        }else{
                            $quotationProducts[$i]['rate'] = $quotationProducts[$i]['rate_per_unit'];
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
                for($i=0 ; $i < count($quotationProducts) ; $i++){
                    $quotationProducts[$i]['product_detail'] = Product::where('id',$quotationProducts[$i]['product_id'])->first()->toArray();
                    $quotationProducts[$i]['category_name'] = Category::where('id',$quotationProducts[$i]['product_detail']['category_id'])->pluck('name')->first();
                    $quotationProducts[$i]['unit'] = Unit::where('id',$quotationProducts[$i]['product_detail']['unit_id'])->pluck('name')->first();
                    $quotationProducts[$i]['previous_quantity'] = 0;
                    if($quotation['discount'] != 0){
                        $quotationProducts[$i]['rate'] = $quotationProducts[$i]['rate_per_unit'] - ($quotationProducts[$i]['rate_per_unit'] * ($quotation['discount'] / 100));
                    }else{
                        $quotationProducts[$i]['rate'] = $quotationProducts[$i]['rate_per_unit'];
                    }
                }
            }
            $taxes = Tax::where('is_active',true)->get()->toArray();
            return view('admin.bill.create')->with(compact('quotation','bills','project_site','quotationProducts','taxes'));
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

    public function getManageView(Request $request){
        try{
            return view('admin.bill.manage');
        }catch (\Exception $e){
            $data = [
                'action' => 'Get bill manage view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function billListing(Request $request){
        try{
            $iterator = 0;
            $listingData = array();
            $quotationIds = Bill::groupBy('quotation_id')->pluck('quotation_id')->toArray();
            $projectSiteIds = Quotation::whereIn('id',$quotationIds)->pluck('project_site_id')->toArray();
            $projectSiteData = ProjectSite::whereIn('id',$projectSiteIds)->get()->toArray();
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
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($listingData); $iterator++,$pagination++ ){
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

    public function editBill(Request $request,$bill){
        try{
            $selectedBillId = $bill['id'];
            $bills = Bill::where('quotation_id',$bill['quotation_id'])->get()->toArray();
            $billQuotationProducts = BillQuotationProducts::where('bill_id',$bill['id'])->get()->toArray();
            $total['previous_bill_amount'] = $total['current_bill_amount'] = $total['cumulative_bill_amount'] = 0;
            for($iterator = 0 ; $iterator < count($billQuotationProducts) ; $iterator++){
                $billQuotationProducts[$iterator]['previous_quantity'] = 0;
                $billQuotationProducts[$iterator]['quotationProducts'] = QuotationProduct::where('id',$billQuotationProducts[$iterator]['quotation_product_id'])->where('quotation_id',$bill['quotation_id'])->first();
                $billQuotationProducts[$iterator]['productDetail'] = Product::where('id',$billQuotationProducts[$iterator]['quotationProducts']['product_id'])->first();
                $billQuotationProducts[$iterator]['unit'] = Unit::where('id',$billQuotationProducts[$iterator]['productDetail']['unit_id'])->pluck('name')->first();
                $billQuotationProducts[$iterator]['current_bill_amount'] = $billQuotationProducts[$iterator]['quantity'] * $billQuotationProducts[$iterator]['quotationProducts']['rate_per_unit'];
                $previousBills = BillQuotationProducts::where('bill_id','<',$bill['id'])->get()->toArray();
                foreach($previousBills as $key => $previousBill){
                    if($billQuotationProducts[$iterator]['quotation_product_id'] == $previousBill['quotation_product_id']){
                        $billQuotationProducts[$iterator]['previous_quantity'] = $billQuotationProducts[$iterator]['previous_quantity'] +  $previousBill['quantity'];
                    }
                }
                $billQuotationProducts[$iterator]['previous_bill_amount'] = $billQuotationProducts[$iterator]['previous_quantity'] * $billQuotationProducts[$iterator]['quotationProducts']['rate_per_unit'];
                $billQuotationProducts[$iterator]['cumulative_quantity'] = $billQuotationProducts[$iterator]['quantity'] + $billQuotationProducts[$iterator]['previous_quantity'];
                $billQuotationProducts[$iterator]['cumulative_bill_amount'] = $billQuotationProducts[$iterator]['cumulative_quantity'] * $billQuotationProducts[$iterator]['quotationProducts']['rate_per_unit'];
                $total['previous_bill_amount'] = $total['previous_bill_amount'] + $billQuotationProducts[$iterator]['previous_bill_amount'];
                $total['current_bill_amount'] = $total['current_bill_amount'] + $billQuotationProducts[$iterator]['current_bill_amount'];
                $total['cumulative_bill_amount'] = $total['cumulative_bill_amount'] + $billQuotationProducts[$iterator]['cumulative_bill_amount'];
            }
            $final['previous_bill_amount'] = $total_rounded['previous_bill_amount'] = round($total['previous_bill_amount']);
            $final['current_bill_amount'] = $total_rounded['current_bill_amount'] = round($total['current_bill_amount']);
            $final['cumulative_bill_amount'] = $total_rounded['cumulative_bill_amount'] = round($total['cumulative_bill_amount']);
            $taxes = BillTax::where('bill_id',$bill['id'])->with('taxes')->get()->toArray();
            for($j = 0 ; $j < count($taxes) ; $j++){
                $taxes[$j]['previous_bill_amount'] = round($total['previous_bill_amount'] * ($taxes[$j]['percentage'] / 100) , 3);
                $taxes[$j]['current_bill_amount'] = round($total['current_bill_amount'] * ($taxes[$j]['percentage'] / 100) , 3);
                $taxes[$j]['cumulative_bill_amount'] = round($total['cumulative_bill_amount'] * ($taxes[$j]['percentage'] / 100) , 3);
                $final['previous_bill_amount'] = round($final['previous_bill_amount'] + $taxes[$j]['previous_bill_amount']);
                $final['current_bill_amount'] = round($final['current_bill_amount'] + $taxes[$j]['current_bill_amount']);
                $final['cumulative_bill_amount'] = round($final['cumulative_bill_amount'] + $taxes[$j]['cumulative_bill_amount']);
            }
            return view('admin.bill.view')->with(compact('selectedBillId','total','total_rounded','final','total_current_bill_amount','bills','billQuotationProducts','taxes'));
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
            $bill['bill_status_id'] = BillStatus::where('slug','unpaid')->pluck('id')->first();
            $bill_created = Bill::create($bill);
            foreach($request['quotation_product_id'] as $key => $value){
                foreach($request['current_quantity'] as $quantities => $quantity){
                    if($key == $quantities){
                        $bill_quotation_product['bill_id'] = $bill_created['id'];
                        $bill_quotation_product['quotation_product_id'] = $value;
                        $bill_quotation_product['quantity'] = $quantity;
                        BillQuotationProducts::create($bill_quotation_product);
                    }
                }
            }
            foreach($request['tax_percentage'] as $key => $value){
                $bill_taxes['tax_id'] = $key;
                $bill_taxes['bill_id'] = $bill_created['id'];
                $bill_taxes['percentage'] = $value;
                BillTax::create($bill_taxes);
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

    public function generateCurrentBill(Request $request,$bill){
        try{
            $data = array();
            $invoiceData = $taxData = array();
            $allBillIds = Bill::where('quotation_id',$bill['quotation_id'])->pluck('id')->toArray();
            $data['currentBillID'] = 1;
            foreach($allBillIds as $key => $billId){
                 if($billId == $bill['id']){
                     $data['currentBillID'] = $key+1;
                 }
             }
            $data['billDate'] =$bill->created_at->formatLocalized('%d/%m/%Y');
            $data['projectSiteName'] = ProjectSite::where('id',$bill->quotation->project_site_id)->pluck('name')->first();
            $data['clientCompany'] = Client::where('id',$bill->quotation->project_site->project->client_id)->pluck('company')->first();
            $billQuotationProducts = BillQuotationProducts::where('bill_id',$bill['id'])->get();
            $i = $j = $data['subTotal'] = $data['grossTotal'] = 0;
            foreach($billQuotationProducts as $key => $billQuotationProduct){
                    $invoiceData[$i]['product_name'] = $billQuotationProduct->quotation_products->product->name;
                    $invoiceData[$i]['quantity'] = $billQuotationProduct->quantity;
                    $invoiceData[$i]['unit'] = $billQuotationProduct->quotation_products->product->unit->name;
                    if($billQuotationProduct->quotation_products->discount != 0){
                        $invoiceData[$i]['rate'] = round(($billQuotationProduct->quotation_products->rate_per_unit - ($billQuotationProduct->quotation_products->rate_per_unit * ($billQuotationProduct->quotation_products->discount / 100))),3);
                    }else{
                        $invoiceData[$i]['rate'] = $billQuotationProduct->quotation_products->rate_per_unit;
                    }
                    $invoiceData[$i]['amount'] = round(($invoiceData[$i]['quantity'] * $invoiceData[$i]['rate']), 3);
                    $data['subTotal'] = $data['subTotal'] + $invoiceData[$i]['amount'];
                $i++;
            }
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
            $pdf = PDF::loadView('admin.bill.pdf.invoice',$data);

            return $pdf->download('Invoice_'.$data['currentBillID'].'_'.date('Y-m-d').'.pdf');
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
            $data['projectSiteName'] = ProjectSite::where('id',$bill->quotation->project_site_id)->pluck('name')->first();
            $data['clientCompany'] = Client::where('id',$bill->quotation->project_site->project->client_id)->pluck('company')->first();
            $previousBillIds = Bill::where('quotation_id',$bill['quotation_id'])->where('id','<',$bill['id'])->pluck('id');
            $billProducts = BillQuotationProducts::whereIn('bill_id',$previousBillIds)->get()->toArray();
            $currentBillProducts = BillQuotationProducts::where('bill_id',$bill['id'])->get()->toArray();
            $allBillIds = Bill::where('quotation_id',$bill['quotation_id'])->where('id','<=',$bill['id'])->pluck('id');
            $distinctProducts = BillQuotationProducts::whereIn('bill_id',$allBillIds)->distinct('quotation_product_id')->orderBy('quotation_product_id')->select('quotation_product_id')->get();
            $invoiceData =array();
            $i = 0;
            foreach($distinctProducts as $key => $distinctProduct){
                $invoiceData[$i]['product_name'] = $distinctProduct->quotation_products->product->name;
                $invoiceData[$i]['unit'] = $distinctProduct->quotation_products->product->unit->name;
                if($distinctProduct->quotation_products->discount != 0){
                    $invoiceData[$i]['rate'] = round(($distinctProduct->quotation_products->rate_per_unit - ($distinctProduct->quotation_products->rate_per_unit * ($distinctProduct->quotation_products->discount / 100))),3);
                }else{
                    $invoiceData[$i]['rate'] = $distinctProduct->quotation_products->rate_per_unit;
                }
                $invoiceData[$i]['quotation_product_id'] = $distinctProduct['quotation_product_id'];
                $invoiceData[$i]['previous_quantity'] = 0;
                foreach($billProducts as $k => $billProduct){
                    if($distinctProduct['quotation_product_id'] == $billProduct['quotation_product_id']){
                        $invoiceData[$i]['previous_quantity'] = $invoiceData[$i]['previous_quantity'] + $billProduct['quantity'];
                        $invoiceData[$i]['current_quantity'] = 0;
                    }
                }
                foreach($currentBillProducts as $j => $currentBillProduct){
                    if($distinctProduct['quotation_product_id'] == $currentBillProduct['quotation_product_id']){
                        $invoiceData[$i]['current_quantity'] = $currentBillProduct['quantity'];
                    }
                }
                $invoiceData[$i]['cumulative_quantity'] = $invoiceData[$i]['previous_quantity'] + $invoiceData[$i]['current_quantity'];
                $invoiceData[$i]['previous_bill_amount'] = $invoiceData[$i]['previous_quantity'] * $invoiceData[$i]['rate'];
                $invoiceData[$i]['current_bill_amount'] = $invoiceData[$i]['current_quantity'] * $invoiceData[$i]['rate'];
                $invoiceData[$i]['cumulative_bill_amount'] = $invoiceData[$i]['cumulative_quantity'] * $invoiceData[$i]['rate'];
                $i++;
            }
            $data['invoiceData'] = $invoiceData;
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML(view('admin.bill.pdf.cumulative',$data));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream();
        }catch(Exception $e){
            $data = [
                'actions' => 'Generate Cumulative Bill',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
        }
    }



}


