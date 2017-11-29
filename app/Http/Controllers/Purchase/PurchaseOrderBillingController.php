<?php

namespace App\Http\Controllers\Purchase;

use App\PurchaseOrderTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class PurchaseOrderBillingController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request){
        try{
            return view('purchase.purchase-order-billing.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get PO billing Manage View',
                'exception' => $e->getMessage()
            ];
            Log::cirtical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request){
        try{
            $purchaseOrderTransactionDetails = PurchaseOrderTransaction::join('purchase_order_transaction_statuses','purchase_order_transaction_statuses.id','=','purchase_order_transactions.purchase_order_transaction_status_id')
                                                                ->where('purchase_order_transaction_statuses.slug','bill-pending')
                                                                ->select('purchase_order_transactions.id as id','purchase_order_transactions.grn as grn')
                                                                ->get();
            return view('purchase.purchase-order-billing.create')->with(compact('purchaseOrderTransactionDetails'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get PO billing Create View',
                'exception' => $e->getMessage()
            ];
            Log::cirtical(json_encode($data));
            abort(500);
        }
    }
}
