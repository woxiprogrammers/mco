<?php

    namespace App\Http\Controllers\CustomTraits;

    use App\Http\Requests\TaxRequest;
    use App\Tax;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Log;

    trait TaxTrait{

        public function getCreateView(Request $request){
            try{
                return view('admin.tax.create');
            }catch(\Exception $e){
                $data = [
                    'action' => "Get tax create view",
                    'params' => $request->all(),
                    'exception' => $e->getMessage()
                ];
                Log::critical(json_encode($data));
                abort(500);
            }
        }

        public function createTax(TaxRequest $request){
            try{
                $data = $request->only('name','base_percentage');
                $data['is_active'] = false;
                $data['name'] = ucwords($data['name']);
                $tax = Tax::create($data);
                $request->session()->flash('success', 'Tax Created successfully.');
                return redirect('/tax/create');
            }catch(\Exception $e){
                $data = [
                    'action' => 'Create Tax',
                    'params' => $request->all(),
                    'exception'=> $e->getMessage()
                ];
                Log::critical(json_encode($data));
                abort(500);
            }
        }

        public function getEditView(Request $request,$tax){
            try{
                    $tax = $tax->toArray();
                    return view('admin.tax.edit')->with(compact('tax'));
            }catch(\Exception $e){
                $data = [
                    'action' => 'Get Tax Edit View',
                    'params' => $request->all(),
                    'exception' => $e->getMessage()
                ];
                Log::critical(json_encode($data));
                abort(500);
            }
        }

        public function editTax(TaxRequest $request,$tax){
            try{
                $tax->update(['name' => ucwords($request->name), 'base_percentage' => $request->base_percentage]);
                $request->session()->flash('success', 'Tax Edited successfully.');
                return redirect('/tax/edit/'.$tax->id);
            }catch(\Exception $e){
                $data = [
                    'action' => 'Edit Tax',
                    'params' => $request->all(),
                    'exception' => $e->getMessage()
                ];
                Log::critical(json_encode($data));
                abort(500);
            }
        }

        public function getManageView(Request $request){
            try{
                return view('admin.tax.manage');
            }catch(\Exception $e){
                $data = [
                    'action' => 'Get Tax manage view',
                    'exception'=> $e->getMessage()
                ];
                Log::critical(json_encode($data));
                abort(500);
            }
        }

        public function taxListing(Request $request){
            try{
                $taxData = Tax::orderBy('id','asc')->get()->toArray();
                $iTotalRecords = count($taxData);
                $records = array();
                $iterator = 0;
                foreach($taxData as $tax){
                    if($tax['is_active'] == true){
                        $tax_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                        $status = 'Disable';
                    }else{
                        $tax_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                        $status = 'Enable';
                    }
                    $records['data'][$iterator] = [
                        $tax['name'],
                        $tax['base_percentage'],
                        $tax_status,
                        date('d M Y',strtotime($tax['created_at'])),
                        '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="/tax/edit/'.$tax['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a href="/tax/change-status/'.$tax['id'].'">
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
                    'action' => 'Get Tax Listing',
                    'params' => $request->all(),
                    'exception'=> $e->getMessage()
                ];
                Log::critical(json_encode($data));
                abort(500);
            }

            return response()->json($records,200);
        }

        public function changeTaxStatus(Request $request, $tax){
            try{
                $newStatus = (boolean)!$tax->is_active;
                $tax->update(['is_active' => $newStatus]);
                $request->session()->flash('success', 'Tax Status changed successfully.');
                return redirect('/tax/manage');
            }catch(\Exception $e){
                $data = [
                    'action' => 'Change tax status',
                    'param' => $request->all(),
                    'exception' => $e->getMessage()
                ];
                Log::critical(json_encode($data));
                abort(500);
            }
        }

    }