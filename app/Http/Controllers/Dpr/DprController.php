<?php

namespace App\Http\Controllers\Dpr;

use App\Client;
use App\DprDetail;
use App\DprDetailImageRelation;
use App\DprImage;
use App\DprMainCategory;
use App\ProjectSite;
use App\Subcontractor;
use App\SubcontractorDPRCategoryRelation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
            $subCategories = DprMainCategory::select('id','name')
                ->orderBy('name','asc')->get()->toArray();
            $iTotalRecords = count($subCategories);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($subCategories) : $request->length;
            $categories = array();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $pagination < count($subCategories); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $subCategories[$pagination]['id'],
                    ucwords($subCategories[$pagination]['name']),
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
            $today = Carbon::now();
            $user = Auth::user();
            if(Session::has('global_project_site')){
                $projectSiteId = Session::get('global_project_site');
                $dprDetailData = [
                    'project_site_id' => $projectSiteId
                ];
                $dprDetailIds = array();
                foreach ($request->number_of_users as $relationId => $numberOfUser){
                    $dprDetail = DprDetail::where('project_site_id', $projectSiteId)
                                        ->where('subcontractor_dpr_category_relation_id', $relationId)
                                        ->whereDate('created_at',$today)
                                        ->first();
                    if($dprDetail == null){
                        $dprDetailData['subcontractor_dpr_category_relation_id'] = $relationId;
                        $dprDetailData['number_of_users'] = $numberOfUser;
                        $dprDetail = DprDetail::create($dprDetailData);
                    }else{
                        $dprDetail->update(['number_of_users' => $numberOfUser]);
                    }
                    $dprDetailIds[] = $dprDetail->id;
                }
                if($request->has('dpr_images')){
                    $userDirectory = sha1($user->id);
                    $projectSiteDirectory = sha1($projectSiteId);
                    $tempImageUploadPath = public_path().env('DPR_TEMP_UPLOAD').DIRECTORY_SEPARATOR.$userDirectory;
                    $imageUploadPath = public_path().env('DPR_UPLOAD').DIRECTORY_SEPARATOR.$projectSiteDirectory;
                    foreach($request->dpr_images as $image){
                        $imageName = basename($image);
                        $newTempImageUploadPath = $tempImageUploadPath.'/'.$imageName;
                        $dprImageData = [
                            'name'=> $imageName
                        ];
                        if (!file_exists($imageUploadPath)) {
                            File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
                        }
                        if(File::exists($newTempImageUploadPath)){
                            $imageUploadNewPath = $imageUploadPath.DIRECTORY_SEPARATOR.$imageName;
                            File::move($newTempImageUploadPath,$imageUploadNewPath);
                        }
                        $dprImage = DprImage::create($dprImageData);
                        foreach($dprDetailIds as $dprDetailId){
                            $dprDetailImageRelationData = [
                                'dpr_detail_id' => $dprDetailId,
                                'dpr_image_id' => $dprImage->id
                            ];
                            $dprDetailImageRelation = DprDetailImageRelation::create($dprDetailImageRelationData);
                        }
                    }
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
                $dprDetailIds = DprDetail::where('project_site_id',$projectSiteId)->orderBy('id','desc')->pluck('id');
            }else{
                $dprDetailIds = DprDetail::orderBy('id','desc')->pluck('id');
            }
            if($request->has('search_date') && $request->search_date != ''){
                $date = date('Y-m-d', strtotime($request->search_date));
                $dprDetailIds = DprDetail::whereDate('created_at', $date)->pluck('id');
            }
            $dprDetails = DprDetail::whereIn('id', $dprDetailIds)->orderBy('created_at','desc')->get();
            $records = array();
            $records['data'] = array();
            $dprListingData = array();
            $tempDprListingData = array();
            foreach($dprDetails as $dprDetail){
                $date = date('j-n-Y',strtotime($dprDetail['created_at']));
                if(!array_key_exists($date,$tempDprListingData)){
                    $tempDprListingData[$date][$dprDetail->subcontractorDprCategoryRelation->subcontractor->id] = [
                        'subcontractor_name' => $dprDetail->subcontractorDprCategoryRelation->subcontractor->company_name,
                        'subcontractor_id' => $dprDetail->subcontractorDprCategoryRelation->subcontractor->id,
                        'date' => date('j F Y', strtotime($dprDetail['created_at'])),
                        'param_date' => $dprDetail['created_at']
                    ];
                    $dprListingData[] = $tempDprListingData[$date][$dprDetail->subcontractorDprCategoryRelation->subcontractor->id];
                }elseif(!array_key_exists($dprDetail->subcontractorDprCategoryRelation->subcontractor->id, $tempDprListingData[$date])){
                    $tempDprListingData[$date][$dprDetail->subcontractorDprCategoryRelation->subcontractor->id] = [
                        'subcontractor_name' => $dprDetail->subcontractorDprCategoryRelation->subcontractor->company_name,
                        'subcontractor_id' => $dprDetail->subcontractorDprCategoryRelation->subcontractor->id,
                        'date' => date('j F Y', strtotime($dprDetail['created_at'])),
                        'param_date' => $dprDetail['created_at']
                    ];
                    $dprListingData[] = $tempDprListingData[$date][$dprDetail->subcontractorDprCategoryRelation->subcontractor->id];
                }
            }
            $iTotalRecords = count($dprListingData);
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($dprListingData); $iterator++,$pagination++ ){
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
                                <input type="hidden" name="subcontractor_id" value="'.$dprListingData[$pagination]['subcontractor_id'].'">
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
            $dprDetailId = $subcontractorDprDetailData[0]['dpr_detail_id'];
            $dprImageData = DprDetailImageRelation::join('dpr_images','dpr_images.id','=','dpr_detail_image_relations.dpr_image_id')
                ->where('dpr_detail_image_relations.dpr_detail_id', $dprDetailId)
                ->select('dpr_images.name as image_name','dpr_detail_image_relations.id as dpr_detail_image_relation_id','dpr_images.id as dpr_image_id')
                ->get();
            $subcontractorCategoryImages = array();
            if(count($dprImageData) > 0){
                $projectSiteDirectory = sha1($projectSiteId);
                $imageUploadPath = env('DPR_UPLOAD').DIRECTORY_SEPARATOR.$projectSiteDirectory;
                foreach($dprImageData as $dprImageDatum){
                    $imagePath = $imageUploadPath.DIRECTORY_SEPARATOR.$dprImageDatum['image_name'];
                    if (file_exists(public_path().$imagePath)) {
                        if(!array_key_exists($dprImageDatum['dpr_image_id'], $subcontractorCategoryImages))
                        $subcontractorCategoryImages[$dprImageDatum['dpr_image_id']] = [
                            'path' => $imagePath,
                            'random' => sha1($dprImageDatum['dpr_detail_image_relation_id']),
                            'dpr_image_id' => $dprImageDatum['dpr_image_id']
                        ];
                    }
                }
            }
            $subcontractorName = Subcontractor::where('id',$request->subcontractor_id)->pluck('company_name')->first();
            return view('dpr.edit-dpr')->with(compact('subcontractorDprDetailData','subcontractorName','subcontractorCategoryImages'));
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
            $user = Auth::user();
            foreach ($request->number_of_users as $dprDetailId => $numberOfUsers){
                DprDetail::where('id',$dprDetailId)->update(['number_of_users' => $numberOfUsers]);
            }
            if($request->has('dpr_images')){
                $projectSiteId = DprDetail::where('id', $dprDetailId)->pluck('project_site_id')->first();
                $userDirectory = sha1($user->id);
                $projectSiteDirectory = sha1($projectSiteId);
                $tempImageUploadPath = public_path().env('DPR_TEMP_UPLOAD').DIRECTORY_SEPARATOR.$userDirectory;
                $imageUploadPath = public_path().env('DPR_UPLOAD').DIRECTORY_SEPARATOR.$projectSiteDirectory;
                foreach($request->dpr_images as $image){
                    $imageName = basename($image);
                    $newTempImageUploadPath = $tempImageUploadPath.'/'.$imageName;
                    $dprImageData = [
                        'name'=> $imageName
                    ];
                    if (!file_exists($imageUploadPath)) {
                        File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
                    }
                    if(File::exists($newTempImageUploadPath)){
                        $imageUploadNewPath = $imageUploadPath.DIRECTORY_SEPARATOR.$imageName;
                        File::move($newTempImageUploadPath,$imageUploadNewPath);
                    }
                    $dprImage = DprImage::create($dprImageData);
                    foreach($request->number_of_users as $dprDetailId => $numberOfUsers){
                        $dprDetailImageRelationData = [
                            'dpr_detail_id' => $dprDetailId,
                            'dpr_image_id' => $dprImage->id
                        ];
                        $dprDetailImageRelation = DprDetailImageRelation::create($dprDetailImageRelationData);
                    }
                }
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
            $today = Carbon::now();
            if(Session::has('global_project_site')){
                $projectSiteId = Session::get('global_project_site');
                $subcontractorCategoryData = DprDetail::join('subcontractor_dpr_category_relations','subcontractor_dpr_category_relations.id','=','dpr_details.subcontractor_dpr_category_relation_id')
                    ->join('dpr_main_categories','dpr_main_categories.id','=','subcontractor_dpr_category_relations.dpr_main_category_id')
                    ->where('subcontractor_dpr_category_relations.subcontractor_id', $request->subcontractor_id)
                    ->whereDate('dpr_details.created_at', $today)
                    ->where('dpr_details.project_site_id', $projectSiteId)
                    ->select('dpr_details.id as dpr_detail_id','subcontractor_dpr_category_relations.id as subcontractor_dpr_category_relation_id','dpr_details.number_of_users as number_of_users','dpr_main_categories.name as dpr_main_category_name')
                    ->get()->toArray();
                $subcontractorCategoryImages = array();
                if(count($subcontractorCategoryData) <= 0){
                    $subcontractorCategoryData = SubcontractorDPRCategoryRelation::join('dpr_main_categories','dpr_main_categories.id','=','subcontractor_dpr_category_relations.dpr_main_category_id')
                        ->where('subcontractor_dpr_category_relations.subcontractor_id', $request->subcontractor_id)
                        ->select('subcontractor_dpr_category_relations.id as subcontractor_dpr_category_relation_id','dpr_main_categories.name as dpr_main_category_name')
                        ->get()->toArray();
                }else{
                    $dprDetailId = $subcontractorCategoryData[0]['dpr_detail_id'];
                    $dprImageData = DprDetailImageRelation::join('dpr_images','dpr_images.id','=','dpr_detail_image_relations.dpr_image_id')
                                        ->where('dpr_detail_image_relations.dpr_detail_id', $dprDetailId)
                                        ->select('dpr_images.name as image_name','dpr_detail_image_relations.id as dpr_detail_image_relation_id','dpr_images.id as dpr_image_id')
                                        ->get();
                    if(count($dprImageData) > 0){
                        $projectSiteDirectory = sha1($projectSiteId);
                        $imageUploadPath = env('DPR_UPLOAD').DIRECTORY_SEPARATOR.$projectSiteDirectory;
                        foreach($dprImageData as $dprImageDatum){
                            $imagePath = $imageUploadPath.DIRECTORY_SEPARATOR.$dprImageDatum['image_name'];
                            if (file_exists(public_path().$imagePath)) {
                                $subcontractorCategoryImages[] = [
                                    'path' => $imagePath,
                                    'random' => sha1($dprImageDatum['dpr_detail_image_relation_id']),
                                    'dpr_image_id' => $dprImageDatum['dpr_image_id']
                                ];
                            }
                        }
                    }
                }
            }else{
                $subcontractorCategoryData = array();
                $subcontractorCategoryImages = array();
            }
            return view('partials.dpr.category-table')->with(compact('subcontractorCategoryData','subcontractorCategoryImages'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Subcontractor\'s DPR categories',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json([], 500);
        }
    }
    public function uploadTempImages(Request $request){
        try{
            $user = Auth::user();
            $userDirectoryName = sha1($user->id);
            $tempUploadPath = public_path().env('DPR_TEMP_UPLOAD');
            $tempImageUploadPath = $tempUploadPath.DIRECTORY_SEPARATOR.$userDirectoryName;
            /* Create Upload Directory If Not Exists */
            if (!file_exists($tempImageUploadPath)) {
                File::makeDirectory($tempImageUploadPath, $mode = 0777, true, true);
            }
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
            $request->file('file')->move($tempImageUploadPath,$filename);
            $path = env('DPR_TEMP_UPLOAD').DIRECTORY_SEPARATOR.$userDirectoryName.DIRECTORY_SEPARATOR.$filename;
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

    public function displayTempImages(Request $request){
        try{
            $path = $request->path;
            $count = $request->count;
            $random = mt_rand(1,10000000000);
        }catch (\Exception $e){
            $path = null;
            $count = null;
        }
        return view('partials.dpr.image-listing')->with(compact('path','count','random'));
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

    public function removeImage(Request $request){
        try{
            $sellerUploadPath = public_path().$request->path;
            File::delete($sellerUploadPath);
            DprDetailImageRelation::where('dpr_image_id', $request->dpr_image_id)->delete();
            DprImage::where('id', $request->dpr_image_id)->delete();
            return response(200);
        }catch(\Exception $e){
            return response(500);
        }
    }
}
