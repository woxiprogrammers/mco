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

    public function getEditView(Request $request){
        return view('checklist/category-management/edit');
    }

    public function getCategoryManagementListing(Request $request,$slug){
        try {
            $user = Auth::user();
            $records = [
                'data' => array()
            ];
            switch($slug){
                case 'main-category':
                    $categoriesData = ChecklistCategory::whereNull('category_id')->orderBy('created_at','desc')->get();
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
                                                        <a href="javascript:void(0);">
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
                            ($categoriesData[$pagination]['id']+1),
                            $categoriesData[$pagination]['name'],
                            $category_status,
                            date('d M Y',strtotime($categoriesData[$pagination]['created_at'])),
                            $actionButton
                        ];
                    }
                    break;

                case 'sub-category':
                    $categoriesData = ChecklistCategory::whereNotNull('category_id')->orderBy('created_at','desc')->get();
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
                                                        <a href="javascript:void(0);">
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
                            ($categoriesData[$pagination]['id']+1),
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