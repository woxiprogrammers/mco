<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\InventoryComponentTransferStatus;
use App\InventoryTransferChallan;
use App\ProjectSite;
use Exception;
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
            $challanStatus = InventoryComponentTransferStatus::whereIn('slug', ['open', 'close'])->select('id', 'name', 'slug')->get()->toArray();
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
            $totalRecordCount = 0;
            $user = Auth::user();
            $status = 200;
            $search_from = null;
            $search_to = null;
            $search_challan = null;
            $search_qty = null;
            $search_amt = null;
            $search_grn_out = null;
            $search_grn_in = null;
            $search_status = 'all';



            $inventoryTransferData = array();

            $challanData = InventoryTransferChallan::orderBy('created_at', 'desc')->skip($skip)->take($take)->get();

            $iTotalRecords = count($challanData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($challanData) : $request->length;

            for ($iterator = 0, $pagination = 0; $iterator < $end && $pagination < count($challanData); $iterator++, $pagination++) {
                $challanRelatedData = $challanData[$pagination]->otherData();
                if ($challanData[$pagination]->inventoryComponentTransferStatus->slug == 'open') {
                    $actionDropDownStatus = '<i class="fa fa-check-circle" title="Open" style="font-size:24px;color:green">&nbsp;&nbsp;</i>';
                } else {
                    $actionDropDownStatus = '<i class="fa fa-times-circle" title="Close" style="font-size:24px;color:red">&nbsp;&nbsp;</i>';
                }
                $actionDropDown =  '<div id="sample_editable_1_new" class="btn btn-small blue">
                                            <a href="/inventory/pdf/' . $challanData[$pagination]['id'] . '" style="color: white"> 
                                                PDF <i class="fa fa-download" aria-hidden="true"></i>
                                            </a>
                                        </div>';


                $records['data'][$iterator] = [
                    date('d M Y', strtotime($challanData[$pagination]->created_at)),
                    $challanData[$pagination]->challan_number,
                    $challanData[$pagination]->projectSiteOut->project->name,
                    $challanData[$pagination]->projectSiteIn->project->name,
                    $challanRelatedData['transportation_amount'],
                    $challanRelatedData['transportation_tax_total'],
                    $challanRelatedData['transportation_total'],
                    $actionDropDownStatus,
                    $actionDropDown
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = count($challanData);
            $records["recordsFiltered"] = count($challanData);
        } catch (\Exception $e) {
            dd($e->getMessage());
            $data = [
                'action' => 'Request Component listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = array();
        }
        return response()->json($records, $status);
    }
}
