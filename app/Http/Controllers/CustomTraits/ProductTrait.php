<?php
namespace App\Http\Controllers\CustomTraits;
use App\Category;
use App\Material;
use App\MaterialVersion;
use App\Product;
use App\ProductMaterialRelation;
use App\ProductProfitMarginRelation;
use App\ProductVersion;
use App\ProfitMargin;
use App\ProfitMarginVersion;
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

    public function getCreateView(Request $request) {
        try{
            $categories = Category::where('is_active', true)->select('id','name')->orderBy('name','asc')->get()->toArray();
            $profitMargins = ProfitMargin::where('is_active', true)->select('id','name','base_percentage')->orderBy('id','asc')->get()->toArray();
            $units = Unit::where('is_active', true)->select('id','name')->orderBy('name','asc')->get()->toArray();
            return view('admin.product.create')->with(compact('categories','profitMargins','units'));
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

    public function getEditView(Request $request, $product) {
        try{
            $product = $product->toArray();
            $recentProductVersion = ProductVersion::where('product_id',$product['id'])->orderBy('created_at','desc')->first()->toArray();
            $product['category'] = Category::where('id',$product['category_id'])->pluck('name')->first();
            $profitMargins = ProfitMargin::where('is_active', true)->select('id','name','base_percentage')->orderBy('id','asc')->get()->toArray();
            $units = Unit::where('is_active', true)->select('id','name')->orderBy('name','asc')->get()->toArray();
            $materials = Material::join('category_material_relations','category_material_relations.material_id','=','materials.id')
                                    ->where('category_material_relations.category_id',$product['category_id'])
                                    ->where('materials.is_active', true)
                                    ->get();
            return view('admin.product.edit')->with(compact('product','profitMargins','units','materials'));
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

    public function getMaterials(Request $request,$category){
        try{
            $materials = Material::join('category_material_relations','materials.id','=','category_material_relations.material_id')
                        ->where('category_material_relations.category_id',$category->id)
                        ->where('materials.is_active', true)
                        ->select('materials.id as id','materials.name as name')
                        ->orderBy('name','asc')
                        ->get();
            $materialOptions = array();
            if($materials == null){
                $materialOptions[] = '<option value=""> No material Available </option>';
            }else{
                foreach($materials as $material){
                    $materialOptions[] = '<option value="'.$material->id.'"> '.$material->name.' </option>';
                }
            }
            $status = 200;
        }catch(\Exception $e){
            $status = 500;
            $materialOptions = array();
            $data = [
                'action' => 'Get Materials',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($materialOptions,$status);
    }

    public function getMaterialsDetails(Request $request){
        try{
            $materialIds = $request->material_ids;
            $materials = Material::whereIn('id',$materialIds)->select('id','name')->orderBy('name','asc')->get()->toArray();
            $materialData = array();
            $iterator = 0;
            $units = Unit::where('is_active', true)->select('id','name')->orderBy('name','asc')->get()->toArray();
            foreach($materials as $material){
                $materialData[$iterator]['material'] = $material;
                $materialData[$iterator]['material_version'] = MaterialVersion::where('material_id',$material['id'])->orderBy('created_at','desc')->first()->toArray();
                $materialData[$iterator]['unit'] = Unit::where('id',$materialData[$iterator]['material_version']['unit_id'])->select('id','name')->first()->toArray();
                $iterator++;
            }
            return view('partials.product.material-listing')->with(compact('materialData','units'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Materials',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createProduct(Request $request){
        try{
            $data = $request->all();
            $productData['name'] = ucwords($data['name']);
            $productData['description'] = $data['description'];
            $productData['category_id'] = $data['category_id'];
            $productData['unit_id'] =  $data['unit_id'];
            $productData['is_active'] =  (boolean)false;
            $product = Product::create($productData);
            $productMaterialProfitMarginData = array();
            $iterator = 0;
            $subTotal = 0;
            foreach($data['material_version'] as $key => $materialVersion){
                $recentVersion = MaterialVersion::where('id',$key)->select('rate_per_unit','unit_id')->first();
                $subTotal += $materialVersion['rate_per_unit']*$data['material_quantity'][$key];
                $productMaterialProfitMarginData[$iterator]['material_quantity'] = $data['material_quantity'][$key];
                if($materialVersion != $recentVersion){
                    $materialVersion['material_id'] = MaterialVersion::where('id',$key)->pluck('material_id')->first();
                    $newVersion = MaterialVersion::create($materialVersion);
                    $productMaterialProfitMarginData[$iterator]['material_version_id'] = $newVersion->id;
                }else{
                    $productMaterialProfitMarginData[$iterator]['material_version_id'] = $key;
                }
                $iterator++;
            }
            $iterator = 0;
            $taxAmount = 0;
            foreach($data['profit_margin'] as $key => $profitMargin){
                $taxAmount += $subTotal * ($profitMargin / 100);
                $recentProfitMarginVersion = ProfitMarginVersion::where('profit_margin_id',$key)->orderBy('created_at','desc')->select('id','percentage')->first()->toArray();
                if($profitMargin == $recentProfitMarginVersion['percentage']){
                    $productMaterialProfitMarginData[$iterator]['profit_margin_version_id'] = $recentProfitMarginVersion['id'];
                }else{
                    $versionData = array();
                    $versionData['profit_margin_id'] = $key;
                    $versionData['percentage'] = $profitMargin;
                    $newProfitMargin = ProfitMarginVersion::create($versionData);
                    $productMaterialProfitMarginData[$iterator]['profit_margin_version_id'] = $newProfitMargin->id;
                }
                $iterator++;
            }
            $productVersionData = array();
            $productVersionData['product_id'] = $product->id;
            $productVersionData['rate_per_unit'] = $subTotal + $taxAmount;
            $productVersion = ProductVersion::create($productVersionData);
            foreach($productMaterialProfitMarginData as $versions){
                if(array_key_exists('material_version_id',$versions) && array_key_exists('material_quantity',$versions)){
                    $productMaterialRelation = ProductMaterialRelation::create([
                        'product_version_id' => $productVersion->id,
                        'material_version_id' => $versions['material_version_id'],
                        'material_quantity' => $versions['material_quantity']
                    ]);
                }
                if(array_key_exists('profit_margin_version_id',$versions)){
                    $productProfitMarginRelation = ProductProfitMarginRelation::create([
                        'product_version_id' => $productVersion->id,
                        'profit_margin_version_id' => $versions['profit_margin_version_id']
                    ]);
                }
            }
            $request->session()->flash('success','Product Created Successfully');
            return redirect('/product/create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Product',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
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