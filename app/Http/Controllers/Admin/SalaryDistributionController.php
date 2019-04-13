<?php

namespace App\Http\Controllers\Admin;

use App\PeticashPurchaseTransactionMonthlyExpense;
use App\ProjectSite;
use App\ProjectSiteSalaryDistribution;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Month;
use App\Year;

class SalaryDistributionController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request){
        try{
            $allProjectSites = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                ->where('projects.is_active', true)
                ->select('projects.name as project_name','project_sites.id as project_site_id','project_sites.name as project_site_name')
                ->orderBy('projects.name','asc')
                ->get();

            $projectSiteData = array();
            $iterator = 0;
            foreach ($allProjectSites as $projectSite){
                $projectSiteData[$iterator] = [
                    'project_site_id' => $projectSite['project_site_id'],
                    'project_site_name' => $projectSite['project_name'].' - '.$projectSite['project_site_name'],
                ];
                $iterator++;
            }
            $month = new Month();
            $year = new Year();
            return view('admin.salary-distribution.manage',['months' => $month->all(),
                'years' => $year->all(), 'projectSiteData' => $projectSiteData,]);
        }catch(\Exception $e){
            $data = [
                'action' => 'Get salary-distribution manage view',
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function salaryDistributionListing(Request $request){
        try{
            $month = new Month();
            $year = new Year();
            if(!(array_key_exists('sd_month_id',$request->all()))){
                $yearId = $monthId = $projectSiteId = 'all';
            } else {
                $yearId = $request['sd_year_id'];
                $monthId = $request['sd_month_id'];
                $projectSiteId = $request['sd_project_site_id'];
            }
            $januaryMonthId = $month->where('slug','january')->pluck('id')->first();
            $decemberMonthId = $month->where('slug','december')->pluck('id')->first();

            $ids = array();

            if ($yearId == 'all') {
                $ids = ProjectSiteSalaryDistribution::pluck('id');
            } else {
                $ids = ProjectSiteSalaryDistribution::where('year_id', $yearId)
                    ->pluck('id');
            }

            if ($monthId == 'all') {
                $ids = ProjectSiteSalaryDistribution::whereIn('id',$ids)->pluck('id');
            } else {
                $ids = ProjectSiteSalaryDistribution::whereIn('id',$ids)
                    ->where('month_id', $monthId)
                    ->pluck('id');
            }

            if ($projectSiteId == 'all' || $projectSiteId == null) {
                $ids = ProjectSiteSalaryDistribution::whereIn('id',$ids)->pluck('id');
            } else {
                $projectSiteIdArray = explode(',',$projectSiteId);
                $ids = ProjectSiteSalaryDistribution::whereIn('id',$ids)
                    ->whereIn('project_site_id', $projectSiteIdArray)
                    ->pluck('id');
            }
            $salaryDistData = ProjectSiteSalaryDistribution::whereIn('id',$ids)->get();

            $iTotalRecords = count($salaryDistData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($salaryDistData) : $request->length;
            $srNoCounter = 1;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($salaryDistData); $iterator++,$pagination++ ){
                $projectSites = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                    ->orderBy('projects.name','asc')
                    ->where('projects.is_active',true)
                    ->where('project_sites.project_id','=', $salaryDistData[$pagination]['project_site_id'])
                    ->select('project_sites.id','project_sites.name','projects.name as project_name')
                    ->get()->toArray();
                    $sitename = $projectSites[0]['project_name'];
                    $totalMontlyExpense = "";
                    $records['data'][$iterator] = [
                        $srNoCounter,
                        $sitename,
                        $salaryDistData[$pagination]['distributed_amount'],
                       /* $salaryDistData[$pagination]['distributed_amount'],
                        $salaryDistData[$pagination]['distributed_amount'],
                        2*/
                    ];
                $srNoCounter++;

            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Salary Distribution Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records);
    }
}
