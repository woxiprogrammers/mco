<?php

namespace App\Http\Controllers\Dpr;

use App\Client;
use App\DprDetail;
use App\DprMainCategory;
use App\ProjectSite;
use App\Subcontractor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

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
            $clients = Client::select('id','company')->get();
            $categories = DprMainCategory::where('status',(boolean)true)->select('id','name')->get();
            $sub_contractors = Subcontractor::select('id','subcontractor_name','company_name')->get();
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
            $q = DprDetail::whereDate('created_at', '=', date('Y-m-d'))->count();
            if($q >= 1){
                $request->session()->flash('error', 'DPR already created for today !');
            }else{
                $categoryData['project_site_id'] = $request->project_site_id;
                $categoryData['subcontractor_id'] = $request->subcontractor_id;
                $categoryData['dpr_main_category_id'] = $request->dpr_main_category_id;
                $categoryData['number_of_users'] = $request->number_of_users;
                DprDetail::create($categoryData);
                $request->session()->flash('success', 'DPR created successfully.');
            }
            return redirect('dpr/create-dpr-view');
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
            $subCategories = DprDetail::select('id','project_site_id','subcontractor_id','dpr_main_category_id','number_of_users','created_at')->get()->toArray();
            $iTotalRecords = count($subCategories);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($subCategories) : $request->length;
            $categories = array();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($subCategories); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    ProjectSite::where('id',$subCategories[$pagination]['project_site_id'])->pluck('name')->first(),
                    Subcontractor::where('id',$subCategories[$pagination]['subcontractor_id'])->pluck('company_name')->first(),
                    DprMainCategory::where('id',$subCategories[$pagination]['dpr_main_category_id'])->pluck('name')->first(),
                    $subCategories[$pagination]['number_of_users'],
                    date('d-m-Y',strtotime($subCategories[$pagination]['created_at'])),
                    '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">'

                    .'<li>'
                    .'<a href="/dpr/dpr-edit/'.$subCategories[$pagination]['id'].'">'
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
                'action' => 'DPR listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function getDprEditView(Request $request,$id){
        try{
            $subCategories = DprDetail::where('id',$id)->select('id','project_site_id','subcontractor_id','dpr_main_category_id','number_of_users','created_at')->get()->toArray();
            $dprData = array();
            foreach ($subCategories as $subCategory){
                $dprData['project_site_id'] = ProjectSite::where('id',$subCategory['project_site_id'])->pluck('name')->first();
                $dprData['subcontractor_id'] = Subcontractor::where('id',$subCategory['subcontractor_id'])->pluck('company_name')->first();
                $dprData['dpr_main_category_id'] = DprMainCategory::where('id',$subCategory['dpr_main_category_id'])->pluck('name')->first();
                $dprData['number_of_users'] = $subCategory['number_of_users'];
                $dprData['date'] = date('d-m-Y',strtotime($subCategory['created_at']));
                $dprData['id'] =$subCategory['id'];
            }
            return view('dpr.edit-dpr')->with(compact('dprData'));
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
            $dprData['number_of_users'] = $request->number_of_users;
            $query = DprDetail::where('id',$request->id)->update($dprData);
            $request->session()->flash('success', 'DPR edited successfully.');
            return redirect('dpr/dpr-edit/'.$request->id);
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

}
