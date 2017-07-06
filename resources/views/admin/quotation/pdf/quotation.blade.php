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
            <table width="100%" style="text-align: center" border="1">
                <tr>
                    <td style="background-color: #c2c2c2">BILL OF QUANTITIES</td>
                </tr>
                <tr><td>(SLAB AREA CONSIDERED = <b>{!! $quotation['built_up_area'] !!} SQFT</b>)</td></tr>
            </table>
            <br>
            <div>
                <table width="100%" border="1">
                    <tr style="text-align: center">
                        <th style="width: 5%; "><b>Sr.no</b></th>
                        <th style="width: 25%"><b>Description</b></th>
                        <th style="width: 15%"><b>Qty</b></th>
                        <th style="width: 10%"><b>Unit</b></th>
                        <th style="width: 15%"><b>Rate</b></th>
                        <th style="width: 20%"><b>Amt</b></th>
                        <th style="width: 20%"><b>Rate/SFT</b></th>
                    </tr>
                    <?php $i = 1 ?>
                    @for($j = 0 ; $j < count($summary_data) ; $j++)
                        <tr>
                            <td colspan="7" style="text-align: left;background-color: #e2e2e2;"><b>{{$summary_data[$j]['summary_name']}}</b></td>

                        </tr>
                        @for($iterator = 0 ; $iterator < count($summary_data[$j]['products']) ; $iterator++)
                            @if($iterator == 0)
                            <tr>
                                <td colspan="2" style="text-align: center;"><b>{{$summary_data[$j]['products'][$iterator]['category_name']}}</b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td style="text-align: center">1</td>
                                <td style="text-align: left;">{{$summary_data[$j]['products'][$iterator]['product_name']}}</td>
                                <td style="text-align: right;">{{$summary_data[$j]['products'][$iterator]['quantity']}}</td>
                                <td style="text-align: right;">{{$summary_data[$j]['products'][$iterator]['unit']}}</td>
                                <td style="text-align: right;">{{$summary_data[$j]['products'][$iterator]['rate']}}</td>
                                <td style="text-align: right;">{{$summary_data[$j]['products'][$iterator]['amount']}}</td>
                                <td></td>
                            </tr>
                            @elseif($summary_data[$j]['products'][$iterator]['category_id'] != $summary_data[$j]['products'][$iterator-1]['category_id'])
                            <?php $i = 1 ?>
                            <tr>
                                <td colspan="2" style="text-align: center;"><b>{{$summary_data[$j]['products'][$iterator]['category_name']}}</b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td style="text-align: center">{!! $i !!}</td>
                                <td style="text-align: left;">{{$summary_data[$j]['products'][$iterator]['product_name']}}</td>
                                <td style="text-align: right;">{{$summary_data[$j]['products'][$iterator]['quantity']}}</td>
                                <td style="text-align: right;">{{$summary_data[$j]['products'][$iterator]['unit']}}</td>
                                <td style="text-align: right;">{{$summary_data[$j]['products'][$iterator]['rate']}}</td>
                                <td style="text-align: right;">{{$summary_data[$j]['products'][$iterator]['amount']}}</td>
                                <td></td>
                            </tr>
                            @else
                            <?php $i++; ?>
                            <tr>
                                <td style="text-align: center">{!! $i !!}</td>
                                <td style="text-align: left;">{{$summary_data[$j]['products'][$iterator]['product_name']}}</td>
                                <td style="text-align: right;">{{$summary_data[$j]['products'][$iterator]['quantity']}}</td>
                                <td style="text-align: right;">{{$summary_data[$j]['products'][$iterator]['unit']}}</td>
                                <td style="text-align: right;">{{$summary_data[$j]['products'][$iterator]['rate']}}</td>
                                <td style="text-align: right;">{{$summary_data[$j]['products'][$iterator]['amount']}}</td>
                                <td></td>
                            </tr>
                            @endif
                        @endfor
                    <tr>
                        <td colspan="5" style="text-align: right;"><b>Total Amount</b></td>
                        <td style="text-align: right;"><b>{{$summary_data[$j]['summary_amount']}}</b></td>
                        <td style="text-align: right;"><b>{!! round(($summary_data[$j]['summary_amount']/$quotation['built_up_area']),3) !!}</b></td>
                    </tr>
                    @endfor
                    <tr>
                        <td colspan="6" style="text-align: right;background-color: #d2d2d2 "><b>Sub Total </b></td>
                        <td style="text-align: right;background-color: #d2d2d2 "><b>{!! $total !!}</b></td>
                    </tr>
                    @if($slug == 'with-tax')
                        @for($iterator = 0; $iterator < count($taxData) ; $iterator++)
                            <tr>
                                <td colspan="6" style="text-align: right;">{!! $taxData[$iterator]['name'] !!}&nbsp;&nbsp;{!! $taxData[$iterator]['percentage'] !!} %</td>
                                <td style="text-align: right;">{!! $taxData[$iterator]['tax_amount'] !!}</td>
                            </tr>
                        @endfor
                    @endif
                    <tr>
                        <td colspan="6" style="text-align: right;background-color: #d2d2d2 "><b>Final Total</b></td>
                        <td style="text-align: right;background-color: #d2d2d2 "><b>{!! $rounded_total !!}</b></td>
                    </tr>
                    <tr>
                        <td colspan="7" style="background-color: #e2e2e2;font-size: 13px"><b><i>Rs. {!! $amount_in_words !!}.</i></b></td>
                    </tr>
                </table>
            </div>
            <br>
            <table style="font-size:15px" width="100%" border="1">
                <tr>
                    <th width="50%" style="background-color: #c2c2c2;"><b>For {!! ucwords($company_name) !!}</b></th>
                    <th width="50%" style="background-color: #c2c2c2; text-align: right;"><b>For Manisha Construction</b></th>
                </tr>
                <tr >
                    <td width="50%" style="padding-top: 80px; text-align: right"><b>Authorised Signatory</b></td>
                    <td width="50%" style="padding-top: 80px; text-align: right;"><b>Authorised Signatory</b></td>
                </tr>
            </table>
            <table>
                <tr><td style="padding-top: 80px"></td></tr>
            </table>
</body>
</html>
