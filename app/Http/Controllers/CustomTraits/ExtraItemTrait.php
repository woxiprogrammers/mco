<?php

namespace App\Http\Controllers\CustomTraits;
use App\ExtraItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait ExtraItemTrait{

    public function getCreateView(Request $request){
        try{
            return view('admin.extraItem.create');
        }catch(\Exception $e){
            $data = [
                'action' => "Get extra item create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request,$extraItem){
        try{
            $extraItem = $extraItem->toArray();
            return view('admin.extraItem.edit')->with(compact('extraItem'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get extra item edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageView(Request $request){
        try{
            return view('admin.extraItem.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Extra Item manage view',
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createExtraItem(Request $request){
        try{
            $data['name'] = ucwords(trim($request->name));
            $data['rate'] = $request->rate;
            $data['is_active'] = false;
            $extraItem = ExtraItem::create($data);
            $request->session()->flash('success', 'Extra Item Created successfully.');
            return redirect('/extra-item/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Extra Item',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editExtraItem(Request $request, $extraItem){
        try{
            $extraItem->update(['name' => ucwords(trim($request->name)) , 'rate' => $request->rate]);
            $request->session()->flash('success', 'Extra Item Edited successfully.');
            return redirect('/extra-item/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Extra Item',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function extraItemListing(Request $request){
        try{
            $user = Auth::user();
            if($request->has('search_name')){
                $extraItemData = ExtraItem::where('name','ilike','%'.$request->search_name.'%')->orderBy('name','asc')->get()->toArray();
            }else{
                $extraItemData = ExtraItem::orderBy('name','asc')->get()->toArray();
            }
            $iTotalRecords = count($extraItemData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($extraItemData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($extraItemData); $iterator++,$pagination++ ){
                if($extraItemData[$pagination]['is_active'] == true){
                    $extraItem_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $extraItem_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-manage-extra-items')){
                    $actionButton = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="/extra-item/edit/'.$extraItemData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                                </li>
                                <li>
                                    <a href="/extra-item/change-status/'.$extraItemData[$pagination]['id'].'">
                                        <i class="icon-tag"></i> '.$status.' </a>
                                </li>
                            </ul>
                        </div>';
                }else{
                    $actionButton = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="/extra-item/edit/'.$extraItemData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                                </li>
                            </ul>
                        </div>';
                }
                $records['data'][$iterator] = [
                        $extraItemData[$pagination]['name'],
                        $extraItemData[$pagination]['rate'],
                        $extraItem_status,
                        date('d M Y',strtotime($extraItemData[$pagination]['created_at'])),
                        $actionButton
                    ];
            }

            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Extra Item Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records);
    }

    public function changeExtraItemStatus(Request $request, $extraItem){
        try{
            $newStatus = (boolean)!$extraItem->is_active;
            $extraItem->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Extra Item Status changed successfully.');
            return redirect('/extra-item/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change extra item status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function checkExtraItemName(Request $request){
        try{
            $extraItemName = $request->name;
            if($request->has('extra_item_id')){
                $nameCount = ExtraItem::where('name','ilike',$extraItemName)->where('id','!=',$request->extra_item_id)->count();
            }else{
                $nameCount = ExtraItem::where('name','ilike',$extraItemName)->count();
            }
            if($nameCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Check Extra Item name',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

}