<?php

namespace App\Http\Controllers\Drawing;

use App\DrawingCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategoryManagementController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        return view('drawing/category-management/manage');
    }
    public function getSubCategoryManageView(Request $request){
        return view('drawing/category-management/manage-sub-category');
    }
    public function getCreateMainView(Request $request){
        return view('drawing/category-management/create-main');
    }
    public function getCreateSubView(Request $request){
        try{
              $categories = DrawingCategory::whereNull('drawing_category_id')->where('is_active',TRUE)->select('name','id')->get();
        }catch(\Exception $e){
            $data = [
                'action' => 'create Sub view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return view('drawing/category-management/create-sub')->with(compact('categories'));
    }

    public function getMainEditView(Request $request,$id)
    {   try{
                $main_category = DrawingCategory::where('id',$id)->select('id','name')->first()->toArray();
                return view('drawing/category-management/edit-main')->with(compact('main_category'));
           }catch(\Exception $e){
                $data = [
                    'action' => 'Main category edit view',
                    'params' => $request->all(),
                    'exception' => $e->getMessage()
                ];
                Log::critical(json_encode($data));
                abort(500);
    }

    }

    public function getSubEditView(Request $request,$id)
    {
        try{
            $categories = DrawingCategory::whereNull('drawing_category_id')->where('is_active',TRUE)->select('name','id')->get();
            $drawing_category_id = DrawingCategory::where('id',$id)->pluck('drawing_category_id')->first();
            $name = DrawingCategory::where('id',$id)->select('id','name')->first()->toArray();
            return view('drawing/category-management/edit-sub')->with(compact('name','drawing_category_id','categories'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit sub category',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

    }

    public function getCreateMainCategory(Request $request){
         try{
               $category['name'] = $request->main_category;
               $query = DrawingCategory::create($category);
               $request->session()->flash('success', 'Category created successfully.');
               return view('/drawing/category-management/create-main');
         }catch(\Exception $e){
             $data = [
                 'action' => 'create',
                 'params' => $request->all(),
                 'exception' => $e->getMessage()
             ];
             Log::critical(json_encode($data));
             abort(500);
         }
    }

    public function mainCategoryEdit(Request $request){
        try{
            $category['name'] = $request->main_category;
            $query = DrawingCategory::where('id',$request->id)->update($category);
            $request->session()->flash('success', 'Category edited successfully.');
            return view('/drawing/category-management/create-main');
        }catch(\Exception $e){
            $data = [
                'action' => 'create',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createSubCategory(Request $request){
        try{
            $category['name'] = $request->sub_category;
            $category['drawing_category_id'] = $request->main_category_id;
            $query = DrawingCategory::create($category);
            $request->session()->flash('success', 'Sub category created successfully.');
            return redirect('/drawing/category-management/create-sub');
        }catch(\Exception $e){
            $data = [
                'action' => 'create',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function mainCategoryListing(Request $request){
        try{
            $user = Auth::user();
            $subCategories = DrawingCategory::whereNull('drawing_category_id')->get()->toArray();
            $iTotalRecords = count($subCategories);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($subCategories) : $request->length;
            $categories = array();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($subCategories); $iterator++,$pagination++ ){
                if($subCategories[$pagination]['is_active'] == true){
                    $status = '<span class="label label-sm label-success"> Enabled </span>';;
                    $action = '<li>
                                    <a href="/drawing/category-management/change-status/'.$subCategories[$pagination]['id'].'/false">'
                        .'<i class="icon-docs"></i> Disable 
                                    </a>'
                        .'</li>';
                }else{
                    $status = '<span class="label label-sm label-danger"> Disabled </span>';;
                    $action = '<li>
                                    <a href="/drawing/category-management/change-status/'.$subCategories[$pagination]['id'].'/true">'
                                        .'<i class="icon-docs"></i> Enable 
                                    </a>'
                                .'</li>';
                }
                if($user->customHasPermission('view-drawing-category')){
                    $actionButton = '<div class="btn-group">
                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                            Actions
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-left" role="menu">
                                            <li>'
                                                .'<a href="/drawing/category-management/edit/'.$subCategories[$pagination]['id'].'">'
                                                    .'<i class="icon-tag"></i> Edit </a>'
                                            .'</li>'
                                        .'</ul>'
                                    .'</div>';
                }else{
                    $actionButton = '<div class="btn-group">
                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                            Actions
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-left" role="menu">'
                                            .$action
                                            .'<li>'
                                                .'<a href="/drawing/category-management/edit/'.$subCategories[$pagination]['id'].'">'
                                                    .'<i class="icon-tag"></i> Edit </a>'
                                            .'</li>'
                                        .'</ul>'
                                    .'</div>';
                }
                $records['data'][$iterator] = [
                    $subCategories[$pagination]['id'],
                    $subCategories[$pagination]['name'],
                    $status,
                    $actionButton
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
            if($status == 'false'){
                $status = false;
            }else{
                $status = true;
            }
            $query = DrawingCategory::where('id',$id)->update(['is_active' => $status]);
            $request->session()->flash('success', 'Status changed successfully.');
            return redirect('/drawing/category-management/manage');
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

    public function editSubCategory(Request $request){
        try {
            $data['drawing_category_id'] =  $request->main_category_id;
            $data['name'] =  $request->sub_category;
            DrawingCategory::where('id',$request->id)->update($data);
            $request->session()->flash('success', 'Edited successfully.');
            return redirect('/drawing/category-management/edit-sub/'.$request->id);
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

    public function SubCategoryListing(Request $request){
        try{
            $user = Auth::user();
            $subCategories = DrawingCategory::whereNotNull('drawing_category_id')->get()->toArray();
            $iTotalRecords = count($subCategories);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($subCategories) : $request->length;
            $categories = array();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($subCategories); $iterator++,$pagination++ ){
                if($subCategories[$pagination]['is_active'] == true){
                    $status = '<span class="label label-sm label-success"> Enabled </span>';;
                    $action = '<li>
                                    <a href="/drawing/category-management/change-status/'.$subCategories[$pagination]['id'].'/false">'
                        .'<i class="icon-docs"></i> Disable 
                                    </a>'
                        .'</li>';
                }else{
                    $status = '<span class="label label-sm label-danger"> Disabled </span>';;
                    $action = '<li>
                                    <a href="/drawing/category-management/change-status/'.$subCategories[$pagination]['id'].'/true">'
                        .'<i class="icon-docs"></i> Enable 
                                    </a>'
                        .'</li>';
                }
                if($user->customHasPermission('view-drawing-category')){
                    $actionButton = '<div class="btn-group">
                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                            Actions
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-left" role="menu">
                                            <li>'
                                                .'<a href="/drawing/category-management/edit-sub/'.$subCategories[$pagination]['id'].'">'
                                                    .'<i class="icon-tag"></i> Edit </a>'
                                            .'</li>'
                                        .'</ul>'
                                    .'</div>';
                }else{
                    $actionButton = '<div class="btn-group">
                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                            Actions
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-left" role="menu">'
                                            .$action
                                            .'<li>'
                                                .'<a href="/drawing/category-management/edit-sub/'.$subCategories[$pagination]['id'].'">'
                                                    .'<i class="icon-tag"></i> Edit </a>'
                                            .'</li>'
                                        .'</ul>'
                                    .'</div>';
                }
                $records['data'][$iterator] = [
                    $subCategories[$pagination]['id'],
                    DrawingCategory::where('id',$subCategories[$pagination]['drawing_category_id'])->pluck('name')->first(),
                    $subCategories[$pagination]['name'],
                    $status,
                    $actionButton
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
}
