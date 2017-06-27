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
    <tr>
        <td style="text-align: center"><b>R. A. BILL NO. - 1 </b></td>
    </tr>
    <tr>
        <td style="height: 80px; padding-left: 40px">
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
                        <th style="width: 12%; text-align: center"><b>Sr no.</b></th>
                        <th style="width: 40%; text-align: center"><b>Description of item</b></th>
                        <th style="width: 12%; text-align: center"><b>Prev Qty</b></th>
                        <th style="width: 12%; text-align: center"><b>Current Qty</b></th>
                        <th style="width: 12%; text-align: center"><b>Cumulative Qty</b></th>
                        <th style="width: 12%; text-align: center"><b>Unit</b></th>
                        <th style="width: 12%; text-align: center"><b>Rate</b></th>
                        <th style="width: 12%; text-align: center"><b>Prev Bill Amnt</b></th>
                        <th style="width: 12%; text-align: center"><b>Currnt Bill Amnt</b></th>
                        <th style="width: 12%; text-align: center"><b>Cumulative Bill Amnt</b></th>
                    </tr>
                    @for($iterator = 0 ; $iterator < count($invoiceData) ; $iterator++ )
                    <tr>
                        <td style="text-align: center;">{!! $iterator+1 !!}</td>
                        <td style="text-align: center;">{!! $invoiceData[$iterator]['product_name'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['previous_quantity'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['current_quantity'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['cumulative_quantity'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['unit'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['rate'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['previous_bill_amount'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['current_bill_amount'] !!}</td>
                        <td style="text-align: right; padding-right: 10px;">{!! $invoiceData[$iterator]['cumulative_bill_amount'] !!}</td>
                    </tr>
                    @endfor
                </table>
            </div>
        </td></tr>
</table>
</body>
</html>
