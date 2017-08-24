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
                <td style="width: 20%">
                    <img style="margin-left: 10%" src="http://mconstruction.co.in/assets/global/img/logo.jpg" height="75px" width="120px">
                </td>
                <td style="width: 80%">
                    <table style="padding-top: 20px; padding-bottom:20px;" width="100%">
                        <tr>
                            <td>
                                <table width="100%" style="text-align: center; ">
                                    <tr>
                                        <td style="font-size: 30px"><i>{!! env('COMPANY_NAME') !!}</i></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px"><i>{!! env('DESIGNATION') !!}</i></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 15px"><i>{!! env('ADDRESS') !!}</i></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 15px">{!! env('CONTACT_NO') !!}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 15px">{!! env('GSTIN_NUMBER') !!}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <hr>
        <table width="100%">
            <tr>
                <td style="width: 50%; font-weight:  lighter;">To : {!! $clientCompany !!}</td>
                <td style="text-align: right;font-weight: bolder;">Invoice No : {!! $invoice_no !!}</td>
            </tr>
            <tr>
                <td style="font-weight: lighter;" colspan="2">Site Name : {!! $projectSiteName !!}</td>
            </tr>
            <tr>
                <td style="width: 50%; font-weight: bolder;">Bill No : RA BILL NO - {!! $currentBillID !!}</td>
                <td style=" font-size: 15px; text-align: right; font-weight: lighter;"> DATE : {!! $billDate !!} </td>
            </tr>
        </table>
        <hr>
        <table width="100%">
            <tr>
                <td style="text-align: center; padding-bottom: 8px;"><i>ABSTRACT</i></td>
            </tr>
        </table>
        <hr>
            <table border="1" width="100%" style="font-size: 14px;">
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
                        <td style="text-align: center;">{!! $iterator+1 !!}</td>
                        <td style="text-align: center;">{!! $invoiceData[$iterator]['product_name'] !!} @if($invoiceData[$iterator]['description'] != null) - {!! $invoiceData[$iterator]['description'] !!} @endif</td>
                        <td style="text-align: center;"> {{$hsnCode}}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['quantity'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['unit'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['rate'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['amount'] !!}</td>
                    </tr>
                @endfor
                @if(count($extraItems) > 0)
                    @for($i = 0; $i < count($extraItems); $i++,$iterator++)
                        <tr>
                            <td style="text-align: center;">{!! $iterator+1  !!}</td>
                            <td colspan="2">
                            {!! $extraItems[$i]->quotationExtraItems->extraItem->name !!} - {!! $extraItems[$i]->description !!}
                            </td>
                            <td colspan="2" style="text-align: right; padding-right: 10px;">
                                {!! $extraItems[$i]->quotationExtraItems->rate !!}
                            </td>
                            <td colspan="1" style="text-align: right; padding-right: 10px;">
                                {!! $extraItems[$i]->previous_rate !!}
                            </td>
                            <td colspan="1" style="text-align: right; padding-right: 10px;">
                                {!! $extraItems[$i]->rate !!}
                            </td>
                        </tr>
                    @endfor
                @endif
                @if((count($invoiceData) + count($extraItems)) < 15)
                    @for($i = 0 ; $i < (15 - (count($invoiceData) + count($extraItems))) ; $i++)
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="2">&nbsp;</td>
                            <td colspan="2">&nbsp;</td>
                            <td colspan="1">&nbsp;</td>
                            <td colspan="1">&nbsp;</td>
                        </tr>
                    @endfor
                @endif
                <tr>
                    <td colspan="6" style="text-align: right; padding-right: 10px;"><b>Total</b></td>
                    <td style="text-align: right; padding-right: 10px;">{!! $subTotal !!}</td>
                </tr>
                @for($iterator = 0 ; $iterator < count($taxData) ; $iterator++)
                    <tr>
                        <td colspan="6" style="text-align: right; padding-right: 10px;">{!! $taxData[$iterator]['name'] !!}&nbsp;&nbsp;{!! $taxData[$iterator]['percentage'] !!} %</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $taxData[$iterator]['tax_amount'] !!}</td>
                    </tr>
                @endfor
                <tr>
                    <td colspan="6" style="text-align: right; padding-right: 10px;"><b>Gross Total</b></td>
                    <td style="text-align: right; padding-right: 10px;">{!! $grossTotal !!}</td>
                </tr>
                <tr>
                    <td colspan="7" style="background-color: #808080"><i>Rs. {!! $amountInWords !!}</i></td>
                </tr>
            </table>
        <br>
        <table style="font-size:15px" width="100%" border="1">
            <tr>
                <th width="65%" colspan="2" style="background-color: #c2c2c2;"><b>For {!! ucwords($company_name) !!}</b></th>
                <th width="35%" style="background-color: #c2c2c2; text-align: right;"><b>For Manisha Construction</b></th>
            </tr>
            <tr >
                <td width="32.5%" style="padding-top: 60px; text-align: right"><b>Head-Engineering</b></td>
                <td width="32.5%" style="padding-top: 60px; text-align: right"><b>Authorised Signatory</b></td>
                <td width="32.5%" style="padding-top: 60px; text-align: right;"><b>Authorised Signatory</b></td>
            </tr>
        </table>
</body>
</html>
