<?php

namespace App\Http\Controllers\Checklist;

use App\ChecklistCategory;
use App\User;
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

    public function getCategoryManagementListing(Request $request){
        try {
            $userData = User::orderBy('id', 'asc')->get()->toArray();
            $iTotalRecords = count($userData);
            $records = array();
            for ($iterator = 0, $pagination = $request->start; $iterator < $request->length && $iterator < count($userData); $iterator++, $pagination++) {
                $records['data'][$iterator] = [
                    $userData[$pagination]['id'],
                    $userData[$pagination]['first_name'],
                    $userData[$pagination]['email'],
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/checklist/category-management/edit">
                                <i class="icon-docs"></i> Edit </a>
                            </li>
                        </ul>
                    </div>'
                ];
                $records["draw"] = intval($request->draw);
                $records["recordsTotal"] = $iTotalRecords;
                $records["recordsFiltered"] = $iTotalRecords;
            }
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
}