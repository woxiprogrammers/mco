<?php

namespace App\Console;

use App\Console\Commands\AssetRentCalculations;
use App\Console\Commands\BillModuleChanges;
use App\Console\Commands\PeticashPurchaseTransactionMonthlyExpenseCalculations;
use App\Console\Commands\PeticashSalaryTransactionMonthlyExpense;
use App\Console\Commands\PeticashSalaryTransactionMonthlyExpenseCalculations;
use App\Console\Commands\PurchaseOrderBillMonthlyExpenseCalculations;
use App\Console\Commands\SalaryDistributionInSites;
use App\Console\Commands\SendPurchaseOrderEmails;
use App\Console\Commands\SubcontractorModuleMerge;
use App\Console\Commands\InventoryAssetMaterialScript;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SendPurchaseOrderEmails::class,
        PurchaseOrderBillMonthlyExpenseCalculations::class,
        PeticashSalaryTransactionMonthlyExpenseCalculations::class,
        PeticashPurchaseTransactionMonthlyExpenseCalculations::class,
        SalaryDistributionInSites::class,
        AssetRentCalculations::class,
        BillModuleChanges::class,
        SubcontractorModuleMerge::class,
        InventoryAssetMaterialScript::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('custom:send-purchase-order-email')
                ->everyFiveMinutes();
        $schedule->command('custom:purchase-order-bill-monthly-expense-calculation')->dailyAt('23:00');
        $schedule->command('custom:peticash-salary-transaction-monthly-expense-calculation')->dailyAt('23:00');
        $schedule->command('custom:peticash-purchase-transaction-monthly-expense-calculation')->dailyAt('23:00');
        $schedule->command('custom:salary-distribution')->monthlyOn(1, '1:00');
        $schedule->command('custom:asset-rent-calculate')->monthlyOn(1, '1:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
