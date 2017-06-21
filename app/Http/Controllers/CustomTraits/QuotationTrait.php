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
use App\Project;
use App\ProjectSite;
use App\Quotation;
use App\QuotationMaterial;
use App\QuotationProduct;
use App\QuotationProfitMarginVersion;
use App\QuotationStatus;
use App\Summary;
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
            $units = Unit::where('is_active', true)->orderBy('name','asc')->get()->toArray();
            foreach($productIds as $id){
                $recentVersionId = ProductVersion::where('product_id',$id)->orderBy('created_at','desc')->pluck('id')->first();
                $materialId = ProductMaterialRelation::join('material_versions','product_material_relation.material_version_id','=','material_versions.id')
                                ->join('materials','materials.id','=','material_versions.material_id')
                                ->where('product_material_relation.product_version_id',$recentVersionId)
                                ->pluck('materials.id')
                                ->toArray();
                $materialIds = array_unique(array_merge($materialIds,$materialId));
            }
            $materials = Material::join('units','materials.unit_id','=','units.id')
                        ->whereIn('materials.id',$materialIds)
                        ->orderBy('name','asc')
                        ->select('materials.id as id','materials.name as name','materials.rate_per_unit as rate_per_unit','materials.unit_id as unit_id','units.name as unit')
                        ->get()
                        ->toArray();
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
                    $productProfitMargins[$id]['profit_margin'][$profitMargin['id']] = $profitMargin;
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
                $productMaterials = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                                                ->join('materials','materials.id','=','material_versions.material_id')
                                                ->where('product_material_relation.product_version_id', $recentVersion)
                                                ->select('materials.id as id','product_material_relation.material_quantity as material_quantity','material_versions.unit_id')
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
                QuotationProduct::where('id',$quotationProduct->id)->update(['rate_per_unit' => $productAmount]);
            }
            foreach($data['material_id'] as $materialId){
                $quotationMaterialData = array();
                $quotationMaterialData['material_id'] = $materialId;
                $quotationMaterialData['rate_per_unit'] = $data['material_rate'][$materialId];
                $quotationMaterialData['unit_id'] = $data['material_unit'][$materialId];
                if(is_array($data['clientSuppliedMaterial']) && in_array($materialId,$data['clientSuppliedMaterial'])){
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
            return view('admin.quotation.edit')->with(compact('quotation','summaries'));
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
}