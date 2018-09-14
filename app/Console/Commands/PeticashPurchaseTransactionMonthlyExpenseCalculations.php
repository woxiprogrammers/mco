<?php

namespace App\Console\Commands;

use App\Month;
use App\PeticashPurchaseTransactionMonthlyExpense;
use App\ProjectSite;
use App\PurcahsePeticashTransaction;
use App\Year;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PeticashPurchaseTransactionMonthlyExpenseCalculations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //php artisan custom:peticash-purchase-transaction-monthly-expense-calculation --month=all --year=all  => Executes Case 1
    //php artisan custom:peticash-purchase-transaction-monthly-expense-calculation --month=all --year=2018  => Executes Case 2
    //php artisan custom:peticash-purchase-transaction-monthly-expense-calculation  => Executes Case 3 => $thisMonth == 'null' && $thisYear == 'null'
    //php artisan custom:peticash-purchase-transaction-monthly-expense-calculation --month=8 --year=2018  => Executes Case 4

    protected $signature = 'custom:peticash-purchase-transaction-monthly-expense-calculation {--month=null} {--year=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try{
            $thisMonth = $this->option('month');
            $thisYear = $this->option('year');
            $year = new Year();
            $month = new Month();
            $projectSite = new ProjectSite();
            $peticashPurchaseTransaction = new PurcahsePeticashTransaction();
            $peticashPurchaseTransactionMonthlyExpense = new PeticashPurchaseTransactionMonthlyExpense();
            switch (true){
                case ($thisMonth == 'all' && $thisYear == 'all') :
                    $currentYearId = $year->where('slug',date('Y'))->pluck('id')->first();
                    $tillCurrentYearIds = $year->where('id','>=',$currentYearId)->pluck('id');
                    $monthsData = $month->pluck('id')->toArray();
                    foreach ($tillCurrentYearIds as $thisYearId){
                        $thisYear = $year->where('id',$thisYearId)->pluck('slug')->first();
                        foreach ($monthsData as $thisMonth){
                            $projectSiteIds = $projectSite->pluck('id')->toArray();
                            foreach ($projectSiteIds as $projectSiteId){
                                $peticashPurchaseTransactionData = $peticashPurchaseTransaction
                                    ->where('project_site_id',$projectSiteId)
                                    ->whereMonth('date',$thisMonth)
                                    ->whereYear('date',$thisYear)
                                    ->get();
                                $totalAmount = $peticashPurchaseTransactionData->sum('bill_amount');
                                if($totalAmount != 0){
                                    $alreadyExist = $peticashPurchaseTransactionMonthlyExpense->where('project_site_id',$projectSiteId)->where('month_id',$thisMonth)->where('year_id',$thisYearId)->first();
                                    if($alreadyExist != null){
                                        $alreadyExist->update([
                                            'total_expense' => round($totalAmount,3)
                                        ]);
                                    }else{
                                        $peticashPurchaseTransactionMonthlyExpense->create([
                                            'project_site_id' => $projectSiteId,
                                            'month_id' => $thisMonth,
                                            'year_id' => $thisYearId,
                                            'total_expense' => round($totalAmount,3)
                                        ]);
                                    }
                                }
                            }
                        }
                    }

                    break;

                case ($thisMonth == 'all' && $thisYear != null) :
                    $yearId = $year->where('slug',$thisYear)->pluck('id')->first();
                    if($yearId == null){
                        $this->info("Please enter proper year in 4 digit (Eg. 2018)");
                    }else{
                        $monthsData = $month->pluck('id')->toArray();
                        foreach ($monthsData as $thisMonth){
                            $projectSiteIds = $projectSite->pluck('id')->toArray();
                            foreach ($projectSiteIds as $projectSiteId){
                                $peticashPurchaseTransactionData = $peticashPurchaseTransaction
                                    ->where('project_site_id',$projectSiteId)
                                    ->whereMonth('date',$thisMonth)
                                    ->whereYear('date',$thisYear)
                                    ->get();
                                $totalAmount = $peticashPurchaseTransactionData->sum('bill_amount');
                                if($totalAmount != 0){
                                    $alreadyExist = $peticashPurchaseTransactionMonthlyExpense->where('project_site_id',$projectSiteId)->where('month_id',$thisMonth)->where('year_id',$yearId)->first();
                                    if($alreadyExist != null){
                                        $alreadyExist->update([
                                            'total_expense' => round($totalAmount,3)
                                        ]);
                                    }else{
                                        $peticashPurchaseTransactionMonthlyExpense->create([
                                            'project_site_id' => $projectSiteId,
                                            'month_id' => $thisMonth,
                                            'year_id' => $yearId,
                                            'total_expense' => round($totalAmount,3)
                                        ]);
                                    }
                                }

                            }
                        }

                    }
                    break;

                case ($thisMonth == 'null' && $thisYear == 'null') :
                    $projectSiteIds = $projectSite->pluck('id')->toArray();
                    $todayDate = Carbon::today();
                    $monthId = date('n',strtotime($todayDate));
                    $yearId = $year->where('slug',date('Y',strtotime($todayDate)))->pluck('id')->first();
                    foreach ($projectSiteIds as $projectSiteId){
                        $peticashPurchaseTransactionData = $peticashPurchaseTransaction
                            ->where('project_site_id',$projectSiteId)
                            ->whereMonth('date',$thisMonth)
                            ->whereYear('date',$thisYear)
                            ->get();
                        $totalAmount = $peticashPurchaseTransactionData->sum('bill_amount');
                        if($totalAmount != 0){
                            $alreadyExist = $peticashPurchaseTransactionMonthlyExpense->where('project_site_id',$projectSiteId)->where('month_id',$monthId)->where('year_id',$yearId)->first();
                            if($alreadyExist != null){
                                $alreadyExist->update([
                                    'total_expense' => round($totalAmount,3)
                                ]);
                            }else{
                                $peticashPurchaseTransactionMonthlyExpense->create([
                                    'project_site_id' => $projectSiteId,
                                    'month_id' => $monthId,
                                    'year_id' => $yearId,
                                    'total_expense' => round($totalAmount,3)
                                ]);
                            }
                        }

                    }
                    break;

                case ($thisMonth != null && $thisYear != null) :
                    $yearId = $year->where('slug',$thisYear)->pluck('id')->first();
                    $monthId = $month->where('id',$thisMonth)->pluck('id')->first();
                    if($yearId == null){
                        $this->info("Please enter proper year in 4 digit (Eg. 2018)");
                    }elseif($monthId == null){
                        $this->info("Please enter proper month (Eg. 2)");
                    }else{
                        $projectSiteIds = $projectSite->pluck('id')->toArray();
                        foreach ($projectSiteIds as $projectSiteId){
                            $peticashPurchaseTransactionData = $peticashPurchaseTransaction
                                ->where('project_site_id',$projectSiteId)
                                ->whereMonth('date',$thisMonth)
                                ->whereYear('date',$thisYear)
                                ->get();
                            $totalAmount = $peticashPurchaseTransactionData->sum('bill_amount');
                            if($totalAmount != 0){
                                $alreadyExist = $peticashPurchaseTransactionMonthlyExpense->where('project_site_id',$projectSiteId)->where('month_id',$monthId)->where('year_id',$yearId)->first();
                                if($alreadyExist != null){
                                    $alreadyExist->update([
                                        'total_expense' => round($totalAmount,3)
                                    ]);
                                }else{
                                    $peticashPurchaseTransactionMonthlyExpense->create([
                                        'project_site_id' => $projectSiteId,
                                        'month_id' => $monthId,
                                        'year_id' => $yearId,
                                        'total_expense' => round($totalAmount,3)
                                    ]);
                                }
                            }

                        }
                    }
                    break;
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Peticash Purchase Transaction Monthly Expense Calculations',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $this->error($e->getMessage());
        }
    }
}
