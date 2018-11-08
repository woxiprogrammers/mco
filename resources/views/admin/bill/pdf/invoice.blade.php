<html>
<head>
    <style>
        table {
            border-collapse: collapse;
        }
    </style>
</head>
<body>
        <table style="border-left: 1px solid black; border-right: 1px solid black; border-top: 1px solid black" width="100%">
            <tr>
                <td style="width: 10%">
                    <img style="margin-left: 30%" src="http://mconstruction.co.in/assets/global/img/logo.jpg" height="90px" width="160px">
                </td>
                <td style="width: 80%">
                    <table style="padding-top: 2px; padding-bottom:2px;" width="100%">
                        <tr>
                            <td>
                                <table width="100%" style="text-align: center; ">
                                    <tr>
                                        <td style="font-size: 20px"><i>{!! env('COMPANY_NAME') !!}</i></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 14px"><i>{!! env('DESIGNATION') !!}</i></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px"><i>{!! env('ADDRESS') !!}</i></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px">{!! env('CONTACT_NO') !!}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px">{!! env('GSTIN_NUMBER') !!}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <hr>
        <table width="100%" style="font-size: 13px;" border="1">
            <tr>
                <td style="width: 50%; font-weight:  lighter;"><b>Party Name :</b> {!! $clientCompany !!}</td>
                <td style="text-align: right;font-weight: bolder;"><b>Invoice No :</b> {!! $invoice_no !!}</td>
            </tr>
            <tr><td style="font-weight: lighter;" colspan="2"><b>Party Address :</b> {!! $address !!}</td></tr>
            <tr>
                <td style="font-weight: lighter;" colspan="2"><b>Site Name :</b> {!! $projectSiteName !!}</td>
            </tr>
            <tr>
                <td style="font-weight: lighter;" colspan="2"><b>Site Address :</b> {!! $projectSiteAddress !!}</td>
            </tr>
            <tr>
                <td style="font-weight: lighter;" colspan="2"><b>GSTIN :</b>{!! $gstin !!}
            </tr>
            <tr>
                <td style="width: 50%; font-weight: bolder;">Bill No : RA BILL NO - {!! $currentBillID !!}</td>
                <td style=" text-align: right; font-weight: lighter;"> <b>DATE :</b> {!! $billDate !!} </td>
            </tr>
        </table>
        <table width="100%">
            @if($slug == 'performa-invoice')
            <tr>
                <td style="text-align: center; padding-bottom: 8px;"><i><b>PROFORMA INVOICE</b></i></td>
            </tr>
            @else
                <tr>
                    <td style="text-align: center; padding-bottom: 8px;"><i><b>TAX INVOICE</b></i></td>
                </tr>
            @endif
        </table>
        <hr>
            <table border="1" width="100%" style="font-size: 12px;">
                <tr>
                    <th style="width: 7%;text-align: center">Sr no.</th>
                    <th style="width: 30%;text-align: center">Description of item</th>
                    <th style="width: 8%;text-align: center; font-size: 10px">SAC/HSN Code</th>
                    <th style="width: 10%;text-align: center">Quantity</th>
                    <th style="width: 10%;text-align: center">Unit</th>
                    <th style="width: 15%;text-align: center">Rate</th>
                    <th style="width: 20%;text-align: center">Amount</th>
                </tr>
                @for($iterator = 0 ; $iterator < count($invoiceData) ; $iterator++ )
                    <tr>
                        <td style="text-align: center;height: 25px;">{!! $iterator+1 !!}</td>
                        <td style="text-align: left; padding-left: 5px">{!! $invoiceData[$iterator]['product_name'] !!} @if($invoiceData[$iterator]['description'] != null) - {!! $invoiceData[$iterator]['description'] !!}@endif</td>
                        <td style="text-align: center;"> {{$hsnCode}}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! round($invoiceData[$iterator]['quantity'],3) !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['unit'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['rate'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['amount'] !!}</td>
                    </tr>
                @endfor
                @if(count($extraItems) > 0)
                    @for($i = 0; $i < count($extraItems); $i++,$iterator++)
                        <tr >
                            <td style="text-align: center;height: 25px;">{!! $iterator+1  !!}</td>
                            <td style="text-align: left; padding-left: 5px">
                            Extra Item : {!! $extraItems[$i]->quotationExtraItems->extraItem->name !!} - {!! $extraItems[$i]->description !!}
                            </td>
                            <td style="text-align: center;"> {{$hsnCode}}</td>
                            <td style="text-align: right; padding-right: 10px;">1</td>
                            <td style="text-align: right; padding-right: 10px;">Nos</td>
                            <td colspan="1" style="text-align: right; padding-right: 10px;">
                                {!! $extraItems[$i]->rate !!}
                            </td>
                            <td style="text-align: right; padding-right: 10px;">
                                {!! $extraItems[$i]->rate !!}
                            </td>
                        </tr>
                    @endfor
                @endif
                @if((count($invoiceData) + count($extraItems)) < 5)
                    @for($i = 0 ; $i < (5 - (count($invoiceData) + count($extraItems))) ; $i++)
                        <tr>
                            <td style="height: 25px;"> </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                @endif
                <tr>
                    <td colspan="6" style="text-align: right; padding-right: 10px;"><b>Sub Total</b></td>
                    <td style="text-align: right; padding-right: 10px;">{!! $sub_total_before_discount !!}</td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align: right; padding-right: 10px;"><b>Discount Amount</b></td>
                    <td style="text-align: right; padding-right: 10px;">{!! $discount_amount !!}</td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align: right; padding-right: 10px;"><b>Total Amount Before Tax</b></td>
                    <td style="text-align: right; padding-right: 10px;">{!! $subTotal !!}</td>
                </tr>
                @for($iterator = 0 ; $iterator < count($taxData) ; $iterator++)
                    <tr>
                        <td colspan="6" style="text-align: right; padding-right: 10px;"> {!! $taxData[$iterator]['name'] !!}&nbsp;&nbsp;{!! $taxData[$iterator]['percentage'] !!} %</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $taxData[$iterator]['tax_amount'] !!}</td>
                    </tr>
                @endfor
                <tr>
                    <td colspan="6" style="text-align: right; padding-right: 10px;"><b>Total After tax</b></td>
                    <td style="text-align: right; padding-right: 10px;">{!! $totalAfterTax !!}</td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align: right; padding-right: 10px;"><b>Rounded By</b></td>
                    <td style="text-align: right; padding-right: 10px;">{!! $roundedBy !!}</td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align: right; padding-right: 10px;"><b>Net Payable</b></td>
                    <td style="text-align: right; padding-right: 10px;">{!! $grossTotal !!}</td>
                </tr>
                <tr>
                    <td colspan="7" style="background-color: #808080"><b>Total Invoice in words:</b> <i>Rs. {!! $amountInWords !!}</i></td>
                </tr>
            </table>
        <br>
        <table style="font-size:15px" width="100%" border="1">
            <tr>
                <th width="50%" style="background-color: #c2c2c2;"><b>Bank Details :</b></th>
                <th width="50%" style="background-color: #c2c2c2; text-align: right;"><b>For Manisha Construction</b></th>
            </tr>
            <tr>
                <td width="50%" style="text-align: left">
                    @if($bankData != null)
                        Bank Name : {!! $bankData->bank_name !!}<br>
                        Account Number : {!! $bankData->account_number !!}<br>
                        IFS Code : {!! $bankData->ifs_code !!}<br>
                        Branch ID : {!! $bankData->branch_id !!}<br>
                        Branch Name : {!! $bankData->branch_name !!}
                    @else
                        Bank Name : <br>
                        Account Number : <br>
                        IFS Code : <br>
                        Branch ID : <br>
                        Branch Name :
                    @endif
                </td>
                <td width="50%" style="padding-top:60px ;text-align: right;"><b>Authorised Signatory</b></td>
            </tr>
        </table>
</body>
</html>
