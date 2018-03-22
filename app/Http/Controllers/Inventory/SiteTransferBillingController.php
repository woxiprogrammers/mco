<?php

namespace App\Http\Controllers\Inventory;

use App\InventoryComponentTransfers;
use App\PaymentType;
use App\SiteTransferBill;
use App\SiteTransferBillImage;
use App\SiteTransferBillPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SiteTransferBillingController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request){
        try{
            return view('inventory.site-transfer-bill.manage');
        }catch (\Exception $e){
            $data = [
                'action' => 'Site Transfer Billing get manage view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request){
        try{
            return view('inventory.site-transfer-bill.create');
        }catch (\Exception $e){
            $data = [
                'action' => 'Site Transfer Billing get manage view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getApprovedTransaction(Request $request){
        try{
            $projectSiteId = Session::get('global_project_site');
            $approvedTransferIds = InventoryComponentTransfers::join('inventory_transfer_types','inventory_transfer_types.id','=','inventory_component_transfers.transfer_type_id')
                                                ->join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                                                ->join('inventory_component_transfer_statuses','inventory_component_transfer_statuses.id','=','inventory_component_transfers.inventory_component_transfer_status_id')
                                                ->where('inventory_component_transfer_statuses.slug', 'approved')
                                                ->where('inventory_components.project_site_id', $projectSiteId)
                                                ->where('inventory_component_transfers.grn','ilike','%'.$request->keyword.'%')
                                                ->where('inventory_transfer_types.slug','site')
                                                ->pluck('inventory_component_transfers.id')->toArray();
            $billCreatedTransferIds = SiteTransferBill::pluck('inventory_component_transfer_id')->toArray();
            $approvedBillPendingTransferIds = array_diff($approvedTransferIds, $billCreatedTransferIds);
            $siteTransfersInfo = InventoryComponentTransfers::whereIn('id', $approvedBillPendingTransferIds)->get()->toArray();
            $iterator = 0;
            $response = array();
            foreach ($siteTransfersInfo as $transferInfo){
                $response[$iterator]['inventory_component_transfer_id'] = $transferInfo['id'];
                $response[$iterator]['grn'] = $transferInfo['grn'];
                if(isset($transferInfo['transportation_amount']) || $transferInfo['transportation_amount'] != null){
                    $response[$iterator]['subtotal'] = $transferInfo['transportation_amount'];
                }else{
                    $response[$iterator]['subtotal'] = 0;
                }
                $response[$iterator]['tax_amount'] = $response[$iterator]['subtotal'] * ($transferInfo['transportation_cgst_percent'] / 100);
                $response[$iterator]['tax_amount'] += $response[$iterator]['subtotal'] * ($transferInfo['transportation_sgst_percent'] / 100);
                $response[$iterator]['tax_amount'] += $response[$iterator]['subtotal'] * ($transferInfo['transportation_igst_percent'] / 100);
                $iterator++;
            }
            $status = 200;
        }catch(\Exception $e){
            $response = [];
            $status = 500;
            $data = [
                'action' => 'Get approved Transaction for typeahead',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
        return response()->json($response, $status);
    }

    public function createSiteTransferBill(Request $request){
        try{
            $siteTransferBillData = $request->except('_token','transfer_grn');
            $siteTransferBill = SiteTransferBill::create($siteTransferBillData);
            $imageUploadPath = public_path().env('SITE_TRANSFER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.sha1($siteTransferBill->id);
            if(!file_exists($imageUploadPath)){
                File::makeDirectory($imageUploadPath,0777, true, true);
            }
            if($request->has('bill_images')){
                foreach($request->bill_images as $billImage){
                    $imageArray = explode(';',$billImage);
                    $image = explode(',',$imageArray[1])[1];
                    $pos  = strpos($billImage, ';');
                    $type = explode(':', substr($billImage, 0, $pos))[1];
                    $extension = explode('/',$type)[1];
                    $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
                    $fileFullPath = $imageUploadPath.DIRECTORY_SEPARATOR.$filename;
                    $billImageData = [
                        'site_transfer_bill_id' => $siteTransferBill->id,
                        'name' => $filename,
                    ];
                    file_put_contents($fileFullPath,base64_decode($image));
                    SiteTransferBillImage::create($billImageData);
                }
            }
            $request->session()->flash('success','Site transfer bill created successfully. ');
            return redirect('/inventory/transfer/billing/manage');
        }catch (\Exception $e){
            $data = [
                'action' => 'Create Site Transfer Bill',
                'data' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function listing(Request $request){
        try{
            $records = array();
            $status = 200;
            $records['data'] = array();
            $records["draw"] = intval($request->draw);
            $siteTransferBillId = SiteTransferBill::orderBy('created_at','desc')->pluck('id')->toArray();
            $filterFlag = true;
            if($request->has('bill_date') && $request->bill_date != ''){
                $siteTransferBillId = SiteTransferBill::whereDate('bill_date', $request->bill_date)
                                                        ->whereIn('id',$siteTransferBillId)
                                                        ->pluck('id')->toArray();
                if(count($siteTransferBillId) > 0){
                    $filterFlag = false;
                }
            }
            if($filterFlag == true && $request->has('vendor_name') && $request->vendor_name != ''){
                $siteTransferBillId = SiteTransferBill::join('inventory_component_transfers','inventory_component_transfers.id','=','site_transfer_bills.inventory_component_transfer_id')
                                                ->join('vendors','vendors.id','=','inventory_component_transfers.vendor_id')
                                                ->whereIn('site_transfer_bills.id', $siteTransferBillId)
                                                ->where('vendors.company','ilike','%'.$request->vendor_name.'%')
                                                ->pluck('site_transfer_bills.id')
                                                ->toArray();
                if(count($siteTransferBillId) > 0){
                    $filterFlag = false;
                }
            }
            $siteTransferBillData = SiteTransferBill::whereIn('id', $siteTransferBillId)->get();
            $records["recordsFiltered"] = $records["recordsTotal"] = count($siteTransferBillData);
            if($request->length == -1){
                $length = $records["recordsTotal"];
            }else{
                $length = $request->length;
            }
            for($iterator = 0,$pagination = $request->start; $iterator < $length && $iterator < count($siteTransferBillData); $iterator++,$pagination++ ){
                $projectName = $siteTransferBillData[$pagination]->inventoryComponentTransfer->inventoryComponent->projectSite->project->name;
                $paidAmount = SiteTransferBillPayment::where('site_transfer_bill_id',$siteTransferBillData[$pagination]['id'])->sum('amount');
                $pendingAmount = $siteTransferBillData[$pagination]['total'] - $paidAmount;
                if($siteTransferBillData[$pagination]->inventoryComponentTransfer->vendor == null){
                    $vendorName = '-';
                }else{
                    $vendorName = $siteTransferBillData[$pagination]->inventoryComponentTransfer->vendor->company;
                }
                $records['data'][$iterator] = [
                    $projectName,
                    $pagination+1,
                    date('j M Y',strtotime($siteTransferBillData[$pagination]['created_at'])),
                    date('j M Y',strtotime($siteTransferBillData[$pagination]['bill_date'])),
                    $siteTransferBillData[$pagination]['bill_number'],
                    $vendorName,
                    $siteTransferBillData[$pagination]['subtotal'] + $siteTransferBillData[$pagination]['extra_amount'],
                    $siteTransferBillData[$pagination]['tax_amount'] + $siteTransferBillData[$pagination]['extra_amount_cgst_amount'] + $siteTransferBillData[$pagination]['extra_amount_sgst_amount'] + $siteTransferBillData[$pagination]['extra_amount_igst_amount'],
                    $siteTransferBillData[$pagination]['total'],
                    $paidAmount,
                    $pendingAmount,
                    '<div id="sample_editable_1_new" class="btn btn-small blue" >
                        <a href="/inventory/transfer/billing/edit/'.$siteTransferBillData[$pagination]['id'].'" style="color: white"> Edit
                    </div>'
                ];
            }

        }catch (\Exception $e){
            $data = [
                'action' => 'Get site transfer bill listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $records = [
                'message' => 'Something went wrong'
            ];
        }
        return response()->json($records, $status);
    }

    public function getEditView(Request $request,$siteTransferBill){
        try{
            $totalPaidAmount = SiteTransferBillPayment::where('site_transfer_bill_id', $siteTransferBill->id)->sum('amount');
            $imageUploadPath = env('SITE_TRANSFER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.sha1($siteTransferBill->id).DIRECTORY_SEPARATOR;
            $paymentTypes = PaymentType::select('id','name')->get()->toArray();
            return view('inventory.site-transfer-bill.edit')->with(compact('siteTransferBill','paymentTypes','imageUploadPath','totalPaidAmount'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get site transfer bill edit view',
                'site_transfer_bill' => $siteTransferBill,
                'exception' => $e->getMessage()
            ];
            Log::critcal(json_encode($data));
            abort(500);
        }
    }
    public function createPayment(Request $request){
        try{
            $siteTransferBillPaymentData = $request->except('_token');
            $siteTransferBillPayment = SiteTransferBillPayment::create($siteTransferBillPaymentData);
            $request->session()->flash('success','Payment is created successfully.');
            return redirect('/inventory/transfer/billing/edit/'.$request->site_transfer_bill_id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Create site transfer bill payment',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function paymentListing(Request $request, $siteTransferBill){
        try{
            $records = array();
            $status = 200;
            $records['data'] = array();
            $records["draw"] = intval($request->draw);
            $siteTransferBillPaymentData = SiteTransferBillPayment::where('site_transfer_bill_id', $siteTransferBill->id)->orderBy('id','desc')->get();
            $records["recordsFiltered"] = $records["recordsTotal"] = count($siteTransferBillPaymentData);
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($siteTransferBillPaymentData); $iterator++,$pagination++ ){
                if($siteTransferBillPaymentData[$pagination]->paymentType == null){
                    $paymentType = '-';
                }else{
                    $paymentType = $siteTransferBillPaymentData[$pagination]->paymentType->name;
                }
                $records['data'][] = [
                    date('d M Y',strtotime($siteTransferBillPaymentData[$pagination]['created_at'])),
                    $siteTransferBillPaymentData[$pagination]['amount'],
                    $paymentType,
                    $siteTransferBillPaymentData[$pagination]['reference_number'],
                ];
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Get site transfer bill payment listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critcal(json_encode($data));
            $status = 500;
            $records = [];
        }
        return response()->json($records, $status);
    }
}
