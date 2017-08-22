<?php
/**
 * Created by Ameya Joshi
 * Date: 23/5/17
 */

namespace App\Http\Controllers\CustomTraits;
use App\Category;
use App\Http\Requests\CategoryRequest;
use App\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Array_;

trait VendorTrait{

    public function getCreateView(Request $request){
        try{
            return view('admin.vendors.create');
        }catch(\Exception $e){
            $data = [
                'action' => "Get vendor create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request,$vendor){
        try{
            $vendor = $vendor->toArray();
            return view('admin.vendors.edit')->with(compact('vendor'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get vendor edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageView(Request $request){
        try{
            return view('admin.vendors.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Vendor manage view',
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createVendor(Request $request){
        try{
            $data = Array();
            $data['name'] = ucwords($request->name);
            $data['company'] = $request->company;
            $data['mobile'] = $request->mobile;
            $data['email'] = $request->email;
            $data['gstin'] = $request->gstin;
            $data['alternate_contact'] = $request->alternate_contact;
            $data['city'] = $request->city;
            //dd($data);
            $vendor = Vendor::create($data);
            $request->session()->flash('success', 'Vendor Created successfully.');
            return redirect('/vendors/create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Vendor',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editVendor(Request $request, $vendor){
        try{
            $vendor->update([
                'name'=>$request->name,
                'company' => $request->company,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'gstin' => $request->gstin,
                'alternate_contact' => $request->alternate_contact,
                'city' => $request->city,

            ]);
            $request->session()->flash('success', 'Vendor Edited successfully.');
            return redirect('/vendors/edit/'.$vendor->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Vendor',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function vendorListing(Request $request){
        try{
            $vendorsData = Vendor::orderBy('id','desc')->get()->toArray();
            $iTotalRecords = count($vendorsData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($vendorsData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($vendorsData); $iterator++,$pagination++ ){
                if($vendorsData[$pagination]['is_active'] == true){
                    $vendor_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $vendor_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                $records['data'][$iterator] = [
                    $vendorsData[$pagination]['name'],
                    $vendorsData[$pagination]['mobile'],
                    $vendorsData[$pagination]['city'],
                    $vendor_status,
                    date('d M Y',strtotime($vendorsData[$pagination]['created_at'])),
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/vendors/edit/'.$vendorsData[$pagination]['id'].'">
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
                'action' => 'Get Vendor Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records);
    }

    public function changeVendorStatus(Request $request, $vendor){
        try{
            $newStatus = (boolean)!$vendor->is_active;
            $vendor->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Vendor Status changed successfully.');
            return redirect('/vendors/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change vendor status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function checkVendorName(Request $request){
        try{
            $vendorName = $request->name;
            if($request->has('vendor_id')){
                $nameCount = Vendor::where('name','ilike',$vendorName)->where('id','!=',$request->vendor_id)->count();
            }else{
                $nameCount = Vendor::where('name','ilike',$vendorName)->count();
            }
            if($nameCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Check Vendor name',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

}