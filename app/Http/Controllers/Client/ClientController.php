<?php

namespace App\Http\Controllers\Client;

use App\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

    public function createClient(Request $request){
        try{
            $data = $request->all();
            $data['is_active'] = (boolean)false;
            $client = Client::create($data);
            $request->session()->flash('success', 'Client created successfully');
            return redirect('/client/create');
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
            $clientData = Client::orderBy('id','asc')->get()->toArray();
            $iTotalRecords = count($clientData);
            $records = array();
            $iterator = 0;
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($clientData); $iterator++,$pagination++ ){
                if($clientData[$pagination]['is_active'] == true){
                    $client_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $client_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                $records['data'][$iterator] = [
                    $clientData[$pagination]['company'],
                    $clientData[$pagination]['email'],
                    $clientData[$pagination]['mobile'],
                    $client_status,
                    date('d M Y',strtotime($clientData[$pagination]['created_at'])),
                    '<div class="btn-group">
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
                    </div>'
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
