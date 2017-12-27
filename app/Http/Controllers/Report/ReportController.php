<?php

namespace App\Http\Controllers\Report;

use App\Category;
use App\Employee;
use App\Material;
use App\ProjectSite;
use App\Subcontractor;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
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
            $categories = Category::where('is_active', true)->get(['id','name','slug'])->toArray();
            $materials = Material::get(['id','name'])->toArray();
            $subcontractors = Subcontractor::get(['id','company_name'])->toArray();
            $employees = Employee::where('employee_type_id', 1)->get(['id','name','employee_id'])->toArray();
            $vendors = Vendor::get(['id','name','company'])->toArray();
            return view('report.mainreport')->with(compact('vendors','employees','subcontractors','sites','categories','start_date','end_date','materials'));
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
        $start_date = $request->start_date;
        $end_date = $request->end_date;

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
                $labour_id = $request->labour_id;
                $header = array(
                    'Sr. No', 'Gross Salary', 'PT', 'PF', 'ESIC',
                    'TDS', 'ADVANCE', 'Net Payment'
                );
                $data = array(
                    array('data1', 'data2'),
                    array('data3', 'data4')
                );
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
                $header = array(
                    'RA Bill Number', 'Basic Amount', 'Tax Amount', 'Total Amount',
                    'Mobilise Advance', 'Debit', 'Hold', 'Retention',
                    'TDS', 'Other Recovery', 'Payble Amount', 'Check Amount',
                    'Balance'
                );
                $data = array(
                    array('data1', 'data2'),
                    array('data3', 'data4')
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
