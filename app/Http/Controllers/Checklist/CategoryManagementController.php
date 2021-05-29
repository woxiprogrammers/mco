<?php

namespace App\Http\Controllers\Checklist;

use App\ChecklistCategory;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategoryManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth')->except('categoryManagementListing');
    }

    public function getManageView(Request $request){
        try{
            $categories = ChecklistCategory::whereNull('category_id')->select('id','name')->get();
            return view('checklist/category-management/manage')->with(compact('categories'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Checklist category manage page',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

    }

    public function getEditView(Request $request, $id){
        $cat_id = $id;
        $catdata = ChecklistCategory::where('id','=',$cat_id)->get(['name','id'])->toArray();
        return view('checklist/category-management/edit')->with(compact('catdata'));
    }

    public function editCategoryMaster(Request $request) {
        try {
            ChecklistCategory::where('id','=',$request->cat_id)
            ->update([
                'name' => $request->category_name,
                'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->category_name)))
            ]);
            $request->session()->flash('success', 'Category/Subcategory updated.');
            return redirect('/checklist/category-management/manage');
        } catch(\Exception $e) {
            $data = [
                'action' => 'Category/Subcategory edit',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCategoryManagementListing(Request $request,$slug){
        try {
            $user = Auth::user();
            $records = [
                'data' => array()
            ];
            switch($slug){
                case 'main-category':
                    if($request->search_category != null) {
                        $categoriesData = ChecklistCategory::whereNull('category_id')
                        ->where('name','ilike','%'.$request->search_category.'%')
                        ->orderBy('created_at','desc')->get();
                    } else {
                        $categoriesData = ChecklistCategory::whereNull('category_id')
                            ->orderBy('created_at','desc')->get();
                    }
                    for ($iterator = 0, $pagination = $request->start; $iterator < $request->length && $iterator < count($categoriesData); $iterator++, $pagination++) {
                        if($categoriesData[$pagination]['is_active'] == true){
                            $category_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                            $status = 'Disable';
                        }else{
                            $category_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                            $status = 'Enable';
                        }
                        if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-checklist-category') || $user->customHasPermission('edit-checklist-category')){
                            $actionButton = '<div class="btn-group">
                                                <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                                <ul class="dropdown-menu pull-left" role="menu">
                                                    <li>
                                                        <a href="/checklist/category-management/edit/'.($categoriesData[$pagination]['id']).'">
                                                        <i class="icon-docs"></i> Edit </a>
                                                    </li>
                                                    <li>
                                                        <a href="/checklist/category-management/change-status/'.($categoriesData[$pagination]['id']).'">
                                                        <i class="icon-tag"></i> '.$status.' </a>
                                                    </li>
                                                </ul>
                                            </div>';
                        }else{
                            $actionButton = '<div class="btn-group">
                                                <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                            </div>';
                        }
                        $records['data'][$iterator] = [
                            ($categoriesData[$pagination]['id']),
                            $categoriesData[$pagination]['name'],
                            $category_status,
                            date('d M Y',strtotime($categoriesData[$pagination]['created_at'])),
                            $actionButton
                        ];
                    }
                    break;

                case 'sub-category':
                    if($request->search_subcategory_sub != null) {
                        $categoriesData = ChecklistCategory::whereNotNull('category_id')
                        ->where('name','ilike','%'.$request->search_subcategory_sub.'%')
                        ->orderBy('created_at','desc')->get();
                    } else {
                        $categoriesData = ChecklistCategory::whereNotNull('category_id')
                            ->orderBy('created_at','desc')->get();
                    }
                    for ($iterator = 0, $pagination = $request->start; $iterator < $request->length && $iterator < count($categoriesData); $iterator++, $pagination++) {
                        if($categoriesData[$pagination]['is_active'] == true){
                            $category_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                            $status = 'Disable';
                        }else{
                            $category_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                            $status = 'Enable';
                        }
                        if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-checklist-category') || $user->customHasPermission('edit-checklist-category')){
                            $actionButton = '<div class="btn-group">
                                                <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                                <ul class="dropdown-menu pull-left" role="menu">
                                                    <li>
                                                    <a href="/checklist/category-management/edit/'.($categoriesData[$pagination]['id']).'">
                                                        <i class="icon-docs"></i> Edit </a>
                                                    </li>
                                                    <li>
                                                        <a href="/checklist/category-management/change-status/'.$categoriesData[$pagination]['id'].'">
                                                        <i class="icon-tag"></i> '.$status.' </a>
                                                    </li>
                                                </ul>
                                            </div>';
                        }else{
                            $actionButton = '<div class="btn-group">
                                                <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                            </div>';
                        }
                        $records['data'][$iterator] = [
                            ($categoriesData[$pagination]['id']),
                            ChecklistCategory::where('id',$categoriesData[$pagination]['category_id'])->pluck('name')->first(),
                            $categoriesData[$pagination]['name'],
                            $category_status,
                            date('d M Y',strtotime($categoriesData[$pagination]['created_at'])),
                            $actionButton
                        ];
                    }
                    break;

                default:
                    $records['data'] = array();
                    $categoriesData = array();
            }
            $records["draw"] = intval($request->draw);
            $records["recordsFiltered"] = $records["recordsTotal"] = count($categoriesData);
            $status = 200;
        }catch(\Exception $e){
            $status = 500;
            $records = array();
            $data = [
            'action' => 'Get Category Listing',
            'params' => $request->all(),
            'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,$status);
    }

    public function createCategories(Request $request,$slug){
        try{
            if($slug == 'main-category'){
                $categoryData = [
                    'name' => $request->name,
                    'is_active' =>false,
                ];
                ChecklistCategory::create($categoryData);
                $request->session()->flash('success',"Main category created successfully");
            }elseif($slug == 'sub-category'){
                $categoryData = [
                    'name' => $request->name,
                    'is_active' =>false,
                    'category_id' => $request->category_id
                ];
                ChecklistCategory::create($categoryData);
                $request->session()->flash('success',"Sub category created successfully");
            }else{
                $request->session()->flash('error',"Something went wrong.");
            }
            return redirect('/checklist/category-management/manage');
        }catch(\Exception $e){
            $data = [
                'action' => "Create Checklist Category",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function changeStatus(Request $request,$checklistCategory){
        try{
            $newStatus = (boolean)!$checklistCategory->is_active;
            $checklistCategory->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Category Status changed successfully.');
            return redirect('/checklist/category-management/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change category status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}