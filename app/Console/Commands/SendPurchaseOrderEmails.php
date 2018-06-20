<?php

namespace App\Console\Commands;

use App\Client;
use App\Helper\MaterialProductHelper;
use App\PurchaseOrder;
use App\PurchaseRequestComponentVendorMailInfo;
use App\PurchaseRequestComponentVendorRelation;
use App\Unit;
use App\User;
use App\UserHasRole;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPurchaseOrderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:send-purchase-order-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Purchase Order Email to vendor / client for which Email is not sent yet.';

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
            $purchaseOrders = PurchaseOrder::where('is_email_sent',false)->get();
            $superadminUserId = UserHasRole::join('roles','roles.id','user_has_roles.role_id')
                ->where('roles.slug','superadmin')
                ->pluck('user_has_roles.user_id')
                ->first();
            foreach($purchaseOrders as $purchaseOrder){
                if($purchaseOrder->is_client_order == true){
                    $vendorInfo = Client::findOrFail($purchaseOrder->client_id)->toArray();
                }else{
                    $vendorInfo = Vendor::findOrFail($purchaseOrder->vendor_id)->toArray();
                }
                $vendorInfo['materials'] = array();
                $iterator = 0;
                $disapprovedVendorId = array();
                foreach ($purchaseOrder->purchaseOrderComponent as $purchaseOrderComponent){
                    $purchaseRequestComponentVendorRelationId = $purchaseOrderComponent->purchaseOrderRequestComponent->purchase_request_component_vendor_relation_id;
                    $disapprovedVendorRelationID = PurchaseRequestComponentVendorRelation::where('id','!=',$purchaseRequestComponentVendorRelationId)
                                                    ->where('purchase_request_component_id', $purchaseOrderComponent->purchase_request_component_id)
                                                    ->pluck('vendor_id')->toArray();
                    $disapprovedVendorId = array_unique(array_merge($disapprovedVendorId, $disapprovedVendorRelationID));
                    $vendorInfo['materials'][$iterator] = array();
                    $vendorInfo['materials'][$iterator]['item_name'] = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name;
                    $vendorInfo['materials'][$iterator]['quantity'] = $purchaseOrderComponent['quantity'];
                    $vendorInfo['materials'][$iterator]['unit'] = Unit::where('id',$purchaseOrderComponent['unit_id'])->pluck('name')->first();
                    $vendorInfo['materials'][$iterator]['rate'] = $purchaseOrderComponent['rate_per_unit'];
                    $vendorInfo['materials'][$iterator]['subtotal'] = MaterialProductHelper::customRound(($purchaseOrderComponent['quantity'] * $purchaseOrderComponent['rate_per_unit']));
                    if($purchaseOrderComponent['cgst_percentage'] == null || $purchaseOrderComponent['cgst_percentage'] == ''){
                        $vendorInfo['materials'][$iterator]['cgst_percentage'] = 0;
                    }else{
                        $vendorInfo['materials'][$iterator]['cgst_percentage'] = $purchaseOrderComponent['cgst_percentage'];
                    }
                    $vendorInfo['materials'][$iterator]['cgst_amount'] = $vendorInfo['materials'][$iterator]['subtotal'] * ($vendorInfo['materials'][$iterator]['cgst_percentage']/100);
                    if($purchaseOrderComponent['sgst_percentage'] == null || $purchaseOrderComponent['sgst_percentage'] == ''){
                        $vendorInfo['materials'][$iterator]['sgst_percentage'] = 0;
                    }else{
                        $vendorInfo['materials'][$iterator]['sgst_percentage'] = $purchaseOrderComponent['sgst_percentage'];
                    }
                    $vendorInfo['materials'][$iterator]['sgst_amount'] = $vendorInfo['materials'][$iterator]['subtotal'] * ($vendorInfo['materials'][$iterator]['sgst_percentage']/100);
                    if($purchaseOrderComponent['igst_percentage'] == null || $purchaseOrderComponent['igst_percentage'] == ''){
                        $vendorInfo['materials'][$iterator]['igst_percentage'] = 0;
                    }else{
                        $vendorInfo['materials'][$iterator]['igst_percentage'] = $purchaseOrderComponent['igst_percentage'];
                    }
                    $vendorInfo['materials'][$iterator]['igst_amount'] = $vendorInfo['materials'][$iterator]['subtotal'] * ($vendorInfo['materials'][$iterator]['igst_percentage']/100);
                    $vendorInfo['materials'][$iterator]['total'] = $vendorInfo['materials'][$iterator]['subtotal'] + $vendorInfo['materials'][$iterator]['cgst_amount'] + $vendorInfo['materials'][$iterator]['sgst_amount'] + $vendorInfo['materials'][$iterator]['igst_amount'];
                    if($purchaseOrderComponent['expected_delivery_date'] == null || $purchaseOrderComponent['expected_delivery_date'] == ''){
                        $vendorInfo['materials'][$iterator]['due_date'] = '';
                    }else{
                        $vendorInfo['materials'][$iterator]['due_date'] = 'Due on '.date('j/n/Y',strtotime($purchaseOrderComponent['expected_delivery_date']));
                    }
                    $purchaseOrderRequestComponent = $purchaseOrderComponent->purchaseOrderRequestComponent;
                    if($purchaseOrderRequestComponent['transportation_amount'] == null || $purchaseOrderRequestComponent['transportation_amount'] == ''){
                        $vendorInfo['materials'][$iterator]['transportation_amount'] = 0;
                    }else{
                        $vendorInfo['materials'][$iterator]['transportation_amount'] = $purchaseOrderRequestComponent['transportation_amount'];
                    }
                    if($purchaseOrderRequestComponent['transportation_cgst_percentage'] == null || $purchaseOrderRequestComponent['transportation_cgst_percentage'] == ''){
                        $vendorInfo['materials'][$iterator]['transportation_cgst_percentage'] = 0;
                    }else{
                        $vendorInfo['materials'][$iterator]['transportation_cgst_percentage'] = $purchaseOrderRequestComponent['transportation_cgst_percentage'];
                    }
                    if($purchaseOrderRequestComponent['transportation_sgst_percentage'] == null || $purchaseOrderRequestComponent['transportation_sgst_percentage'] == ''){
                        $vendorInfo['materials'][$iterator]['transportation_sgst_percentage'] = 0;
                    }else{
                        $vendorInfo['materials'][$iterator]['transportation_sgst_percentage'] = $purchaseOrderRequestComponent['transportation_sgst_percentage'];
                    }
                    if($purchaseOrderRequestComponent['transportation_igst_percentage'] == null || $purchaseOrderRequestComponent['transportation_igst_percentage'] == ''){
                        $vendorInfo['materials'][$iterator]['transportation_igst_percentage'] = 0;
                    }else{
                        $vendorInfo['materials'][$iterator]['transportation_igst_percentage'] = $purchaseOrderRequestComponent['transportation_igst_percentage'];
                    }
                    $vendorInfo['materials'][$iterator]['transportation_cgst_amount'] = ($vendorInfo['materials'][$iterator]['transportation_cgst_percentage'] * $vendorInfo['materials'][$iterator]['transportation_amount']) / 100 ;
                    $vendorInfo['materials'][$iterator]['transportation_sgst_amount'] = ($vendorInfo['materials'][$iterator]['transportation_sgst_percentage'] * $vendorInfo['materials'][$iterator]['transportation_amount']) / 100 ;
                    $vendorInfo['materials'][$iterator]['transportation_igst_amount'] = ($vendorInfo['materials'][$iterator]['transportation_igst_percentage'] * $vendorInfo['materials'][$iterator]['transportation_amount']) / 100 ;
                    $vendorInfo['materials'][$iterator]['transportation_total_amount'] = $vendorInfo['materials'][$iterator]['transportation_amount'] + $vendorInfo['materials'][$iterator]['transportation_cgst_amount'] + $vendorInfo['materials'][$iterator]['transportation_sgst_amount'] + $vendorInfo['materials'][$iterator]['transportation_igst_amount'];
                    $iterator++;
                }
                if(count($vendorInfo['materials']) > 0){
                    $projectSiteInfo = array();
                    $purchaseOrderRequest = $purchaseOrder->purchaseOrderRequest;
                    $projectSiteInfo['project_name'] = $purchaseOrderRequest->purchaseRequest->projectSite->project->name;
                    $projectSiteInfo['project_site_name'] = $purchaseOrderRequest->purchaseRequest->projectSite->name;
                    $projectSiteInfo['project_site_address'] = $purchaseOrderRequest->purchaseRequest->projectSite->address;
                    if($purchaseOrderRequest->purchaseRequest->projectSite->city_id == null){
                        $projectSiteInfo['project_site_city'] = '';
                    }else{
                        $projectSiteInfo['project_site_city'] = $purchaseOrderRequest->purchaseRequest->projectSite->city->name;
                    }
                    $projectSiteInfo['delivery_address'] = $projectSiteInfo['project_name'].', '.$projectSiteInfo['project_site_name'].', '.$projectSiteInfo['project_site_address'].', '.$projectSiteInfo['project_site_city'];
                    $pdf = App::make('dompdf.wrapper');
                    $pdfFlag = "purchase-order-listing-download";
                    $pdfTitle = 'Purchase Order';
                    $formatId = $purchaseOrder->format_id;
                    $pdf->loadHTML(view('purchase.purchase-request.pdf.vendor-quotation')->with(compact('vendorInfo','projectSiteInfo','pdfFlag','pdfTitle','formatId')));
                    $pdfDirectoryPath = env('PURCHASE_VENDOR_ASSIGNMENT_PDF_FOLDER');
                    $date = date('jmYGis');
                    $pdfFileName = sha1($date).'.pdf';
                    $pdfUploadPath = public_path().$pdfDirectoryPath.'/'.$pdfFileName;
                    $pdfContent = $pdf->stream();
                    if(file_exists($pdfUploadPath)){
                        unlink($pdfUploadPath);
                    }
                    if (!file_exists($pdfDirectoryPath)) {
                        File::makeDirectory(public_path().$pdfDirectoryPath, $mode = 0777, true, true);
                    }
                    file_put_contents($pdfUploadPath,$pdfContent);
                    $mailData = ['path' => $pdfUploadPath, 'toMail' => $vendorInfo['email']];
                    $purchaseRequestFormat = $purchaseOrder->purchaseRequest->format_id;
                    $mailMessage = 'Attached herewith the Purchase Order '.$purchaseOrder->format_id.' for Purchase Request '.$purchaseRequestFormat;
                    $this->comment('Sending mail to '.$vendorInfo['company'].' at email address '.$vendorInfo['email']);
                    Mail::send('purchase.purchase-request.email.vendor-quotation', ['mailMessage' => $mailMessage], function($message) use ($mailData, $purchaseRequestFormat){
                        $message->subject('Purchase Order for '.$purchaseRequestFormat);
                        $message->to($mailData['toMail']);
                        $message->cc(env('PURCHASE_CC_MAIL'));
                        $message->bcc('megha.woxi@gmail.com');
                        $message->from(env('MAIL_USERNAME'));
                        $message->attach($mailData['path']);
                    });
                    $this->info('Email Sent successfully.!!');
                    $purchaseOrder->update(['is_email_sent' => true]);

                    if($purchaseOrder->is_client_order == true){
                        $mailInfoData = [
                            'user_id' => $superadminUserId,
                            'type_slug' => 'for-purchase-order',
                            'is_client' => true,
                            'reference_id' => $purchaseOrder->id,
                            'client_id' => $purchaseOrder->client_id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }else{
                        $mailInfoData = [
                            'user_id' => $superadminUserId,
                            'type_slug' => 'for-purchase-order',
                            'is_client' => false,
                            'reference_id' => $purchaseOrder->id,
                            'vendor_id' => $purchaseOrder->vendor_id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }
                    PurchaseRequestComponentVendorMailInfo::insert($mailInfoData);
                    unlink($pdfUploadPath);
                }
                /*if(count($disapprovedVendorId) > 0){
                    $disapprovedVendorId = array_unique($disapprovedVendorId);
                    $purchaseRequestFormatId = $purchaseOrder->purchaseRequest->format_id;
                    foreach($disapprovedVendorId as $vendorId){
                        $mailInfoData = [
                            'user_id' => $superadminUserId,
                            'type_slug' => 'disapprove-purchase-order',
                            'vendor_id' => $vendorId,
                            'is_client' => false
                        ];
                        $vendorEmail = Vendor::where('id', $vendorId)->pluck('email')->first();
                        Mail::send('purchase.purchase-order.email.purchase-order-disapprove', ['purchaseRequestFormatId' => $purchaseRequestFormatId], function($message) use ($vendorEmail, $purchaseRequestFormatId){
                            $message->subject('Disapproval of Quotation ('.$purchaseRequestFormatId.')');
                            $message->to($vendorEmail);
                            $message->from(env('MAIL_USERNAME'));
                        });
                        PurchaseRequestComponentVendorMailInfo::create($mailInfoData);
                    }
                }*/
            }

        }catch(\Exception $e){
            $data = [
              'action' => 'Purchase Order email send from command',
              'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $this->error($e->getMessage());
        }
    }
}
