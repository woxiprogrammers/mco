<?php

namespace App\Http\Controllers\Inventory;

use App\BankInfo;
use App\Http\Controllers\CustomTraits\PeticashTrait;
use App\InventoryComponentTransfers;
use App\PaymentType;
use App\SiteTransferBill;
use App\SiteTransferBillImage;
use App\SiteTransferBillPayment;
use App\Vendor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\InventoryComponentTransferStatus;
use App\InventoryTransferChallan;
use App\InventoryTransferTypes;
use App\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SiteTransferBillingController extends Controller
{
    use PeticashTrait;
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request)
    {
        try {
            return view('inventory.site-transfer-bill.manage');
        } catch (\Exception $e) {
            $data = [
                'action' => 'Site Transfer Billing get manage view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request)
    {
        try {
            return view('inventory.site-transfer-bill.create');
        } catch (\Exception $e) {
            $data = [
                'action' => 'Site Transfer Billing get manage view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getApprovedTransaction(Request $request)
    {
        try {
            $projectSiteId = Session::get('global_project_site');
            $iterator = 0;
            $response = array();
            $alreadyGeneratedBillChallanIds = SiteTransferBill::whereNotNull('inventory_transfer_challan_id')->pluck('inventory_transfer_challan_id')->toArray();
            $closeStatusId = InventoryComponentTransferStatus::where('slug', 'close')->pluck('id')->first();
            $challans = InventoryTransferChallan::join('inventory_component_transfers', 'inventory_component_transfers.inventory_transfer_challan_id', '=', 'inventory_transfer_challan.id')
                ->join('inventory_transfer_types', 'inventory_transfer_types.id', '=', 'inventory_component_transfers.transfer_type_id')
                ->where('inventory_transfer_types.type', 'ilike', 'IN')
                ->where('inventory_transfer_challan.inventory_component_transfer_status_id', $closeStatusId)
                ->whereNotNull('inventory_component_transfers.transportation_amount')
                ->where('inventory_component_transfers.transportation_amount', '!=', 0)
                ->where('inventory_transfer_challan.project_site_in_id', $projectSiteId)
                ->whereNotIn('inventory_transfer_challan.id', $alreadyGeneratedBillChallanIds)->distinct('inventory_transfer_challan.id')->select('inventory_transfer_challan.id', 'inventory_transfer_challan.challan_number')->get();
            foreach ($challans as $challan) {
                $response[$iterator]['challan_id'] = $challan['id'];
                $otherdata = $challan->otherData();
                $vendorInfo = Vendor::where('id', $otherdata['vendor_id'])->first()->toArray();
                $contact_no = ($otherdata['mobile'] != "") ? $otherdata['mobile'] : $vendorInfo['alternate_contact'];
                $response[$iterator]['challan_number'] = $challan['challan_number'] . " : " . $vendorInfo['company'] . " : " . $contact_no;
                if (isset($otherdata['transportation_amount']) || $otherdata['transportation_amount'] != null) {
                    $response[$iterator]['subtotal'] = round($otherdata['transportation_amount'], 3);
                } else {
                    $response[$iterator]['subtotal'] = 0;
                }
                $response[$iterator]['tax_amount'] = round(($response[$iterator]['subtotal'] * ($otherdata['transportation_cgst_percent'] / 100)), 3);
                $response[$iterator]['tax_amount'] += round(($response[$iterator]['subtotal'] * ($otherdata['transportation_sgst_percent'] / 100)), 3);
                $response[$iterator]['tax_amount'] += round(($response[$iterator]['subtotal'] * ($otherdata['transportation_igst_percent'] / 100)), 3);
                $iterator++;
            }
            $status = 200;
        } catch (\Exception $e) {
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

    public function createSiteTransferBill(Request $request)
    {
        try {
            $siteTransferBillData = $request->except('_token', 'transfer_grn', 'extra_amount');
            $siteTransferBillData['extra_amount'] = $request['challan_id'];
            $siteTransferBillData['inventory_transfer_challan_id'] = $request['challan_id'];
            $siteTransferBill = SiteTransferBill::create($siteTransferBillData);
            $imageUploadPath = public_path() . env('SITE_TRANSFER_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . sha1($siteTransferBill->id);
            if (!file_exists($imageUploadPath)) {
                File::makeDirectory($imageUploadPath, 0777, true, true);
            }
            if ($request->has('bill_images')) {
                foreach ($request->bill_images as $billImage) {
                    $imageArray = explode(';', $billImage);
                    $image = explode(',', $imageArray[1])[1];
                    $pos  = strpos($billImage, ';');
                    $type = explode(':', substr($billImage, 0, $pos))[1];
                    $extension = explode('/', $type)[1];
                    $filename = mt_rand(1, 10000000000) . sha1(time()) . ".{$extension}";
                    $fileFullPath = $imageUploadPath . DIRECTORY_SEPARATOR . $filename;
                    $billImageData = [
                        'site_transfer_bill_id' => $siteTransferBill->id,
                        'name' => $filename,
                    ];
                    file_put_contents($fileFullPath, base64_decode($image));
                    SiteTransferBillImage::create($billImageData);
                }
            }
            $request->session()->flash('success', 'Site transfer bill created successfully. ');
            return redirect('/inventory/transfer/billing/manage');
        } catch (\Exception $e) {
            $data = [
                'action' => 'Create Site Transfer Bill',
                'data' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function listing(Request $request)
    {
        try {
            $skip = $request->start;
            $take = $request->length;
            $totalRecordCount = 0;
            $siteTransferBillData = array();
            $user = Auth::user();
            $records = array();
            $status = 200;
            $records['data'] = array();
            $records["draw"] = intval($request->draw);
            $siteTransferBillId = SiteTransferBill::orderBy('created_at', 'desc')->pluck('id')->toArray();
            $filterFlag = true;
            if ($request->has('bill_date') && $request->bill_date != '') {
                $siteTransferBillId = SiteTransferBill::whereDate('bill_date', $request->bill_date)
                    ->whereIn('id', $siteTransferBillId)
                    ->pluck('id')->toArray();
                if (count($siteTransferBillId) <= 0) {
                    $filterFlag = false;
                }
            }
            if ($filterFlag == true && $request->has('vendor_name') && $request->vendor_name != '') {
                $siteTransferBillId = SiteTransferBill::join('inventory_component_transfers', 'inventory_component_transfers.inventory_transfer_challan_id', '=', 'site_transfer_bills.inventory_transfer_challan_id')
                    ->join('vendors', 'vendors.id', '=', 'inventory_component_transfers.vendor_id')
                    ->whereIn('site_transfer_bills.id', $siteTransferBillId)
                    ->where('vendors.company', 'ilike', '%' . $request->vendor_name . '%')
                    ->pluck('site_transfer_bills.id')
                    ->toArray();
                if (count($siteTransferBillId) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($filterFlag == true && $request->has('project_name') && $request->project_name != '') {
                $siteTransferBillId = SiteTransferBill::join('inventory_component_transfers', 'inventory_component_transfers.inventory_transfer_challan_id', '=', 'site_transfer_bills.inventory_transfer_challan_id')
                    ->join('inventory_components', 'inventory_component_transfers.inventory_component_id', '=', 'inventory_components.id')
                    ->join('project_sites', 'project_sites.id', '=', 'project_sites.project_id')
                    ->join('projects', 'projects.id', '=', 'inventory_components.project_site_id')
                    ->where('projects.name', 'ilike', '%' . $request->project_name . '%')
                    ->whereIn('site_transfer_bills.id', $siteTransferBillId)
                    ->pluck('site_transfer_bills.id')
                    ->toArray();
                if (count($siteTransferBillId) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($filterFlag == true && $request->has('bill_number') && $request->bill_number != '') {
                $siteTransferBillId = SiteTransferBill::where('bill_number', 'like', $request->bill_number)
                    ->whereIn('id', $siteTransferBillId)
                    ->pluck('id')->toArray();
                if (count($siteTransferBillId) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($filterFlag == true && $request->has('basic_amt') && $request->basic_amt != '') {
                $siteTransferBillId = SiteTransferBill::whereRaw('(subtotal + extra_amount) = ?', $request->basic_amt)
                    ->whereIn('id', $siteTransferBillId)
                    ->pluck('id')->toArray();
                if (count($siteTransferBillId) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($filterFlag == true && $request->has('total_amt') && $request->total_amt != '') {
                $siteTransferBillId = SiteTransferBill::where('total', '=', $request->total_amt)
                    ->whereIn('id', $siteTransferBillId)
                    ->pluck('id')->toArray();
                if (count($siteTransferBillId) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($filterFlag) {
                $siteTransferBillData = SiteTransferBill::whereIn('id', $siteTransferBillId)
                    ->skip($skip)->take($take)
                    ->orderBy('created_at', 'desc')->get();
                $totalRecordCount = SiteTransferBill::whereIn('id', $siteTransferBillId)->count();
            }

            $paidAmount = $total = $pendingAmount = 0;
            if ($request->has('get_total')) {
                if ($filterFlag) {
                    $total = $siteTransferBillData->sum('total');
                    $paidAmount = SiteTransferBillPayment::whereIn('site_transfer_bill_id', $siteTransferBillData->pluck('id'))->sum('amount');
                    $pendingAmount = $total - $paidAmount;
                }
                $records['total'] = $total;
                $records['billtotal'] = $paidAmount;
                $records['paidtotal'] = $pendingAmount;
            } else {
                $records["recordsFiltered"] = $records["recordsTotal"] = $totalRecordCount;
                if ($request->length == -1) {
                    $length = count($siteTransferBillData);
                } else {
                    $length = $request->length;
                }
                $inTransferTypeId = InventoryTransferTypes::where('slug', 'site')->where('type', 'IN')->pluck('id')->first();
                for ($iterator = 0, $pagination = 0; $iterator < $length && $pagination < count($siteTransferBillData); $iterator++, $pagination++) {
                    $challan = $siteTransferBillData[$pagination]->inventoryTransferChallan;
                    $firstInTransfer = InventoryComponentTransfers::where('inventory_transfer_challan_id', $challan['id'])->where('transfer_type_id', $inTransferTypeId)->first();
                    $projectName = Project::join('project_sites', 'project_sites.project_id', '=', 'projects.id')
                        ->where('project_sites.id', $challan['project_site_in_id'])
                        ->pluck('projects.name')->first();
                    $paidAmount = SiteTransferBillPayment::where('site_transfer_bill_id', $siteTransferBillData[$pagination]['id'])->sum('amount');
                    $pendingAmount = $siteTransferBillData[$pagination]['total'] - $paidAmount;
                    if ($firstInTransfer->vendor == null) {
                        $vendorName = '-';
                    } else {
                        $vendorName = $firstInTransfer->vendor->company;
                    }
                    if ($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('edit-asset-maintenance-billing')) {
                        $actionButton = '<div id="sample_editable_1_new" class="btn btn-small blue" >
                        <a href="/inventory/transfer/billing/edit/' . $siteTransferBillData[$pagination]['id'] . '" style="color: white"> Edit
                    </div>';
                    } else {
                        $actionButton = '';
                    }

                    $records['data'][$iterator] = [
                        $challan['challan_number'],
                        $projectName,
                        date('j M Y', strtotime($siteTransferBillData[$pagination]['created_at'])),
                        date('j M Y', strtotime($siteTransferBillData[$pagination]['bill_date'])),
                        $siteTransferBillData[$pagination]['bill_number'],
                        $vendorName,
                        round(($siteTransferBillData[$pagination]['subtotal'] + $siteTransferBillData[$pagination]['extra_amount']), 3),
                        round(($siteTransferBillData[$pagination]['tax_amount'] + $siteTransferBillData[$pagination]['extra_amount_cgst_amount'] + $siteTransferBillData[$pagination]['extra_amount_sgst_amount'] + $siteTransferBillData[$pagination]['extra_amount_igst_amount']), 3),
                        $siteTransferBillData[$pagination]['total'],
                        $paidAmount,
                        $pendingAmount,
                        $actionButton
                    ];
                }
            }
        } catch (\Exception $e) {
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

    public function getEditView(Request $request, $siteTransferBill)
    {
        try {
            $totalPaidAmount = SiteTransferBillPayment::where('site_transfer_bill_id', $siteTransferBill->id)->sum('amount');
            $imageUploadPath = env('SITE_TRANSFER_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . sha1($siteTransferBill->id) . DIRECTORY_SEPARATOR;
            $paymentTypes = PaymentType::select('id', 'name')->whereIn('slug', ['cheque', 'neft', 'rtgs', 'internet-banking'])->get()->toArray();
            $banks = BankInfo::where('is_active', true)->select('id', 'bank_name', 'balance_amount')->get();
            $statistics = $this->getSiteWiseStatistics();
            $cashAllowedLimit = ($statistics['remainingAmount'] > 0) ? $statistics['remainingAmount'] : 0;
            return view('inventory.site-transfer-bill.edit')->with(compact('siteTransferBill', 'paymentTypes', 'imageUploadPath', 'totalPaidAmount', 'banks', 'cashAllowedLimit'));
        } catch (\Exception $e) {
            $data = [
                'action' => 'Get site transfer bill edit view',
                'site_transfer_bill' => $siteTransferBill,
                'exception' => $e->getMessage()
            ];
            Log::critcal(json_encode($data));
            abort(500);
        }
    }

    public function createPayment(Request $request)
    {
        try {
            $siteTransferBillPaymentData = $request->except('_token');
            if ($request['paid_from_slug'] == 'bank') {
                $bank = BankInfo::where('id', $request['bank_id'])->first();
                if ($request['amount'] <= $bank['balance_amount']) {
                    $siteTransferBillPayment = SiteTransferBillPayment::create($siteTransferBillPaymentData);
                    $bankData['balance_amount'] = $bank['balance_amount'] - $request['amount'];
                    $bank->update($bankData);
                    $request->session()->flash('success', 'Payment is created successfully.');
                    return redirect('/inventory/transfer/billing/edit/' . $request->site_transfer_bill_id);
                } else {
                    $request->session()->flash('success', 'Bank Balance Amount is insufficient for this transaction');
                    return redirect('/inventory/transfer/billing/edit/' . $request->site_transfer_bill_id);
                }
            } else {
                $statistics = $this->getSiteWiseStatistics();
                $cashAllowedLimit = ($statistics['remainingAmount'] > 0) ? $statistics['remainingAmount'] : 0;
                if ($request['amount'] <= $cashAllowedLimit) {
                    $siteTransferBillPayment = SiteTransferBillPayment::create($siteTransferBillPaymentData);
                    $request->session()->flash('success', 'Payment is created successfully.');
                    return redirect('/inventory/transfer/billing/edit/' . $request->site_transfer_bill_id);
                } else {
                    $request->session()->flash('success', 'Bank Balance Amount is insufficient for this transaction');
                    return redirect('/inventory/transfer/billing/edit/' . $request->site_transfer_bill_id);
                }
            }
        } catch (\Exception $e) {
            $data = [
                'action' => 'Create site transfer bill payment',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function paymentListing(Request $request, $siteTransferBill)
    {
        try {
            $records = array();
            $status = 200;
            $records['data'] = array();
            $records["draw"] = intval($request->draw);
            $siteTransferBillPaymentData = SiteTransferBillPayment::where('site_transfer_bill_id', $siteTransferBill->id)->orderBy('id', 'desc')->get();
            $records["recordsFiltered"] = $records["recordsTotal"] = count($siteTransferBillPaymentData);
            for ($iterator = 0, $pagination = $request->start; $iterator < $request->length && $pagination < count($siteTransferBillPaymentData); $iterator++, $pagination++) {
                if ($siteTransferBillPaymentData[$pagination]->paymentType == null) {
                    $paymentType = '-';
                } else {
                    $paymentType = $siteTransferBillPaymentData[$pagination]->paymentType->name;
                }
                $records['data'][] = [
                    date('d M Y', strtotime($siteTransferBillPaymentData[$pagination]['created_at'])),
                    $siteTransferBillPaymentData[$pagination]['amount'],
                    ($siteTransferBillPaymentData[$pagination]->paymentType != null) ? ucfirst($siteTransferBillPaymentData[$pagination]->paid_from_slug) . ' - ' . $siteTransferBillPaymentData[$pagination]->paymentType->name : ucfirst($siteTransferBillPaymentData[$pagination]->paid_from_slug),
                    $siteTransferBillPaymentData[$pagination]['reference_number'],
                ];
            }
        } catch (\Exception $e) {
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
