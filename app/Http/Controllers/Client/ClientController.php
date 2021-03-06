<?php

namespace App\Http\Controllers\Client;

use App\Client;
use App\Http\Requests\ClientRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    public function getClientView(Request $request){
        try{
            return view('client.create');
        }catch(\Exception $e){
            $data = [
                'action' => 'View Client',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createClient(ClientRequest $request){
        try{
            $data = $request->all();
            $data['is_active'] = (boolean)false;
            $client = Client::create($data);
            $request->session()->flash('success', 'Client created successfully');
            return redirect('/client/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create new Client',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request,$client){
        try{
            $client = $client->toArray();
            return view('client.edit')->with(compact('client'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get client edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editClient(ClientRequest $request, $client){
        try{
            $client->update($request->all());
            $request->session()->flash('success', 'Client Edited successfully.');
            return redirect('/client/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Client',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageView(Request $request){
        try{
            return view('client.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get manage view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function clientListing(Request $request){
        try{
            $user = Auth::user();
            $search_name = null;
            if($request->has('search_name')) {
                $search_name = $request->search_name;
            }

            $clientData = Client::where('company','ilike','%'.$search_name.'%')
                          ->orderBy('company','asc')->get()->toArray();
            $iTotalRecords = count($clientData);
            $records = array();
            $records['data'] = array();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $pagination < count($clientData); $iterator++,$pagination++ ){
                if($clientData[$pagination]['is_active'] == true){
                    $client_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $client_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-manage-client')){
                    $button = '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/client/edit/'.$clientData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a href="/client/change-status/'.$clientData[$pagination]['id'].'">
                                    <i class="icon-tag"></i> '.$status.' </a>
                            </li>
                        </ul>
                    </div>';
                }else{
                    $button = '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/client/edit/'.$clientData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                        </ul>
                    </div>';
                }
                $records['data'][$iterator] = [
                    ucwords($clientData[$pagination]['company']),
                    $clientData[$pagination]['email'],
                    $clientData[$pagination]['mobile'],
                    $client_status,
                    date('d M Y',strtotime($clientData[$pagination]['created_at'])),
                    $button
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Client listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

        return response()->json($records,200);
    }

    public function changeClientStatus(Request $request, $client){
        try{
            $newStatus = (boolean)!$client['is_active'];
            $client->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Client Status changed successfully.');
            return redirect('/client/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change client status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
