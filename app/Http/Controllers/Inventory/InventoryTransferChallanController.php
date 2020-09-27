<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\InventoryComponentTransfers;
use App\InventoryComponentTransferStatus;
use App\InventoryTransferChallan;
use App\InventoryTransferTypes;
use App\ProjectSite;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class InventoryTransferChallanController extends Controller
{

    public function __construct()
    {
        $this->middleware('custom.auth');
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
            $challan['from_site'] = $challan->projectSiteOut->project->name;
            $challan['to_site'] = $challan->projectSiteIn->project->name ?? '-';
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
}
