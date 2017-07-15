<?php

namespace App\Http\Controllers\CustomTraits;
use App\Bill;
use App\BillImage;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

trait BillTrait{

    public function getCreateView(Request $request,$project_site){
        try{
            $quotation = Quotation::where('project_site_id',$project_site['id'])->first()->toArray();
            $cancelBillStatusId = BillStatus::where('slug','cancelled')->pluck('id')->first();
            $bills = Bill::where('quotation_id',$quotation['id'])->where('bill_status_id','!=',$cancelBillStatusId)->get()->toArray();
            $quotationProducts = QuotationProduct::where('quotation_id',$quotation['id'])->get()->toArray();
            if($bills != null){
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
            $taxes = Tax::where('is_active',true)->get();
            return view('admin.bill.manage-bill')->with(compact('taxes','project_site'));
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

    public function billListing(Request $request,$project_site){
        try{
            $listingData = $currentTaxes = array();
            $iterator = $i = 0;
            $array_no = 1;
            $quotation = Quotation::where('project_site_id',$project_site->id)->first();
            $bills = Bill::where('quotation_id',$quotation->id)->get();
            $cancelBillStatusId = BillStatus::where('slug','cancelled')->pluck('id')->first();
            foreach($bills as $key => $bill){
                $listingData[$iterator]['bill_id'] = $bill->id;
                if($bill->bill_status_id != $cancelBillStatusId){
                    $listingData[$iterator]['array_no'] = $array_no;
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
                $billTaxes = BillTax::where('bill_id',$bill->id)->pluck('tax_id')->toArray();
                if($billTaxes != null){
                    $currentTaxes = Tax::whereNotIn('id',$billTaxes)->where('is_active',true)->select('id as tax_id','name')->get();
                }
                if($currentTaxes != null){
                    $currentTaxes = array_merge($bill->bill_tax->toArray(),$currentTaxes->toArray());
                    usort($currentTaxes, function($a, $b) {
                        return $a['tax_id'] > $b['tax_id'];
                    });
                }else{
                    $currentTaxes = Tax::where('is_active',true)->select('id as tax_id')->get();
                }
                $listingData[$iterator]['final_total'] = $total_amount;
                foreach($currentTaxes as $key2 => $tax){
                    if(array_key_exists('percentage',$tax)){
                        $listingData[$iterator]['tax'][$i] = $total_amount * ($tax['percentage'] / 100);
                    }else{
                        $listingData[$iterator]['tax'][$i] = 0;
                    }
                    $listingData[$iterator]['final_total'] = round($listingData[$iterator]['final_total'] + $listingData[$iterator]['tax'][$i]);
                    $i++;
                }
                $iterator++;
            }
            $iTotalRecords = count($listingData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($listingData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($listingData); $iterator++,$pagination++ ){
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
                            <li>
                                <a href="javascript:void(0);">
                                    <i class="icon-docs"></i> Make Payment </a>
                            </li>
                        </ul>
                    </div>');
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
            $bills = Bill::where('quotation_id',$bill['quotation_id'])->get()->toArray();
            $billQuotationProducts = BillQuotationProducts::where('bill_id',$bill['id'])->get()->toArray();
            $total['previous_bill_amount'] = $total['current_bill_amount'] = $total['cumulative_bill_amount'] = 0;
            for($iterator = 0 ; $iterator < count($billQuotationProducts) ; $iterator++){
                $billQuotationProducts[$iterator]['previous_quantity'] = 0;
                $billQuotationProducts[$iterator]['quotationProducts'] = QuotationProduct::where('id',$billQuotationProducts[$iterator]['quotation_product_id'])->where('quotation_id',$bill['quotation_id'])->first();
                $billQuotationProducts[$iterator]['productDetail'] = Product::where('id',$billQuotationProducts[$iterator]['quotationProducts']['product_id'])->first();
                $billQuotationProducts[$iterator]['unit'] = Unit::where('id',$billQuotationProducts[$iterator]['productDetail']['unit_id'])->pluck('name')->first();
                $quotation_id = Bill::where('id',$billQuotationProducts[$iterator]['bill_id'])->pluck('quotation_id')->first();
                $discount = Quotation::where('id',$quotation_id)->pluck('discount')->first();
                $rate_per_unit = QuotationProduct::where('id',$billQuotationProducts[$iterator]['quotation_product_id'])->pluck('rate_per_unit')->first();
                $billQuotationProducts[$iterator]['rate'] = round(($rate_per_unit - ($rate_per_unit * ($discount / 100))),3);
                $billQuotationProducts[$iterator]['current_bill_amount'] = round(($billQuotationProducts[$iterator]['quantity'] * $billQuotationProducts[$iterator]['rate']),3);
                $previousBills = BillQuotationProducts::where('bill_id','<',$bill['id'])->get()->toArray();
                foreach($previousBills as $key => $previousBill){
                    if($billQuotationProducts[$iterator]['quotation_product_id'] == $previousBill['quotation_product_id']){
                        $billQuotationProducts[$iterator]['previous_quantity'] = $billQuotationProducts[$iterator]['previous_quantity'] +  $previousBill['quantity'];
                    }
                }
                $billQuotationProducts[$iterator]['cumulative_quantity'] = round(($billQuotationProducts[$iterator]['quantity'] + $billQuotationProducts[$iterator]['previous_quantity']),3);
                $total['current_bill_amount'] = round(($total['current_bill_amount'] + $billQuotationProducts[$iterator]['current_bill_amount']),3);
            }
            $final['current_bill_amount'] = $total_rounded['current_bill_amount'] = round($total['current_bill_amount']);
            $taxes = BillTax::where('bill_id',$bill['id'])->with('taxes')->get()->toArray();
            for($j = 0 ; $j < count($taxes) ; $j++){
                $taxes[$j]['current_bill_amount'] = round($total['current_bill_amount'] * ($taxes[$j]['percentage'] / 100) , 3);
                $final['current_bill_amount'] = round($final['current_bill_amount'] + $taxes[$j]['current_bill_amount']);
            }
            return view('admin.bill.view')->with(compact('bill','selectedBillId','total','total_rounded','final','total_current_bill_amount','bills','billQuotationProducts','taxes'));
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
            $bill_created = Bill::create($bill);
            foreach($request['quotation_product_id'] as $key => $value){
                $bill_quotation_product['bill_id'] = $bill_created['id'];
                $bill_quotation_product['quotation_product_id'] = $key;
                $bill_quotation_product['quantity'] = $value['current_quantity'];
                $bill_quotation_product['description'] = ucfirst($value['product_description']);
                BillQuotationProducts::create($bill_quotation_product);
            }
            if($request->has('tax_percentage')){
                foreach($request['tax_percentage'] as $key => $value){
                    if($value != 0){
                        $bill_taxes['tax_id'] = $key;
                        $bill_taxes['bill_id'] = $bill_created['id'];
                        $bill_taxes['percentage'] = $value;
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
            $paidStatusId = BillStatus::where('slug','paid')->pluck('id')->first();
            Bill::where('id',$request->bill_id)->update(['remark' => $request->remark , 'bill_status_id' => $paidStatusId]);
            $imagesUploaded = $this->uploadPaidBillImages($request->bill_images,$request->bill_id);
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
            $allBillIds = Bill::where('quotation_id',$bill['quotation_id'])->pluck('id')->toArray();
            $data['company_name'] = $bill->quotation->project_site->project->client->company;
            $data['billData'] = $bill;
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
                    $invoiceData[$i]['description'] = $billQuotationProduct->description;
                    $invoiceData[$i]['quantity'] = round(($billQuotationProduct->quantity),3);
                    $invoiceData[$i]['unit'] = $billQuotationProduct->quotation_products->product->unit->name;
                    $invoiceData[$i]['rate'] = round(($billQuotationProduct->quotation_products->rate_per_unit - ($billQuotationProduct->quotation_products->rate_per_unit * ($billQuotationProduct->quotation_products->quotation->discount / 100))),3);
                    $invoiceData[$i]['amount'] = round(($invoiceData[$i]['quantity'] * $invoiceData[$i]['rate']), 3);
                    $data['subTotal'] = round(($data['subTotal'] + $invoiceData[$i]['amount']),3);
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
            $previousBillIds = Bill::where('quotation_id',$bill['quotation_id'])->where('id','<',$bill['id'])->pluck('id');
            $billProducts = BillQuotationProducts::whereIn('bill_id',$previousBillIds)->get()->toArray();
            $currentBillProducts = BillQuotationProducts::where('bill_id',$bill['id'])->get()->toArray();
            $allBillIds = Bill::where('quotation_id',$bill['quotation_id'])->where('id','<=',$bill['id'])->pluck('id');
            foreach($allBillIds as $key => $billId){
                if($billId == $bill['id']){
                    $data['currentBillID'] = $key+1;
                    break;
                }
            }
            $distinctProducts = BillQuotationProducts::whereIn('bill_id',$allBillIds)->distinct('quotation_product_id')->orderBy('quotation_product_id')->select('quotation_product_id')->get();
            $invoiceData = $total = array();
            $i = $total['previous_quantity'] = $total['current_quantity'] = $total['cumulative_quantity'] = $total['rate'] = $total['previous_bill_amount'] = $total['current_bill_amount'] = $total['cumulative_bill_amount'] = 0;
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
                $total['previous_bill_amount'] = round(($total['previous_bill_amount'] + $invoiceData[$i]['previous_bill_amount']),3);
                $total['current_bill_amount'] = round(($total['current_bill_amount'] + $invoiceData[$i]['current_bill_amount']),3);
                $total['cumulative_bill_amount'] = round(($total['cumulative_bill_amount']  + $invoiceData[$i]['cumulative_bill_amount']),3);
                $i++;
            }
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
            $allBillIDs = Bill::where('id','<=',$bill->id)->where('quotation_id',$bill->quotation_id)->pluck('id')->toArray();
            $billQuotationProducts = BillQuotationProducts::whereIn('bill_id',$allBillIDs)->get();
            foreach($quotationProducts as $key => $quotationProduct){
                $quotationProduct['previous_quantity'] = 0;
                foreach($billQuotationProducts as $key1 => $billQuotationProduct){
                    $quotationProduct['discounted_rate'] = round(($quotationProduct['rate_per_unit'] - ($quotationProduct['rate_per_unit'] * ($quotationProduct->quotation->discount / 100))),3);
                    if($billQuotationProduct->quotation_product_id == $quotationProduct->id){
                        $quotationProduct['previous_quantity'] = $quotationProduct['previous_quantity'] + $billQuotationProduct->quantity;
                        if($billQuotationProduct->bill_id == $bill->id){
                            $quotationProduct['previous_quantity'] = $quotationProduct['previous_quantity'] + $billQuotationProduct->quantity - $billQuotationProduct->quantity;
                            $quotationProduct['bill_description'] = $billQuotationProduct->description;
                            $quotationProduct['current_quantity'] = $billQuotationProduct->quantity;
                        }
                    }
                }
            }
            $billTaxes = BillTax::where('bill_id',$bill->id)->pluck('tax_id')->toArray();
            $taxes = $currentTaxes =  array();
            if($billTaxes != null){
                $currentTaxes = Tax::whereNotIn('id',$billTaxes)->where('is_active',true)->get();
            }
            $currentTaxes = array_merge($bill->bill_tax->toArray(),$currentTaxes->toArray());
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
            return view('admin.bill.edit')->with(compact('bill','quotationProducts','taxes'));
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
            $products = $request->quotation_product_id;
            $alreadyExistQuotationProductIds = BillQuotationProducts::where('bill_id',$bill->id)->pluck('quotation_product_id')->toArray();
            $editQuotationProductIds = array_keys($products);
            $deletedQuotationProductIds = array_values(array_diff($alreadyExistQuotationProductIds,$editQuotationProductIds));
            foreach($deletedQuotationProductIds as $productId){
                BillQuotationProducts::where('bill_id',$bill->id)->where('quotation_product_id',$productId)->delete();
            }
            foreach($products as $key => $product){
                $alreadyExistProduct = BillQuotationProducts::where('bill_id',$bill->id)->where('quotation_product_id',$key)->first();
                if($key == $alreadyExistProduct->quotation_product_id){
                    if($product['current_quantity'] != $alreadyExistProduct->quantity){
                        $billQuotationProduct['quantity'] = $product['current_quantity'];
                    }
                    $billQuotationProduct['description'] = $product['product_description'];
                    BillQuotationProducts::where('bill_id',$bill->id)->where('quotation_product_id',$key)->update($billQuotationProduct);
                }else{
                    $billQuotationProduct['bill_id'] = $bill->id;
                    $billQuotationProduct['quotation_product_id'] = $key;
                    $billQuotationProduct['quantity'] = $product['current_quantity'];
                    $billQuotationProduct['description'] = $product['product_description'];
                    BillQuotationProducts::create($billQuotationProduct);
                }
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
}


