<?php

namespace App\Http\Controllers\User;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }
  public function getManageView(Request $request){
        return view('purchase/materialRequest/manage');
  }
  public function getCreateView(Request $request){
        return view('purchase/materialRequest/create');
  }
  public function getMaterialRequestListing(Request $request){
      try{
          $userData = User::orderBy('id','asc')->get()->toArray();
          $iTotalRecords = count($userData);
          $records = array();
          $iterator = 0;
          for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($userData); $iterator++,$pagination++ ){
              if($userData[$pagination]['is_active'] == true){
                  $user_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                  $status = 'Disable';
              }else{
                  $user_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                  $status = 'Enable';
              }
              $records['data'][$iterator] = [
                  '<input type="checkbox">',
                  '1',
                  $userData[$pagination]['first_name'].' '.$userData[$pagination]['last_name'] ,
                  $userData[$pagination]['email'],
                  $userData[$pagination]['mobile'],
                  date('d M Y',strtotime($userData[$pagination]['created_at'])),
                  $user_status,
                  '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                        <li>
                                <a href="/purchase/material-request/edit">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a data-toggle="modal" data-target="#remarkModal">
                                    <i class="icon-tag"></i> Approve / Disapprove </a>
                            </li>
                        </ul>
                    </div>'
              ];
          }
          $records["draw"] = intval($request->draw);
          $records["recordsTotal"] = $iTotalRecords;
          $records["recordsFiltered"] = $iTotalRecords;
      }catch(\Exception $e){
          $records = array();
          $data = [
              'action' => 'User listing',
              'params' => $request->all(),
              'exception'=> $e->getMessage()
          ];
          Log::critical(json_encode($data));
          abort(500);
      }

      return response()->json($records,200);

  }
    public function editMaterialRequest(Request $request){
        return view('purchase/materialRequest/edit');
    }
}
