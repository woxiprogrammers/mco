<?php

namespace App\Http\Controllers\Awareness;

use App\AwarenessMainCategory;
use App\AwarenessSubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class CategoryManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth')->except('categoryManagementListing');
    }
    public function getManageView(Request $request){
        return view('awareness.category-management.main-category.manage');
    }

    public function getSubManageView(Request $request){
        try{
            return view('awareness.category-management.sub-category.manage');
        }catch(\Exception $e){

        }
    }
    public function getCategoryCreateView(){
        try{
            return view('awareness.category-management.main-category.create');
        }catch(\Exception $e){

        }
    }
    public function getSubCategoryCreateView(Request $request){
        try{
            $main_categories = AwarenessMainCategory::select('id','name')->get();
            return view('awareness.category-management.sub-category.create')->with(compact('main_categories'));
        }catch(\Exception $e){

        }
    }
    public function createMainCategory(Request $request){
        try{
             $categoryData['name'] = $request->name;
             $query = AwarenessMainCategory::create($categoryData);
             $request->session()->flash('success', 'Category created successfully.');
            return view('awareness.category-management.main-category.create');
        }catch(\Exception $e){
            $data = [
                'action' => 'create category',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function createSubCategory(Request $request){
        try{
            $categoryData['awareness_main_category_id'] = $request->awareness_main_category_id;
            $categoryData['name'] = $request->name;
            $query = AwarenessSubCategory::create($categoryData);
            $request->session()->flash('success', 'Sub category created successfully.');
            return redirect('/awareness/category-management/sub-category-create');
        }catch(\Exception $e){
            $data = [
                'action' => 'create sub category',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function mainCategoryListing(Request $request){
        try{
            $mainCategories = AwarenessMainCategory::select('id','name')->get();
            $iTotalRecords = count($mainCategories);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($mainCategories) : $request->length;
            $categories = array();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($mainCategories); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $mainCategories[$pagination]['id'],
                    $mainCategories[$pagination]['name'],
                    '<div class="btn btn-xs blue"><a href="/awareness/category-management/main-category-edit/'.$mainCategories[$pagination]['id'].'"> Edit</a> </div>'

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
    public function subCategoryListing(Request $request){
        try{
            $subCategories = AwarenessSubCategory::get();
            $iTotalRecords = count($subCategories);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($subCategories) : $request->length;
            $categories = array();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($subCategories); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $subCategories[$pagination]['id'],
                    $subCategories[$pagination]['name'],
                    $subCategories[$pagination]->awarenessMainCategory->name,
                    '<div class="btn btn-xs blue"><a href="/awareness/category-management/sub-category-edit/'.$subCategories[$pagination]['id'].'"> Edit</a> </div>'
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
    public function getCategoryEditView(Request $request,$id){
         try{
              $categories = AwarenessMainCategory::where('id',$id)->select('id','name')->get();
              return view('awareness.category-management.main-category.edit')->with(compact('categories'));
         }catch (\Exception $e){
             $data = [
                 'action' => 'Listing',
                 'params' => $request->all(),
                 'exception' => $e->getMessage()
             ];
             Log::critical(json_encode($data));
             abort(500);
         }
    }
    public function mainCategoryEdit(Request $request){
        try{
            $categoryData = array();
            $categoryData['name'] = $request->name;
            $query = AwarenessMainCategory::where('id',$request->id)->update($categoryData);
            $categories = AwarenessMainCategory::where('id',$request->id)->select('id','name')->get();
            $request->session()->flash('success', 'Category edited successfully.');
            return view('awareness.category-management.main-category.edit')->with(compact('categories'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function subCategoryEditView(Request $request,$id){
        try{
            $subCategories = AwarenessSubCategory::where('id',$id)->select('id','name')->get();
            return view('awareness.category-management.sub-category.edit')->with(compact('subCategories'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function subCategoryEdit(Request $request){
        try{
            $categoryData = array();
            $categoryData['name'] = $request->name;
            $query = AwarenessSubCategory::where('id',$request->id)->update($categoryData);
            $subCategories = AwarenessSubCategory::where('id',$request->id)->select('id','name')->get();
            $request->session()->flash('success', 'Sub category edited successfully.');
            return view('awareness.category-management.sub-category.edit')->with(compact('subCategories'));
        }catch (\Exception $e){
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
