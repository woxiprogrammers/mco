<?php

namespace App\Http\Controllers\CustomTraits;
use App\Bill;
use App\BillQuotationProducts;
use App\BillStatus;
use App\BillTax;
use App\Category;
use App\Client;
use App\Product;
use App\Project;
use App\ProjectSite;
use App\Quotation;
use App\QuotationProduct;
use App\Tax;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait BillTrait{

    public function getCreateView(Request $request,$project_site){
        try{
            $quotation = Quotation::where('project_site_id',$project_site['id'])->first()->toArray();
            $bills = Bill::where('quotation_id',$quotation['id'])->get()->toArray();
            $quotationProducts = QuotationProduct::where('quotation_id',$quotation['id'])->get()->toArray();
            if($bills != null){
                for($i=0 ; $i < count($quotationProducts) ; $i++){
                    $quotationProducts[$i]['previous_quantity'] = 0;
                    for($j = 0; $j < count($bills) ; $j++ ){
                        $quotationProducts[$i]['product_detail'] = Product::where('id',$quotationProducts[$i]['product_id'])->first()->toArray();
                        $quotationProducts[$i]['category_name'] = Category::where('id',$quotationProducts[$i]['product_detail']['category_id'])->pluck('name')->first();
                        $quotationProducts[$i]['unit'] = Unit::where('id',$quotationProducts[$i]['product_detail']['unit_id'])->pluck('name')->first();
                        $bill_produc = BillQuotationProducts::where('bill_id',$bills[$j]['id'])->where('quotation_product_id',$quotationProducts[$i]['id'])->get()->toArray();
                        for($k =0 ; $k < count($bill_produc); $k++ ){
                            if($bill_produc[$k]['quotation_product_id'] == $quotationProducts[$i]['id']){
                                $quotationProducts[$i]['previous_quantity'] = $quotationProducts[$i]['previous_quantity'] + $bill_produc[$k]['quantity'];
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
            $clients = Client::where('is_active',true)->orderBy('id','asc')->get()->toArray();
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
            $projects = Project::where('client_id',$client['id'])->get()->toArray();
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
            $listingData = array();
            $k = 0;
            $clientData = Client::where('is_active',true)->orderBy('id','asc')->get()->toArray();
            for($i = 0 ; $i < count($clientData) ; $i++){
                $project = Project::where('client_id',$clientData[$i]['id'])->get()->toArray();
                for($j = 0 ; $j < count($project) ; $j++){
                    $project_site = ProjectSite::where('project_id',$project[$j]['id'])->get()->toArray();
                    for($l = 0 ; $l < count($project_site) ; $l++){
                        $listingData[$k]['company'] = $clientData[$i]['company'];
                        $listingData[$k]['project_name'] = $project[$j]['name'];
                        $listingData[$k]['project_site_id'] = $project_site[$l]['id'];
                        $listingData[$k]['project_site_name'] = $project_site[$l]['name'];
                        $k++;
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

    public function createBill(Request $request){
        try{
            $bill_quotation_product = array();
            //dd($request->all());
            /*if(){

            }else{*/
                 $bill['quotation_id'] = $request['quotation_id'];
                 $bill['bill_status_id'] = BillStatus::where('slug','unpaid')->pluck('id')->first();
                 Bill::create($bill);
                 foreach($request['quotation_product_id'] as $key=>$value){
                     foreach($request['current_quantity'] as $quantities=>$quantity){
                         if($key == $quantities){
                             $bill_quotation_product['bill_id'] = $request->bill_id;
                             $bill_quotation_product['quotation_product_id'] = $value;
                             $bill_quotation_product['quantity'] = $quantity;
                             BillQuotationProducts::create($bill_quotation_product);
                         }
                     }
                 }
                 foreach($request['tax_percentage'] as $key=>$value){
                     $bill_taxes['tax_id'] = $key;
                     $bill_taxes['percentage'] = $value;
                     BillTax::create($bill_taxes);
                 }
            //}
            //dd(123);
            $request->session()->flash('success','Bill Created Successfully');
            return redirect('/bill/create');
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

}


