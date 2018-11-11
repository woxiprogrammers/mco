<?php

namespace App\Http\Controllers\Subcontractor;

use App\SubcontractorBill;
use App\Summary;
use App\Tax;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class SubcontractorBillController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request, $subcontractorStructure){
        try{
            $subcontractorStructureId = $subcontractorStructure->id;
            return view('subcontractor.bill.manage')->with(compact('subcontractorStructureId'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Bill manage view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request, $subcontractorStructure){
        try{
            $subcontractorStructureSummaries = $subcontractorStructure->summaries->toArray();
            $iterator = 0;
            foreach($subcontractorStructureSummaries as $subcontractorStructureSummary){
                $subcontractorStructureSummaries[$iterator]['summary_name'] = Summary::where('id', $subcontractorStructureSummary['summary_id'])->pluck('name')->first();
                $subcontractorStructureSummaries[$iterator]['prev_quantity'] = SubcontractorBill::join('subcontractor_structure', 'subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                                                    ->join('subcontractor_bill_summaries','subcontractor_bill_summaries.subcontractor_bill_id','=','subcontractor_bills.id')
                                                    ->where('subcontractor_bill_summaries.subcontractor_structure_summary_id', $subcontractorStructureSummary['id'])
                                                    ->where('subcontractor_bills.sc_structure_id', $subcontractorStructure->id)
                                                    ->sum('quantity');

                $subcontractorStructureSummaries[$iterator]['allowed_quantity'] = $subcontractorStructureSummary['total_work_area'] - $subcontractorStructureSummaries[$iterator]['prev_quantity'];
                $iterator += 1;
            }
            $totalBillCount = $subcontractorStructure->subcontractorBill->count();
            $billName = "R.A. ".($totalBillCount + 1);
            $taxes = Tax::whereNotIn('slug',['vat'])->where('is_active',true)->where('is_special',false)->select('id','name','slug','base_percentage')->get();
            return view('subcontractor.bill.create')->with(compact('billName', 'taxes', 'subcontractorStructure', 'subcontractorStructureSummaries'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get subcontractor bill create view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
