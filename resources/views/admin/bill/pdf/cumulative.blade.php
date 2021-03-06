<html>
<head>
    <style>
        table {
            border-collapse: collapse;
        }
    </style>
</head>
<body>
<table style="border-left: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black" width="100%">
    <tr>
        <td style="width: 20%">
            <img style="margin-left: 10%" src="http://mconstruction.co.in/assets/global/img/logo.jpg" height="90px" width="140px">
        </td>
        <td style="width: 80%">
            <table style="padding-top: 20px; padding-bottom:20px;" width="100%">
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

        </td>
    </tr>
    </tr>
</table>
<table border="1" style="padding-top: 20px; padding-bottom:20px " width="100%">
    <tr>
        <td style="text-align: center"><b>{!! $currentBillName !!} BILL NO. - {!! $currentBillID !!} </b></td>
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
                        <th style="width: 10%; text-align: center"><b>Current Bill Amnt (Rs)</b></th>
                        <th style="width: 10%; text-align: center"><b>Cumulative Amnt (Rs)</b></th>
                    </tr>
                    @for($iterator = 0 ; $iterator < count($invoiceData) ; $iterator++ )
                        <tr>
                            <td style="text-align: center;">{!! $iterator+1 !!}</td>
                            <td style="text-align: left; padding-left: 5px"><b>{!! $invoiceData[$iterator]['product_name'] !!}<b></td>
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
                    @if(count($extraItems) > 0)
                        @for($i = 0; $i < count($extraItems); $i++,$iterator++)
                            <tr>
                                <td style="text-align: center;">{!! $iterator+1  !!}</td>
                                <td style="text-align: left; padding-left: 5px">
                                    <b>Extra Item : {!! $extraItems[$i]->quotationExtraItems->extraItem->name !!}</b>
                                </td>
                                <td style="text-align: center;"> - </td>
                                <td style="text-align: center;"> - </td>
                                <td style="text-align: center;"> - </td>
                                <td style="text-align: center;"> - </td>
                                <td style="text-align: center;"> - </td>
                                <td style="text-align: center;">
                                    {!! $extraItems[$i]->previous_rate !!}
                                </td>
                                <td style="text-align: center; padding-right: 10px;">
                                    {!! $extraItems[$i]->rate !!}
                                </td>
                                <td style="text-align: center; padding-right: 10px;">
                                    {!! $extraItems[$i]->previous_rate + $extraItems[$i]->rate !!}
                                </td>
                            </tr>
                        @endfor
                    @endif
                    @if((count($invoiceData) + count($extraItems)) < 12)
                        @for($iterator = 0 ; $iterator < (12 - (count($invoiceData) + count($extraItems))) ; $iterator++ )
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="6">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        @endfor
                    @endif
                    <tr>
                        <td colspan="7" style="text-align: center;"><b>Total</b></td>
                        <td style="text-align: center; font-weight: bold;">{!! $total['previous_bill_amount'] !!}</td>
                        <td style="text-align: center; font-weight: bold;">{!! $total['current_bill_amount'] !!}</td>
                        <td style="text-align: center; font-weight: bold;">{!! $total['cumulative_bill_amount'] !!}</td>
                    </tr>
                </table>
            </div>
        </td></tr>
</table>
</body>
</html>
