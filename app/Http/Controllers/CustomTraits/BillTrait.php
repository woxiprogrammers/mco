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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait BillTrait{

    public function getCreateView(Request $request,$project_site){
        try{
            //$quotation = Quotation::where('project_site_id',$project_site['id'])->first()->toArray();
            /*$quotationProducts = QuotationProduct::where('quotation_id',$quotation['id'])->lists('product_id');
            $categories = Product::whereIn('id',$quotationProducts)->get()->toArray();*/
            /*$quotationProducts = [1,2,3,4,5,6];
            $categories = [1,2,3];
            foreach($categories as $category){

           }*/
     //       dd($project_site);
            $clients = Client::where('is_active',true)->get()->toArray();
            $categories = Category::where('is_active',true)->get()->toArray();
            return view('admin.bill.create')->with(compact('clients','categories'));
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

    public function getProjects(Request $request,$client){
        try{
            $status = 200;
            Log::info('inside projestc');
            $projects = array();
            $projects = Project::where('client_id',$client['id'])->get()->toArray();
        }catch (\Exception $e){
            $status = 500;
            $projects = array();
            $data = [
                'action' => 'Get Projects',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($projects,$status);
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


