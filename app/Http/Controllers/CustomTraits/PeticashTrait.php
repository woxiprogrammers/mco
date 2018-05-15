<?php
    /**
     * Created by PhpStorm.
     * User: manoj
     * Date: 14/5/18
     * Time: 12:39 PM
     */

namespace App\Http\Controllers\CustomTraits;


use App\BillReconcileTransaction;
use App\PeticashSalaryTransaction;
use App\PeticashSiteTransfer;
use App\PeticashStatus;
use App\PeticashTransactionType;
use App\ProjectSiteAdvancePayment;
use App\PurcahsePeticashTransaction;
use Illuminate\Support\Facades\Session;

trait PeticashTrait{

    public function getSiteWiseStatistics(){
        $projectSiteId = Session::get('global_project_site');
        try{
            $projectSiteAdvancedAmount = ProjectSiteAdvancePayment::where('paid_from_slug','cash')->sum('amount');
            $salesBillCashAmount = BillReconcileTransaction::where('paid_from_slug','cash')->sum('amount');
            $approvedPeticashStatusId = PeticashStatus::where('slug','approved')->pluck('id')->first();
            $allocatedAmount  = PeticashSiteTransfer::where('project_site_id',$projectSiteId)->sum('amount');
            $totalSalaryAmount = PeticashSalaryTransaction::where('peticash_transaction_type_id',PeticashTransactionType::where('slug','salary')->pluck('id')->first())
                ->where('project_site_id',$projectSiteId)
                ->where('peticash_status_id',$approvedPeticashStatusId)
                ->sum('payable_amount');
            $totalAdvanceAmount = PeticashSalaryTransaction::where('peticash_transaction_type_id',PeticashTransactionType::where('slug','advance')->pluck('id')->first())
                ->where('project_site_id',$projectSiteId)
                ->where('peticash_status_id',$approvedPeticashStatusId)
                ->sum('amount');
            $totalPurchaseAmount = PurcahsePeticashTransaction::whereIn('peticash_transaction_type_id', PeticashTransactionType::where('type','PURCHASE')->pluck('id'))
                ->where('project_site_id',$projectSiteId)
                ->where('peticash_status_id',$approvedPeticashStatusId)
                ->sum('bill_amount');
            $remainingAmount = ($allocatedAmount + $projectSiteAdvancedAmount + $salesBillCashAmount) - ($totalSalaryAmount + $totalAdvanceAmount + $totalPurchaseAmount);
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Peticash sitewise statistics',
                'exception' => $e->getMessage(),
                'params' => $projectSiteId
            ];
            $remainingAmount = $allocatedAmount = 0;
        }
        $response = [
            'remainingAmount' => $remainingAmount,
            'allocatedAmount' => $allocatedAmount
        ];
        return $response;
    }

}