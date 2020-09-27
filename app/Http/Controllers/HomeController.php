<?php

namespace App\Http\Controllers;

use App\InventoryComponentTransfers;
use App\InventoryComponentTransferStatus;
use App\InventoryTransferChallan;
use App\InventoryTransferTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    public function handlee()
    {
        $outTransferType = InventoryTransferTypes::where('slug', 'site')->where('type', 'OUT')->first();
        $inventoryComponentOutTransfers = $outTransferType->inventoryComponentTransfers;
        $challanStatus = InventoryComponentTransferStatus::whereIn('slug', ['close', 'open'])->select('id as id', 'slug as slug')->get();
        $imageUploadPath = public_path() . env('INVENTORY_COMPONENT_IMAGE_UPLOAD');
        $newOutUploadPath = $imageUploadPath . DIRECTORY_SEPARATOR . 'out';
        $newInUploadPath = $imageUploadPath . DIRECTORY_SEPARATOR . 'in';

        if (!file_exists($newOutUploadPath)) {
            File::makeDirectory($newOutUploadPath, $mode = 0777, true, true);
        }
        if (!file_exists($newInUploadPath)) {
            File::makeDirectory($newInUploadPath, $mode = 0777, true, true);
        }

        foreach ($inventoryComponentOutTransfers as $outTransfer) {
            Log::info($outTransfer['id']);

            $inTransfer = InventoryComponentTransfers::find($outTransfer['related_transfer_id']);
            if ($outTransfer->inventoryComponentTransferStatus->slug === 'disapproved' || $outTransfer->inventoryComponentTransferStatus->slug === 'requested') {
                $challanStatusId = $outTransfer->inventory_component_transfer_status_id;
            }
            if ($outTransfer->inventoryComponentTransferStatus->slug === 'grn-generated' || $outTransfer->inventoryComponentTransferStatus->slug === 'approved') {
                $challanStatusId = $challanStatus->where('slug', 'open')->pluck('id')->first();
            }
            if ($inTransfer) {
                if ($inTransfer->quantity === $outTransfer->quantity) {
                    $challanStatusId = $challanStatus->where('slug', 'close')->pluck('id')->first();
                }
            }
            if (!$outTransfer->inventory_transfer_challan_id) {
                $challan = new InventoryTransferChallan([
                    'challan_number'                        => 'ch',
                    'project_site_out_id'                   => $outTransfer->inventoryComponent->project_site_id,
                    'project_site_in_id'                    => $inTransfer->inventoryComponent->project_site_id ?? null,
                    'project_site_out_date'                 => $outTransfer['date'] ?? null,
                    'project_site_in_date'                  => $inTransfer['date'] ?? null,
                    'inventory_component_transfer_status_id' => $challanStatusId
                ]);
                $challan->save();
                $challan->fresh();
                $challan->update(['challan_number'  => 'ch' . $challan->id]);
            } else {
                $challan = $outTransfer->inventoryTransferChallan;
            }


            $outTransfer->update(['inventory_transfer_challan_id'   => $challan['id']]);
            if ($inTransfer) {
                $inTransfer->update(['inventory_transfer_challan_id'    => $challan['id']]);
            }

            $inventoryComponentDirectoryName = sha1($outTransfer->inventoryComponent->id);
            $inventoryComponentTransferDirectoryName = sha1($outTransfer->id);
            $originalImageUploadDirectoryPath = $imageUploadPath . DIRECTORY_SEPARATOR . $inventoryComponentDirectoryName . DIRECTORY_SEPARATOR . 'transfers' . DIRECTORY_SEPARATOR . $inventoryComponentTransferDirectoryName;

            $outImages = $outTransfer['images'];
            foreach ($outImages as $outImage) {
                $fileName = $outImage->name;
                if (file_exists($originalImageUploadDirectoryPath . DIRECTORY_SEPARATOR . $fileName)) {
                    File::move($originalImageUploadDirectoryPath . DIRECTORY_SEPARATOR . $fileName, $newOutUploadPath . DIRECTORY_SEPARATOR . $fileName);
                }
            }

            $inImages = $inTransfer['images'];
            if ($inImages) {
                foreach ($inImages as $inImage) {
                    $fileName = $inImage->name;
                    if (file_exists($originalImageUploadDirectoryPath . DIRECTORY_SEPARATOR . $fileName)) {
                        File::move($originalImageUploadDirectoryPath . DIRECTORY_SEPARATOR . $fileName, $newInUploadPath . DIRECTORY_SEPARATOR . $fileName);
                    }
                }
            }
        }
        dd($inventoryComponentOutTransfers);
    }
}
