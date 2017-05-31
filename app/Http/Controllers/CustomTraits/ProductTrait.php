<?php
namespace App\Http\Controllers\CustomTraits;
use App\Category;
use App\Product;
use App\ProductVersion;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait ProductTrait{
    public function getManageView(Request $request) {
        try{
            return view('admin.product.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Manage View',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function getCreateView() {
        try{
            return view('admin.product.create');
        }catch(\Exception $e){

        }
    }
    public function getEditView() {
        try{
            return view('admin.product.edit');
        }catch(\Exception $e){

        }
    }

    public function productListing(Request $request){
        try{
            $productData = Product::orderBy('id','asc')->get()->toArray();
            $iTotalRecords = count($productData);
            $records = array();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($productData); $iterator++,$pagination++ ){
                $productVersion = ProductVersion::where('product_id',$productData[$pagination]['id'])->select('rate_per_unit')->orderBy('created_at','desc')->first();
                if($productData[$pagination]['is_active'] == true){
                    $product_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $product_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                $records['data'][$iterator] = [
                    $productData[$pagination]['name'],
                    Category::where('id',$productData[$pagination]['category_id'])->pluck('name')->first(),
                    $productVersion->rate_per_unit,
                    Unit::where('id',$productData[$pagination]['unit_id'])->pluck('name')->first(),
                    $product_status,
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/product/edit/'.$productData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a href="/product/change-status/'.$productData[$pagination]['id'].'">
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
                'action' => 'Product Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

        return response()->json($records,200);
    }

    public function changeProductStatus(Request $request, $product){
        try{
            $newStatus = (boolean)!$product->is_active;
            $product->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Product Status changed successfully.');
            return redirect('/product/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change product status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}