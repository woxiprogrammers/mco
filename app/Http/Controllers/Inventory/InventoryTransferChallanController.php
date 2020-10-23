<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\InventoryCart;
use App\InventoryComponent;
use App\InventoryComponentTransfers;
use App\InventoryComponentTransferStatus;
use App\InventoryTransferChallan;
use App\InventoryTransferTypes;
use App\Material;
use App\ProjectSite;
use App\Unit;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class InventoryTransferChallanController extends Controller
{
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
            dd($request->all());
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

    public function showSiteIn()
    {
        try {
            $projectSiteId = Session::get('global_project_site');
            // $challan = InventoryTransferChallan::join('inventory_component_transfers', 'inventory_component_transfers.inventory_transfer_challan_id', '=', 'inventory_transfer_challan.id')
            //     ->join('inventory_component_transfer_statuses', 'inventory_transfer_challan.inventory_component_transfer_status_id', '=', 'inventory_component_transfer_statuses.id')
            //     ->where('inventory_component_transfer_statuses.slug', 'open')
            //     ->where('inventory_component_transfers.challan_id')->where('project_site_in_id', $projectSiteId);
            $challans = InventoryTransferChallan::select('id', 'challan_number')->limit(10)->get()->toArray();
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
            //$challan = InventoryTransferChallan::find($request['challan_id']);
            $challan = InventoryTransferChallan::find(4);
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

    public function createSiteIn(Request $request)
    {
        try {
            dd($request->all());
        } catch (Exception $e) {
            dd($e->getMessage());
            $data = [
                'action' => 'Create Site In for Challan',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            $status = 500;
            $challan = array();
        }
    }

    public function getInventoryCartComponents(Request $request)
    {
        try {
        } catch (Exception $e) {
            $data = [
                'action'    => 'Get Inventory cart components.',
                'params'    => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function addInventoryCartComponents(Request $request)
    {
        try {
            dd('in add inventory Component....');
        } catch (Exception $e) {
            $data = [
                'action'    => 'Add Inventory cart components.',
                'params'    => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }
}
