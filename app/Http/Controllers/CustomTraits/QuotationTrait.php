<?php
/**
 * Created by Ameya Joshi.
 * Date: 5/6/17
 * Time: 4:19 PM
 */

namespace App\Http\Controllers\CustomTraits;

use App\Category;
use App\Material;
use App\Product;
use App\ProductMaterialRelation;
use App\ProductProfitMarginRelation;
use App\ProductVersion;
use App\ProfitMargin;
use App\Unit;
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
            $rowIndex = $request->row_count + 1;
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

    public function getMaterials(Request $request){
        try{
            $productIds = $request->product_ids;
            $materialIds = array();
            $materials = array();
            $units = Unit::where('is_active', true)->orderBy('name','asc')->get()->toArray();
            foreach($productIds as $id){
                $recentVersionId = ProductVersion::where('product_id',$id)->pluck('id')->first();
                $materialId = ProductMaterialRelation::join('material_versions','product_material_relation.material_version_id','=','material_versions.id')
                                ->join('materials','materials.id','=','material_versions.material_id')
                                ->where('product_material_relation.product_version_id',$recentVersionId)
                                ->pluck('material_versions.material_id')
                                ->toArray();
                if(!(in_array($materialId,$materialIds))){
                    $materialIds[] = $materialId;
                    $materials[] = ProductMaterialRelation::join('material_versions','product_material_relation.material_version_id','=','material_versions.id')
                        ->join('materials','materials.id','=','material_versions.material_id')
                        ->join('units','units.id','=','material_versions.unit_id')
                        ->where('product_material_relation.product_version_id',$recentVersionId)
                        ->select('materials.id as id','materials.name as name','materials.rate_per_unit as rate_per_unit','materials.unit_id as unit_id','units.name as unit')
                        ->first()
                        ->toArray();
                }
            }
            return view('partials.quotation.materials-table')->with(compact('materials','units'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Add Product Row',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getProfitMargins(Request $request){
        try{
            $productIds = $request->product_ids;
            $profitMargins = ProfitMargin::where('is_active', true)->orderBy('name','asc')->get()->toArray();
            $productProfitMargins = array();
            foreach($productIds as $id){
                $recentVersion = ProductVersion::where('product_id',$id)->orderBy('created_at')->pluck('id')->first();
                $productProfitMargins[$id]['products'] = Product::where('id',$id)->pluck('name')->first();
                $productProfitMargins[$id]['profit_margin'] = ProductProfitMarginRelation::join('profit_margin_versions','profit_margin_versions.id','=','products_profit_margins_relation.profit_margin_version_id')
                                                            ->join('profit_margins','profit_margins.id','=','profit_margin_versions.profit_margin_id')
                                                            ->where('products_profit_margins_relation.product_version_id',$recentVersion)
                                                            ->orderBy('profit_margins.name','asc')
                                                            ->select('profit_margins.name as name','profit_margins.id as id','profit_margin_versions.percentage as percentage')
                                                            ->get()
                                                            ->toArray();

            }
            return view('partials.quotation.profit-margin-table')->with(compact('productProfitMargins','profitMargins'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Add Product Row',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function quotationListing(Request $request){
        try{
            $records = array();
            $records['data'] = array();
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = 0;
            $records["recordsFiltered"] = 0;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Quotation Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

        return response()->json($records);
    }
}