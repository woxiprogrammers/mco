<?php
/**
 * Created by Ameya Joshi.
 * Date: 1/11/17
 * Time: 12:10 PM
 */

namespace App\Http\Controllers\CustomTraits\Inventory;

use App\GRNCount;
use App\InventoryComponentTransferImage;
use App\InventoryComponentTransfers;
use App\InventoryTransferTypes;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

trait InventoryTrait{
    public function createInventoryComponentTransfer($data){
        try{

            if(!array_key_exists('transfer_type_id',$data)){
                if(array_key_exists('in_or_out',$data)){
                    $data['transfer_type_id'] = InventoryTransferTypes::where('type','ilike','IN')->where('slug',$data['transfer_type'])->pluck('id')->first();
                    unset($data['in_or_out']);
                }else{
                    $data['transfer_type_id'] = InventoryTransferTypes::where('type','ilike','OUT')->where('slug',$data['transfer_type'])->pluck('id')->first();
                }
                unset($data['transfer_type']);
            }
            if(!array_key_exists('grn',$data)){
                $data['grn'] = $this->generateGRN();
            }
            $inventoryComponentTransfer = InventoryComponentTransfers::create($data);
            return $inventoryComponentTransfer;
        }catch(\Exception $e){
            $logData = [
                'action' => 'Create Inventory Component Transfer',
                'data' => $data,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($logData));
            abort(500);
        }
    }

    public function uploadInventoryComponentTransferImages($images,$inventoryComponentId,$inventoryComponentTransferId){
        try{
            $imageUploadPath = public_path().env('INVENTORY_COMPONENT_IMAGE_UPLOAD');
            $inventoryComponentDirectoryName = sha1($inventoryComponentId);
            $inventoryComponentTransferDirectoryName = sha1($inventoryComponentTransferId);
            $newImageUploadDirectoryPath = $imageUploadPath.DIRECTORY_SEPARATOR.$inventoryComponentDirectoryName.DIRECTORY_SEPARATOR.'transfers'.DIRECTORY_SEPARATOR.$inventoryComponentTransferDirectoryName;
            if(!file_exists($newImageUploadDirectoryPath)){
                File::makeDirectory($newImageUploadDirectoryPath,$mode = 0777, true, true);
            }
            foreach ($images as $imagePath){
                $imagePathChunks = explode('/',$imagePath['image_name']);
                $fileName = end($imagePathChunks);
                $inventoryComponentTransferImageData = [
                    'inventory_component_transfer_id' => $inventoryComponentTransferId,
                    'name' => $fileName
                ];
                File::move(public_path().$imagePath['image_name'],$newImageUploadDirectoryPath.DIRECTORY_SEPARATOR.$fileName);
                InventoryComponentTransferImage::create($inventoryComponentTransferImageData);
            }
            return true;
        }catch(\Exception $e){
            $logData = [
                'action' => 'Upload Inventory Component Transfer Images',
                'images' => $images,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($logData));
            return false;
        }
    }

    public function generateGRN(){
        try{
            $currentDate = Carbon::now();
            $monthlyGrnGeneratedCount = GRNCount::where('month',$currentDate->month)->where('year',$currentDate->year)->pluck('count')->first();
            if ($monthlyGrnGeneratedCount != null) {
                GRNCount::where('month', $currentDate->month)->where('year', $currentDate->year)->update(['count' => (++$monthlyGrnGeneratedCount)]);
            } else {
                $monthlyGrnGeneratedCount = 1;
                GRNCount::create(['month' => $currentDate->month, 'year' => $currentDate->year, 'count' => 1]);
            }
            return "GRN".date('Ym').$monthlyGrnGeneratedCount;
        }catch(\Exception $e){
            $logData = [
                'action' => 'Generate GRN',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($logData));
            return null;
        }
    }
}