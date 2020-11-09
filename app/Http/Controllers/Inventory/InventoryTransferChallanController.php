<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomTraits\Inventory\InventoryTrait;
use App\Http\Controllers\CustomTraits\Notification\NotificationTrait;
use App\InventoryCart;
use App\InventoryComponent;
use App\InventoryComponentTransfers;
use App\InventoryComponentTransferStatus;
use App\InventoryTransferChallan;
use App\InventoryTransferTypes;
use App\Material;
use App\ProjectSite;
use App\Unit;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

use App\GRNCount;
use App\InventoryComponentOpeningStockHistory;
use App\User;
use Illuminate\Support\Facades\File;

class InventoryTransferChallanController extends Controller
{
    use InventoryTrait;
    use NotificationTrait;
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    /**
     * Delete Cart items
     */
    public function deleteCartItems(Request $request)
    {
        try {
            foreach ($request['cart_ids'] as $cartId) {
                $cartItem = InventoryCart::find($cartId);
                if ($cartItem) {
                    $cartItem->delete();
                }
            }
            return response()->json([
                "message"   => "success"
            ], 200);
        } catch (Exception $e) {
            $data = [
                'action' => 'Inventory Challan cart delete',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return false;
        }
    }

    /**
     * Create Cart items
     */
    public function createCartItems(Request $request)
    {
        try {
            $projectSiteId = Session::get('global_project_site');
            foreach ($request['component_ids'] as $componentId) {
                $inventoryComponent = InventoryComponent::where('id', $componentId)->where('project_site_id', $projectSiteId)->first();
                if ($inventoryComponent) {
                    $alreadyPresentCount = InventoryCart::where('inventory_component_id', $inventoryComponent['id'])->where('project_site_id', $inventoryComponent['project_site_id'])->count();
                    if ($alreadyPresentCount <= 0) {
                        if ($inventoryComponent->is_material == true) {
                            $materialUnit = Material::where('id', $inventoryComponent['reference_id'])->pluck('unit_id')->first();
                            if ($materialUnit == null) {
                                $materialUnit = Material::where('name', 'ilike', $inventoryComponent['name'])->pluck('unit_id')->first();
                            }
                            $unitId = $materialUnit->unit->id ?? null;
                        } else {
                            $unitId = Unit::where('slug', 'nos')->pluck('id')->first();
                        }
                        InventoryCart::create([
                            'inventory_component_id'    => $inventoryComponent['id'],
                            'project_site_id'           => $inventoryComponent['project_site_id'],
                            'unit_id'                   => $unitId,
                            'quantity'                  => 0
                        ]);
                    }
                }
            }
            return response()->json([
                "message"   => "success"
            ], 200);
        } catch (Exception $e) {
            $data = [
                'action' => 'Inventory Challan cart save',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return false;
        }
    }

    /**
     * Update Cart items
     */
    public function updateCartItems(Request $request)
    {
        try {
            if ($request->has('materials')) {
                foreach ($request['materials'] as $material) {
                    InventoryCart::where('id', $material['cart_id'])->update([
                        'quantity'  => $material['quantity'],
                        'unit_id'  => $material['unit_id']
                    ]);
                }
            }
            if ($request->has('assets')) {
                foreach ($request['assets'] as $asset) {
                    InventoryCart::where('id', $asset['cart_id'])->update([
                        'quantity'  => $asset['quantity'],
                    ]);
                }
            }
            return response()->json([
                "message"   => "success"
            ], 200);
        } catch (Exception $e) {
            $data = [
                'action' => 'Inventory Challan cart update',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return false;
        }
    }

    /**
     * Create Challan
     */
    public function createChallan(Request $request)
    {
        try {
            $now = Carbon::now();
            $challan = new InventoryTransferChallan([
                'challan_number'                        => 'CH',
                'project_site_out_id'                   => $request->out_project_site_id,
                'project_site_in_id'                    => $request->in_project_site_id,
                'project_site_out_date'                 => $now,
                'inventory_component_transfer_status_id' => InventoryComponentTransferStatus::where('slug', 'requested')->pluck('id')->first()
            ]);
            $challan->save();
            $challan->fresh();
            $challan->update(['challan_number'  => 'CH' . $challan->id]);
            $additionalData = $request->only(['out_project_site_id', 'user_id', 'in_project_site_id', 'vendor_id', 'transportation_amount', 'transportation_cgst_percent', 'transportation_sgst_percent', 'transportation_igst_percent', 'driver_name', 'mobile', 'vehicle_number', 'remark']);
            foreach ($request['inventory_cart'] as $cartId => $requestCartItem) {
                if (array_key_exists('quantity', $requestCartItem)) {
                    $cartItem = InventoryCart::find($cartId);
                    if ($cartItem) {
                        $inventoryComponentOutTransfer = $this->createSiteOutTransferData($requestCartItem, $now, $additionalData, $cartItem->inventoryComponent, $challan);
                        $cartItem->delete();
                    }
                }
            }
            $projectSites  = ProjectSite::join('projects', 'projects.id', '=', 'project_sites.project_id')
                ->where('project_sites.name', '!=', env('OFFICE_PROJECT_SITE_NAME'))->where('projects.is_active', true)->select('project_sites.id', 'project_sites.name', 'projects.name as project_name')->get()->toArray();
            $challanStatus = InventoryComponentTransferStatus::whereIn('slug', ['requested', 'open', 'close', 'disapproved'])->select('id', 'name', 'slug')->get()->toArray();
            return view('inventory/transfer/challan/manage')->with(compact('projectSites', 'challanStatus'));
        } catch (Exception $e) {
            dd($e->getMessage());
            $data = [
                'action' => 'Inventory Transfer Challan manage',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function createSiteOutTransferData($requestedCartData, $timestamp, $additionalData, $inventoryComponent, $challan)
    {
        try {
            $projectSite = ProjectSite::where('id', $additionalData['out_project_site_id'])->first();
            $inventoryComponentOutTransfer = [
                'transfer_type'                 => 'site',
                'unit_id'                       => $requestedCartData['unit_id'],
                'quantity'                      => $requestedCartData['quantity'],
                'source_name'                   => $projectSite->project->name . '-' . $projectSite->name,
                'rate_per_unit'                 => $requestedCartData['rate_per_unit'],
                'cgst_percentage'               => $requestedCartData['gst_percent'] / 2,
                'cgst_amount'                   => $requestedCartData['cgst_amount'],
                'sgst_percentage'               => $requestedCartData['gst_percent'] / 2,
                'sgst_amount'                   => $requestedCartData['sgst_amount'],
                'igst_percentage'               => '0',
                'igst_amount'                   => '0.00',
                'vendor_id'                     => $additionalData['vendor_id'],
                'transportation_amount'         => $additionalData['transportation_amount'] ?? 0,
                'transportation_cgst_percent'   => $additionalData['transportation_cgst_percent'] ?? 0,
                'transportation_sgst_percent'   => $additionalData['transportation_sgst_percent'] ?? 0,
                'transportation_igst_percent'   => $additionalData['transportation_igst_percent'] ?? 0,
                'driver_name'                   => $additionalData['driver_name'] ?? '',
                'mobile'                        => $additionalData['mobile'] ?? '',
                'vehicle_number'                => $additionalData['vehicle_number'] ?? '',
                'remark'                        => $additionalData['remark'] ?? '',
                'inventory_component_id'        => $inventoryComponent['id'],
                'user_id'                       => $additionalData['user_id'],
                'date'                          => $timestamp,
                'in_time'                       => $timestamp,
                'out_time'                      => $timestamp,
                'inventory_component_transfer_status_id'    =>  InventoryComponentTransferStatus::where('slug', 'requested')->pluck('id')->first(),
                'inventory_transfer_challan_id' => $challan['id']
            ];
            $inventoryComponentTransfer = $this->createInventoryComponentTransfer($inventoryComponentOutTransfer);
            // if ($request->has('work_order_images')) {
            //     $imageUploads = $this->uploadInventoryComponentTransferImages($request->work_order_images, $inventoryComponent->id, $inventoryComponentTransfer->id);
            // }
        } catch (Exception $e) {
            $data = [
                'action' => 'Add site out transfer',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function showSiteIn()
    {
        try {
            $projectSiteId = Session::get('global_project_site');
            $challans = InventoryTransferChallan::join('inventory_component_transfer_statuses', 'inventory_transfer_challan.inventory_component_transfer_status_id', '=', 'inventory_component_transfer_statuses.id')
                ->where('inventory_component_transfer_statuses.slug', 'open')
                ->whereNull('project_site_in_date')
                ->where('project_site_in_id', $projectSiteId)
                ->select('inventory_transfer_challan.id', 'inventory_transfer_challan.challan_number')
                ->get()->toArray();
            return view('inventory/transfer/challan/site/in')->with(compact('challans'));
        } catch (Exception $e) {
            $data = [
                'action' => 'Inventory Transfer Challan Site In Show',
                'params' => [],
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getManageView(Request $request)
    {
        try {
            $projectSites  = ProjectSite::join('projects', 'projects.id', '=', 'project_sites.project_id')
                ->where('project_sites.name', '!=', env('OFFICE_PROJECT_SITE_NAME'))->where('projects.is_active', true)->select('project_sites.id', 'project_sites.name', 'projects.name as project_name')->get()->toArray();
            $challanStatus = InventoryComponentTransferStatus::whereIn('slug', ['requested', 'open', 'close', 'disapproved'])->select('id', 'name', 'slug')->get()->toArray();
            return view('inventory/transfer/challan/manage')->with(compact('projectSites', 'challanStatus'));
        } catch (Exception $e) {
            $data = [
                'action' => 'Inventory Transfer Challan manage',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getChallanListing(Request $request)
    {
        try {
            $skip = $request->start;
            $take = $request->length;
            $status = 200;

            $challanData = new InventoryTransferChallan();
            $totalRecords = $challanData->count();

            if ($request->has('search_challan') && $request['search_challan'] != null) {
                $challanData = $challanData->where('challan_number', 'ilike', '%' . $request['search_challan'] . '%');
            }

            if ($request->has('search_site_from') && $request['search_site_from'] != null) {
                $projectSiteIds = ProjectSite::join('projects', 'projects.id', 'project_sites.project_id')
                    ->where('projects.name', 'ilike', '%' . $request['search_site_from'] . '%')->pluck('project_sites.id')->toArray();
                $challanData = $challanData->whereIn('project_site_out_id', $projectSiteIds);
            }

            if ($request->has('search_site_in') && $request['search_site_in'] != null) {
                $projectSiteIds = ProjectSite::join('projects', 'projects.id', 'project_sites.project_id')
                    ->where('projects.name', 'ilike', '%' . $request['search_site_in'] . '%')->pluck('id')->toArray();
                $challanData = $challanData->whereIn('project_site_in_id', $projectSiteIds);
            }

            if ($request->has('status') && $request['status'] != null && $request['status'] != 'all') {
                $challanData = $challanData->where('inventory_component_transfer_status_id', $request['status']);
            }

            $challanData = $challanData->orderBy('created_at', 'desc')->skip($skip)->take($take)->get();

            $records['data'] = array();
            $end = $request->length < 0 ? count($challanData) : $request->length;

            for ($iterator = 0, $pagination = 0; $iterator < $end && $pagination < count($challanData); $iterator++, $pagination++) {
                $challanRelatedData = $challanData[$pagination]->otherData();
                switch ($challanData[$pagination]->inventoryComponentTransferStatus->slug) {
                    case 'requested':
                        $actionDropDownStatus = '<i class="fa fa-circle-o" title="Requested" style="font-size:24px;color:orange">&nbsp;&nbsp;</i>';
                        break;
                    case 'disapproved':
                        $actionDropDownStatus = '<i class="fa fas fa-ban" title="Disapproved" style="font-size:24px;color:red">&nbsp;&nbsp;</i>';
                        break;
                    case 'open':
                        $actionDropDownStatus = '<i class="fa fa-check-circle" title="Open" style="font-size:24px;color:green">&nbsp;&nbsp;</i>';
                        break;
                    case 'close':
                        $actionDropDownStatus = '<i class="fa fa-times-circle" title="Close" style="font-size:24px;color:red">&nbsp;&nbsp;</i>';
                        break;
                }

                $actionDropDown =  '<div class="btn btn-small blue" title="PDF">
                                            <a href="/inventory/transfer/challan/pdf/' . $challanData[$pagination]['id'] . '" style="color: white"> 
                                                <i class="fa fa-download" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                        <div class="btn btn-small blue" title="EDIT">
                                            <a href="/inventory/transfer/challan/edit/' . $challanData[$pagination]['id'] . '" style="color: white">
                                                <i class="fa fa-edit" aria-hidden="true"></i>
                                            </a>
                                        </div>';
                $records['data'][$iterator] = [
                    date('d M Y', strtotime($challanData[$pagination]->created_at)),
                    $challanData[$pagination]->challan_number,
                    $challanData[$pagination]->projectSiteOut->project->name,
                    $challanData[$pagination]->projectSiteIn->project->name ?? '-',
                    $challanRelatedData['transportation_amount'] ?? 0,
                    $challanRelatedData['transportation_tax_total'] ?? 0,
                    $actionDropDownStatus,
                    $actionDropDown
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $totalRecords;
            $records["recordsFiltered"] = $totalRecords;
        } catch (Exception $e) {
            dd($e->getMessage());
            $data = [
                'action' => 'Challan listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $records = [];
        }
        return response()->json($records, $status);
    }

    public function show(Request $request, $challanId)
    {
        try {
            $challan = InventoryTransferChallan::find($challanId);
            $outTransferType = InventoryTransferTypes::where('slug', 'site')->where('type', 'OUT')->first();
            $inventoryComponentOutTransfers = $outTransferType->inventoryComponentTransfers->where('inventory_transfer_challan_id', $challan['id']);
            foreach ($inventoryComponentOutTransfers as $outTransferComponent) {
                $siteInQuantity = '-';
                if ($outTransferComponent['related_transfer_id'] != null) {
                    $inTransferComponent = InventoryComponentTransfers::find($outTransferComponent['related_transfer_id']);
                    $siteInQuantity = $inTransferComponent->quantity ?? '-';
                }
                $components[] = [
                    'name'              => $outTransferComponent->inventoryComponent->name,
                    'is_material'       => $outTransferComponent->inventoryComponent->is_material,
                    'unit'              => $outTransferComponent->unit->name,
                    'site_out_quantity' => $outTransferComponent->quantity,
                    'site_in_quantity'  => $siteInQuantity
                ];
            }
            $challan['other_data'] = $challan->otherData()->toArray();
            return view('inventory/transfer/challan/edit')->with(compact('challan', 'projectSites', 'challanStatus', 'components'));
        } catch (Exception $e) {
            dd($e->getMessage());
            $data = [
                'action'    => 'Inventory Transfer Challan Edit view',
                'params'    => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function generatePDF(Request $request, $challanId)
    {
        try {
            $challan = InventoryTransferChallan::find($challanId);
            $challan['from_site'] = $challan->projectSiteOut->project->name;
            $challan['to_site'] = $challan->projectSiteIn->project->name ?? '-';
            $challan['from_site'] = $challan->projectSiteOut->project->name;
            $challan['to_site'] = $challan->projectSiteIn->project->name ?? '-';
            $data = [
                'challan'       => $challan,
                'other_data'    => $challan->otherData()->toArray()
            ];
            $outTransferComponents = InventoryComponentTransfers::join('inventory_components', 'inventory_components.id', '=', 'inventory_component_transfers.inventory_component_id')
                ->join('units', 'units.id', '=', 'inventory_component_transfers.unit_id')
                ->join('inventory_transfer_types', 'inventory_transfer_types.id', '=', 'inventory_component_transfers.transfer_type_id')
                ->where('inventory_transfer_types.slug', 'site')->where('inventory_transfer_types.type', 'OUT')
                ->where('inventory_component_transfers.inventory_transfer_challan_id', $challan['id'])
                ->select('inventory_components.name as inventory_component_name', 'inventory_components.is_material', 'inventory_component_transfers.quantity', 'units.name as unit_name', 'inventory_component_transfers.rate_per_unit', 'inventory_component_transfers.cgst_amount', 'inventory_component_transfers.sgst_amount', 'inventory_component_transfers.igst_amount', DB::raw('COALESCE((cgst_amount+sgst_amount+igst_amount),0) as gst'), DB::raw('(rate_per_unit+COALESCE((cgst_amount+sgst_amount+igst_amount),0)) as total'))->get();
            $data['materials'] = $outTransferComponents->where('is_material', true)->toArray();
            $data['materialTotal'] = [
                'quantity_total' => array_sum(array_column($data['materials'], 'quantity')),
                'rate_per_unit'  => array_sum(array_column($data['materials'], 'rate_per_unit')),
                'gst_total'      => array_sum(array_column($data['materials'], 'gst')),
                'total'          => array_sum(array_column($data['materials'], 'total'))
            ];
            $data['assets'] = $outTransferComponents->where('is_material', false);
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML(view('inventory/transfer/challan/pdf/challan', $data));
            // $pdf->setPaper('A4', 'landscape');
            return $pdf->stream();
        } catch (\Exception $e) {
            $data = [
                'actions' => 'Generate Challan PDF',
                'params' => [],
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500, $e->getMessage());
        }
    }

    /**
     * Authenticate password for challan close
     */
    public function authenticateChallanClose(Request $request)
    {
        try {
            $password = $request->password;
            if (Hash::check($password, env('CLOSE_INVENTORY_CHALLAN_PASSWORD'))) {
                $status = 200;
                $message = 'Authentication successful !!';
            } else {
                $status = 401;
                $message = 'You are not authorised to close this purchase order.';
            }
        } catch (\Exception $e) {
            $message = 'Fail';
            $status = 500;
            $data = [
                'action' => 'Authenticate Challan Close',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
        }
        $response = [
            'message' => $message,
        ];
        return response()->json($response, $status);
    }

    /**
     * Close Challan
     */
    public function closeChallan(Request $request)
    {
        try {
            $closeStatusId = InventoryComponentTransferStatus::where('slug', 'close')->pluck('id')->first();
            $challan = InventoryTransferChallan::find($request['challan_id']);
            if ($challan) {
                $challan->update(['inventory_component_transfer_status_id' => $closeStatusId]);
            }
            $message = "Challan closed successfully !";
        } catch (\Exception $e) {
            $data = [
                'action' => 'Close Challan',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $message = "Something went wrong" . $e->getMessage();
        }
        return response()->json($message);
    }

    /**
     * Get Challan Detail
     */
    public function getChallanDetail(Request $request)
    {
        try {
            $challan = InventoryTransferChallan::find($request['challan_id']);
            $outTransferType = InventoryTransferTypes::where('slug', 'site')->where('type', 'OUT')->first();
            $inventoryComponentOutTransfers = $outTransferType->inventoryComponentTransfers->where('inventory_transfer_challan_id', $challan['id']);
            foreach ($inventoryComponentOutTransfers as $outTransferComponent) {
                $siteInQuantity = '-';
                if ($outTransferComponent['related_transfer_id'] != null) {
                    $inTransferComponent = InventoryComponentTransfers::find($outTransferComponent['related_transfer_id']);
                    $siteInQuantity = $inTransferComponent->quantity ?? '-';
                }
                $components[] = [
                    'site_out_transfer_id'  => $outTransferComponent->id,
                    'name'              => $outTransferComponent->inventoryComponent->name,
                    'is_material'       => $outTransferComponent->inventoryComponent->is_material,
                    'reference_id'      => $outTransferComponent->inventoryComponent->reference_id,
                    'unit'              => $outTransferComponent->unit->name,
                    'site_out_grn'      => $outTransferComponent->grn,
                    'site_out_quantity' => $outTransferComponent->quantity,
                    'site_in_quantity'  => $siteInQuantity
                ];
            }
            $challan['other_data'] = $challan->otherData()->toArray();
            $challan['from_site'] = $challan->projectSiteOut->project->name;
            $challan['to_site'] = $challan->projectSiteIn->project->name ?? '-';
            $status = 200;
            return view('partials.inventory.transfer.challan.detail')->with(compact('challan', 'components'));
        } catch (Exception $e) {
            $data = [
                'action' => 'Get Challan Details',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            $status = 500;
            $challan = array();
            Log::critical(json_encode($data));
        }
        return response()->json($challan, $status);
    }

    /**
     * Challan Site IN Post GRN generation
     */
    public function createSiteIn(Request $request)
    {
        try {
            $updateChallanStatusToClose = true;
            $now = Carbon::now();
            $approvedStatusId = InventoryComponentTransferStatus::where('slug', 'approved')->pluck('id')->first();
            foreach ($request['component'] as $transferComponent) {
                $inventoryComponentInTransfer = InventoryComponentTransfers::find($transferComponent['site_in_transfer_id']);
                if ($inventoryComponentInTransfer) {
                    $inventoryComponentOutTransfer = InventoryComponentTransfers::find($inventoryComponentInTransfer['related_transfer_id']);
                    $inventoryComponentInTransfer->update([
                        'quantity'                              => $transferComponent['site_in_quantity'],
                        'out_time'                              => $now,
                        'date'                                  => $now,
                        'inventory_component_transfer_status_id' => $approvedStatusId,
                        'remark'                                => $request['remark'] ?? ''
                    ]);
                    if ($updateChallanStatusToClose && ($inventoryComponentOutTransfer['quantity'] != $inventoryComponentInTransfer['quantity'])) {
                        $updateChallanStatusToClose = false;
                    }
                    $inventoryComponentTransferImages[] = [
                        'inventory_component_transfer_id'   => $inventoryComponentInTransfer['id'],
                    ];

                    $webTokens = [$inventoryComponentOutTransfer->user->web_fcm_token];
                    $mobileTokens = [$inventoryComponentOutTransfer->user->mobile_fcm_token];
                    $notificationString = 'From ' . $inventoryComponentInTransfer->source_name . ' stock received to ';
                    $notificationString .= $inventoryComponentInTransfer->inventoryComponent->projectSite->project->name . ' - ' . $inventoryComponentInTransfer->inventoryComponent->projectSite->name . ' ';
                    $notificationString .= $inventoryComponentInTransfer->inventoryComponent->name . ' - ' . $inventoryComponentInTransfer->quantity . ' and ' . $inventoryComponentInTransfer->unit->name;
                    $this->sendPushNotification('Manisha Construction', $notificationString, $webTokens, $mobileTokens, 'c-m-s-i-t');
                }
            }
            if ($request->has('post_grn_image') && count($request->post_grn_image) > 0) {
                $sha1challanId = sha1($request['challan_id']);
                $imageUploadPath = public_path() . env('INVENTORY_COMPONENT_IMAGE_UPLOAD');
                $newInUploadPath = $imageUploadPath . DIRECTORY_SEPARATOR . $sha1challanId . DIRECTORY_SEPARATOR . 'in';
                if (!file_exists($newInUploadPath)) {
                    File::makeDirectory($newInUploadPath, $mode = 0777, true, true);
                }
                foreach ($request['post_grn_image'] as $key1 => $imageName) {
                    $imageArray = explode(';', $imageName);
                    $image = explode(',', $imageArray[1])[1];
                    $pos  = strpos($imageName, ';');
                    $type = explode(':', substr($imageName, 0, $pos))[1];
                    $extension = explode('/', $type)[1];
                    $filename = mt_rand(1, 10000000000) . sha1(time()) . ".{$extension}";
                    $fileFullPath = $newInUploadPath . DIRECTORY_SEPARATOR . $filename;
                    file_put_contents($fileFullPath, base64_decode($image));
                    data_fill($inventoryComponentTransferImages, '*.name', $filename);
                    data_fill($inventoryComponentTransferImages, '*.created_at', $now);
                    data_fill($inventoryComponentTransferImages, '*.updated_at', $now);
                    DB::table('inventory_component_transfer_images')->insert($inventoryComponentTransferImages);
                }
            }

            $request->session()->flash('success', 'Inventory Component In Transfer for Challan Saved Successfully!!');
            return redirect('/inventory/transfer/challan/manage');
        } catch (Exception $e) {
            $data = [
                'action' => 'Create Site In for Challan',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            $request->session()->flash('fail', 'Something went wrong');
            return redirect('/inventory/transfer/challan/manage');
        }
    }

    /**
     * Challan Site In Pre GRN
     */
    public function preUploadSIteInImages(Request $request)
    {
        try {
            $user = Auth::user();
            $updateChallanStatusToClose = true;
            $currentDate = Carbon::now();
            $challan = InventoryTransferChallan::find($request['challan_id']);
            $grnGeneratedStatusId = InventoryComponentTransferStatus::where('slug', 'grn-generated')->pluck('id')->first();
            $siteInTypeId = InventoryTransferTypes::where('slug', 'site')->where('type', 'ilike', 'IN')->pluck('id')->first();
            foreach ($request['items'] as $transferComponent) {
                $relatedInventoryComponentOutTransferData = InventoryComponentTransfers::where('id', $transferComponent['related_inventory_component_transfer_id'])->first();
                $outInventoryComponent = $relatedInventoryComponentOutTransferData->inventoryComponent;
                $projectSite = ProjectSite::where('id', $outInventoryComponent->project_site_id)->first();
                $sourceName = $projectSite->project->name . '-' . $projectSite->name;
                $monthlyGrnGeneratedCount = GRNCount::where('month', $currentDate->month)->where('year', $currentDate->year)->pluck('count')->first();
                if ($monthlyGrnGeneratedCount != null) {
                    $serialNumber = $monthlyGrnGeneratedCount + 1;
                } else {
                    $serialNumber = 1;
                }
                $grn = "GRN" . date('Ym') . ($serialNumber);
                $inInventoryComponent = InventoryComponent::where('project_site_id', $challan['project_site_in_id'])->where('reference_id', $outInventoryComponent['reference_id'])
                    ->where('is_material', $outInventoryComponent['is_material'])->first();
                if (!$inInventoryComponent) {
                    $inInventoryComponent = InventoryComponent::create([
                        'name'              => $outInventoryComponent['name'],
                        'project_site_id'   => $challan['project_site_in_id'],
                        'is_material'       => $outInventoryComponent['is_material'],
                        'reference_id'      => $outInventoryComponent['reference_id'],
                        'opening_stock'     => 0
                    ]);
                    InventoryComponentOpeningStockHistory::create([
                        'inventory_component_id' => $inInventoryComponent['id'],
                        'opening_stock' => $inInventoryComponent['opening_stock']
                    ]);
                }
                $inventoryComponentInTransfer = InventoryComponentTransfers::create([
                    'inventory_component_id'                    => $inInventoryComponent['id'],
                    'transfer_type_id'                          => $siteInTypeId,
                    'quantity'                                  => $transferComponent['site_in_quantity'],
                    'unit_id'                                   => $relatedInventoryComponentOutTransferData['unit_id'],
                    'source_name'                               => $sourceName,
                    'bill_number'                               => $relatedInventoryComponentOutTransferData['bill_number'],
                    'bill_amount'                               => $relatedInventoryComponentOutTransferData['bill_amount'],
                    'vehicle_number'                            => $relatedInventoryComponentOutTransferData['vehicle_number'],
                    'in_time'                                   => $currentDate,
                    'date'                                      => $currentDate,
                    'user_id'                                   => $user['id'],
                    'grn'                                       => $grn,
                    'inventory_component_transfer_status_id'    => $grnGeneratedStatusId,
                    'rate_per_unit'                             => $relatedInventoryComponentOutTransferData['rate_per_unit'],
                    'cgst_percentage'                           => $relatedInventoryComponentOutTransferData['cgst_percentage'],
                    'sgst_percentage'                           => $relatedInventoryComponentOutTransferData['sgst_percentage'],
                    'igst_percentage'                           => $relatedInventoryComponentOutTransferData['igst_percentage'],
                    'cgst_amount'                               => $relatedInventoryComponentOutTransferData['cgst_amount'],
                    'sgst_amount'                               => $relatedInventoryComponentOutTransferData['sgst_amount'],
                    'igst_amount'                               => $relatedInventoryComponentOutTransferData['igst_amount'],
                    'total'                                     => $relatedInventoryComponentOutTransferData['total'],
                    'vendor_id'                                 => $relatedInventoryComponentOutTransferData['vendor_id'],
                    'transportation_amount'                     => $relatedInventoryComponentOutTransferData['transportation_amount'],
                    'transportation_cgst_percent'               => $relatedInventoryComponentOutTransferData['transportation_cgst_percent'],
                    'transportation_sgst_percent'               => $relatedInventoryComponentOutTransferData['transportation_sgst_percent'],
                    'transportation_igst_percent'               => $relatedInventoryComponentOutTransferData['transportation_igst_percent'],
                    'driver_name'                               => $relatedInventoryComponentOutTransferData['driver_name'],
                    'mobile'                                    => $relatedInventoryComponentOutTransferData['mobile'],
                    'related_transfer_id'                       => $relatedInventoryComponentOutTransferData['id'],
                    'inventory_transfer_challan_id'             => $relatedInventoryComponentOutTransferData['inventory_transfer_challan_id']
                ]);

                $relatedInventoryComponentOutTransferData->update(['related_transfer_id' => $inventoryComponentInTransfer['id']]);
                if ($monthlyGrnGeneratedCount != null) {
                    GRNCount::where('month', $currentDate->month)->where('year', $currentDate->year)->update(['count' => $serialNumber]);
                } else {
                    GRNCount::create(['month' => $currentDate->month, 'year' => $currentDate->year, 'count' => $serialNumber]);
                }
                if ($updateChallanStatusToClose && ($relatedInventoryComponentOutTransferData['quantity'] != $inventoryComponentInTransfer['quantity'])) {
                    $updateChallanStatusToClose = false;
                }
                $response[] = [
                    'inventory_component_transfer_id'   => $inventoryComponentInTransfer['id'],
                    'grn'                               => $grn,
                    'reference_id'                      => $outInventoryComponent['reference_id']
                ];
                $inventoryComponentTransferImages[] = [
                    'inventory_component_transfer_id'   => $inventoryComponentInTransfer['id'],
                ];
            }
            $challanUpdateData['project_site_in_date'] = $currentDate;
            if ($updateChallanStatusToClose) {
                $challanUpdateData['inventory_component_transfer_status_id'] = InventoryComponentTransferStatus::where('slug', 'close')->pluck('id')->first();
            }
            $challan->update($challanUpdateData);

            if ($request->has('imageArray')) {
                $sha1challanId = sha1($challan['id']);
                $imageUploadPath = public_path() . env('INVENTORY_COMPONENT_IMAGE_UPLOAD');
                $newInUploadPath = $imageUploadPath . DIRECTORY_SEPARATOR . $sha1challanId . DIRECTORY_SEPARATOR . 'in';
                if (!file_exists($newInUploadPath)) {
                    File::makeDirectory($newInUploadPath, $mode = 0777, true, true);
                }
                $imageArray = explode(';', $request['imageArray']);
                $image = explode(',', $imageArray[1])[1];
                $pos  = strpos($request['imageArray'], ';');
                $type = explode(':', substr($request['imageArray'], 0, $pos))[1];
                $extension = explode('/', $type)[1];
                $filename = mt_rand(1, 10000000000) . sha1(time()) . ".{$extension}";
                $fileFullPath = $newInUploadPath . DIRECTORY_SEPARATOR . $filename;
                file_put_contents($fileFullPath, base64_decode($image));
                data_fill($inventoryComponentTransferImages, '*.name', $filename);
                data_fill($inventoryComponentTransferImages, '*.created_at', $currentDate);
                data_fill($inventoryComponentTransferImages, '*.updated_at', $currentDate);
                DB::table('inventory_component_transfer_images')->insert($inventoryComponentTransferImages);
            }
            $status = 200;
        } catch (Exception $e) {
            $data = [
                'action' => 'Generate Pre GRN Site In and image upload',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $response = array();
            $status = 500;
        }
        return response()->json($response, $status);
    }

    public function approveDisapproveChallan(Request $request, $challanId)
    {
        try {
            $challan = InventoryTransferChallan::find($challanId);
            if ($challan) {
                $statusSlug = ($request['status'] === 'approved') ? 'open' : $request['status'];
                $statusId = InventoryComponentTransferStatus::where('slug', $statusSlug)->pluck('id')->first();
                if ($statusId) {
                    $challan->update(['inventory_component_transfer_status_id' => $statusId]);
                    return response()->json([
                        "message"   => "Success",
                        "success"   => true
                    ], 200);
                }
            }
            return response()->json([
                "message"   => "Not found",
                "success"   => false
            ], 422);
        } catch (Exception $e) {
            $data = [
                'action'    => 'Approve Disapprove Challan',
                'params'    => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json([
                "message"   => "Internal Server error",
                "success"   => false
            ], 500);
        }
    }
}
