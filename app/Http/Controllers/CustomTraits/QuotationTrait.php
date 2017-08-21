<?php
/**
 * Created by Ameya Joshi.
 * Date: 5/6/17
 * Time: 4:19 PM
 */

namespace App\Http\Controllers\CustomTraits;

use App\BillQuotationProducts;
use App\Category;
use App\Client;
use App\ExtraItem;
use App\Helper\NumberHelper;
use App\Helper\MaterialProductHelper;
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
use App\QuotationExtraItem;
use App\QuotationMaterial;
use App\QuotationProduct;
use App\QuotationProfitMarginVersion;
use App\QuotationStatus;
use App\QuotationTaxVersion;
use App\QuotationWorkOrder;
use App\Summary;
use App\Tax;
use App\Unit;
use App\WorkOrderImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

trait QuotationTrait{

    public function getCreateView(Request $request){
        try{
            $clients = Client::where('is_active', true)->select('id','company')->get()->toArray();
            $categories = Category::orderBy('name','asc')->where('is_active', true)->select('id','name')->get()->toArray();
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
            $categories = Category::orderBy('name','asc')->where('is_active', true)->select('id','name')->get()->toArray();
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
                $productMaterialIds = ProductMaterialRelation::join('material_versions','product_material_relation.material_version_id','=','material_versions.id')
                    ->join('materials','materials.id','=','material_versions.material_id')
                    ->where('product_material_relation.product_version_id',$recentVersionId)
                    ->pluck('materials.id')
                    ->toArray();
                $materialIds = array_unique(array_merge($materialIds,$productMaterialIds));
            }
            if($request->has('quotation_id')){
                $quotationMaterialIds = QuotationMaterial::where('quotation_id',$request->quotation_id)->whereIn('material_id',$materialIds)->pluck('material_id')->toArray();
                if($request->has('material_rate')){
                    $iterator = 0;
                    $materials = array();
                    foreach($request->material_rate as $materialId => $materialRate){
                        if(in_array($materialId,$materialIds)){
                            $materials[$iterator]['id'] = $materialId;
                            $materials[$iterator]['name'] = Material::where('id',$materialId)->pluck('name')->first();
                            $materials[$iterator]['rate_per_unit'] = $materialRate;
                            $materials[$iterator]['unit_id'] = $request->material_unit[$materialId];
                            $iterator++;
                        }
                    }
                    if(count(array_diff($materialIds,array_keys($request->material_rate))) > 0){
                        $newMaterials = array_diff($materialIds,array_keys($request->material_rate));
                        foreach($newMaterials as $newMaterialId){
                            $materialInfo = Material::findOrFail($newMaterialId);
                            $materials[$iterator]['id'] = $newMaterialId;
                            $materials[$iterator]['name'] = $materialInfo->name;
                            $materials[$iterator]['rate_per_unit'] = $materialInfo->rate_per_unit;
                            $materials[$iterator]['unit_id'] = $materialInfo->unit_id;
                            $iterator++;
                        }
                    }
                }else{
                    $materials = QuotationMaterial::join('materials','materials.id','=','quotation_materials.material_id')
                        ->join('units','units.id','=','quotation_materials.unit_id')
                        ->where('quotation_id',$request->quotation_id)
                        ->whereIn('quotation_materials.material_id',$materialIds)
                        ->select('materials.id as id','materials.name as name','quotation_materials.rate_per_unit as rate_per_unit','quotation_materials.unit_id as unit_id','units.name as unit','quotation_materials.is_client_supplied as is_client_supplied')
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
                        if(in_array($materialId,$materialIds)){
                            $materials[$iterator]['id'] = $materialId;
                            $materials[$iterator]['name'] = Material::where('id',$materialId)->pluck('name')->first();
                            $materials[$iterator]['rate_per_unit'] = $materialRate;
                            $materials[$iterator]['unit_id'] = $request->material_unit[$materialId];
                            $materials[$iterator]['unit'] = Unit::where('id',$request->material_unit[$materialId]);
                            $iterator++;
                        }
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
                $isEdit = true;
                $quotationDraftStatusId = QuotationStatus::where('slug','draft')->pluck('id')->first();
                $quotationStatusId = Quotation::where('id',$request->quotation_id)->pluck('quotation_status_id')->first();
                if($quotationDraftStatusId != $quotationStatusId && $quotationStatusId != null){
                    $hideSubmit = true;
                }else{
                    $hideSubmit = false;
                }
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
                        if($productProfitMarginRelation != null && (count($productProfitMarginRelation) > 0)){
                            foreach($productProfitMarginRelation as $profitMargin){
                                $productProfitMargins[$id]['profit_margin'][$profitMargin['id']] = $profitMargin->percentage;
                            }
                        }else{
                            $structureProfitMargins = ProfitMargin::where('is_active', true)->orderBy('id','asc')->select('id','base_percentage as percentage')->get();
                            foreach($structureProfitMargins as $profitMargin){
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
                $isEdit = false;
                $hideSubmit = false;
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
            return view('partials.quotation.profit-margin-table')->with(compact('productProfitMargins','profitMargins','isEdit','hideSubmit'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Add Product Row',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function quotationListing(Request $request, $status){
        try{
            $records = array();
            $records['data'] = array();
            $quotations = Quotation::where('quotation_status_id','=', $status)->orderBy('updated_at','desc')->get();
            $end = $request->length < 0 ? count($quotations) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($quotations); $iterator++,$pagination++ ){
                if($quotations[$pagination]->quotation_status->slug == 'draft'){
                    $quotationStatus = '<td><span class="btn btn-xs btn-warning"> Draft </span></td>';
                }elseif($quotations[$pagination]->quotation_status->slug == 'approved'){
                    $quotationStatus = '<td><span class="btn btn-xs green-meadow"> Approved </span></td>';
                }else{
                    $quotationStatus = '<td><span class="btn btn-xs btn-danger"> Disapproved </span></td>';
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

    public function createQuotation(Request $request){
        try{
            $data = $request->all();
            $quotationData = array();
            $quotationData['project_site_id'] = $data['project_site_id'];
            $quotationMaterialIds = array();
            if($request->ajax()){
                $response = array();
                if($request->has('quotation_id')){
                    $quotation = Quotation::findOrFail($request->quotation_id);
                    $quotation->update($quotationData);
                    $quotationProduct = QuotationProduct::where('quotation_id',$quotation->id)->where('product_id',$request->product_id[0])->first();
                    if($quotationProduct != null){
                        foreach($quotationProduct->quotation_profit_margins as $quotationProfitMargin){
                            QuotationProfitMarginVersion::where('id',$quotationProfitMargin['id'])->delete();
                        }
                        if($quotationProduct->product_version_id != null){
                            $usedProductVersion[$quotationProduct->product_id] = $quotationProduct->product_version_id;
                        }
                        QuotationProduct::where('id',$quotationProduct['id'])->delete();
                    }
                }else{
                    $quotation = Quotation::where('project_site_id', $data['project_site_id'])->first();
                    if($quotation == null){
                        $quotation = Quotation::create($quotationData);
                    }
                }
                $response['quotation_id'] = $quotation->id;
            }else{
                $draftStatusId = QuotationStatus::where('slug','draft')->pluck('id')->first();
                $quotationData['quotation_status_id'] = $draftStatusId;
                if($request->has('quotation_id')){
                    $quotation = Quotation::findOrFail($request->quotation_id);
                    $quotation->update($quotationData);
                    foreach($quotation->quotation_products as $quotationProduct){
                        foreach($quotationProduct->quotation_profit_margins as $quotationProfitMargin){
                            QuotationProfitMarginVersion::where('id',$quotationProfitMargin['id'])->delete();
                        }
                        if($quotationProduct->product_version_id != null){
                            $usedProductVersion[$quotationProduct->product_id] = $quotationProduct->product_version_id;
                        }
                        QuotationProduct::where('id',$quotationProduct['id'])->delete();
                    }
                    foreach($quotation->quotation_materials as $quotationMaterial){
                        QuotationMaterial::where('id',$quotationMaterial['id'])->delete();
                    }
                }else{
                    $quotation = Quotation::where('project_site_id', $data['project_site_id'])->first();
                    if($quotation != null){
                        $quotation->update($quotationData);
                    }else{
                        $quotation = Quotation::create($quotationData);
                    }
                }
            }
            $quotation = $quotation->toArray();
            foreach($data['product_id'] as $productId){
                $response['product_id'] = $productId;
                $response['product_description'] = $request->product_description[$productId];
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
                        $rateConversion = UnitHelper::unitConversion($data['material_unit'][$material['id']],$material['unit_id'],$data['material_rate'][$material['id']]);
                        if(is_array($rateConversion)){
                            $request->session()->flash('error',$rateConversion['message']);
                            Quotation::where('id',$quotation['id'])->delete();
                            return redirect('/quotation/create');
                        }
                    }
                    $productAmount = $productAmount + ($rateConversion * $material['material_quantity']);
                }
                $quotationProductData['rate_per_unit'] = $productAmount;
                $quotationProductData['quantity'] = $data['product_quantity'][$productId];
                $productRecentVersion = ProductVersion::where('product_id',$productId)->orderBy('created_at','desc')->pluck('id')->first();
                $quotationProductData['product_version_id'] = $productRecentVersion;
                $quotationProduct = QuotationProduct::where('quotation_id',$quotationProductData['quotation_id'])->where('product_id',$quotationProductData['product_id'])->first();
                if($quotationProduct == null){
                    $quotationProduct = QuotationProduct::create($quotationProductData);
                }else{
                    $quotationProduct->update($quotationProductData);
                }
                $profitMarginAmount = 0;
                foreach($data['profit_margins'][$productId] as $id => $percentage){
                    $quotationProfitMarginData = array();
                    $quotationProfitMarginData['profit_margin_id'] = $id;
                    $quotationProfitMarginData['percentage'] = $percentage;
                    $quotationProfitMarginData['quotation_product_id'] = $quotationProduct->id;
                    QuotationProfitMarginVersion::create($quotationProfitMarginData);
                    $profitMarginAmount = round($profitMarginAmount + ($productAmount * ($percentage / 100)),3);
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
                            $productAmount = round($productAmount - ($materialQuantity * $data['material_rate'][$materialId]),3);
                        }
                    }
                }
                $productAmount = round(($productAmount + $profitMarginAmount),3);
                QuotationProduct::where('id',$quotationProduct->id)->update(['rate_per_unit' => $productAmount]);
                $response['product_amount'] = $productAmount;
            }
            $materialIds = array_keys($data['material_rate']);
            foreach($materialIds as $materialId){
                $quotationMaterialData = array();
                $quotationMaterialData['material_id'] = $materialId;
                if($request->ajax()){
                    $material = Material::findOrFail($materialId);
                    $rateConversion = UnitHelper::unitConversion($data['material_unit'][$materialId],$material['unit_id'],$data['material_rate'][$materialId]);
                    if(!is_array($rateConversion)){
                        $quotationMaterialData['rate_per_unit'] = round($rateConversion,3);
                        $quotationMaterialData['unit_id'] = $material['unit_id'];
                    }else{
                        // If conversion is array
                    }
                }else{
                    $quotationMaterialData['rate_per_unit'] = round($data['material_rate'][$materialId],3);
                    $quotationMaterialData['unit_id'] = $data['material_unit'][$materialId];
                }
                if($request->has('clientSuppliedMaterial') && is_array($data['clientSuppliedMaterial']) && in_array($materialId,$data['clientSuppliedMaterial'])){
                    $quotationMaterialData['is_client_supplied'] = true;
                }else{
                    $quotationMaterialData['is_client_supplied'] = false;
                }
                $quotationMaterialData['quotation_id'] = $quotation['id'];
                $quotationMaterial = QuotationMaterial::where('quotation_id',$request->quotation_id)->where('material_id',$materialId)->first();
                if($quotationMaterial == null){
                    $quotationMaterial = QuotationMaterial::create($quotationMaterialData);
                }else{
                    $quotationMaterial->update($quotationMaterialData);
                }

                $quotationMaterialIds[$materialId] = $quotationMaterial->id;
            }
            if($request->ajax()){
                $status = 200;
                return response()->json($response,$status);
            }else{
                $request->session()->flash('success','Quotation created successfully.');
                return redirect('/quotation/create');
            }
        }catch(\Exception $e){
            $status = 500;
            $response = [
                'message' => "Something went wrong !!",                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    'message' => 'Something went wrong.'
            ];
            $data = [
                'action' => 'Create Quotation',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            if($request->ajax()){
                return response()->json($response,$status);
            }else{
                abort(500);
            }
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
            $quotationProjectSiteIds = Quotation::whereNotNull('quotation_status_id')->pluck('project_site_id')->toArray();
            $projectSites = ProjectSite::where('project_id',$projectId)->whereNotIn('id',$quotationProjectSiteIds)->select('id','name')->get();
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
            $user = Auth::user();
            $userRole = $user->roles[0]->role->slug;
            $orderValue = QuotationProduct::where('quotation_id',$quotation->id)->select(DB::raw('sum(rate_per_unit * quantity)'))->first();
            $orderValue = $orderValue->sum;
            if($quotation->quotation_status->slug == 'approved'){
                if($quotation->work_order != null){
                    $quotation->work_order->images = $this->getWorkOrderImagePath($quotation->id,$quotation->work_order->images);
                }
            }
            $quotationProducts = array();
            $iterator = 0;
            foreach($quotation->quotation_products as $quotationProduct){
                $quotationProducts[$iterator]['product_id'] = $quotationProduct['product_id'];
                $productBillCount = BillQuotationProducts::join('bills','bills.id','=','bill_quotation_products.bill_id')
                                ->join('quotations','quotations.id','=','bills.quotation_id')
                                ->join('quotation_products',function($join){
                                    $join->on('quotation_products.quotation_id','=','quotations.id');
                                    $join->on('quotation_products.id','=','bill_quotation_products.quotation_product_id');
                                })
                                ->where('quotation_products.product_id',$quotationProduct['product_id'])
                                ->where('bills.quotation_id',$quotation['id'])
                                ->pluck('bill_quotation_products.quantity')
                                ->first();
                if($productBillCount == null){
                    $quotationProducts[$iterator]['product_bill_count'] = 0;
                }else{
                    $quotationProducts[$iterator]['product_bill_count'] = $productBillCount;
                }
                $iterator++;
            }
            $quotationProducts = json_encode($quotationProducts);
            $summaries = Summary::where('is_active', true)->select('id','name')->get();
            if($quotation->is_tax_applied == true){
                $taxes = QuotationTaxVersion::join('taxes','taxes.id','=','quotation_tax_versions.tax_id')
                    ->where('quotation_id',$quotation->id)
                    ->select('taxes.id as id','taxes.name as name','quotation_tax_versions.percentage as base_percentage')
                    ->get();
            }else{
                $taxes = Tax::where('is_active', true)->select('id','name','base_percentage')->get();
            }
            $taxAmount = 0;
            foreach($taxes as $tax){
                $taxAmount = $taxAmount + round(($orderValue * ($tax['base_percentage'] / 100)),3);
            }
            $orderValue = $orderValue + $taxAmount;
            $extraItems = QuotationExtraItem::join('extra_items','extra_items.id','=','quotation_extra_items.extra_item_id')
                                            ->where('quotation_extra_items.quotation_id',$quotation['id'])
                                            ->select('quotation_extra_items.extra_item_id as id','quotation_extra_items.rate as rate','extra_items.name as name')
                                            ->get();
            if($extraItems == null){
                $extraItems = ExtraItem::where('is_active', true)->select('id','name','rate')->orderBy('name','asc')->get();
            }else{
                $extraItems = $extraItems->toArray();
                $newExtraItems = QuotationExtraItem::join('extra_items','extra_items.id','!=','quotation_extra_items.extra_item_id')
                    ->where('extra_items.is_active', true)
                    ->select('extra_items.id as id','extra_items.rate as rate','extra_items.name as name')
                    ->get();
                if($newExtraItems != null){
                    $newExtraItems = $newExtraItems->toArray();
                    $extraItems = array_merge($extraItems,$newExtraItems);
                }
            }
            return view('admin.quotation.edit')->with(compact('quotation','summaries','taxes','orderValue','user','quotationProducts','extraItems'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Quotation Edit View',
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
                    ->select('material_versions.material_id as material_id','material_versions.unit_id as unit_id','product_material_relation.material_quantity as quantity')
                    ->get()
                    ->toArray();
                $productAmount = 0;
                if($request->has('quotation_id')){
                    if($request->has('material_rate')){
                        foreach($materials as $material){
                            $conversion = UnitHelper::unitConversion($data['material_unit'][$material['material_id']],$material['unit_id'],$data['material_rate'][$material['material_id']]);
                            if(!(is_array($conversion))){
                                $productAmount = $productAmount + ($conversion * $material['quantity']);
                            }else{
                                $status = 201;
                                $request->session()->flash('error',$conversion['message']);
                                $response['message'] = $conversion['message'];
                                return response()->json($response,$status);
                            }
                        }
                    }else{
                        foreach($materials as $material){
                            $fromUnit = QuotationMaterial::where('quotation_id',$data['quotation_id'])->where('material_id',$material['material_id'])->pluck('unit_id')->first();
                            $rate = QuotationMaterial::where('quotation_id',$data['quotation_id'])->where('material_id',$material['material_id'])->pluck('rate_per_unit')->first();
                            if($rate ==  null || $fromUnit == null){
                                $newMaterial = Material::findOrFail($material['material_id']);
                                $conversion = UnitHelper::unitConversion($newMaterial['unit_id'],$material['unit_id'],$data['material_rate'][$material['material_id']]);
                            }else{
                                $conversion = UnitHelper::unitConversion($fromUnit,$data['material_unit'][$material['material_id']],$rate);
                            }
                            if(!(is_array($conversion))){
                                $productAmount = $productAmount + ($conversion * $material['quantity']);
                            }else{
                                $status = 201;
                                $response = ['message'=>$conversion['message']];
                                $request->session()->flash('error',$conversion['message']);
                                return response()->json($response,$status);
                            }
                        }
                    }
                    $profitMarginAmount = 0;
                    if($request->has('profit_margins')){
                        foreach($data['profit_margins'][$productId] as $profitMarginId => $percentage){
                            $profitMarginAmount =  $profitMarginAmount + ($productAmount * ($percentage/100));
                        }

                    }else{
                        $quotationProductId = QuotationProduct::where('quotation_id',$request->quotation_id)->where('product_id',$productId)->pluck('id')->first();
                        if($quotationProductId == null){
                            $profitMarginPercentages = ProductProfitMarginRelation::join('profit_margin_versions','profit_margin_versions.id','=','products_profit_margins_relation.profit_margin_version_id')
                                ->where('products_profit_margins_relation.product_version_id',$recentVersion)
                                ->select('products_profit_margins_relation.profit_margin_version_id as profit_margin_version_id','profit_margin_versions.percentage as percentage')
                                ->distinct('profit_margin_version_id')
                                ->get()
                                ->toArray();
                            foreach($profitMarginPercentages as $percentage){
                                $profitMarginAmount =  $profitMarginAmount + ($productAmount * ($percentage['percentage']/100));
                            }
                        }else{
                            $profitMarginPercentages = QuotationProfitMarginVersion::where('quotation_product_id',$quotationProductId)->select('percentage')->get()->toArray();
                            foreach($profitMarginPercentages as $percentage){
                                $profitMarginAmount =  $profitMarginAmount + ($productAmount * ($percentage['percentage']/100));
                            }
                        }
                    }
                }else{
                    if($request->has('material_rate')){
                        foreach($materials as $material){
                            $conversion = UnitHelper::unitConversion($data['material_unit'][$material['material_id']],$material['unit_id'],$data['material_rate'][$material['material_id']]);
                            if(!(is_array($conversion))){
                                $productAmount = $productAmount + ($conversion * $material['quantity']);
                            }else{
                                $request->session()->flash('error',$conversion['message']);
                                $status = 201;
                                $response['message'] = $conversion['message'];
                                return response()->json($response,$status);
                            }
                        }
                    }else{
                        $productAmount = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                            ->where('product_material_relation.product_version_id',$recentVersion)
                            ->sum('material_versions.rate_per_unit');
                    }
                    $profitMarginAmount = 0;
                    if($request->has('profit_margins')){
                        foreach($data['profit_margins'][$productId] as $profitMarginId => $percentage){
                            $profitMarginAmount =  $profitMarginAmount + ($productAmount * ($percentage/100));
                        }
                    }else{
                        $profitMarginPercentages = ProductProfitMarginRelation::join('profit_margin_versions','profit_margin_versions.id','=','products_profit_margins_relation.profit_margin_version_id')
                            ->where('products_profit_margins_relation.product_version_id',$recentVersion)
                            ->select('products_profit_margins_relation.profit_margin_version_id as profit_margin_version_id','profit_margin_versions.percentage as percentage')
                            ->distinct('profit_margin_version_id')
                            ->get()
                            ->toArray();
                        foreach($profitMarginPercentages as $percentage){
                            $profitMarginAmount =  $profitMarginAmount + ($productAmount * ($percentage['percentage']/100));
                        }
                    }
                }
                $productAmount = $productAmount + $profitMarginAmount;
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
                $response['amount'][$productId] = round($productAmount,3);
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
            if($request->has('tax')){
                $taxData = array();
                $quotationData['is_tax_applied'] = true;
                $taxData['quotation_id'] = $quotation['id'];
                QuotationTaxVersion::where('quotation_id',$quotation->id)->delete();
                foreach($data['tax'] as $taxId => $taxPercentage){
                    $taxData['tax_id'] = $taxId;
                    $taxData['percentage'] = $taxPercentage;
                    QuotationTaxVersion::create($taxData);
                }
            }
            $quotationData['built_up_area'] = $data['built_up_area'];
            if(in_array(!null,$data['product_summary'])){
                $quotationData['is_summary_applied'] = true;
            }
            $quotation->update($quotationData);
            $quotationProductsProdcutIds = QuotationProduct::where('quotation_id',$quotation['id'])->pluck('product_id')->toArray();
            $removedProducts = array_diff($quotationProductsProdcutIds,$data['product_id']);
            if(count($removedProducts) > 0){
                foreach($removedProducts as $removedProductId){
                    $removedQuotationProduct = QuotationProduct::where('quotation_id',$quotation['id'])->where('product_id',$removedProductId)->first();
                    foreach ($removedQuotationProduct->quotation_profit_margins as $quotationProfitMargin){
                        $quotationProfitMargin->delete();
                    }
                    $removedQuotationProduct->delete();
                }
            }
            foreach($data['product_id'] as $productId){
                $quotationProductData = array();
                $quotationProductData['product_id'] = $productId;
                $quotationProductData['quotation_id'] = $quotation['id'];
                $quotationProduct = QuotationProduct::where('quotation_id',$quotation['id'])->where('product_id',$productId)->first();
                if($quotationProduct != null && $quotationProduct['product_version_id'] != null){
                    $recentVersion = $quotationProduct['product_version_id'];
                }else{
                    $recentVersion = ProductVersion::where('product_id',$productId)->orderBy('created_at','desc')->pluck('id')->first();
                    $quotationProductData['product_version_id'] = $recentVersion;
                }
                $quotationProductData['description'] = $data['product_description'][$productId];
                $productMaterialsId = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                    ->join('materials','materials.id','=','material_versions.material_id')
                    ->where('product_material_relation.product_version_id', $recentVersion)
                    ->pluck('materials.id')
                    ->toArray();
                $productMaterials = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                    ->join('materials','materials.id','=','material_versions.material_id')
                    ->where('product_material_relation.product_version_id', $recentVersion)
                    ->select('materials.id as id','product_material_relation.material_quantity as material_quantity','material_versions.unit_id as unit_id','materials.rate_per_unit as material_rate_per_unit','materials.unit_id as material_unit_id')
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
                                $request->session()->flash('error',$rateConversion['message']);
                                return redirect('/quotation/edit/'.$quotation['id']);
                            }
                        }
                    }else{
                        $quotationMaterialDetails = QuotationMaterial::where('quotation_id',$quotation['id'])->where('material_id',$material['id'])->first();
                        if($quotationMaterialDetails == null){
                            $quotationMaterialData = array();
                            $quotationMaterialData['material_id'] = $material['id'];
                            $quotationMaterialData['rate_per_unit'] = $material['material_rate_per_unit'];
                            $quotationMaterialData['unit_id'] = $material['material_unit_id'];
                            if($request->has('clientSuppliedMaterial') && is_array($data['clientSuppliedMaterial']) && in_array($material['id'],$data['clientSuppliedMaterial'])){
                                $quotationMaterialData['is_client_supplied'] = true;
                            }else{
                                $quotationMaterialData['is_client_supplied'] = false;
                            }
                            $quotationMaterialData['quotation_id'] = $quotation['id'];
                            $quotationMaterialDetails = QuotationMaterial::create($quotationMaterialData);
                        }
                        if($quotationMaterialDetails['unit_id'] == $material['unit_id']){
                            $rateConversion = $quotationMaterialDetails['rate_per_unit'];
                        }else{
                            $rateConversion = UnitHelper::unitConversion($quotationMaterialDetails['unit_id'],$material['unit_id'],$quotationMaterialDetails['rate_per_unit']);
                            if(is_array($rateConversion)){
                                $request->session()->flash('error',$rateConversion['message']);
                                return redirect('/quotation/edit/'.$quotation['id']);
                            }
                        }
                    }
                    $productAmount = round($productAmount + ($rateConversion * $material['material_quantity']),3);
                }
                $quotationProductData['rate_per_unit'] = $productAmount;
                $quotationProductData['quantity'] = $data['product_quantity'][$productId];
                if(isset($quotationData['is_summary_applied']) && $quotationData['is_summary_applied'] == true){
                    if(array_key_exists($productId,$data['product_summary'])){
                        $quotationProductData['summary_id'] = $data['product_summary'][$productId];
                    }
                }
                $quotationProduct = QuotationProduct::where('quotation_id',$quotationProductData['quotation_id'])->where('product_id',$quotationProductData['product_id'])->first();
                if($quotationProduct == null){
                    $quotationProduct = QuotationProduct::create($quotationProductData);
                }else{
                    $quotationProduct->update($quotationProductData);
                }
                $profitMarginAmount = 0;
                foreach($data['profit_margins'][$productId] as $id => $percentage){
                    $quotationProfitMarginData = array();
                    $quotationProfitMarginData['profit_margin_id'] = $id;
                    $quotationProfitMarginData['percentage'] = $percentage;
                    $quotationProfitMarginData['quotation_product_id'] = $quotationProduct->id;
                    $quotationProfitMargin = QuotationProfitMarginVersion::where('profit_margin_id',$id)->where('quotation_product_id', $quotationProduct->id)->first();
                    if($quotationProfitMargin == null){
                        QuotationProfitMarginVersion::create($quotationProfitMarginData);

                    }else{
                        $quotationProfitMargin->update($quotationProfitMarginData);
                    }
                    $profitMarginAmount = round($profitMarginAmount + ($productAmount * ($percentage / 100)),3);
                }
                $productAmount = round(($productAmount + $profitMarginAmount),3);
                if($request->has('material_rate') && $request->has('material_unit')){
                    $materialIds = array_keys($data['material_rate']);
                    $quotationMaterialIds = QuotationMaterial::where('quotation_id',$quotation['id'])->pluck('material_id')->toArray();
                    $removedQuotationMaterialIds = array_diff($quotationMaterialIds, $materialIds);
                    if(count($removedQuotationMaterialIds) > 0){
                        foreach($removedQuotationMaterialIds as $removedQuotationMaterialId){
                            QuotationMaterial::where('quotation_id', $quotation['id'])->where('material_id',$removedQuotationMaterialId)->delete();
                        }
                    }
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
                        $quotationMaterial = QuotationMaterial::where('quotation_id',$quotation['id'])->where('material_id',$materialId)->first();
                        if($quotationMaterial == null){
                            QuotationMaterial::create($quotationMaterialData);
                        }else{
                            $quotationMaterial->update($quotationMaterialData);
                        }
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
                                $productAmount = round($productAmount - ($materialQuantity * $data['material_rate'][$materialId]),3);
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
                            $productAmount = round($productAmount - ($materialQuantity * $material['rate_per_unit']),3);
                        }
                    }
                }
                QuotationProduct::where('id',$quotationProduct->id)->update(['rate_per_unit' => round($productAmount,3)]);
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

    public function generateQuotationPdf(Request $request,$quotation,$slug,$summarySlug){
        try{
            $data = $summary_data = array();
            $data['summary_slug'] = $summarySlug;
            $data['quotation'] = $quotation;
            $data['slug'] = $slug;
            $quotationProductData = array();
            $iterator = $total = $j =0;
            $data['company_name'] = $quotation->project_site->project->client->company;
            $distinct_summaryIds = QuotationProduct::where('quotation_id',$quotation->id)->distinct('summary_id')->select('summary_id')->get();
            foreach($quotation->quotation_products as $quotationProducts){
                $quotationProductData[$iterator]['summary_id'] = $quotationProducts->summary_id;
                $quotationProductData[$iterator]['product_name'] = $quotationProducts->product->name;
                $quotationProductData[$iterator]['category_id'] = $quotationProducts->product->category_id;
                $quotationProductData[$iterator]['category_name'] = $quotationProducts->product->category->name;
                $quotationProductData[$iterator]['quantity'] = $quotationProducts->quantity;
                $quotationProductData[$iterator]['unit'] = $quotationProducts->product->unit->name;
                $quotationProductData[$iterator]['rate'] = round(($quotationProducts->rate_per_unit - ($quotationProducts->rate_per_unit * ($quotationProducts->quotation->discount / 100))),3);
                $quotationProductData[$iterator]['amount'] = round(($quotationProductData[$iterator]['rate'] * $quotationProductData[$iterator]['quantity']),3);
                $total = $total + $quotationProductData[$iterator]['amount'];
                $iterator++;
            }
            usort($quotationProductData, function($a, $b) {
                return $a['category_id'] > $b['category_id'];
            });
            foreach($distinct_summaryIds as $key => $distinct_summary_id){
                $summary_data[$j]['summary_amount'] = $amount = $i = 0;
                $summary_data[$j]['summary_id'] = $distinct_summary_id['summary_id'];
                $summary_data[$j]['summary_name'] = Summary::where('id',$distinct_summary_id['summary_id'])->pluck('name')->first();
                foreach($quotationProductData as $k => $productData){
                    if($distinct_summary_id['summary_id'] == $productData['summary_id']){
                        $summary_data[$j]['products'][$i] = $productData;
                        $amount = $amount +  $productData['amount'];
                        $i++;
                    }
                }
                $summary_data[$j]['summary_amount'] = $summary_data[$j]['summary_amount'] + $amount;
                $j++;
            }
            $data['summary_data'] = $summary_data;
            $rounded_amount = $total;
            if($data['slug'] == 'with-tax'){
                $taxData = array();
                $i = 0;
                foreach($quotation->tax_version as $key => $tax){
                    $taxData[$i]['id'] = $tax->id;
                    $taxData[$i]['name'] = $tax->tax->name;
                    $taxData[$i]['percentage'] = abs($tax->percentage);
                    $taxData[$i]['tax_amount'] = round($total * ($tax->percentage / 100) , 3);
                    $rounded_amount = $rounded_amount + $taxData[$i]['tax_amount'];
                    $i++;
                }
                $data['taxData'] = $taxData;
            }
            $data['total'] = $total;
            $data['rounded_total'] = round($rounded_amount);
            $data['amount_in_words'] = ucwords(NumberHelper::getIndianCurrency($data['rounded_total']));
            $data['quotationProductData'] = $quotationProductData;
            $data['quotation_no'] = "Q-".strtoupper(date('M',strtotime($quotation['created_at'])))."-".$quotation->id."/".date('y',strtotime($quotation['created_at']));
            $pdf = App::make('dompdf.wrapper');
            if($summarySlug == 'with-summary'){
                $pdf->loadHTML(view('admin.quotation.pdf.quotation',$data));
            }else{
                $pdf->loadHTML(view('admin.quotation.pdf.quotationWithoutSummary',$data));
            }
            return $pdf->stream();
        }catch (\Exception $e){
            $data = [
                'action' => 'Generate Quotation PDF',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function uploadTempWorkOrderImages(Request $request,$quotationId){
        try{
            $quotationDirectoryName = sha1($quotationId);
            $tempUploadPath = public_path().env('WORK_ORDER_TEMP_IMAGE_UPLOAD');
            $tempImageUploadPath = $tempUploadPath.DIRECTORY_SEPARATOR.$quotationDirectoryName;
            /* Create Upload Directory If Not Exists */
            if (!file_exists($tempImageUploadPath)) {
                File::makeDirectory($tempImageUploadPath, $mode = 0777, true, true);
            }
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
            $request->file('file')->move($tempImageUploadPath,$filename);
            $path = env('WORK_ORDER_TEMP_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$quotationDirectoryName.DIRECTORY_SEPARATOR.$filename;
            $response = [
                'jsonrpc' => '2.0',
                'result' => 'OK',
                'path' => $path
            ];
        }catch (\Exception $e){
            $response = [
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => 101,
                    'message' => 'Failed to open input stream.',
                ],
                'id' => 'id'
            ];
        }
        return response()->json($response);
    }

    public function displayWorkOrderImages(Request $request){
        try{
            $path = $request->path;
            $count = $request->count;
            $random = mt_rand(1,10000000000);
        }catch (\Exception $e){
            $path = null;
            $count = null;
        }
        return view('partials.quotation.work-order-images')->with(compact('path','count','random'));
    }

    public function removeTempImage(Request $request){
        try{
            $sellerUploadPath = public_path().$request->path;
            File::delete($sellerUploadPath);
            return response(200);
        }catch(\Exception $e){
            return response(500);
        }
    }

    public function approve(Request $request,$quotation){
        try{
            $quotationApprovedStatusId = QuotationStatus::where('slug','approved')->pluck('id')->first();
            $quotationData = array();
            $quotationData['quotation_status_id'] = $quotationApprovedStatusId;
            $quotationData['remark'] = $request->remark;
            $workOrderData = $request->except('_token','product_images','remark');
            $workOrder = QuotationWorkOrder::create($workOrderData);
            $quotationExtraItemData = array();
            $quotationExtraItemData['quotation_id'] = $request->quotation_id;
            foreach($request->extra_item as $extraItemId => $extraItemValue){
                $quotationExtraItemData['extra_item_id'] = $extraItemId;
                $quotationExtraItemData['rate'] = $extraItemValue;
                QuotationExtraItem::create($quotationExtraItemData);
            }
            $imagesUploaded = $this->uploadWorkOrderImages($request->work_order_images,$request->quotation_id,$workOrder['id']);
            $materials = array();
            $iterator = 0;
            foreach($quotation->quotation_materials as $quotationMaterial){
                $materials[$iterator]['id'] = $quotationMaterial->material_id;
                $materials[$iterator]['rate_per_unit'] = $quotationMaterial->rate_per_unit;
                $materials[$iterator]['unit_id'] = $quotationMaterial->unit_id;
                $iterator++;
            }
            $profitMargins = array();
            foreach($quotation->quotation_products as $quotationProduct){
                $profitMargins[$quotationProduct->product_id] = array();
                $iterator = 0;
                foreach($quotationProduct->quotation_profit_margins as $quotationProfitMargin){
                    $profitMargins[$quotationProduct->product_id][$iterator]['profit_margin_id'] = $quotationProfitMargin->profit_margin_id;
                    $profitMargins[$quotationProduct->product_id][$iterator]['percentage'] = $quotationProfitMargin->percentage;
                    $iterator++;
                }
            }
            $updateMaterial = MaterialProductHelper::updateMaterialsProductsAndProfitMargins($materials,$profitMargins);
            if($updateMaterial['slug'] == 'error'){
                $request->session()->flash('error', $updateMaterial['message']);
                $quotationData['quotation_status_id'] = QuotationStatus::where('slug','draft')->pluck('id')->first();
            }else{
                $request->session()->flash('success','Quotation Approved Successfully');
            }
            Quotation::where('id',$request->quotation_id)->update($quotationData);
            return redirect('/quotation/edit/'.$request->quotation_id);
        }catch (\Exception $e){
            $data = [
                'action' => 'Create Work order',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function generateSummaryPdf(Request $request,$quotation){
        try{
            $data = array();
            $data['project_site'] = $quotation->project_site;
            $summaryData = QuotationProduct::where('quotation_id',$quotation['id'])->distinct('summary_id')->orderBy('summary_id')->select('summary_id')->get();
            $quotationProducts = QuotationProduct::where('quotation_id',$quotation['id'])->get();
            $i = $total['rate_per_sft'] = $total['rate_per_carpet'] = 0;
            foreach($summaryData as $key => $summary){
                $summary_amount = 0;
                foreach($quotationProducts as $j => $quotationProduct){
                    if($quotationProduct->summary_id == $summary['summary_id']){
                        $discounted_price_per_product = round(($quotationProduct->rate_per_unit - ($quotationProduct->rate_per_unit * ($quotationProduct->quotation->discount / 100))),3);
                        $discounted_price = $quotationProduct->quantity * $discounted_price_per_product;
                        $summary_amount = $summary_amount + $discounted_price;
                    }
                }
                $data['quotation'] = $quotation;
                $summaryData[$i]['description'] = $summary->summary->name;
                if(!empty($quotation['built_up_area'])){
                    $summaryData[$i]['rate_per_sft'] = round(($summary_amount / $quotation['built_up_area']),3);
                }else{
                    $summaryData[$i]['rate_per_sft'] = 0.00;
                }
                $total['rate_per_sft'] = $total['rate_per_sft'] + $summaryData[$i]['rate_per_sft'];
                $i++;
            }
            $data['summary_no'] = "S-".strtoupper(date('M',strtotime($quotation['created_at'])))."-".$quotation->id."/".date('y',strtotime($quotation['created_at']));
            $data['summaryData'] = $summaryData;
            $data['total'] = $total;
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML(view('admin.quotation.pdf.summary',$data));
            return $pdf->stream();
        }catch(\Exception $e){
            $data = [
                'action' => 'Generate Summary PDF',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getWorkOrderForm(Request $request){
        try{
            $quotationId = $request->quotation_id;
            return view('partials.quotation.create-work-order')->with(compact('quotationId'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get work order form',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getWorkOrderImagePath($quotationId,$workOrderImages){
        $quotationDirectoryName = sha1($quotationId);
        $imageUploadPath = env('QUOTATION_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$quotationDirectoryName.DIRECTORY_SEPARATOR.'work_order_images';
        $iterator = 0;
        foreach($workOrderImages as $image){
            $workOrderImages[$iterator]['path'] = $imageUploadPath.DIRECTORY_SEPARATOR.$image->image;
            $iterator++;
        }
        return $workOrderImages;
    }

    public function uploadWorkOrderImages($images,$quotationId,$workOrderId){
        try{
            $quotationDirectoryName = sha1($quotationId);
            $tempImageUploadPath = public_path().env('WORK_ORDER_TEMP_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$quotationDirectoryName;
            $imageUploadPath = public_path().env('QUOTATION_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$quotationDirectoryName.DIRECTORY_SEPARATOR.'work_order_images';
            $workOrderImagesData = array();
            $workOrderImagesData['quotation_work_order_id'] = $workOrderId;
            foreach($images as $image){
                $imageName = basename($image['image_name']);
                $newTempImageUploadPath = $tempImageUploadPath.'/'.$imageName;
                $workOrderImagesData['image'] = $imageName;
                WorkOrderImage::create($workOrderImagesData);
                if (!file_exists($imageUploadPath)) {
                    File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
                }
                if(File::exists($newTempImageUploadPath)){

                    $imageUploadNewPath = $imageUploadPath.DIRECTORY_SEPARATOR.$imageName;
                    File::move($newTempImageUploadPath,$imageUploadNewPath);
                }
            }
            if(count(scandir($tempImageUploadPath)) <= 2){
                rmdir($tempImageUploadPath);
            }
            return true;
        }catch (\Exception $e){
            $data = [
                'action' => 'Upload Work Order Images',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return false;
        }
    }

    public function editWorkOrder(Request $request, $workOrder){
        try{
            $workOrder->quotation->update(['remark' => $request->remark]);
            $workOrderData = $request->except('_token','work_order_images');
            $workOrder->update($workOrderData);
            foreach($workOrder->images as $image){
                $image->delete();
            }
            $quotationExtraItemData = array();
            $quotationExtraItemData['quotation_id'] = $request->quotation_id;
            foreach($request->extra_item as $extraItemId => $extraItemValue){
                $quotationExtraItemData['extra_item_id'] = $extraItemId;
                $quotationExtraItemData['rate'] = $extraItemValue;
                $quotationExtraItem = QuotationExtraItem::where('quotation_id',$request->quotation_id)->where('extra_item_id',$extraItemId)->first();
                if($quotationExtraItem != null){
                    $quotationExtraItem->update($quotationExtraItemData);
                }else{
                    QuotationExtraItem::create($quotationExtraItemData);
                }
            }
            $isImagesUploaded = $this->uploadWorkOrderImages($request->work_order_images,$workOrder->quotation_id,$workOrder['id']);
            $request->session()->flash('success','Work Order Updated Successfully');
            return redirect('/quotation/edit/'.$request->quotation_id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Work Order',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function disapprove(Request $request,$quotation){
        try{
            $quotationData = array();
            $quotationData['remark'] = $request->remark;
            $quotationDisapproveStatusId = QuotationStatus::where('slug','disapproved')->pluck('id')->first();
            $quotationData['quotation_status_id'] = $quotationDisapproveStatusId;
            $quotation->update($quotationData);
            $request->session()->flash('success','Quotation disapproved.');
            return redirect('/quotation/edit/'.$quotation->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Disapprove functionality',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getProductEditView(Request $request){
        try{
            $data = $request->all();
            $user = Auth::user();
            $quotationProduct = QuotationProduct::where('quotation_id',$data['quotation_id'])->where('product_id',$data['product_id'])->first();
            if($quotationProduct == null){
                return redirect('/product/edit/'.$data['product_id']);
            }else{
                $quotationDraftStatusId = QuotationStatus::where('slug','draft')->pluck('id')->first();
                $quotation = Quotation::findOrFail($data['quotation_id']);
                $productBillCount = $this->getProductBillCount($quotation['id'],$data['product_id']);
                if($quotation->quotation_status_id == $quotationDraftStatusId || $quotation->quotation_status_id == null || ($user->role->slug == 'superadmin' && $productBillCount <= 0)){
                    $canUpdateProduct = true;
                }else{
                    $canUpdateProduct = false;
                }
                if($quotationProduct->product_version_id == null){
                    $version = ProductVersion::where('product_id', $quotationProduct->product_id)->orderBy('created_at','desc')->pluck('id')->first();
                }else{
                    $version = $quotationProduct->product_version_id;
                }
                $productMaterialVersions = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                    ->join('units','units.id','=','material_versions.unit_id')
                    ->join('materials','materials.id','=','material_versions.material_id')
                    ->where('product_material_relation.product_version_id', $version)
                    ->select('material_versions.id as id','materials.id as material_id','materials.name as name','material_versions.unit_id as unit_id','product_material_relation.material_quantity as quantity','units.name as unit','materials.unit_id as material_unit_id')
                    ->get()->toArray();
                for($iterator = 0; $iterator < count($productMaterialVersions); $iterator++){
                    $quotationMaterial = QuotationMaterial::where('material_id',$productMaterialVersions[$iterator]['material_id'])->where('quotation_id',$data['quotation_id'])->select('rate_per_unit','unit_id')->first();
                    $rateConversion = UnitHelper::unitConversion($quotationMaterial['unit_id'],$productMaterialVersions[$iterator]['unit_id'],$quotationMaterial['rate_per_unit']);
                    if(!is_array($rateConversion)){
                        $productMaterialVersions[$iterator]['rate_per_unit'] = $rateConversion;
                    }else{
                        // handle if unit conversion is not present
                    }
                }
                return view('partials.quotation.quotation-product-view')->with(compact('canUpdateProduct','quotationProduct','productMaterialVersions'));
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Save Quotation Product',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function checkProductRemove(Request $request){
        try{
            $status = 200;
            $response = array();
            $quotationId = $request->quotationId;
            $productId = $request->productId;
            $productBillCount = $this->getProductBillCount($quotationId,$productId);
            if($productBillCount > 0){
                $response['can_remove'] = false;
                $response['message'] = 'A bill is already created for this product, so you can not remove this product.';
            }else{
                $response['can_remove'] = true;
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Save Quotation Product',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = ['message' => 'Something went wrong.'];
        }
        return response()->json($response,$status);
    }

    public function getProductBillCount($quotationId,$productId){
        $productBillCount = BillQuotationProducts::join('bills','bills.id','=','bill_quotation_products.bill_id')
            ->join('quotations','quotations.id','=','bills.quotation_id')
            ->join('quotation_products',function($join){
                $join->on('quotation_products.quotation_id','=','quotations.id');
                $join->on('quotation_products.id','=','bill_quotation_products.quotation_product_id');
            })
            ->where('quotation_products.product_id',$productId)
            ->where('bills.quotation_id',$quotationId)
            ->count();
        return $productBillCount;
    }
}