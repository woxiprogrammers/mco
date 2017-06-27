<?php
/**
 * Created by Ameya Joshi.
 * Date: 5/6/17
 * Time: 4:19 PM
 */

namespace App\Http\Controllers\CustomTraits;

use App\Category;
use App\Client;
use App\Helper\UnitHelper;
use App\Material;
use App\Product;
use App\ProductMaterialRelation;
use App\ProductProfitMarginRelation;
use App\ProductVersion;
use App\ProfitMargin;
use App\ProfitMarginVersion;
use App\Project;
use App\ProjectSite;
use App\Quotation;
use App\QuotationMaterial;
use App\QuotationProduct;
use App\QuotationProfitMarginVersion;
use App\QuotationStatus;
use App\QuotationTaxVersion;
use App\Summary;
use App\Tax;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait QuotationTrait{

    public function getCreateView(Request $request){
        try{
            $clients = Client::where('is_active', true)->select('id','company')->get()->toArray();
            $categories = Category::where('is_active', true)->select('id','name')->get()->toArray();
            return view('admin.quotation.create')->with(compact('categories','clients'));
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
            $isEdit = false;
            $categories = Category::where('is_active', true)->select('id','name')->get()->toArray();
            if($request->has('is_edit')){
                $isEdit = true;
                $summaries = Summary::where('is_active', true)->select('id','name')->orderBy('name','asc')->get();
                return view('partials.quotation.product-table-listing')->with(compact('categories','rowIndex','summaries','isEdit'));
            }
            return view('partials.quotation.product-table-listing')->with(compact('categories','rowIndex','isEdit'));
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
            $productIds = $request->productIds;
            $materialIds = array();
            $units = Unit::where('is_active', true)->orderBy('name','asc')->get()->toArray();
            if($request->clientSuppliedMaterial == null){
                $clientSuppliedMaterial = array();
            }else{
                $clientSuppliedMaterial = $request->clientSuppliedMaterial;
            }
            foreach($productIds as $id){
                $recentVersionId = ProductVersion::where('product_id',$id)->orderBy('created_at','desc')->pluck('id')->first();
                $materialId = ProductMaterialRelation::join('material_versions','product_material_relation.material_version_id','=','material_versions.id')
                                ->join('materials','materials.id','=','material_versions.material_id')
                                ->where('product_material_relation.product_version_id',$recentVersionId)
                                ->pluck('materials.id')
                                ->toArray();
                $materialIds = array_unique(array_merge($materialIds,$materialId));
            }
            if($request->has('quotation_id')){
                $quotationMaterialIds = QuotationMaterial::where('quotation_id',$request->quotation_id)->pluck('material_id')->toArray();
                if($request->has('material_rate')){
                    $iterator = 0;
                    $materials = array();
                    foreach($request->material_rate as $materialId => $materialRate){
                        $materials[$iterator]['id'] = $materialId;
                        $materials[$iterator]['name'] = Material::where('id',$materialId)->pluck('name')->first();
                        $materials[$iterator]['rate_per_unit'] = $materialRate;
                        $materials[$iterator]['unit_id'] = $request->material_unit[$materialId];
                        $materials[$iterator]['unit'] = Unit::where('id',$request->material_unit[$materialId]);
                        $iterator++;
                    }
                }else{
                    $materials = QuotationMaterial::join('materials','materials.id','=','quotation_materials.material_id')
                        ->join('units','units.id','=','quotation_materials.unit_id')
                        ->where('quotation_id',$request->quotation_id)
                        ->whereIn('quotation_materials.material_id',$materialIds)
                        ->select('materials.id as id','materials.name as name','quotation_materials.rate_per_unit as rate_per_unit','quotation_materials.unit_id as unit_id','units.name as unit')
                        ->get()
                        ->toArray();
                }
                $newMaterialIds = array_diff($materialIds,$quotationMaterialIds);
                if(count($newMaterialIds) > 0){
                    $newMaterials = Material::join('units','materials.unit_id','=','units.id')
                        ->whereIn('materials.id',$newMaterialIds)
                        ->orderBy('name','asc')
                        ->select('materials.id as id','materials.name as name','materials.rate_per_unit as rate_per_unit','materials.unit_id as unit_id','units.name as unit')
                        ->get()
                        ->toArray();
                    $materials = array_merge($materials,$newMaterials);
                }
            }else{
                if($request->has('material_rate')){
                    $formMaterialIds = array_keys($request->material_rate);
                    $newMaterialIds = array_diff($materialIds,$formMaterialIds);
                    $materials = array();
                    $iterator = 0;
                    foreach($request->material_rate as $materialId => $materialRate){
                        $materials[$iterator]['id'] = $materialId;
                        $materials[$iterator]['name'] = Material::where('id',$materialId)->pluck('name')->first();
                        $materials[$iterator]['rate_per_unit'] = $materialRate;
                        $materials[$iterator]['unit_id'] = $request->material_unit[$materialId];
                        $materials[$iterator]['unit'] = Unit::where('id',$request->material_unit[$materialId]);
                        $iterator++;
                    }
                    if(count($newMaterialIds) > 0){
                        $newMaterials = Material::join('units','materials.unit_id','=','units.id')
                            ->whereIn('materials.id',$newMaterialIds)
                            ->orderBy('name','asc')
                            ->select('materials.id as id','materials.name as name','materials.rate_per_unit as rate_per_unit','materials.unit_id as unit_id','units.name as unit')
                            ->get()
                            ->toArray();
                        $materials = array_merge($materials,$newMaterials);
                    }
                }else{
                    $materials = Material::join('units','materials.unit_id','=','units.id')
                        ->whereIn('materials.id',$materialIds)
                        ->orderBy('name','asc')
                        ->select('materials.id as id','materials.name as name','materials.rate_per_unit as rate_per_unit','materials.unit_id as unit_id','units.name as unit')
                        ->get()
                        ->toArray();
                }
            }
            return view('partials.quotation.materials-table')->with(compact('materials','units','clientSuppliedMaterial'));
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
            if($request->has('quotation_id')){
                $quotationProductIds = QuotationProduct::where('quotation_id',$request->quotation_id)->pluck('product_id')->toArray();
                $newProductIds = array_diff($productIds,$quotationProductIds);
                if($request->has('profit_margins')){
                    foreach($request['profit_margins'] as $productId => $profitMargin){
                        $productProfitMargins[$productId]['products'] = Product::where('id',$productId)->pluck('name')->first();
                        foreach($profitMargin as $id => $percentage){
                            $productProfitMargins[$productId]['profit_margin'][$id] = $percentage;
                        }
                    }
                }else{
                    foreach($productIds as $id){
                        $quotationProductId = QuotationProduct::where('quotation_id',$request->quotation_id)->where('product_id',$id)->pluck('id')->first();
                        $productProfitMargins[$id]['products'] = Product::where('id',$id)->pluck('name')->first();
                        $productProfitMarginRelation = QuotationProfitMarginVersion::join('profit_margins','profit_margins.id','=','quotation_profit_margin_versions.profit_margin_id')
                            ->where('quotation_profit_margin_versions.quotation_product_id',$quotationProductId)
                            ->orderBy('profit_margins.name','asc')
                            ->select('profit_margins.name as name','profit_margins.id as id','quotation_profit_margin_versions.percentage as percentage')
                            ->get();
                        if($productProfitMarginRelation != null){
                            foreach($productProfitMarginRelation as $profitMargin){
                                $productProfitMargins[$id]['profit_margin'][$profitMargin['id']] = $profitMargin->percentage;
                            }
                        }
                    }

                }
                if(count($newProductIds) > 0){
                    foreach($newProductIds as $id){
                        $recentVersion = ProductVersion::where('product_id',$id)->orderBy('created_at','desc')->pluck('id')->first();
                        $productProfitMargins[$id]['products'] = Product::where('id',$id)->pluck('name')->first();
                        $productProfitMarginRelation = ProductProfitMarginRelation::join('profit_margin_versions','profit_margin_versions.id','=','products_profit_margins_relation.profit_margin_version_id')
                            ->join('profit_margins','profit_margins.id','=','profit_margin_versions.profit_margin_id')
                            ->where('products_profit_margins_relation.product_version_id',$recentVersion)
                            ->orderBy('profit_margins.name','asc')
                            ->select('profit_margins.name as name','profit_margins.id as id','profit_margin_versions.percentage as percentage')
                            ->get()
                            ->toArray();
                        foreach($productProfitMarginRelation as $profitMargin){
                            $productProfitMargins[$id]['profit_margin'][$profitMargin['id']] = $profitMargin['percentage'];
                        }
                    }
                }
            }else{
                if($request->has('profit_margins')){
                    foreach($request['profit_margins'] as $productId => $profitMargin){
                        $productProfitMargins[$productId]['products'] = Product::where('id',$productId)->pluck('name')->first();
                        foreach($profitMargin as $id => $percentage){
                            $productProfitMargins[$productId]['profit_margin'][$id] = $percentage;
                        }
                    }
                }else{
                    foreach($productIds as $id){
                        $recentVersion = ProductVersion::where('product_id',$id)->orderBy('created_at','desc')->pluck('id')->first();
                        $productProfitMargins[$id]['products'] = Product::where('id',$id)->pluck('name')->first();
                        $productProfitMarginRelation = ProductProfitMarginRelation::join('profit_margin_versions','profit_margin_versions.id','=','products_profit_margins_relation.profit_margin_version_id')
                            ->join('profit_margins','profit_margins.id','=','profit_margin_versions.profit_margin_id')
                            ->where('products_profit_margins_relation.product_version_id',$recentVersion)
                            ->orderBy('profit_margins.name','asc')
                            ->select('profit_margins.name as name','profit_margins.id as id','profit_margin_versions.percentage as percentage')
                            ->get()
                            ->toArray();
                        foreach($productProfitMarginRelation as $profitMargin){
                            $productProfitMargins[$id]['profit_margin'][$profitMargin['id']] = $profitMargin['percentage'];
                        }
                    }
                }
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
            $quotations = Quotation::get();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($quotations); $iterator++,$pagination++ ){
                if($quotations[$pagination]->quotation_status->slug == 'draft'){
                    $quotationStatus = '<td><span class="label label-sm label-warning"> Draft </span></td>';
                }elseif($quotations[$pagination]->quotation_status->slug == 'approved'){
                    $quotationStatus = '<td><span class="label label-sm label-success"> Approved </span></td>';
                }else{
                    $quotationStatus = '<td><span class="label label-sm label-danger"> Disapproved </span></td>';
                }
                $records['data'][] = [
                    $quotations[$pagination]->project_site->project->client->company,
                    $quotations[$pagination]->project_site->project->name,
                    $quotations[$pagination]->project_site->name,
                    $quotationStatus,
                    date('d M Y',strtotime($quotations[$pagination]->created_at)),
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/quotation/edit/'.$quotations[$pagination]->id.'">
                                <i class="icon-docs"></i> Edit </a>
                            </li>
                        </ul>
                    </div>'
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = count($quotations);
            $records["recordsFiltered"] = count($quotations);
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

    public function checkProjectSiteName(Request $request){
        try{
            $projectSiteId = $request->projectSiteId;
            $nameCount = ProjectSite::where('id',$projectSiteId)->count();
            if($nameCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Check Project Site name',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function checkProjectNames(Request $request){
        try{
            $project = $request->name;
            $nameCount = Project::where('name','ilike',$project)->count();
            if($nameCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch(\Exception $e){
            $response = array();
            $status = 500;
            $data = [
                'action' => 'Get Project names',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createQuotation(Request $request){
        try{
            $data = $request->all();
            $quotationData = array();
            $draftStatusId = QuotationStatus::where('slug','draft')->pluck('id')->first();
            $quotationData['project_site_id'] = $data['project_site_id'];
            $quotationData['quotation_status_id'] = $draftStatusId;
            $quotation = Quotation::create($quotationData);
            $quotation = $quotation->toArray();
            foreach($data['product_id'] as $productId){
                $quotationProductData = array();
                $quotationProductData['product_id'] = $productId;
                $quotationProductData['quotation_id'] = $quotation['id'];
                $quotationProductData['description'] = $data['product_description'][$productId];
                $recentVersion = ProductVersion::where('product_id',$productId)->orderBy('created_at','desc')->pluck('id')->first();
                $productMaterialIds = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                                                ->join('materials','materials.id','=','material_versions.material_id')
                                                ->where('product_material_relation.product_version_id', $recentVersion)
                                                ->pluck('materials.id')
                                                ->toArray();
                $productMaterials = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                                                ->join('materials','materials.id','=','material_versions.material_id')
                                                ->where('product_material_relation.product_version_id', $recentVersion)
                                                ->select('materials.id as id','product_material_relation.material_quantity as material_quantity','material_versions.unit_id as unit_id')
                                                ->get()
                                                ->toArray();
                $productAmount = 0;
                foreach($productMaterials as $material){
                    if($data['material_unit'][$material['id']] == $material['unit_id']){
                        $rateConversion = $data['material_rate'][$material['id']];
                    }else{
                        $rateConversion = UnitHelper::unitConversion($material['unit_id'],$data['material_unit'][$material['id']],$data['material_rate'][$material['id']]);
                        if(is_array($rateConversion)){
                            $request->session()->flash('error','Unit Conversion is invalid');
                            return redirect('/quotation/create');
                        }
                    }
                    $productAmount = $productAmount + ($rateConversion * $material['material_quantity']);
                }
                $quotationProductData['rate_per_unit'] = $productAmount;
                $quotationProductData['quantity'] = $data['product_quantity'][$productId];
                $quotationProduct = QuotationProduct::create($quotationProductData);
                foreach($data['profit_margins'][$productId] as $id => $percentage){
                    $quotationProfitMarginData = array();
                    $quotationProfitMarginData['profit_margin_id'] = $id;
                    $quotationProfitMarginData['percentage'] = $percentage;
                    $quotationProfitMarginData['quotation_product_id'] = $quotationProduct->id;
                    QuotationProfitMarginVersion::create($quotationProfitMarginData);
                    $productAmount = $productAmount + ($productAmount * ($percentage / 100));
                }
                if($request->has('clientSuppliedMaterial')){
                    foreach($data['clientSuppliedMaterial'] as $materialId){
                        if(in_array($materialId,$productMaterialIds)){
                            $materialQuantity = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                                ->join('materials','materials.id','=','material_versions.material_id')
                                ->where('materials.id','=',$materialId)
                                ->where('product_material_relation.product_version_id',$recentVersion)
                                ->pluck('product_material_relation.material_quantity')
                                ->first();
                            $productAmount = $productAmount - ($materialQuantity * $data['material_rate'][$materialId]);
                        }
                    }
                }
                QuotationProduct::where('id',$quotationProduct->id)->update(['rate_per_unit' => $productAmount]);
            }
            foreach($data['material_id'] as $materialId){
                $quotationMaterialData = array();
                $quotationMaterialData['material_id'] = $materialId;
                $quotationMaterialData['rate_per_unit'] = $data['material_rate'][$materialId];
                $quotationMaterialData['unit_id'] = $data['material_unit'][$materialId];
                if($request->has('clientSuppliedMaterial') && is_array($data['clientSuppliedMaterial']) && in_array($materialId,$data['clientSuppliedMaterial'])){
                    $quotationMaterialData['is_client_supplied'] = true;
                }else{
                    $quotationMaterialData['is_client_supplied'] = false;
                }
                $quotationMaterialData['quotation_id'] = $quotation['id'];
                QuotationMaterial::create($quotationMaterialData);
            }
            $request->session()->flash('success','Quotation created successfully.');
            return redirect('/quotation/create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Quotation',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getProjects(Request $request){
        try{
            $clientId = $request->client_id;
            $projects = Project::where('client_id', $clientId)->where('is_active', true)->get();
            $response = array();
            foreach($projects as $project){
                $response[] = '<option value="'.$project->id.'">'.$project->name.'</option> ';
            }
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Projects',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            $status = 500;
            $response = array();
            Log::critical(json_encode($data));
        }
        return response()->json($response,$status);
    }

    public function getProjectSites(Request $request){
        try{
            $projectId = $request->project_id;
            $projectSites = ProjectSite::join('quotations','quotations.project_site_id','!=','project_sites.id')
                                ->where('project_id', $projectId)
                                ->select('project_sites.id as id','project_sites.name as name')
                                ->get();
            $response = array();
            foreach($projectSites as $projectSite){
                $response[] = '<option value="'.$projectSite->id.'">'.$projectSite->name.'</option> ';
            }
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Projects',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            $status = 500;
            $response = array();
            Log::critical(json_encode($data));
        }
        return response()->json($response,$status);
    }

    public function getEditView(Request $request, $quotation){
        try{
            $summaries = Summary::where('is_active', true)->select('id','name')->get();
            $taxes = Tax::where('is_active', true)->select('id','name','base_percentage')->get();
            return view('admin.quotation.edit')->with(compact('quotation','summaries','taxes'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Projects',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function calculateProductsAmount(Request $request){
        try{
            $status = 200;
            $response = array();
            $data = $request->all();
            $productIds = $data['product_ids'];
            foreach($productIds as $productId){
                $recentVersion = ProductVersion::where('product_id',$productId)->orderBy('created_at','desc')->pluck('id')->first();
                $productMaterialIds = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                    ->join('materials','materials.id','=','material_versions.material_id')
                    ->where('product_material_relation.product_version_id',$recentVersion)
                    ->pluck('material_versions.material_id')
                    ->toArray();
                $materials = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                                                ->join('materials','materials.id','=','material_versions.material_id')
                                                ->where('product_material_relation.product_version_id',$recentVersion)
                                                ->select('material_versions.material_id as material_id','materials.unit_id as unit_id','product_material_relation.material_quantity as quantity')
                                                ->get()
                                                ->toArray();
                $productAmount = 0;
                if($request->has('quotation_id')){
                    if($request->has('material_rate')){
                        foreach($materials as $material){
                            $conversion = UnitHelper::unitConversion($material['unit_id'],$data['material_unit'][$material['material_id']],$data['material_rate'][$material['material_id']]);
                            if(!(is_array($conversion))){
                                $productAmount = $productAmount + ($conversion * $material['quantity']);
                            }else{
                                $productAmount = $productAmount + ($conversion['rate'] * $material['quantity']);
                            }
                        }
                    }else{
                        foreach($materials as $material){
                            $fromUnit = QuotationMaterial::where('quotation_id',$data['quotation_id'])->where('material_id',$material['material_id'])->pluck('unit_id')->first();
                            $rate = QuotationMaterial::where('quotation_id',$data['quotation_id'])->where('material_id',$material['material_id'])->pluck('rate_per_unit')->first();
                            if($rate ==  null || $fromUnit == null){
                                $conversion = UnitHelper::unitConversion($material['unit_id'],$data['material_unit'][$material['material_id']],$data['material_rate'][$material['material_id']]);
                            }else{
                                $conversion = UnitHelper::unitConversion($fromUnit,$$data['material_unit'][$material['material_id']],$rate);
                            }
                            if(!(is_array($conversion))){
                                $productAmount = $productAmount + ($conversion * $material['quantity']);
                            }else{
                                $productAmount = $productAmount + ($conversion['rate'] * $material['quantity']);
                            }
                        }
                    }
                    if($request->has('profit_margins')){
                        foreach($data['profit_margins'][$productId] as $profitMarginId => $percentage){
                            $productAmount = $productAmount + ($productAmount * ($percentage/100));
                        }
                    }else{
                        $quotationProductId = QuotationProduct::where('quotation_id',$request->quotation_id)->where('product_id',$productId)->pluck('id')->first();
                        if($quotationProductId == null){
                            $profitMarginPercentages = ProductProfitMarginRelation::join('profit_margin_versions','profit_margin_versions.id','=','products_profit_margins_relation.profit_margin_version_id')
                                                ->where('products_profit_margins_relation.product_version_id',$recentVersion)
                                                ->select('profit_margin_versions.percentage')
                                                ->get()
                                                ->toArray();
                            foreach($profitMarginPercentages as $percentage){
                                $productAmount = $productAmount + ($productAmount * ($percentage/100));
                            }
                        }else{
                            $profitMarginPercentages = QuotationProfitMarginVersion::where('quotation_product_id',$quotationProductId)->select('percentage')->get()->toArray();
                            foreach($profitMarginPercentages as $percentage){
                                $productAmount = $productAmount + ($productAmount * ($percentage['percentage']/100));
                            }
                        }
                    }
                }else{
                    if($request->has('material_rate')){
                        foreach($materials as $material){
                            $conversion = UnitHelper::unitConversion($material['unit_id'],$data['material_unit'][$material['material_id']],$data['material_rate'][$material['material_id']]);
                            if(!(is_array($conversion))){
                                $productAmount = $productAmount + ($conversion * $material['quantity']);
                            }else{
                                $productAmount = $productAmount + ($conversion['rate'] * $material['quantity']);
                            }
                        }
                    }else{
                        $productAmount = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                                                    ->where('product_material_relation.product_version_id',$recentVersion)
                                                    ->sum('material_versions.rate_per_unit');
                    }
                    if($request->has('profit_margins')){
                        foreach($data['profit_margins'][$productId] as $profitMarginId => $percentage){
                            $productAmount = $productAmount + ($productAmount * ($percentage/100));
                        }
                    }else{
                        $profitMarginPercentages = ProductProfitMarginRelation::join('profit_margin_versions','profit_margin_versions.id','=','products_profit_margins_relation.profit_margin_version_id')
                                                                ->where('products_profit_margins_relation.product_version_id',$recentVersion)
                                                                ->select('profit_margin_versions.percentage as percentage')
                                                                ->get()
                                                                ->toArray();
                        foreach($profitMarginPercentages as $percentage){
                            $productAmount = $productAmount + ($productAmount * ($percentage['percentage']/100));
                        }
                    }
                }
                if($request->has('clientSuppliedMaterial')){
                    foreach($data['clientSuppliedMaterial'] as $materialId){
                        if(in_array($materialId,$productMaterialIds)){
                            $materialQuantity = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                                ->join('materials','materials.id','=','material_versions.material_id')
                                ->where('materials.id','=',$materialId)
                                ->where('product_material_relation.product_version_id',$recentVersion)
                                ->pluck('product_material_relation.material_quantity')
                                ->first();
                            $productAmount = $productAmount - ($materialQuantity * $data['material_rate'][$materialId]);
                        }
                    }
                }
                $response['amount'][$productId] = $productAmount;
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Product Calculations',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = array();
        }
        return response()->json($response,$status);
    }

    public function editQuotation(Request $request, $quotation){
        try{
            $data = $request->all();
            $quotationData = array();
            $quotationData['discount'] = $data['discount'];
            $quotationData['is_tax_applied'] = true;
            if(in_array(!null,$data['product_summary'])){
                $quotationData['is_summary_applied'] = true;
            }
            $quotation->update($quotationData);
            $taxData = array();
            $taxData['quotation_id'] = $quotation['id'];
            foreach($data['tax'] as $taxId => $taxPercentage){
                $taxData['tax_id'] = $taxId;
                $taxData['percentage'] = $taxPercentage;
                QuotationTaxVersion::create($taxData);
            }
            foreach($quotation->quotation_products as $quotationProduct){
                foreach($quotationProduct->quotation_profit_margins as $quotationProfitMargin){
                    $quotationProfitMargin->delete();
                }
                $quotationProduct->delete();
            }

            foreach($data['product_id'] as $productId){
                $quotationProductData = array();
                $quotationProductData['product_id'] = $productId;
                $quotationProductData['quotation_id'] = $quotation['id'];
                $quotationProductData['description'] = $data['product_description'][$productId];
                $recentVersion = ProductVersion::where('product_id',$productId)->orderBy('created_at','desc')->pluck('id')->first();
                $productMaterialsId = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                    ->join('materials','materials.id','=','material_versions.material_id')
                    ->where('product_material_relation.product_version_id', $recentVersion)
                    ->pluck('materials.id')
                    ->toArray();
                $productMaterials = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                    ->join('materials','materials.id','=','material_versions.material_id')
                    ->where('product_material_relation.product_version_id', $recentVersion)
                    ->select('materials.id as id','product_material_relation.material_quantity as material_quantity','material_versions.unit_id as unit_id')
                    ->get()
                    ->toArray();
                $productAmount = 0;
                foreach($productMaterials as $material){
                    if($request->has('material_unit') && $request->has('material_rate')){
                        if($data['material_unit'][$material['id']] == $material['unit_id']){
                            $rateConversion = $data['material_rate'][$material['id']];
                        }else{
                            $rateConversion = UnitHelper::unitConversion($data['material_unit'][$material['id']],$material['unit_id'],$data['material_rate'][$material['id']]);
                            if(is_array($rateConversion)){
                                $request->session()->flash('error','Unit Conversion is invalid');
                                return redirect('/quotation/edit/'.$quotation['id']);
                            }
                        }
                    }else{
                        $quotationMaterialDetails = QuotationMaterial::where('quotation_id',$quotation['id'])->where('material_id',$material['id'])->first()->toArray();
                        if($quotationMaterialDetails['unit_id'] == $material['unit_id']){
                            $rateConversion = $quotationMaterialDetails['rate_per_unit'];
                        }else{
                            $rateConversion = UnitHelper::unitConversion($quotationMaterialDetails['unit_id'],$material['unit_id'],$quotationMaterialDetails['rate_per_unit']);
                            if(is_array($rateConversion)){
                                $request->session()->flash('error','Unit Conversion is invalid');
                                return redirect('/quotation/edit/'.$quotation['id']);
                            }
                        }
                    }
                    $productAmount = $productAmount + ($rateConversion * $material['material_quantity']);
                }
                $quotationProductData['rate_per_unit'] = $productAmount;
                $quotationProductData['quantity'] = $data['product_quantity'][$productId];
                if(isset($quotationData['is_summary_applied']) && $quotationData['is_summary_applied'] == true){
                    if(array_key_exists($productId,$data['product_summary'])){
                        $quotationProductData['summary_id'] = $data['product_summary'][$productId];
                    }
                }
                $quotationProduct = QuotationProduct::create($quotationProductData);
                foreach($data['profit_margins'][$productId] as $id => $percentage){
                    $quotationProfitMarginData = array();
                    $quotationProfitMarginData['profit_margin_id'] = $id;
                    $quotationProfitMarginData['percentage'] = $percentage;
                    $quotationProfitMarginData['quotation_product_id'] = $quotationProduct->id;
                    QuotationProfitMarginVersion::create($quotationProfitMarginData);
                    $productAmount = $productAmount + ($productAmount * ($percentage / 100));
                }
                if($request->has('material_rate') && $request->has('material_unit')){
                    foreach($quotation->quotation_materials as $quotationMaterial){
                        $quotationMaterial->delete();
                    }
                    $materialIds = array_keys($data['material_rate']);
                    foreach($materialIds as $materialId){
                        $quotationMaterialData = array();
                        $quotationMaterialData['material_id'] = $materialId;
                        $quotationMaterialData['rate_per_unit'] = $data['material_rate'][$materialId];
                        $quotationMaterialData['unit_id'] = $data['material_unit'][$materialId];
                        if($request->has('clientSuppliedMaterial') && is_array($data['clientSuppliedMaterial']) && in_array($materialId,$data['clientSuppliedMaterial'])){
                            $quotationMaterialData['is_client_supplied'] = true;
                        }else{
                            $quotationMaterialData['is_client_supplied'] = false;
                        }
                        $quotationMaterialData['quotation_id'] = $quotation['id'];
                        QuotationMaterial::create($quotationMaterialData);
                    }
                    if($request->has('clientSuppliedMaterial') && is_array($data['clientSuppliedMaterial']) && (count(array_intersect($productMaterialsId,$data['clientSuppliedMaterial'])) > 0)){
                        foreach($productMaterialsId as $materialId){
                            if(in_array($materialId,$data['clientSuppliedMaterial'])){
                                $materialQuantity = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                                    ->join('materials','materials.id','=','material_versions.material_id')
                                    ->where('materials.id','=',$materialId)
                                    ->where('product_material_relation.product_version_id',$recentVersion)
                                    ->pluck('product_material_relation.material_quantity')
                                    ->first();
                                $productAmount = $productAmount - ($materialQuantity * $data['material_rate'][$materialId]);
                            }
                        }
                    }
                }else{
                    $clientSuppliedMaterials = QuotationMaterial::where('quotation_id',$quotation['id'])->whereIn('material_id',$productMaterialsId)->where('is_client_supplied', true)->select('id','rate_per_unit')->get();
                    if($clientSuppliedMaterials != null){
                        foreach($clientSuppliedMaterials as $material){
                            $materialQuantity = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                                                        ->join('materials','materials.id','=','material_versions.material_id')
                                                        ->where('materials.id','=',$material['id'])
                                                        ->where('product_material_relation.product_version_id',$recentVersion)
                                                        ->pluck('product_material_relation.material_quantity')
                                                        ->first();
                            $productAmount = $productAmount - ($materialQuantity * $material['rate_per_unit']);
                        }
                    }
                }
                QuotationProduct::where('id',$quotationProduct->id)->update(['rate_per_unit' => $productAmount]);
            }
            $request->session()->flash('success','Quotation Edited Successfully');
            return redirect('/quotation/edit/'.$quotation->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Quotation',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }
}