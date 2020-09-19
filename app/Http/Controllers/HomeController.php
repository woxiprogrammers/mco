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
        $inventoryComponentOutTransfers = $outTransferType->inventoryComponentTransfers->where('id', 39753);
        $closedChallanStatus = InventoryComponentTransferStatus::where('slug', 'close')->pluck('id')->first();

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
            $inTransfer = InventoryComponentTransfers::find($outTransfer['related_transfer_id']);
            $challan = new InventoryTransferChallan([
                'challan_number'        => 'ch1',  // To be renewed, 
                'project_site_out_id'   => $outTransfer->inventoryComponent->project_site_id,
                'project_site_in_id'    => $inTransfer->inventoryComponent->project_site_id ?? '',
                'project_site_out_date' => $outTransfer['date'] ?? null,
                'project_site_in_date'  => $inTransfer['date'] ?? null,
                'inventory_component_transfer_status_id' => $closedChallanStatus
            ]);
            $challan->save();
            $challan->fresh();

            $outTransfer->update(['inventory_transfer_challan_id'   => $challan['id']]);
            $inTransfer->update(['inventory_transfer_challan_id'    => $challan['id']]);

            $inventoryComponentDirectoryName = sha1($outTransfer->inventoryComponent->id);
            $inventoryComponentTransferDirectoryName = sha1($outTransfer->id);
            $originalImageUploadDirectoryPath = $imageUploadPath . DIRECTORY_SEPARATOR . $inventoryComponentDirectoryName . DIRECTORY_SEPARATOR . 'transfers' . DIRECTORY_SEPARATOR . $inventoryComponentTransferDirectoryName;

            $outImages = $outTransfer->images;
            foreach ($outImages as $outImage) {
                $fileName = $outImage->name;
                if (file_exists($originalImageUploadDirectoryPath . DIRECTORY_SEPARATOR . $fileName)) {
                    File::move($originalImageUploadDirectoryPath . DIRECTORY_SEPARATOR . $fileName, $newOutUploadPath . DIRECTORY_SEPARATOR . $fileName);
                }
            }

            $inImages = $inTransfer->images;
            foreach ($inImages as $inImage) {
                $fileName = $inImage->name;
                if (file_exists($originalImageUploadDirectoryPath . DIRECTORY_SEPARATOR . $fileName)) {
                    File::move($originalImageUploadDirectoryPath . DIRECTORY_SEPARATOR . $fileName, $newInUploadPath . DIRECTORY_SEPARATOR . $fileName);
                }
            }
        }
        dd($inventoryComponentOutTransfers);
    }
}
