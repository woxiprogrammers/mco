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
            $productProfitMarginsData = ProductProfitMarginRelation::join('profit_margin_versions','profit_margin_versions.id','=','products_profit_margins_relation.profit_margin_version_id')
                                    ->join('profit_margins','profit_margins.id','=','profit_margin_versions.profit_margin_id')
                                    ->select('profit_margins.id as id','profit_margin_versions.percentage as percentage')
                                    ->get()->toArray();
            $productProfitMargins = array();
            foreach($productProfitMarginsData as $productProfitMargin){
                $productProfitMargins[$productProfitMargin['id']] = $productProfitMargin['percentage'];
            }
            $units = Unit::where('is_active', true)->select('id','name')->orderBy('name','asc')->get()->toArray();
            $materials = Material::join('category_material_relations','category_material_relations.material_id','=','materials.id')
                                    ->where('category_material_relations.category_id',$product['category_id'])
                                    ->where('materials.is_active', true)
                                    ->select('materials.id as id','materials.name as name')
                                    ->get();
            $productMaterialIds = Material::join('material_versions','materials.id','=','material_versions.material_id')
                                        ->join('product_material_relation','product_material_relation.material_version_id','=','material_versions.id')
                                        ->where('product_material_relation.product_version_id',$recentProductVersion['id'])
                                        ->pluck('materials.id')
                                        ->toArray();
            $productMaterialVersions = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                                        ->join('materials','materials.id','=','material_versions.material_id')
                                        ->where('product_material_relation.product_version_id',$recentProductVersion['id'])
                                        ->select('material_versions.id as id','materials.name as name','material_versions.rate_per_unit as rate_per_unit','material_versions.unit_id as unit_id','product_material_relation.material_quantity as quantity')
                                        ->get()->toArray();
            $materialVersionIds = implode(',',ProductMaterialRelation::where('product_version_id','=',$recentProductVersion['id'])->pluck('material_version_id')->toArray());
            return view('admin.product.edit')->with(compact('product','profitMargins','units','materials','productMaterialIds','productMaterialVersions','productProfitMargins','materialVersionIds'));
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
            $materials = Material::whereIn('id',$materialIds)->select('id','name','rate_per_unit','unit_id')->orderBy('name','asc')->get()->toArray();
            $materialData = array();
            $iterator = 0;
            $units = Unit::where('is_active', true)->select('id','name')->orderBy('name','asc')->get()->toArray();
            foreach($materials as $material){
                $materialData[$iterator]['material'] = $material;
                $materialData[$iterator]['unit'] = Unit::where('id',$materialData[$iterator]['material']['unit_id'])->select('id','name')->first()->toArray();
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
            $productData['name'] = ucwords(trim($data['name']));
            $productData['description'] = $data['description'];
            $productData['category_id'] = $data['category_id'];
            $productData['unit_id'] =  $data['unit_id'];
            $productData['is_active'] =  (boolean)false;
            $product = Product::create($productData);
            $productMaterialProfitMarginData = array();
            $iterator = 0;
            $subTotal = 0;
            foreach($data['material'] as $key => $materialVersion){
                $material = Material::findOrFail($key);
                $fromUnit = $materialVersion['unit_id'];
                $toUnit = $material->unit_id;
                if($fromUnit != $toUnit){
                    $conversionRate = $this->unitConversion($fromUnit,$toUnit,$materialVersion['rate_per_unit']);
                    Material::where('id',$key)->update(['rate_per_unit' => $conversionRate]);
                }else{
                    Material::where('id',$key)->update(['rate_per_unit' => $materialVersion['rate_per_unit']]);
                }
                $recentVersion = MaterialVersion::where('material_id',$key)->orderBy('created_at','desc')->select('rate_per_unit','unit_id')->first();
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
            $productVersionData['rate_per_unit'] = round(($subTotal + $taxAmount),3);
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
            if($request->has('search_product_name')){
                $productData = Product::where('name','ilike','%'.$request->search_product_name.'%')->orderBy('id','asc')->get()->toArray();
            }else{
                $productData = Product::orderBy('id','asc')->get()->toArray();
            }
            $iTotalRecords = count($productData);
            $records = array();
            $records['data'] = array();
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
                    $productVersion['rate_per_unit'],
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

    public function editProduct(Request $request, $product){
        try{
            $data = $request->all();
            $productData['name'] = ucwords(trim($data['name']));
            $productData['description'] = $data['description'];
            $productData['category_id'] = $data['category_id'];
            $productData['unit_id'] =  $data['unit_id'];
            $product->update($productData);
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
                $material = Material::findOrFail(MaterialVersion::where('id',$key)->pluck('material_id')->first());
                $fromUnit = $materialVersion['unit_id'];
                $toUnit = $material->unit_id;
                if($fromUnit != $toUnit){
                    $conversionRate = $this->unitConversion($fromUnit,$toUnit,$materialVersion['rate_per_unit']);
                    Material::where('id',$key)->update(['rate_per_unit' => $conversionRate]);
                }else{
                    Material::where('id',$key)->update(['rate_per_unit' => $materialVersion['rate_per_unit']]);
                }
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
            $productVersionData['rate_per_unit'] = round(($subTotal + $taxAmount),3);
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
            $request->session()->flash('success','Product Edited Successfully');
            return redirect('/product/edit/'.$product->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function autoSuggest(Request $request, $keyword){
        try{
            $products = Product::where('name','ilike','%'.$keyword.'%')->select('id','name')->get();
            if($products == null){
                $response = array();
            }else{
                $response = $products->toArray();
            }
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Product auto suggest',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = array();
        }
        return response()->json($response,$status);
    }

    public function checkProductName(Request $request){
        try{
            if($request->has('product_id')){
                $productCount = Product::where('name','ilike',$request->product_name)->where('id','!=',$request->product_id)->count();
            }else{
                $productCount = Product::where('name','ilike',$request->product_name)->count();
            }
            if($productCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Check Product Name',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function unitConversion($fromUnit,$toUnit, $rate){
        $conversion = UnitConversion::where('unit_1_id',$fromUnit)->where('unit_2_id',$toUnit)->first();
        if($conversion != null){
            $materialRateFrom = $conversion->unit_1_value / $conversion->unit_2_value;
            $materialRateTo = $rate * $materialRateFrom;
        }else{
            $conversion = UnitConversion::where('unit_2_id',$fromUnit)->where('unit_1_id',$toUnit)->first();
            if($conversion != null){
                $materialRateFrom = $conversion->unit_2_value / $conversion->unit_1_value;
                $materialRateTo = $rate * $materialRateFrom;
            }else{
                $materialRateTo['unit'] = $fromUnit;
                $materialRateTo['rate'] = $rate;
            }
        }
        return $materialRateTo;
    }
}
