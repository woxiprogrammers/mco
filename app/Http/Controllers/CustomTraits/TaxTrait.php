<?php

    namespace App\Http\Controllers\CustomTraits;

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

        public function createTax(Request $request){
            try{
                $data = $request->only('name','base_percentage');
                $data['is_active'] = false;
                $data['name'] = ucwords($data['name']);
                $tax = Tax::create($data);
                $request->session()->flash('success', 'Tax Created successfully.');
                return redirect('/tax/create');
            }catch(\Exception $e){
                $data = [
                    'action' => 'Create Category',
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
                    'action' => 'Get Edit View',
                    'params' => $request->all(),
                    'exception' => $e->getMessage()
                ];
                Log::critical(json_encode($data));
                abort(500);
            }
        }

        public function editTax(Request $request,$tax){
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







    }