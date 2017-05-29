<?php
namespace App\Http\Controllers\CustomTraits;
use App\ProfitMargin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait ProfitMarginTrait{
    public function getManageView(Request $request) {
        try{
            return view('admin.profitMargin.manage');
        }catch(\Exception $e){
            $data = [
                'action' => "Get profit margin manage view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request) {
        try{
            return view('admin.profitMargin.create');
        }catch(\Exception $e){
            $data = [
                'action' => "Get profit margin create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request,$profit_margin){
        try{
            $profit_margin = $profit_margin->toArray();
            return view('admin.profitMargin.edit')->with(compact('profit_margin'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get profit margin edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createProfitMargin(Request $request){
        try{
            $data = $request->only('name','base_percentage');
            $data['is_active'] = false;
            $data['name'] = ucwords($data['name']);
            $profitMargin = ProfitMargin::create($data);
            $request->session()->flash('success', 'Profit Margin Created successfully.');
            return redirect('/profit-margin/create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Profit Margin',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editProfitMargin(Request $request,$profit_margin){
        try{
            $profit_margin->update(['name' => ucwords($request->name), 'base_percentage' => $request->base_percentage]);
            $request->session()->flash('success', 'Profit Margin Edited successfully.');
            return redirect('/profit-margin/edit/'.$profit_margin->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit existing Profit Margin',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function profitMarginListing(Request $request){
        try{
            $profitMarginData = ProfitMargin::orderBy('id','asc')->get()->toArray();
            $iTotalRecords = count($profitMarginData);
            $records = array();
            $iterator = 0;
            foreach($profitMarginData as $profitMargin){
                if($profitMargin['is_active'] == true){
                    $profitMargin_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $profitMargin_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                $records['data'][$iterator] = [
                    $profitMargin['name'],
                    $profitMargin['base_percentage'],
                    $profitMargin_status,
                    date('d M Y',strtotime($profitMargin['created_at'])),
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/profit-margin/edit/'.$profitMargin['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a href="/profit-margin/change-status/'.$profitMargin['id'].'">
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
                'action' => 'Get Profit Margin Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

        return response()->json($records,200);
    }

    public function changeProfitMarginStatus(Request $request, $profitMargin){
        try{
            $newStatus = (boolean)!$profitMargin->is_active;
            $profitMargin->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Profit Margin Status changed successfully.');
            return redirect('/profit-margin/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change profit margin status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

}