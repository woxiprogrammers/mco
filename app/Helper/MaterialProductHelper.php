<?php
/**
 * Created by Ameya Joshi.
 * Date: 29/6/17
 * Time: 2:44 PM
 */

namespace App\Helper;

use App\Material;
use App\MaterialVersion;
use App\Product;
use App\ProductMaterialRelation;
use App\ProductProfitMarginRelation;
use App\ProductVersion;
use App\ProfitMarginVersion;
use Illuminate\Support\Facades\Log;

class MaterialProductHelper{

    public static function updateMaterialsProductsAndProfitMargins($materials=null,$profitMargins=null){
        try{
            $materialSecondRecentVersionIds = array();
            $materialRecentVersions = array();
            $materialData = array();
            $materialVersionData = array();
            $oldMaterialData = array();
            $response = array();
            foreach($materials as $material){
                $mainMaterialData = Material::where('id',$material['id'])->select('id','rate_per_unit','unit_id')->first();
                $oldMaterialData[$mainMaterialData['id']]['rate_per_unit'] = $mainMaterialData['rate_per_unit'];
                $oldMaterialData[$mainMaterialData['id']]['unit_id'] = $mainMaterialData['unit_id'];
                if($material != $mainMaterialData){
                    $materialData['rate_per_unit'] = $material['rate_per_unit'];
                    $materialData['unit_id'] = $material['unit_id'];
                    Material::where('id',$material['id'])->update($materialData);
                    $materialSecondRecentVersionIds[$material['id']] = MaterialVersion::where('material_id',$material['id'])->orderBy('created_at','desc')->pluck('id')->first();
                    $materialVersionData['material_id'] = $material['id'];
                    $materialVersionData['rate_per_unit'] = $material['rate_per_unit'];
                    $materialVersionData['unit_id'] = $material['unit_id'];
                    $materialVersion = MaterialVersion::create($materialVersionData);
                    $materialRecentVersions[$material['id']] = $materialVersion->id;
                }
            }
            $changedMaterialIds = array_keys($materialRecentVersions);
            foreach($materialSecondRecentVersionIds as $materialId => $secondRecentId){
                $products = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                                            ->join('product_versions','product_versions.id','=','product_material_relation.product_version_id')
                                            ->join('materials','materials.id','=','material_versions.material_id')
                                            ->where('materials.id',$materialId)
                                            ->select('product_versions.id as product_version_id','product_versions.product_id as id','product_material_relation.material_quantity','material_versions.id as material_version_id','material_versions.material_id as material_id')
                                            ->get()
                                            ->toArray();
                foreach($products as $product){
                    $recentProductVersion = ProductVersion::where('product_id',$product['id'])->orderBy('created_at','desc')->select('id','product_id','rate_per_unit')->first()->toArray();
                    $productAmount = 0;
                    if($product['product_version_id'] == $recentProductVersion['id']){
                        $productVersionMaterials = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                                ->join('materials','materials.id','=','material_versions.material_id')
                                ->join('product_versions','product_versions.id','=','product_material_relation.product_version_id')
                                ->where('product_material_relation.product_version_id',$recentProductVersion['id'])
                                ->select('product_versions.id as product_version_id','product_versions.product_id as id','product_material_relation.material_quantity as material_quantity','material_versions.id as material_version_id','material_versions.material_id as material_id','material_versions.unit_id as unit_id','material_versions.rate_per_unit as rate_per_unit')
                                ->get()
                                ->toArray();
                        $productVersionData = array();
                        $productVersionData['rate_per_unit'] = $recentProductVersion['rate_per_unit'];
                        $productVersionData['product_id'] = $recentProductVersion['product_id'];
                        $newProductVersion = ProductVersion::create($productVersionData);
                        $productMaterialRelationData = array();
                        foreach($productVersionMaterials as $materialInfo){
                            $productMaterialRelationData['product_version_id'] = $newProductVersion['id'];
                            $productMaterialRelationData['material_quantity'] = $materialInfo['material_quantity'];
                            if(in_array($materialInfo['material_id'],$changedMaterialIds)){
                                $materialRecentVersion = MaterialVersion::join('materials','materials.id','=','material_versions.material_id')
                                                                        ->where('material_versions.material_id',$materialInfo['material_id'])
                                                                        ->orderBy('material_versions.created_at','desc')
                                                                        ->select('material_versions.id as id','material_versions.unit_id as unit_id','material_versions.rate_per_unit as rate_per_unit','material_versions.material_id as material_id','materials.unit_id as material_unit_id')
                                                                        ->first();
                                $productMaterialRelationData['material_quantity'] = $materialInfo['material_quantity'];
                                if($materialInfo['unit_id'] == $materialRecentVersion['unit_id']){
                                    $rateConversion = $materialRecentVersion['rate_per_unit'];
                                }else{
                                    $rateConversion = UnitHelper::unitConversion($materialRecentVersion['material_unit_id'],$materialInfo['unit_id'],$materialRecentVersion['rate_per_unit']);
                                }
                                if(!is_array($rateConversion)){
                                    $productAmount = round($productAmount + ($rateConversion * $materialInfo['material_quantity']),3);
                                }else{
                                    Log::info('in unit cionversion else');
                                    Log::info($rateConversion);
                                    ProductMaterialRelation::where('product_version_id',$newProductVersion->id)->delete();
                                    ProductProfitMarginRelation::where('product_version_id',$newProductVersion->id)->delete();
                                    $newProductVersion->delete();
                                    foreach($materialRecentVersions as $newMaterialVersionId){
                                        $updateMaterialId = MaterialVersion::where('id',$newMaterialVersionId)->pluck('material_id')->first();
                                        Material::where('id',$updateMaterialId)->update($oldMaterialData[$updateMaterialId]);
                                        ProductMaterialRelation::where('material_version_id',$newMaterialVersionId)->delete();
                                        MaterialVersion::where('id',$newMaterialVersionId)->delete();
                                    }
                                    $response['slug'] = 'error';
                                    $response['message'] = $rateConversion['message'];
                                    return $response;
                                }
                                $materialVersionData = array();
                                if($materialRecentVersion['rate_per_unit'] == $rateConversion && $materialRecentVersion['unit_id'] == $materialInfo['unit_id']){
                                    $productMaterialRelationData['material_version_id'] = $materialRecentVersion['id'];
                                }else{
                                    $materialVersionData['material_id'] = $materialRecentVersion['material_id'];
                                    $materialVersionData['rate_per_unit'] = round($rateConversion,3);
                                    $materialVersionData['unit_id'] = $materialInfo['unit_id'];
                                    $newMaterialVersion = MaterialVersion::create($materialVersionData);
                                    $productMaterialRelationData['material_version_id'] = $newMaterialVersion['id'];

                                }
                            }else{
                                $productMaterialRelationData['material_version_id'] = $materialInfo['material_version_id'];
                                $productAmount = round($productAmount + ($materialInfo['rate_per_unit'] * $materialInfo['material_quantity']),3);
                            }
                            ProductMaterialRelation::create($productMaterialRelationData);
                        }
                        $productProfitMarginRelationData =array();
                        $productProfitMarginRelationData['product_version_id'] = $newProductVersion['id'];
                        $profitMarginAmount = 0;
                        Log::info('product id');
                        Log::info($product['id']);
                        if($profitMargins != null){
                            Log::info(' in profit margin if');
                            Log::info($profitMargins);
                            if(array_key_exists($product['id'],$profitMargins)){
                                Log::info('in array key exists if');
                                foreach($profitMargins[$product['id']] as $profitMargin){
                                    $recentProfitMarginVersion = ProfitMarginVersion::where('profit_margin_id',$profitMargin['profit_margin_id'])->orderBy('created_at','desc')->select('id','percentage')->first();
                                    if($profitMargin['percentage'] == $recentProfitMarginVersion['percentage']){
                                        $productProfitMarginRelationData['profit_margin_version_id'] = $recentProfitMarginVersion['id'];
                                        $profitMarginAmount = $profitMarginAmount + round($productAmount * ($profitMargin['percentage'] / 100),3);
                                    }else{
                                        $profitMarginVersionData = array();
                                        $profitMarginVersionData['profit_margin_id'] = $profitMargin['profit_margin_id'];
                                        $profitMarginVersionData['percentage'] = $profitMargin['percentage'];
                                        $newProfitMarginVersion = ProfitMarginVersion::create($profitMarginVersionData);
                                        $productProfitMarginRelationData['profit_margin_version_id'] = $newProfitMarginVersion['id'];
                                        $profitMarginAmount = $profitMarginAmount + round($productAmount * ($profitMargin['percentage'] / 100),3);
                                    }
                                    Log::info($productProfitMarginRelationData);
                                    ProductProfitMarginRelation::create($productProfitMarginRelationData);
                                }
                            }else{
                                Log::info('in array key exists else');

                                $lastRecentProductVersion = ProductVersion::where('product_id',$product['id'])->orderBy('created_at','desc')->skip(1)->first();
                                foreach($lastRecentProductVersion->profit_margin_relations as $profitMarginRelation){
                                    $productProfitMarginRelationData['profit_margin_version_id'] = $profitMarginRelation->profit_margin_version_id;
                                    Log::info($productProfitMarginRelationData);
                                    ProductProfitMarginRelation::create($productProfitMarginRelationData);
                                }
                            }
                        }else{
                            Log::info(' in profit margin else');
                            $productProfitMargins = ProductProfitMarginRelation::join('profit_margin_versions','profit_margin_versions.id','=','products_profit_margins_relation.profit_margin_version_id')
                                ->where('product_version_id',$recentProductVersion['id'])
                                ->select('products_profit_margins_relation.profit_margin_version_id as profit_margin_version_id','profit_margin_versions.percentage as percentage')
                                ->distinct('profit_margin_version_id')
                                ->get()
                                ->toArray();
                            foreach($productProfitMargins as $profitMargin){
                                $productProfitMarginRelationData['profit_margin_version_id'] = $profitMargin['profit_margin_version_id'];
                                ProductProfitMarginRelation::create($productProfitMarginRelationData);
                                $profitMarginAmount = $profitMarginAmount + round($productAmount * ($profitMargin['percentage'] / 100),3);
                            }
                        }
                        $productAmount = $productAmount + $profitMarginAmount;
                        $newProductVersion->update(['rate_per_unit' => round($productAmount,3)]);
                    }
                }
            }
            $response['slug'] = 'success';
            $response['message'] = 'Materials and products updated successfully.';
            return $response;
        }catch(\Exception $e){
            $data = [
                'action' => 'Update Material and Product',
                'materialData' => $materials,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }
}