<?php

namespace App\Http\Controllers\Drawing;

use App\DrawingCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class CategoryManagementController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        return view('drawing/category-management/manage');
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
    public function getMainEditView(Request $request)
    {
        return view('drawing/category-management/edit-main');
    }
    public function getSubEditView(Request $request)
    {
        return view('drawing/category-management/edit-sub');
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
    public function createSubCategory(Request $request){
        try{
            $category['name'] = $request->sub_category;
            $category['drawing_category_id'] = $request->main_category_id;
            $query = DrawingCategory::create($category);
            $request->session()->flash('success', 'Sub category created successfully.');
            return redirect('/drawing/category-management/create-main');
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
            $subCategories = DrawingCategory::get()->toArray();
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
                                    <a href="/drawing/category-management/change-status/'.$subCategories[$pagination]['id'].'/TRUE">'
                                    .'<i class="icon-docs"></i> Enable </a>'
                                .'</li>'
                                .'<li>'
                                    .'<a href="/drawing/category-management/change-status/'.$subCategories[$pagination]['id'].'/FALSE">'
                                    .'    <i class="icon-tag"></i> Disable </a>'
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
               $categoryData['is_active'] = (bool)$status;
               $query = DrawingCategory::where('id',$id)->update($categoryData);
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
    public function SubCategoryListing(Request $request){
        try{
            $subCategories = DrawingCategory::whereNull('drawing_category_id')->get()->toArray();
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
                                    <a href="/drawing/category-management/change-status/'.$subCategories[$pagination]['id'].'/TRUE">'
                    .'<i class="icon-docs"></i> Enable </a>'
                    .'</li>'
                    .'<li>'
                    .'<a href="/drawing/category-management/change-status/'.$subCategories[$pagination]['id'].'/FALSE">'
                    .'    <i class="icon-tag"></i> Disable </a>'
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
}
