<?php

namespace App\Http\Controllers\Checklist;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth')->except('categoryManagementListing');
    }
    public function getManageView(Request $request){
        return view('checklist/categoryManagement/manage');
    }
    public function getEditView(Request $request){
        return view('checklist/categoryManagement/edit');
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

        }catch(\Exception $e){
                $records = array();
                $data = [
                'action' => 'Get Category Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
                ];
                Log::critical(json_encode($data));
                abort(500);
            }
        return response()->json($records);
    }
}