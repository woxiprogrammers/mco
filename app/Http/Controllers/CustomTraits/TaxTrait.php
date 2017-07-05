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
                $data['name'] = ucwords(trim($data['name']));
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
                $tax->update(['name' => ucwords(trim($request->name)), 'base_percentage' => $request->base_percentage]);
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
                if($request->has('search_name')){
                    $taxData = Tax::where('name','ilike','%'.$request->search_name.'%')->orderBy('name','asc')->get()->toArray();
                }else{
                    $taxData = Tax::orderBy('name','asc')->get()->toArray();
                }
                $iTotalRecords = count($taxData);
                $records = array();
                $records['data'] = array();
                for($iterator = 0 , $pagination = $request->start ; $iterator < $request->length && $iterator < count($taxData) ; $iterator++ , $pagination++){
                    if($taxData[$pagination]['is_active'] == true){
                        $tax_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                        $status = 'Disable';
                    }else{
                        $tax_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                        $status = 'Enable';
                    }
                    $records['data'][$iterator] = [
                        $taxData[$pagination]['name'],
                        $taxData[$pagination]['base_percentage'],
                        $tax_status,
                        date('d M Y',strtotime($taxData[$pagination]['created_at'])),
                        '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="/tax/edit/'.$taxData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a href="/tax/change-status/'.$taxData[$pagination]['id'].'">
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

        public function checkTaxName(Request $request){
            try{
                $taxName = $request->name;
                if($request->has('tax_id')){
                    $nameCount = Tax::where('name','ilike',$taxName)->where('id','!=',$request->tax_id)->count();
                }else{
                    $nameCount = Tax::where('name','ilike',$taxName)->count();
                }
                if($nameCount > 0){
                    return 'false';
                }else{
                    return 'true';
                }
            }catch(\Exception $e){
                $data = [
                    'action' => 'Check Material name',
                    'param' => $request->all(),
                    'exception' => $e->getMessage()
                ];
                Log::critical(json_encode($data));
                abort(500);
            }

        }

    }