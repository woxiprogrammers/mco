<?php
namespace App\Http\Controllers\CustomTraits;
use App\Category;
use App\Helper\MaterialProductHelper;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

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
            $request->url();
            if( strpos( $request->url(), "copy" ) !== false ) {
                $copyProduct = true;
            }else{
                $copyProduct = false;
            }
            $recentProductVersion = ProductVersion::where('product_id',$product['id'])->orderBy('created_at','desc')->first();
            $product['category'] = Category::where('id',$product['category_id'])->pluck('name')->first();
            $profitMargins = ProfitMargin::where('is_active', true)->select('id','name','base_percentage')->orderBy('id','asc')->get()->toArray();
            $productProfitMarginsData = ProductProfitMarginRelation::join('profit_margin_versions','profit_margin_versions.id','=','products_profit_margins_relation.profit_margin_version_id')
                                    ->join('profit_margins','profit_margins.id','=','profit_margin_versions.profit_margin_id')
                                    ->where('products_profit_margins_relation.product_version_id',$recentProductVersion['id'])
                                    ->select('profit_margins.id as id','profit_margin_versions.percentage as percentage')
                                    ->get();
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
                                        ->join('units','units.id','=','material_versions.unit_id')
                                        ->join('materials','materials.id','=','material_versions.material_id')
                                        ->where('product_material_relation.product_version_id',$recentProductVersion['id'])
                                        ->select('material_versions.id as id','materials.id as material_id','materials.name as name','material_versions.rate_per_unit as rate_per_unit','material_versions.unit_id as unit_id','product_material_relation.material_quantity as quantity','units.name as unit')
                                        ->get()->toArray();
            $materialVersionIds = implode(',',ProductMaterialRelation::where('product_version_id','=',$recentProductVersion['id'])->pluck('material_version_id')->toArray());
            if($request->ajax()){
                return view('partials.quotation.product-view')->with(compact('product','profitMargins','units','materials','productMaterialIds','productMaterialVersions','productProfitMargins','materialVersionIds'));
            }else{
                return view('admin.product.edit')->with(compact('product','profitMargins','units','materials','productMaterialIds','productMaterialVersions','productProfitMargins','materialVersionIds','copyProduct'));
            }
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
                        ->orderBy('materials.name','asc')
                        ->get();
            $materialOptions = array();
            if($materials == null){
                $materialOptions[] = '<option value=""> No material Available </option>';
            }else{
                foreach($materials as $material){
                    $materialOptions[] = '<li  class="list-group-item"><input type="checkbox" name="material_ids" value="'.$material->id.'"> '.$material->name.'</li>';
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
                if($request->has('materials')){
                    if(array_key_exists($material['id'],$request->materials)){
                        $materialData[$iterator]['material'] = $request->materials[$material['id']];
                        $materialData[$iterator]['material']['name'] = $material['name'];
                    }else{
                        $materialData[$iterator]['material'] = $material;
                        $materialData[$iterator]['material']['quantity'] = 0;
                    }
                }else{
                    $materialData[$iterator]['material'] = $material;
                    $materialData[$iterator]['material']['quantity'] = 0;
                }
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
                $recentVersion = MaterialVersion::where('material_id',$key)->orderBy('created_at','desc')->select('rate_per_unit','unit_id')->first();
                $subTotal += round($data['material_amount'][$key],3);
                Log::info('subtotal in loop');
                Log::info($subTotal);
                $productMaterialProfitMarginData[$iterator]['material_quantity'] = $data['material_quantity'][$key];
                if($materialVersion != $recentVersion){
                    $materialVersion['material_id'] = $key;
                    $newVersion = MaterialVersion::create($materialVersion);
                    $productMaterialProfitMarginData[$iterator]['material_version_id'] = $newVersion->id;
                }else{
                    $productMaterialProfitMarginData[$iterator]['material_version_id'] = $key;
                }
                $iterator++;
            }
            Log::info('subtotal out of loop');
            Log::info($subTotal);
            $iterator = 0;
            $taxAmount = 0;
            if(array_key_exists('profit_margin',$data)){
                Log::info('in profit margin');
                foreach($data['profit_margin'] as $key => $profitMargin){
                    Log::info('current taxAmount');
                    Log::info(round(($subTotal * ($profitMargin / 100)),3));
                    $taxAmount += round(($subTotal * ($profitMargin / 100)),3);
                    Log::info('till this total amount');
                    Log::info($taxAmount);
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
            }
            $productVersionData = array();
            $productVersionData['product_id'] = $product->id;
            Log::info('product rate per unit');
            Log::info(MaterialProductHelper::customRound(($subTotal + $taxAmount)));
            $productVersionData['rate_per_unit'] = MaterialProductHelper::customRound(($subTotal + $taxAmount));
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
            return redirect('/product/manage');
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
                $productData = Product::where('name','ilike','%'.$request->search_product_name.'%')->orderBy('name','asc')->get()->toArray();
            }else{
                $productData = Product::orderBy('name','asc')->get()->toArray();
            }
            $iTotalRecords = count($productData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($productData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($productData); $iterator++,$pagination++ ){
                $productVersion = ProductVersion::where('product_id',$productData[$pagination]['id'])->select('rate_per_unit')->orderBy('created_at','desc')->first();
                if($productData[$pagination]['is_active'] == true){
                    $product_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $product_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                if(Auth::user()->hasPermissionTo('edit-product')){
                    $records['data'][$iterator] = [
                        $productData[$pagination]['name'],
                        Category::where('id',$productData[$pagination]['category_id'])->pluck('name')->first(),
                        Unit::where('id',$productData[$pagination]['unit_id'])->pluck('name')->first(),
                        MaterialProductHelper::customRound($productVersion['rate_per_unit']),
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
                            <li>
                                <a href="/product/product-analysis-pdf/'.$productData[$pagination]['id'].'">
                                    <i class="icon-cloud-download"></i> Download </a>
                            </li>
                            <li>
                                <a href="/product/copy/'.$productData[$pagination]['id'].'">
                                    <i class="fa fa-files-o"></i> Copy Product</a>
                            </li>
                        </ul>
                    </div>'
                    ];
                }else{
                    $records['data'][$iterator] = [
                        $productData[$pagination]['name'],
                        Category::where('id',$productData[$pagination]['category_id'])->pluck('name')->first(),
                        Unit::where('id',$productData[$pagination]['unit_id'])->pluck('name')->first(),
                        MaterialProductHelper::customRound($productVersion['rate_per_unit']),
                        $product_status,
                        '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/product/product-analysis-pdf/'.$productData[$pagination]['id'].'">
                                    <i class="icon-cloud-download"></i> Download </a>
                            </li>
                        </ul>
                    </div>'
                    ];
                }

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
            if($request->ajax()){
                return response()->json(['message' => 'Product Edited successfully'],200);
            }
            $productData['name'] = ucwords(trim($data['name']));
            $productData['description'] = $data['description'];
            $productData['category_id'] = $data['category_id'];
            $productData['unit_id'] =  $data['unit_id'];
            $product->update($productData);
            $productMaterialProfitMarginData = array();
            $iterator = 0;
            $subTotal = 0;
            foreach($data['material'] as $key => $materialVersion){
                if(array_key_exists('material_version_id',$materialVersion)){
                    $recentVersion = MaterialVersion::where('id',$materialVersion['material_version_id'])->select('rate_per_unit','unit_id')->first();
                }else{
                    $recentVersion = MaterialVersion::where('material_id',$key)->orderBy('created_at','desc')->select('rate_per_unit','unit_id')->first();
                }
                $subTotal += round(($materialVersion['rate_per_unit'] * $data['material_quantity'][$key]),3);
                $productMaterialProfitMarginData[$iterator]['material_quantity'] = $data['material_quantity'][$key];
                if($materialVersion != $recentVersion){
                    $materialVersion['material_id'] = $key;
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
                $taxAmount += round(($subTotal * ($profitMargin / 100)),3);
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
            $productVersionData['rate_per_unit'] = MaterialProductHelper::customRound(($subTotal + $taxAmount));
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

    public function generateProductAnalysisPdf(Request $request, $product){
        try{
            $recentProductVersion = ProductVersion::where('product_id',$product['id'])->orderBy('created_at','desc')->first();
            $product['category'] = Category::where('id',$product['category_id'])->pluck('name')->first();
            $profitMargins = ProfitMargin::where('is_active', true)->select('id','name','base_percentage')->orderBy('id','asc')->get()->toArray();
            $productProfitMarginsData = ProductProfitMarginRelation::join('profit_margin_versions','profit_margin_versions.id','=','products_profit_margins_relation.profit_margin_version_id')
                ->join('profit_margins','profit_margins.id','=','profit_margin_versions.profit_margin_id')
                ->where('products_profit_margins_relation.product_version_id',$recentProductVersion['id'])
                ->select('profit_margins.id as id','profit_margin_versions.percentage as percentage')
                ->get();
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
                ->join('units','units.id','=','material_versions.unit_id')
                ->join('materials','materials.id','=','material_versions.material_id')
                ->where('product_material_relation.product_version_id',$recentProductVersion['id'])
                ->select('material_versions.id as id','materials.id as material_id','materials.name as name','material_versions.rate_per_unit as rate_per_unit','material_versions.unit_id as unit_id','product_material_relation.material_quantity as quantity','units.name as unit')
                ->get()->toArray();
            $materialVersionIds = implode(',',ProductMaterialRelation::where('product_version_id','=',$recentProductVersion['id'])->pluck('material_version_id')->toArray());
            $data['product'] = $product;
            $profitMarginData = array();
            foreach ($profitMargins as $pms) {
                array_push($profitMarginData, $pms['name']);
            }
            $data['profitMargins'] = $profitMargins;
            $data['units'] = $units;
            $data['materials'] = $materials;
            $data['product'] = $product;
            $data['productMaterialIds'] = $productMaterialIds;
            $data['productMaterialVersions'] = $productMaterialVersions;
            $subtotal = 0;
            foreach ($productMaterialVersions as $mat) {
                $subtotal = $subtotal + MaterialProductHelper::customRound(($mat['quantity']*$mat['rate_per_unit']));
            }
            $data['subtotal'] = $subtotal;

            $finalAmount = $subtotal;
            $profitMarginRestruct = array();
            $pmCount = 0;
            foreach ($productProfitMargins as $pm) {
                $pmArray = array(
                    'pm_name' => $profitMarginData[$pmCount],
                    'percentage' => $pm,
                    'total' => MaterialProductHelper::customRound(($subtotal*$pm)/100)
                );
                array_push($profitMarginRestruct, $pmArray);
                $finalAmount = MaterialProductHelper::customRound($finalAmount + MaterialProductHelper::customRound(($subtotal*$pm)/100));
                $pmCount++;
            }
            $data['finalAmount'] = $finalAmount;
            $data['productProfitMargins'] = $profitMarginRestruct;
            $data['materialVersionIds'] = $materialVersionIds;
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML(view('admin.product.pdf.product_analysis',$data));
            return $pdf->stream();
        } catch(\Exception $e) {
            $data = [
                'action' => 'Generate Summary PDF',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }
}
