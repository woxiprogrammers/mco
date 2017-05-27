<?php
namespace App\Http\Controllers\CustomTraits;
use App\Unit;
use App\UnitConversion;
use Illuminate\Http\Request;
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
            $units = Unit::select('id','name')->orderBy('name','asc')->get()->toArray();
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

    public function createUnit(Request $request){
        try{
            $data = $request->only('name');
            $data['name'] = ucwords($data['name']);
            $data['is_active'] = false;
            $unit = Unit::create($data);
            $request->session()->flash('success','Unit Created Successfully');
            return redirect('/units/create');
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

    public function editUnit(Request $request, $unit){
        try{
            $unit->update(['name' => ucwords($request->name)]);
            $request->session()->flash('success','Unit Edited Successfully');
            return redirect('/units/edit/'.$unit->id);
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
            $unitData = Unit::orderBy('id','asc')->get()->toArray();
            $iTotalRecords = count($unitData);
            $records = array();
            $iterator = 0;
            foreach($unitData as $unit){
                if($unit['is_active'] == true){
                    $unit_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $unit_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                $records['data'][$iterator] = [
                    $unit['name'],
                    $unit_status,
                    date('d M Y',strtotime($unit['created_at'])),
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/units/edit/'.$unit['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a href="/units/change-status/'.$unit['id'].'">
                                    <i class="icon-tag"></i> '.$status.' </a>
                            </li>
                        </ul>
                    </div>'
                ];
                $iterator++;
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Create Category',
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
                'action' => 'Change category status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createConversion(Request $request){
        try{
            $data['unit_1_id'] = $request->from_unit;
            $data['unit_2_id'] = $request->to_unit;
            $data['unit_1_value'] = $request->from_value;
            $data['unit_2_value'] = $request->to_value;
            $unitConversion = UnitConversion::create($data);
            $request->session()->flash('success','Conversion Saved successfully');
            return redirect('/units/conversion/create');
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
            $conversions = UnitConversion::orderBy('id','asc')->get();
            $iTotalRecords = count($conversions);
            $records = array();
            $iterator = 0;
            foreach($conversions as $unit){
                $fromUnit = Unit::findOrFail($unit['unit_1_id']);
                $toUnit = Unit::findOrFail($unit['unit_2_id']);
                $records['data'][$iterator] = [
                    $fromUnit['name'],
                    $unit['unit_1_value'],
                    $toUnit['name'],
                    $unit['unit_2_value'],
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/units/conversion/edit/'.$fromUnit['id'].'-'.$toUnit['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                        </ul>
                    </div>'
                ];
                $iterator++;
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

    public function getEditConversionView(Request $request, $units){
        try{
            $unitIds = explode('-',$units);
            $conversion = UnitConversion::where('unit_1_id',$unitIds[0])->where('unit_2_id',$unitIds[1])->first();
            $units = array();
            $units[$unitIds[0]] = Unit::where('id',$unitIds[0])->pluck('name')->first();
            $units[$unitIds[1]] = Unit::where('id',$unitIds[1])->pluck('name')->first();
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

    public function editConversion(Request $request, $units){
        try{
            $unitString = $units;
            $unitIds = explode('-',$units);
            $units = array();
            $units['unit_1_value'] = $request->from_value;
            $units['unit_2_value'] = $request->to_value;
            $conversion = UnitConversion::where('unit_1_id',$unitIds[0])->where('unit_2_id',$unitIds[1])->update($units);
            $request->session()->flash('success','Conversion Edited Successfully');
            return redirect('/units/conversion/edit/'.$unitString);
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
}
