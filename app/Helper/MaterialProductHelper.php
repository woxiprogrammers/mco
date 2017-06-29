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
use App\ProductVersion;
use Illuminate\Support\Facades\Log;

class MaterialProductHelper{

    public static function updateMaterialAndProducts($materials){
        try{
            dd($materials);
            $materialSecondRecentVersionIds = array();
            $materialRecentVersions = array();
            $materialData = array();
            $materialVersionData = array();
            foreach($materials as $material){
                $mainMaterialData = Material::where('id',$material['id'])->select('id','rate_per_unit','unit_id')->first();
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
                                            ->where('material_versions.id',$secondRecentId)
                                            ->select('product_versions.id as product_version_id','product_versions.product_id as id','product_material_relation.material_quantity','material_versions.id as material_version_id','material_versions.material_id as material_id')
                                            ->get()
                                            ->toArray();
                foreach($products as $product){
                    $recentProductVersion = ProductVersion::where('id',$product['id'])->orderBy('created_at','desc')->pluck('id')->first();
                    if($product['product_version'] == $recentProductVersion){
                        $productVersionData = array();
                        $productVersionMaterialIds = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                                                        ->join('materials','materials.id','=','material_versions.material_id')
                                                        ->where('product_material_relation.product_version_id',$recentProductVersion)
                                                        ->pluck('materials.id')
                                                        ->toArray();
                        $productVersionMaterials = ProductMaterialRelation::join('material_versions','material_versions.id','=','product_material_relation.material_version_id')
                                ->join('materials','materials.id','=','material_versions.material_id')
                                ->where('product_material_relation.product_version_id',$recentProductVersion)
                                ->select('product_versions.id as product_version_id','product_versions.product_id as id','product_material_relation.material_quantity as material_quantity','material_versions.id as material_version_id','material_versions.material_id as material_id','material_versions.unit_id as unit_id')
                                ->toArray();
                        $changedProductMaterialIds = array_intersect($productVersionMaterialIds,$changedMaterialIds);
                        $nonChangedProductMaterials = array_diff($productVersionMaterialIds,$changedMaterialIds);
                        $productAmount = 0;
                        foreach($productVersionMaterials as $materialInfo){
                            if(in_array($materialInfo['material_id'],$changedMaterialIds)){
                                $materialRecentVersion = MaterialVersion::where('id',$materialRecentVersions[$materialInfo['material_id']])->select('unit_id','rate_per_unit')->first();
                                $rateConversion = UnitHelper::unitConversion($materialInfo['unit_id'],$materialRecentVersion['unit_id'],$materialRecentVersion['rate_per_unit']);
                                if(!is_array($rateConversion)){
                                    $productAmount = $productAmount + ($rateConversion * $materialInfo['material_quantity']);
                                }else{
                                    $productAmount = $productAmount + ($materialRecentVersion['rate_per_unit'] * $materialInfo['material_quantity']);
                                }
                            }else{
                                $productAmount = $productAmount + ($materialInfo['rate_per_unit'] * $materialInfo['material_quantity']);
                            }
                        }

                    }
                }
            }
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