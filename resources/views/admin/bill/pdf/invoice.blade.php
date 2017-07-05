<html>
<head>
    <style>
        table {
            border-collapse: collapse;
        }
    </style>
</head>
<body>
        <table border="1" style="padding-top: 20px; padding-bottom:20px " width="100%">
            <tr><td>
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
                    </table>
                </td></tr>
        </table>
        <hr>
        <table>
            <tr>
                <td style="width: 600px"><b>To : {!! $clientCompany !!} </b></td>
                <td style=" font-size: 15px"> <i>DATE : {!! $billDate !!}</i> </td>
            </tr>
            <tr>
                <td><b>Site Name : {!! $projectSiteName !!}</b></td>
            </tr>
            <tr>
                <td><b>Bill No : RA BILL NO - {!! $currentBillID !!}</b></td>
            </tr>
        </table>
        <hr>
        <table width="100%">
            <tr>
                <td style="text-align: center; padding-bottom: 8px;"><i>ABSTRACT</i></td>
            </tr>
        </table>
        <hr>
        <div>
            <table border="1" width="100%">
                <tr>
                    <th style="width: 12%; text-align: center"><b>Sr no.</b></th>
                    <th style="width: 40%; text-align: center"><b>Description of item</b></th>
                    <th style="width: 12%; text-align: center"><b>Quantity</b></th>
                    <th style="width: 12%; text-align: center"><b>Unit</b></th>
                    <th style="width: 12%; text-align: center"><b>Rate</b></th>
                    <th style="width: 12%; text-align: center"><b>Amount</b></th>
                </tr>
                @for($iterator = 0 ; $iterator < count($invoiceData) ; $iterator++ )
                    <tr>
                        <td style="text-align: center;">{!! $iterator+1 !!}</td>
                        <td style="text-align: center;">{!! $invoiceData[$iterator]['product_name'] !!} - {!! $invoiceData[$iterator]['description'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['quantity'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['unit'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['rate'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['amount'] !!}</td>
                    </tr>
                @endfor
                @for($i = 0 ; $i < (15 - count($invoiceData)) ; $i++)
                    <tr>
                        <td style="height: 2.7%"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
                <tr>
                    <td colspan="5" style="text-align: center; width: 650px"><b>Total</b></td>
                    <td style="text-align: right; padding-right: 10px;">{!! $subTotal !!}</td>
                </tr>
                @for($iterator = 0 ; $iterator < count($taxData) ; $iterator++)
                    <tr>
                        <td colspan="5" style="text-align: right; padding-right: 10px;">{!! $taxData[$iterator]['name'] !!}&nbsp;&nbsp;{!! $taxData[$iterator]['percentage'] !!} %</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $taxData[$iterator]['tax_amount'] !!}</td>
                    </tr>
                @endfor
                <tr>
                    <td colspan="5" style="text-align: center; padding-right: 10px;"><b>Gross Total</b></td>
                    <td style="text-align: right; padding-right: 10px;">{!! $grossTotal !!}</td>
                </tr>
                <tr>
                    <td colspan="6" style="background-color: #808080"><b>Amount in Words : {!! $amountInWords !!}</b></td>
                </tr>
            </table>
        </div>
        <br>
        <table style="font-size:15px ; padding-left:500px">
            <tr>
                <td>For Manisha Construction</td>
            </tr>
            <tr>
                <td style="padding-top: 30px">Authorised Signatory</td>
            </tr>
        </table>
</body>
</html>
