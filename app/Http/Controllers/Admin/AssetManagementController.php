<?php

namespace App\Http\Controllers\Admin;

use App\AssetManagement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;



class AssetManagementController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        return view('admin.asset.manage');
    }
    public function getCreateView(Request $request){
        return view('admin.asset.create');
    }
    public function getEditView(Request $request,$asset){
        try{
            $asset = $asset->toArray();
            return view('admin.asset.edit')->with(compact('asset'));
        }catch (Exception $e){
            $data = [
                'action' => "Get asset edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createAsset(Request $request){
        try{
                $data = Array();
                $data['name'] = $request->name;
                $data['model_number'] = $request->model_number;
                $data['expiry_date'] = $request->expiry_date;
                $data['price'] = $request->price;
                $data['is_fuel_dependent'] = $request->is_fuel_dependent;
                $data['litre_per_unit'] = $request->litre_per_unit;
                $data['is_active'] = false;
                $asset = AssetManagement::create($data);
                $request->session()->flash('success', 'Asset Created successfully.');
                return redirect('/asset/create');

        }catch (\Exception $e){
            $data = [
                'action' => 'Create Asset',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }


    }

    public function editAsset(Request $request,$asset){
        try{
            $data = $request->all();
            $assetData['name'] = ucwords(trim($data['name']));
            $assetData['model_number'] = $data['model_number'];
            $assetData['expiry_date'] = $data['expiry_date'];
            $assetData['price'] = $data['price'];
            $assetData['is_fuel_dependent'] = $data['is_fuel_dependent'];
            $assetData['litre_per_unit'] = $data['litre_per_unit'];
            $asset->update($assetData);
            $request->session()->flash('success', 'Asset Edited successfully.');
            return redirect('/asset/edit/'.$asset->id);
        }catch (Exception $e){
            $data = [
                'action' => 'Edit Asset',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

    }

    public function assetListing(Request $request){
        try{
            if($request->has('search_name')){
                $assetData = AssetManagement::where('id','ilike','%'.$request->search_name.'%')->orderBy('name','asc')->get()->toArray();
            }else{
                $assetData = AssetManagement::orderBy('id','asc')->get()->toArray();
            }
            $iTotalRecords = count($assetData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($assetData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($assetData); $iterator++,$pagination++ ){
                if($assetData[$pagination]['is_active'] == true){
                    $asset_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $asset_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                $records['data'][$iterator] = [
                    $assetData[$pagination]['id'],
                    $assetData[$pagination]['model_number'],
                    $asset_status,

                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/asset/edit/'.$assetData[$pagination]['id'].'">
                                <i class="icon-docs"></i> Edit </a>
                        </li>
                        <li>
                            <a href="/asset/change-status/'.$assetData[$pagination]['id'].'">
                                <i class="icon-tag"></i> '.$status.' </a>
                        </li>
                    </ul>
                </div>'
                ];
            }

            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch (Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Asset Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records);
    }

    public function changeAssetStatus(Request $request, $asset){
        try{
            $newStatus = (boolean)!$asset->is_active;
            $asset->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Asset Status changed successfully.');
            return redirect('/asset/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change asset status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function checkAssetName(Request $request){
        try{
            $assetName = $request->name;
            if($request->has('id')){
                $nameCount = AssetManagement::where('name','ilike',$assetName)->where('id','!=',$request->id)->count();
            }else{
                $nameCount = AssetManagement::where('name','ilike',$assetName)->count();
            }
            if($nameCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Check Asset Name',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

}
