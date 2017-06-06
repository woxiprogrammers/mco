<?php
/**
 * Created by Ameya Joshi.
 * Date: 5/6/17
 * Time: 4:19 PM
 */

namespace App\Http\Controllers\CustomTraits;

use App\Category;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait QuotationTrait{

    public function getCreateView(Request $request){
        try{
            $categories = Category::where('is_active', true)->select('id','name')->get()->toArray();
            return view('admin.quotation.create')->with(compact('categories'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Create Quotation View',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageView(Request $request){
        try{
            return view('admin.quotation.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Create Quotation View',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getProducts(Request $request){
        try{
            $status = 200;
            $products = Product::where('category_id', $request->category_id)->where('is_active', true)->select('id','name')->orderBy('name','asc')->get();
            $response = array();
            if($products == null){
                $response[] = '<option value="">No Products Found</option>';
            }else{
                foreach($products as $product){
                    $response[] = '<option value="'.$product->id.'">'.$product->name.'</option>';
                }
            }
        }catch(\Exception $e){
            $status = 500;
            $response = array();
            $data = [
                'action' => 'Get Create Quotation View',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($response,$status);
    }

    public function getProductDetail(Request $request){
        try{
            $status = 200;
            $productId = $request->product_id;
            $product = Product::join('product_versions','products.id','=','product_versions.product_id')
                       ->join('units','units.id','=','products.unit_id')
                       ->where('products.id',$productId)
                       ->orderBy('product_versions.created_at','desc')
                       ->select('products.id as id','products.name as name','products.description as description','product_versions.id as product_version_id','products.unit_id as unit_id','product_versions.rate_per_unit as rate_per_unit','units.name as unit')
                       ->first();
            $response = $product;
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Product Details Quotation',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = array();
        }
        return response()->json($response,$status);
    }

    public function addProductRow(Request $request){
        try{
            $rowIndex = $request->row_count;
            $categories = Category::where('is_active', true)->select('id','name')->get()->toArray();
            return view('partials.quotation.product-table-listing')->with(compact('categories','rowIndex'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Add Product Row',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}