<?php

namespace App\Http\Controllers\CustomTraits;
use App\Category;
use App\Client;
use App\Http\Requests\CategoryRequest;
use App\Product;
use App\Project;
use App\ProjectSite;
use App\Quotation;
use App\QuotationProduct;
use App\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait BillTrait{

    public function getCreateView(Request $request,$project_site){
        try{
            return view('admin.bill.create1')->with(compact('project_site'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get bill create view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function billProductListing(Request $request){
        try{
            Log::info('inside listing');
            $records = array();
            $category_products = array();
            $quotation = Quotation::where('project_site_id',$project_site['id'])->first()->toArray();
            $quotationProductIds = QuotationProduct::where('quotation_id',$quotation['id'])->pluck('product_id')->toArray();
            $productData = Product::whereIn('id',$quotationProductIds)->get()->toArray();
            $quotationProducts = QuotationProduct::where('quotation_id',$quotation['id'])->get()->toArray();
            for($i = 0; $i < count($productData); $i++){
                $k = 0;
                for($j = 0 ; $j < count($quotationProducts); $j++){
                    if($productData[$i]['id'] == $quotationProducts[$j]['product_id']){
                        $category_products[$i] = Category::where('id',$productData[$i]['category_id'])->first()->toArray();
                        $category_products[$i]['product'] = $quotationProducts[$j];
                        $k++;
                    }
                }
            }
            $taxes = Tax::where('is_active',true)->get()->toArray();
        }catch (\Exception $e){
            $records = array();
            $data = [
                'action' => 'Bill Product listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,200);

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

}


