<?php

namespace App\Http\Controllers\Report;

use App\Bill;
use App\BillStatus;
use App\BillTax;
use App\BillTransaction;
use App\Category;
use App\Employee;
use App\Http\Controllers\CustomTraits\BillTrait;
use App\Material;
use App\Helper\MaterialProductHelper;
use App\PeticashSalaryTransaction;
use App\PeticashTransactionType;
use App\ProjectSite;
use App\Quotation;
use App\Subcontractor;
use App\Tax;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    use BillTrait;
    public function __construct()
    {
        $this->middleware('custom.auth');
    }
    public function reportsRoute(Request $request) {
        try {
            $curr_date = Carbon::now()->subDays(30);
            $last_date = Carbon::now();
            $start_date = date('d/m/Y',strtotime($curr_date));
            $end_date = date('d/m/Y',strtotime($last_date));
            $sites = ProjectSite::get(['id','name','address'])->toArray();
            $billIds = Bill::where('bill_status_id',BillStatus::where('slug','approved')->pluck('id')->first())->pluck('id');
            $billProjectSites = Quotation::join('bills','quotations.id','=','bills.quotation_id')
                                    ->join('project_sites','quotations.project_site_id','=','project_sites.id')
                                    ->whereIn('bills.id',$billIds)
                ->select('project_sites.id','project_sites.name','project_sites.address')->get()->toArray();
            $categories = Category::where('is_active', true)->get(['id','name','slug'])->toArray();
            $materials = Material::get(['id','name'])->toArray();
            $subcontractors = Subcontractor::get(['id','company_name'])->toArray();
            $employees = Employee::where('employee_type_id', 1)->get(['id','name','employee_id'])->toArray();
            $vendors = Vendor::get(['id','name','company'])->toArray();
            return view('report.mainreport')->with(compact('vendors','employees','subcontractors','sites','categories','start_date','end_date','materials','billProjectSites'));
        } catch(\Exception $e) {
            $data = [
                'action' => 'Get Report View',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function downloadReports(Request $request) {
        $downloadSheetFlag = true;
        $curr_date = Carbon::now();
        $curr_date = date('d_m_Y_h_i_s',strtotime($curr_date));
        $report_type = $request->report_type;
        //$start_date = $request->start_date;
        $startDate = explode('/',$request->start_date);
        $start_date = $startDate[2].'-'.$startDate[1].'-'.$startDate[0].' 00:00:00';
        $endDate = explode('/',$request->end_date);
        $end_date = $endDate[2].'-'.$endDate[1].'-'.$endDate[0].' 24:00:00';

        switch($report_type) {
            case 'materialwise_purchase_report':
                $site_id = $request->materialwise_purchase_report_site_id;
                $category_id = $request->category_id;
                $material_ids = $request->material_id;
                $header = array(
                    'Sr. No', 'Material Name', 'Quantity', 'Unit', 'Basic Amount', 'Total Tax Amount',
                    'Total Amount', 'Average Amount'
                );
                $data = array(
                    array('data1', 'data2'),
                    array('data3', 'data4')
                );
                break;
            case 'receiptwise_p_and_l_report':
                $site_id = $request->receiptwise_p_and_l_report_site_id;
                $header = array(null, null);
                $data = array(
                    array('Total Sale Entry', 1),
                    array('Total receipt entry', 1),
                    array(null, null),
                    array('Labour + Staff Salary', null),
                    array('Total Purchase', null),
                    array('Total Miscellaneous Purchase', null),
                    array('Subcontractor', null),
                    array('Indirect Expences (GST,TDS Paid to government from manisha)', null),
                    array('Total Expence', null),
                    array(null, null),
                    array('Profit/ Loss Salewise', 'Profit/ Loss Receiptwise'),
                    array(1, 1),
                );
                break;
            case 'subcontractor_report':
                $site_id = $request->subcontractor_report_site_id;
                $subcontractor_id = $request->subcontractor_id;
                $header = array(
                    'Sr. No', 'Summary Type', 'Bill No', 'Total Bill Amount', 'TDS',
                    'Retention', 'Total Bill Amount', 'Total Pay Amount', 'Balance'
                );
                $data = array(
                    array('data1', 'data2'),
                    array('data3', 'data4')
                );
                break;
            case 'labour_specific_report':
                $site_id = $request->labour_specific_report_site_id;
                $emp_id = $request->labour_id;

                if($site_id == 'all') {
                    $salaryData = PeticashSalaryTransaction::where('employee_id',$emp_id)
                        ->where('peticash_transaction_type_id', PeticashTransactionType::where('slug','salary')->get()->pluck(['id']))
                        ->select('employee_id', DB::raw('SUM(amount) as salary, SUM(tds) as tds, SUM(pt) as pt, SUM(pf) as pf, SUM(esic) as esic'))
                        ->whereBetween('created_at', array($start_date, $end_date))
                        ->groupBy('employee_id')
                        ->first();
                } else {
                    $salaryData = PeticashSalaryTransaction::where('employee_id',$emp_id)
                        ->where('project_site_id', $site_id)
                        ->where('peticash_transaction_type_id', PeticashTransactionType::where('slug','salary')->get()->pluck(['id']))
                        ->select('employee_id', DB::raw('SUM(amount) as salary, SUM(tds) as tds, SUM(pt) as pt, SUM(pf) as pf, SUM(esic) as esic'))
                        ->whereBetween('created_at', array($start_date, $end_date))
                        ->groupBy('employee_id')
                        ->first();
                }
                if($site_id == 'all') {
                    $advanceData = PeticashSalaryTransaction::where('employee_id',$emp_id)
                        ->where('peticash_transaction_type_id', PeticashTransactionType::where('slug','advance')->get()->pluck(['id']))
                        ->select('employee_id', DB::raw('SUM(amount) as salary, SUM(tds) as tds, SUM(pt) as pt, SUM(pf) as pf, SUM(esic) as esic'))
                        ->whereBetween('created_at', array($start_date, $end_date))
                        ->groupBy('employee_id')
                        ->first();
                } else {
                    $advanceData = PeticashSalaryTransaction::where('employee_id',$emp_id)
                        ->where('project_site_id', $site_id)
                        ->where('peticash_transaction_type_id', PeticashTransactionType::where('slug','advance')->get()->pluck(['id']))
                        ->select('employee_id', DB::raw('SUM(amount) as salary, SUM(tds) as tds, SUM(pt) as pt, SUM(pf) as pf, SUM(esic) as esic'))
                        ->whereBetween('created_at', array($start_date, $end_date))
                        ->groupBy('employee_id')
                        ->first();
                }

                $header = array(
                    'Sr. No', 'Gross Salary', 'PT', 'PF', 'ESIC',
                    'TDS', 'ADVANCE', 'Net Payment'
                );

                if (count($salaryData) == 0) {
                    $data = array(
                        array(null, null)
                    );
                } else {
                    $net_payable = $salaryData['salary']-$salaryData['pt']-$salaryData['pf']
                        -$salaryData['esic']-$salaryData['tds']-$advanceData['salary'];
                    $data = array(
                        array (1,
                            $salaryData['salary'],
                            $salaryData['pt'],
                            $salaryData['pf'],
                            $salaryData['esic'],
                            $salaryData['tds'],
                            $advanceData['salary'],
                            $net_payable
                        ),
                    );
                }
                break;
            case 'purchase_bill_tax_report':
                $site_id = $request->purchase_bill_tax_report_site_id;
                $vendor_id = $request->vendor_id;
                $header = array(
                    'Sr. No', 'Basic Amount', 'IGST Amount', 'SGST Amount', 'CGST Amount',
                    'With Tax Amount'
                );
                $data = array(
                    array('data1', 'data2'),
                    array('data3', 'data4')
                );
                break;
            case 'sales_bill_tax_report':
                $site_id = $request->sales_bill_tax_report_site_id;
                $array_no = 1;
                $iterator = $i= 0;
                $data = $currentTaxes = $listingData = array();
                $quotation = Quotation::where('project_site_id',$site_id)->first();
                $allBills = Bill::where('quotation_id',$quotation->id)->get();
                $statusId = BillStatus::whereIn('slug',['approved'])->get()->toArray();
                $bills = Bill::where('quotation_id',$quotation->id)->whereIn('bill_status_id',array_column($statusId,'id'))->whereBetween('created_at', array($start_date, $end_date))->orderBy('created_at','asc')->get();
                $cancelBillStatusId = BillStatus::where('slug','cancelled')->pluck('id')->first();
                $taxesAppliedToBills = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                    ->whereIn('bill_taxes.bill_id',array_column($allBills->toArray(),'id'))
                    ->where('taxes.is_special', false)
                    ->distinct('bill_taxes.tax_id')
                    ->orderBy('bill_taxes.tax_id')
                    ->pluck('bill_taxes.tax_id')
                    ->toArray();
                $specialTaxesAppliedToBills = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                    ->whereIn('bill_taxes.bill_id',array_column($allBills->toArray(),'id'))
                    ->where('taxes.is_special', true)
                    ->distinct('bill_taxes.tax_id')
                    ->orderBy('bill_taxes.tax_id')
                    ->pluck('bill_taxes.tax_id')
                    ->toArray();
                foreach($bills as $key => $bill){
                    $listingData[$iterator]['status'] = $bill->bill_status->slug ;
                    $listingData[$iterator]['bill_id'] = $bill->id;
                    if($bill->bill_status_id != $cancelBillStatusId){
                        $listingData[$iterator]['array_no'] = "RA Bill - ".$array_no;
                        $array_no++;
                    }else{
                        $listingData[$iterator]['array_no'] = '-';
                    }
                    $listingData[$iterator]['bill_no_format'] = "B-".strtoupper(date('M',strtotime($bill['created_at'])))."-".$bill->id."/".date('y',strtotime($bill['created_at']));
                    $total_amount = 0;
                    foreach($bill->bill_quotation_product as $key1 => $product){
                        $rate = MaterialProductHelper::customRound(($product->quotation_products->rate_per_unit - ($product->quotation_products->rate_per_unit * ($product->quotation_products->quotation->discount / 100))),3);
                        $total_amount = $total_amount + ($product->quantity * $rate) ;
                    }
                    if(count($bill->bill_quotation_extraItems) > 0){
                        $extraItemsTotal = $bill->bill_quotation_extraItems->sum('rate');
                    }else{
                        $extraItemsTotal = 0;
                    }
                    $total_amount = ($total_amount + $extraItemsTotal) - $bill->discount_amount;
                    $listingData[$iterator]['subTotal'] = $total_amount;
                    $thisBillTax = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                        ->where('bill_taxes.bill_id',$bill->id)
                        ->where('taxes.is_special', false)
                        ->pluck('bill_taxes.tax_id')
                        ->toArray();
                    $otherTaxes = array_values(array_diff($taxesAppliedToBills,$thisBillTax));
                    if($thisBillTax != null){
                        $currentTaxes = Tax::whereIn('id',$otherTaxes)->where('is_active',true)->where('is_special', false)->select('id as tax_id','name')->get();
                    }
                    if($currentTaxes != null){
                        $thisBillTaxInfo = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                            ->where('bill_taxes.bill_id',$bill->id)
                            ->where('taxes.is_special', false)
                            ->select('bill_taxes.percentage as percentage','bill_taxes.tax_id as tax_id')
                            ->get()
                            ->toArray();
                        $currentTaxes = array_merge($thisBillTaxInfo,$currentTaxes->toArray());
                        usort($currentTaxes, function($a, $b) {
                            return $a['tax_id'] > $b['tax_id'];
                        });
                    }else{
                        $currentTaxes = Tax::where('is_active',true)->where('is_special', false)->select('id as tax_id')->get();
                    }
                    $listingData[$iterator]['final_total'] = $total_amount;
                    foreach($currentTaxes as $key2 => $tax){
                        if(array_key_exists('percentage',$tax)){
                            $listingData[$iterator]['tax'][$tax['tax_id']] = $total_amount * ($tax['percentage'] / 100);
                        }else{
                            $listingData[$iterator]['tax'][$tax['tax_id']] = 0;
                        }
                        $listingData[$iterator]['final_total'] = MaterialProductHelper::customRound($listingData[$iterator]['final_total'] + $listingData[$iterator]['tax'][$tax['tax_id']]);
                        $i++;
                    }
                    $thisBillSpecialTax = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                        ->where('bill_taxes.bill_id',$bill->id)
                        ->where('taxes.is_special', true)
                        ->pluck('bill_taxes.tax_id')
                        ->toArray();
                    $otherSpecialTaxes = array_values(array_diff($specialTaxesAppliedToBills,$thisBillSpecialTax));
                    if($thisBillSpecialTax != null){
                        $currentSpecialTaxes = Tax::whereIn('id',$otherSpecialTaxes)->where('is_active',true)->where('is_special', true)->select('id as tax_id','name','base_percentage as percentage')->get();
                    }else{
                        $currentSpecialTaxes = Tax::where('is_active',true)->where('is_special', true)->select('id as tax_id','name','base_percentage as percentage')->get();

                    }
                    if($currentSpecialTaxes != null){
                        $thisBillSpecialTaxInfo = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                            ->where('bill_taxes.bill_id',$bill->id)
                            ->where('taxes.is_special', true)
                            ->select('bill_taxes.percentage as percentage','bill_taxes.applied_on as applied_on','bill_taxes.tax_id as tax_id')
                            ->get()
                            ->toArray();
                        if(!is_array($currentSpecialTaxes)){
                            $currentSpecialTaxes = $currentSpecialTaxes->toArray();
                        }
                        $currentSpecialTaxes = array_merge($thisBillSpecialTaxInfo,$currentSpecialTaxes);
                        usort($currentSpecialTaxes, function($a, $b) {
                            return $a['tax_id'] > $b['tax_id'];
                        });
                    }else{
                        $currentSpecialTaxes = Tax::where('is_active',true)->where('is_special', true)->select('id as tax_id','base_percentage as percentage')->get();
                    }
                    foreach($currentSpecialTaxes as $key2 => $tax){
                        $taxAmount = 0;
                        if(array_key_exists('applied_on',$tax)){
                            $appliedOnTaxes = json_decode($tax['applied_on']);
                            foreach($appliedOnTaxes as $appliedTaxId){
                                if($appliedTaxId == 0){                 // On Subtotal
                                    $taxAmount += $total_amount * ($tax['percentage'] / 100);
                                }else{
                                    $taxAmount += $listingData[$iterator]['tax'][$appliedTaxId] * ($tax['percentage'] / 100);
                                }
                            }
                        }else{
                            $taxAmount += $total_amount * ($tax['percentage'] / 100);
                        }

                        $listingData[$iterator]['tax'][$tax['tax_id']] = $taxAmount;
                        $listingData[$iterator]['final_total'] = MaterialProductHelper::customRound($listingData[$iterator]['final_total'] + $listingData[$iterator]['tax'][$tax['tax_id']]);
                    }
                    $iterator++;
                }
                $jIterator = 0;
                foreach($listingData as $key => $billData){
                    $data[$jIterator]['bill_no'] = $billData['array_no'];
                    $data[$jIterator]['basic_amount'] = $billData['subTotal'];
                    $data[$jIterator]['tax_amount'] = $billData['final_total'] - $billData['subTotal'];
                    $data[$jIterator]['total_amount'] = $billData['final_total'];
                    $bllTransactions = BillTransaction::where('bill_id',$billData['bill_id'])->get();
                    //need to work mobilise_amount
                    $data[$jIterator]['mobilise_amount'] = 0;
                    $data[$jIterator]['debit'] = -$bllTransactions->sum('debit');
                    $data[$jIterator]['hold'] = -$bllTransactions->sum('hold');
                    $data[$jIterator]['retention'] = -$bllTransactions->sum('retention');
                    $data[$jIterator]['tds'] = -$bllTransactions->sum('tds');
                    //need to work other_recovery
                    $data[$jIterator]['other_recovery'] = 0;
                    $data[$jIterator]['payable_amount'] = $billData['final_total'];
                    $data[$jIterator]['check_amount'] = $bllTransactions->sum('total');
                    $data[$jIterator]['balance'] = $data[$jIterator]['payable_amount'] - $data[$jIterator]['check_amount'];
                }

                $header = array(
                    'RA Bill Number', 'Basic Amount', 'Tax Amount', 'Total Amount',
                    'Mobilise Advance', 'Debit', 'Hold', 'Retention',
                    'TDS', 'Other Recovery', 'Payble Amount', 'Check Amount',
                    'Balance'
                );

                break;
            default :
                $downloadSheetFlag = false;
                break;
        }

        if ($downloadSheetFlag) {
            Excel::create($report_type."_".$curr_date, function($excel) use($data, $report_type, $header) {
                $excel->sheet($report_type, function($sheet) use($data, $header) {
                    $sheet->setOrientation('landscape');
                    $sheet->setPageMargin(0.25);
                    $sheet->protect('constro');
                    // Manipulate first row
                    $sheet->fromArray($data, null, 'A1', false, false);

                    // Add before first row
                    $sheet->prependRow(1, $header);

                    // Set black background
                    $sheet->row(1, function($row) {
                        // call cell manipulation methods
                        $row->setBackground('#f2f2f2');
                    });
                    // Freeze first row
                    $sheet->freezeFirstRow();
                    // Set auto size for sheet
                    $sheet->setAutoSize(true);

                });
            })->export('xls');
        }

    }
}
