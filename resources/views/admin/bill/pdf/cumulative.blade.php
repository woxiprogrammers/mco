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
<table border="1" style="padding-top: 20px; padding-bottom:20px " width="100%">
    <tr>
        <td style="text-align: center"><b>R. A. BILL NO. - {!! $currentBillID !!} </b></td>
    </tr>
    <tr>
        <td style="height: 80px; padding-left: 40px; font-size: 14px;">
        <div>
            SITE - {!! strtoupper($projectSiteName) !!} <br>
            CONTRACTOR - MANISHA CONSTRUCTIONS , PUNE. <br>
            CLIENT -  {!! strtoupper($clientCompany) !!}
        </div>
        </td>
    </tr>
    <tr>
        <td>
            <div>
                <table border="1" width="100%">
                    <tr>
                        <th style="width: 5%; text-align: center"><b>SRN</b></th>
                        <th style="width: 20%; text-align: center"><b>Description</b></th>
                        <th style="width: 9%; text-align: center"><b>Prev Qty</b></th>
                        <th style="width: 9%; text-align: center"><b>Current Qty</b></th>
                        <th style="width: 9%; text-align: center"><b>Cumulative Qty</b></th>
                        <th style="width: 9%; text-align: center"><b>Unit</b></th>
                        <th style="width: 9%; text-align: center"><b>Rate (Rs)</b></th>
                        <th style="width: 10%; text-align: center"><b>Prev. Bill Amnt (Rs)</b></th>
                        <th style="width: 10%; text-align: center"><b>Currnt Bill Amnt (Rs)</b></th>
                        <th style="width: 10%; text-align: center"><b>Cumulative Amnt (Rs)</b></th>
                    </tr>
                    @for($iterator = 0 ; $iterator < count($invoiceData) ; $iterator++ )
                    <tr>
                        <td style="text-align: center;">{!! $iterator+1 !!}</td>
                        <td style="text-align: center;"><b>{!! $invoiceData[$iterator]['product_name'] !!}<b></td>
                        <td style="text-align: center;">{!! $invoiceData[$iterator]['previous_quantity'] !!}</td>
                        <td style="text-align: center;">{!! $invoiceData[$iterator]['current_quantity'] !!}</td>
                        <td style="text-align: center;">{!! $invoiceData[$iterator]['cumulative_quantity'] !!}</td>
                        <td style="text-align: center;">{!! $invoiceData[$iterator]['unit'] !!}</td>
                        <td style="text-align: center;">{!! $invoiceData[$iterator]['rate'] !!}</td>
                        <td style="text-align: center;">{!! $invoiceData[$iterator]['previous_bill_amount'] !!}</td>
                        <td style="text-align: center;">{!! $invoiceData[$iterator]['current_bill_amount'] !!}</td>
                        <td style="text-align: center;">{!! $invoiceData[$iterator]['cumulative_bill_amount'] !!}</td>
                    </tr>
                    @endfor
                    <tr>
                        <td colspan="2" style="text-align: center;"><b>Total</b></td>
                        <td style="text-align: center;">{!! $total['previous_quantity'] !!}</td>
                        <td style="text-align: center;">{!! $total['current_quantity'] !!}</td>
                        <td style="text-align: center;">{!! $total['cumulative_quantity'] !!}</td>
                        <td style="text-align: center;"></td>
                        <td style="text-align: center;">{!! $total['rate'] !!}</td>
                        <td style="text-align: center;">{!! $total['previous_bill_amount'] !!}</td>
                        <td style="text-align: center;">{!! $total['current_bill_amount'] !!}</td>
                        <td style="text-align: center;">{!! $total['cumulative_bill_amount'] !!}</td>

                    </tr>
                </table>
            </div>
        </td></tr>
</table>
</body>
</html>
