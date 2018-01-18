<?php

namespace App\Http\Controllers\Dpr;

use App\Client;
use App\DprDetail;
use App\DprMainCategory;
use App\ProjectSite;
use App\Subcontractor;
use App\SubcontractorDPRCategoryRelation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class DprController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    public function getCategoryManageView(Request $request){
        try{
            return view('dpr.manage-category');
        }catch(\Exception $e){
            $data = [
                'action' => 'Dpr Manage Category',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function getCategoryCreateView(Request $request){
        try{
            $clients = Client::select('id','company')->get();
            return view('dpr.create-category')->with(compact('clients'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Dpr Create Category',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function createCategory(Request $request){
        try{

            $categoryData['name'] = $request->category;
            $categoryData['status'] = (boolean)0;
            DprMainCategory::create($categoryData);
            $request->session()->flash('success', 'Category created successfully.');
            return view('dpr.create-category');
        }catch(\Exception $e){
            $data = [
                'action' => 'Dpr Create Category',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function categoryListing(Request $request){
        try{
            $subCategories = DprMainCategory::select('id','name')->get()->toArray();
            $iTotalRecords = count($subCategories);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($subCategories) : $request->length;
            $categories = array();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($subCategories); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $subCategories[$pagination]['id'],
                    $subCategories[$pagination]['name'],
                    '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="/dpr/change-status/'.$subCategories[$pagination]['id'].'/TRUE">'
                    .'<i class="icon-docs"></i> Enable </a>'
                    .'</li>'
                    .'<li>'
                    .'<a href="/dpr/change-status/'.$subCategories[$pagination]['id'].'/FALSE">'
                    .'    <i class="icon-tag"></i> Disable </a>'
                    .'</li>'
                    .'<li>'
                    .'<a href="/dpr/category-edit/'.$subCategories[$pagination]['id'].'">'
                    .'    <i class="icon-tag"></i> Edit </a>'
                    .'</li>'
                    .'</ul>'
                    .'</div>'
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
            $responseStatus = 200;
            return response()->json($records);
        }catch(\Exception $e){
            $data = [
                'action' => 'Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

    }
    public function changeStatus(Request $request,$id,$status){
        try {
            $categoryData['status'] = (boolean)$status;
            $query = DprMainCategory::where('id',$id)->update($categoryData);
            $request->session()->flash('success', 'Status changed successfully.');
            return redirect('/dpr/category_manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Dpr Category Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function getDprManageView(Request $request){
        try {
            return view('/dpr/manage-dpr');
        }catch(\Exception $e){
            $data = [
                'action' => 'DPR manage',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function getDprCreateView(Request $request){
        try {
            $sub_contractors = Subcontractor::where('is_active', true)->select('id','subcontractor_name','company_name')->get();
            return view('/dpr/create-dpr')->with(compact('clients','categories','sub_contractors'));
        }catch(\Exception $e){
            $data = [
                'action' => 'DPR manage',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function createDpr(Request $request){
        try{
            if(Session::has('global_project_site')){
                $dprDetailData = [
                    'project_site_id' => Session::get('global_project_site')
                ];
                foreach ($request->number_of_users as $relationId => $numberOfUser){
                    $dprDetailData['subcontractor_dpr_category_relation_id'] = $relationId;
                    $dprDetailData['number_of_users'] = $numberOfUser;
                    DprDetail::create($dprDetailData);
                }
                $request->session()->flash('success','Data saved successfully !');
            }else{
                $request->session()->flash('error','Project Site not selected. Please refresh page !');
            }
            return redirect('/dpr/create-dpr-view');
        }catch(\Exception $e){
            $data = [
                'action' => 'DPR create',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function dprListing(Request $request){
        try{
            if(Session::has('global_project_site')){
                $projectSiteId = Session::get('global_project_site');
                $dprDetails = DprDetail::where('project_site_id',$projectSiteId)->orderBy('created_at','desc')->get();
            }else{
                $dprDetails = DprDetail::orderBy('created_at','desc')->get();
            }
            $records = array();
            $records['data'] = array();
            $dprListingData = array();
            foreach($dprDetails as $dprDetail){
                $date = date('j-n-Y',strtotime($dprDetail['created_at']));
                if(!array_key_exists($date,$dprListingData)){
                    $dprListingData[$date] = [
                        'subcontractor_name' => $dprDetail->subcontractorDprCategoryRelation->subcontractor->company_name,
                        'subcontractor_id' => $dprDetail->subcontractorDprCategoryRelation->subcontractor->id,
                        'date' => date('j F Y', strtotime($dprDetail['created_at'])),
                        'param_date' => $dprDetail['created_at']
                    ];
                }
            }
            $dprListingData = array_values($dprListingData);
            $iTotalRecords = count($dprListingData);
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($dprDetails); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $pagination+1,
                    $dprListingData[$pagination]['subcontractor_name'],
                    $dprListingData[$pagination]['date'],
                    '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">'

                    .'<li>'

                    .'<form action="/dpr/dpr-edit-view" method="POST">
                            <input type="hidden" name="_token" value="'.$request->_token.'">
                            <input type="hidden" name="subcontractor_id" value="'.$dprDetails[$pagination]->subcontractorDprCategoryRelation->subcontractor_id.'">
                            <input type="hidden" name="date" value="'.$date.'">'
                            .'<a href="javascript:void(0);" onclick="submitEditForm(this)">'
                            .'    <i class="icon-tag"></i> Edit </a>'
                    .'</form>'
                    .'</li>'
                    .'</ul>'
                    .'</div>'
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'DPR listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $records = array();
            $status = 500;
        }
        return response()->json($records,$status);
    }
    public function getDprEditView(Request $request){
        try{
            $date = date('Y-m-j', strtotime($request->date));
            $projectSiteId = Session::get('global_project_site');
            $subcontractorDprDetailData = DprDetail::join('subcontractor_dpr_category_relations','subcontractor_dpr_category_relations.id','=','dpr_details.subcontractor_dpr_category_relation_id')
                                            ->join('dpr_main_categories','dpr_main_categories.id','=','subcontractor_dpr_category_relations.dpr_main_category_id')
                                            ->where('subcontractor_dpr_category_relations.subcontractor_id', $request->subcontractor_id)
                                            ->whereDate('dpr_details.created_at', $date)
                                            ->where('dpr_details.project_site_id', $projectSiteId)
                                            ->select('dpr_details.project_site_id','dpr_details.number_of_users as number_of_users','dpr_main_categories.name as category_name','dpr_details.id as dpr_detail_id')
                                            ->get();
            $subcontractorName = Subcontractor::where('id',$request->subcontractor_id)->pluck('company_name')->first();
            return view('dpr.edit-dpr')->with(compact('subcontractorDprDetailData','subcontractorName'));
        }catch(\Exception $e){
            $data = [
                'action' => 'DPR create',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function getCategoryEditView(Request $request,$id){
        try{
            $category = DprMainCategory::where('id',$id)->select('id','name')->get()->first()->toArray();
            return view('dpr.edit-category')->with(compact('category'));
        }catch(\Exception $e){
            $data = [
                'action' => 'DPR create',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function dprEdit(Request $request){
        try{
            foreach ($request->number_of_users as $dprDetailId => $numberOfUsers){
                DprDetail::where('id',$dprDetailId)->update(['number_of_users' => $numberOfUsers]);
            }
            $request->session()->flash('success', 'DPR edited successfully.');
            return redirect('dpr/manage_dpr');
        }catch(\Exception $e){
            $data = [
                'action' => 'DPR create',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function categoryEdit(Request $request){
        try{
            $dprData['name'] = $request->category;
            $query = DprMainCategory::where('id',$request->id)->update($dprData);
            $request->session()->flash('success', 'Category edited successfully.');
            return redirect('dpr/category-edit/'.$request->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'DPR edit',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getSubcontractorsCategories(Request $request){
        try{
            $subcontractorDprCategoryRelations = SubcontractorDPRCategoryRelation::where('subcontractor_id',$request->subcontractor_id)->get();
            return view('partials.dpr.category-table')->with(compact('subcontractorDprCategoryRelations'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Subcontractor\'s DPR categories',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json([], 500);
        }
    }
}
