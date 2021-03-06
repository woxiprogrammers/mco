<?php
namespace App\Http\Controllers\CustomTraits;
use App\Helper\UnitHelper;
use App\Http\Requests\UnitConversionRequest;
use App\Http\Requests\UnitRequest;
use App\Material;
use App\MaterialVersion;
use App\Unit;
use App\UnitConversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait UnitsTrait{

    public function getManageView(Request $request) {
        try{
            return view('admin.units.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get manage Unit view',
                'name' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request) {
        try{
            return view('admin.units.create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Create Unit view',
                'name' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request, $unit) {
        try{
            $unit = $unit->toArray();
            return view('admin.units.edit')->with(compact('unit'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Edit Unit view',
                'name' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateConversionView(Request $request) {
        try{
            $units = Unit::where('is_active',true)->select('id','name')->orderBy('name','asc')->get()->toArray();
            return view('admin.units.createConversion')->with(compact('units'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Unit conversions view',
                'name' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createUnit(UnitRequest $request){
        try{
            $data = $request->only('name');
            $data['name'] = ucwords(trim($data['name']));
            $data['is_active'] = false;
            $unit = Unit::create($data);
            $request->session()->flash('success','Unit Created Successfully');
            return redirect('/units/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Unit',
                'name' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editUnit(UnitRequest $request, $unit){
        try{
            $unit->update(['name' => ucwords(trim($request->name))]);
            $request->session()->flash('success','Unit Edited Successfully');
            return redirect('/units/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Unit',
                'name' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function unitsListing(Request $request){
        try{
            $user = Auth::user();
            if($request->has('search_name')){
                $unitData = Unit::where('name','ilike','%'.$request->search_name.'%')->orderBy('name','asc')->get()->toArray();
            }else{
                $unitData = Unit::orderBy('name','asc')->get()->toArray();
            }
            $iTotalRecords = count($unitData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($unitData) : $request->length;
            $sr_no = 0;
            for($iterator = 0 , $pagination = $request->start ; $iterator < $end && $pagination < count($unitData) ; $iterator++ , $pagination++){
                if($unitData[$pagination]['is_active'] == true){
                    $unit_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $unit_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-units')){
                    $actionButton =  '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/units/edit/'.$unitData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a href="/units/change-status/'.$unitData[$pagination]['id'].'">
                                    <i class="icon-tag"></i> '.$status.' </a>
                            </li>
                        </ul>
                    </div>';
                }else{
                    $actionButton =  '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/units/edit/'.$unitData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                        </ul>
                    </div>';
                }
                    $records['data'][$iterator] = [
                        ++$sr_no,
                        $unitData[$pagination]['name'],
                        $unit_status,
                        date('d M Y',strtotime($unitData[$pagination]['created_at'])),
                        $actionButton
                    ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Unit listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::citical(json_encode($data));
            abort(500);
        }

        return response()->json($records,200);
    }

    public function changeUnitStatus(Request $request, $unit){
        try{
            $newStatus = (boolean)(!$unit->is_active);
            $unit->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Unit Status changed successfully.');
            return redirect('/units/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change unit status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createConversion(UnitConversionRequest $request){
        try{
            $data['unit_1_id'] = $request->from_unit;
            $data['unit_2_id'] = $request->to_unit;
            $data['unit_1_value'] = $request->from_value;
            $data['unit_2_value'] = $request->to_value;
            $unitConversion = UnitConversion::create($data);
            $request->session()->flash('success','Conversion Saved successfully');
            return redirect('/units/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Conversion',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function unitConversionsListing(Request $request){
        try{
            if($request->has('search_unit_1_name') && $request->search_unit_1_name != '' && $request->has('search_unit_2_name') && $request->search_unit_2_name != ''){
                $unit1 = Unit::where('name','ilike','%'.$request->search_unit_1_name.'%')->pluck('id');
                $unit2 = Unit::where('name','ilike','%'.$request->search_unit_2_name.'%')->pluck('id');
                $conversions = UnitConversion::whereIn('unit_1_id',$unit1)->whereIn('unit_2_id',$unit2)->orderBy('id','asc')->get();
            }elseif($request->has('search_unit_1_name') && $request->search_unit_1_name != ''){
                $unit1 = Unit::where('name','ilike','%'.$request->search_unit_1_name.'%')->pluck('id');
                $conversions = UnitConversion::whereIn('unit_1_id',$unit1)->orderBy('id','asc')->get();
            }elseif($request->has('search_unit_2_name') && $request->search_unit_2_name != ''){
                $unit2 = Unit::where('name','ilike','%'.$request->search_unit_2_name.'%')->pluck('id');
                $conversions = UnitConversion::whereIn('unit_2_id',$unit2)->orderBy('id','asc')->get();
            }else{
                $conversions = UnitConversion::orderBy('id','asc')->get();
            }
            $iTotalRecords = count($conversions);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($conversions) : $request->length;
            $sr_no = 0;
            for($iterator = 0 , $pagination = $request->start ; $iterator < $end && $pagination < count($conversions) ; $iterator++ , $pagination++){
                $fromUnit = Unit::findOrFail($conversions[$pagination]['unit_1_id']);
                $toUnit = Unit::findOrFail($conversions[$pagination]['unit_2_id']);
                $records['data'][$iterator] = [
                    ++$sr_no,
                    $fromUnit['name'],
                    $conversions[$pagination]['unit_1_value'],
                    $toUnit['name'],
                    $conversions[$pagination]['unit_2_value'],
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/units/conversion/edit/'.$conversions[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
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
                'action' => 'Conversion Listing',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,200);
    }

    public function getEditConversionView(Request $request, $conversion){
        try{
            $units = Unit::where('is_active', true)->get()->toArray();
            $conversion = $conversion->toArray();
            return view('admin.units.edit-conversion')->with(compact('units','conversion'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Conversion',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editConversion(UnitConversionRequest $request, $conversion){
        try{
            $units = array();
            $units['unit_1_value'] = $request->from_value;
            $units['unit_1_id'] = $request->from_unit;
            $units['unit_2_id'] = $request->to_unit;
            $units['unit_2_value'] = $request->to_value;
            $conversion->update($units);
            $request->session()->flash('success','Conversion Edited Successfully');
            return redirect('/units/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Conversion',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function checkUnitName(Request $request){
        try{
            $unitName = $request->name;
            if($request->has('unit_id')){
                $nameCount = Unit::where('name','ilike',$unitName)->where('id','!=',$request->unit_id)->count();
            }else{
                $nameCount = Unit::where('name','ilike',$unitName)->count();
            }
            if($nameCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Check Unit name',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function convertUnits(Request $request){
        try{
            $data = $request->all();
            if($request->has('current_unit') && $request->has('rate')){
                $rate = $request->rate;
                $fromUnit = $data['current_unit'];
                $toUnit = $data['new_unit'];
            }else{
                $material = Material::where('id',$data['material_id'])->first()->toArray();
                $rate = $material['rate_per_unit'];
                $fromUnit = $material['unit_id'];
                $toUnit = $data['new_unit'];
            }
            $response = array();
            $conversion = UnitHelper::unitConversion($fromUnit,$toUnit,$rate);
            if(is_array($conversion)){
                $status = 203;
                $response = $conversion;
            }else{
                $status = 200;
                $response['rate'] = $conversion;
            }
        }catch(\Exception $e){
            $status = 500;
            $response = array();
            $data = [
                'action' => 'Convert Units',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
        return response()->json($response,$status);
    }

}
