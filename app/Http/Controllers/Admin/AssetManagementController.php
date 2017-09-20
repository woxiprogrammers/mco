<?php

namespace App\Http\Controllers\Admin;

use App\AssetManagement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
    public function getEditView(Request $request){
        return view('admin.asset.edit');
    }

    public function createAsset(Request $request){
        try{
//            dd($request);
                $data = Array();
                $data['name'] = $request->name;
                $data['model_number'] = $request->model_number;
                $data['expiry_date'] = $request->expiry_date;
                $data['price'] = $request->price;
                $data['is_fuel_dependent'] = $request->is_fuel_dependent;
                $data['litre_per_unit'] = $request->litre_per_unit;
                $data['is_active'] = false;
                $asset = AssetManagement::create($data);
                $request->session()->flash('success', 'Vendor Created successfully.');
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
}
