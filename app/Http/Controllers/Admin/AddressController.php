<?php
    /**
     * Created by PhpStorm.
     * User: manoj
     * Date: 11/6/18
     * Time: 2:28 PM
     */

namespace App\Http\Controllers\Admin;

use App\Address;
use App\City;
use App\Country;
use App\Http\Controllers\Controller;
use App\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class AddressController extends Controller{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getCreateView(Request $request){
        try{
            $countries = Country::select('id','name')->get()->toArray();
            return view('admin.address.create')->with(compact('countries'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Project create view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getStates(Request $request){
        $stateOptions = array();
        try{
            $status = 200;
            $states = State::where('country_id',$request['country_id'])->select('id','name')->get();
            foreach ($states as $state){
                $stateOptions[] = '<option value="'.$state['id'].'"> '.$state['name'].' </option>';
            }
        }catch(\Exception $e){
            $status = 500;
            $data = [
                'action' => 'Get States',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        $response = [
            'states' => $stateOptions
        ];
        return response()->json($response,$status);
    }

    public function getCities(Request $request){
        $cityOptions = array();
        try{
            $status = 200;
            $cities = City::where('state_id',$request['state_id'])->select('id','name')->get();
            foreach ($cities as $city){
                $cityOptions[] = '<option value="'.$city['id'].'"> '.$city['name'].' </option>';
            }
        }catch(\Exception $e){
            $status = 500;
            $data = [
                'action' => 'Get Cities',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        $response = [
            'cities' => $cityOptions
        ];
        return response()->json($response,$status);
    }

    public function createAddress(Request $request){
        try{
            $addressData = $request->only('address','city_id','pincode');
            $addressData['is_active'] = false;
            Address::create($addressData);
            $request->session()->flash('success','Address Created Successfully');
            return redirect('/address/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Address',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getAddressManageView(Request $request){
        try{
            return view('admin.address.manage');
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Address manage view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function addressListing(Request $request){
        try{
            $addressData = Address::orderBy('address','desc')->get();
            $iTotalRecords = count($addressData);
            $records = array();
            $records['data'] = array();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $pagination < count($addressData); $iterator++,$pagination++ ){
                if($addressData[$pagination]['is_active'] == true){
                    $address_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $address_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                $records['data'][$iterator] = [
                    ucwords($addressData[$pagination]['address']),
                    $addressData[$pagination]->cities->name,
                    $addressData[$pagination]->cities->state->name,
                    $addressData[$pagination]->cities->state->country->name,
                    $addressData[$pagination]['pincode'],
                    $address_status,
                    $button = '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/address/edit/'.$addressData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a href="/address/change-status/'.$addressData[$pagination]['id'].'">
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
                'action' => 'Address listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

        return response()->json($records,200);
    }

    public function getEditView(Request $request,$address){
        try{
            $countries = Country::select('id','name')->get()->toArray();
            $address['country_id'] = $address->cities->state->country->id;
            $address['state_id'] = $address->cities->state->id;
            return view('admin.address.edit')->with(compact('address','countries'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get address edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editAddress(Request $request, $address){
        try{
            $addressData = $request->only('address','city_id','pincode');
            $address->update($addressData);
            $request->session()->flash('success', 'Address Edited successfully.');
            return redirect('/address/manage');
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

    public function changeAddressStatus(Request $request, $address){
        try{
            $newStatus = (boolean)!$address['is_active'];
            $address->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Address Status changed successfully.');
            return redirect('/address/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change address status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}