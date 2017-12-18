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
        //download report code here
        dd($request->all());
    }
}
