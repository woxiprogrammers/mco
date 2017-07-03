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
            </table>
            <br>
            <div>
                <table width="100%" border="1">
                    <tr style="text-align: center">
                        <th style="width: 5%; "><b>Sr.no</b></th>
                        <th style="width: 25%"><b>Description</b></th>
                        <th style="width: 20%"><b>Qty</b></th>
                        <th style="width: 10%"><b>Unit</b></th>
                        <th style="width: 15%"><b>Rate</b></th>
                        <th style="width: 15%"><b>Amt</b></th>
                    </tr>
                    <?php $i = 1 ?>
                    @for($iterator = 0 ; $iterator < count($quotationProductData) ; $iterator++)
                        @if($iterator == 0)
                            <tr>
                                <td colspan="2" style="text-align: center;background-color: #c2c2c2;"><b>{{$quotationProductData[$iterator]['category_name']}}</b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td style="text-align: center">{!! $i !!}</td>
                                <td style="text-align: left; padding-left: 10px">{{$quotationProductData[$iterator]['product_name']}}</td>
                                <td style="text-align: right; padding-right: 10px">{{$quotationProductData[$iterator]['quantity']}}</td>
                                <td style="text-align: right; padding-right: 10px">{{$quotationProductData[$iterator]['unit']}}</td>
                                <td style="text-align: right; padding-right: 10px">{{$quotationProductData[$iterator]['rate']}}</td>
                                <td style="text-align: right; padding-right: 10px">{{$quotationProductData[$iterator]['amount']}}</td>
                            </tr>
                        @elseif($quotationProductData[$iterator]['category_id'] != $quotationProductData[$iterator-1]['category_id'])
                            <tr>
                                <td colspan="2" style="text-align: center;background-color: #c2c2c2;"><b>{{$quotationProductData[$iterator]['category_name']}}</b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td style="text-align: center">{!! $i !!}</td>
                                <td style="text-align: left; padding-left: 10px">{{$quotationProductData[$iterator]['product_name']}}</td>
                                <td style="text-align: right; padding-right: 10px">{{$quotationProductData[$iterator]['quantity']}}</td>
                                <td style="text-align: right; padding-right: 10px">{{$quotationProductData[$iterator]['unit']}}</td>
                                <td style="text-align: right; padding-right: 10px">{{$quotationProductData[$iterator]['rate']}}</td>
                                <td style="text-align: right; padding-right: 10px">{{$quotationProductData[$iterator]['amount']}}</td>
                            </tr>
                        @else
                    <?php $i++; ?>
                            <tr>
                                <td style="text-align: center">{!! $i !!}</td>
                                <td style="text-align: left; padding-left: 10px">{{$quotationProductData[$iterator]['product_name']}}</td>
                                <td style="text-align: right; padding-right: 10px">{{$quotationProductData[$iterator]['quantity']}}</td>
                                <td style="text-align: right; padding-right: 10px">{{$quotationProductData[$iterator]['unit']}}</td>
                                <td style="text-align: right; padding-right: 10px">{{$quotationProductData[$iterator]['rate']}}</td>
                                <td style="text-align: right; padding-right: 10px">{{$quotationProductData[$iterator]['amount']}}</td>
                            </tr>
                    @endif
                    @endfor
                    <tr>
                        <td colspan="5" style="text-align: right; padding-right:10px;background-color: #c2c2c2 ">Total </td>
                        <td style="text-align: right; padding-right:10px;background-color: #c2c2c2 ">{!! $total !!}</td>
                    </tr>
                    @if($slug == 'with-tax')
                        @for($iterator = 0; $iterator < count($taxData) ; $iterator++)
                            <tr>
                                <td colspan="5" style="text-align: right; padding-right: 10px;">{!! $taxData[$iterator]['name'] !!}&nbsp;&nbsp;{!! $taxData[$iterator]['percentage'] !!} %</td>
                                <td style="text-align: right; padding-right: 10px;">{!! $taxData[$iterator]['tax_amount'] !!}</td>
                            </tr>
                        @endfor
                    @endif
                    <tr>
                        <td colspan="5" style="text-align: right; padding-right:10px;background-color: #c2c2c2 ">Final Total</td>
                        <td style="text-align: right; padding-right:10px;background-color: #c2c2c2 ">{!! $rounded_total !!}</td>
                    </tr>
                    <tr>
                        <td colspan="6" style="background-color: #c2c2c2"><b><i>Rs. {!! $amount_in_words !!}.</i></b></td>
                    </tr>
                </table>
            </div>
            <br>
            <table style="font-size:15px" width="100%" border="1">
                <tr>
                    <th width="65%" colspan="2" style="background-color: #c2c2c2; padding-left: 10px"><b>For {!! ucwords($company_name) !!}</b></th>
                    <th width="35%" style="background-color: #c2c2c2; text-align: right; padding-right: 10px"><b>For Manisha Construction</b></th>
                </tr>
                <tr >
                    <td width="32.5%" style="padding-top: 80px"><b>Head-Engineering</b></td>
                    <td width="32.5%" style="padding-top: 80px; text-align: center"><b>Authorised signatory</b></td>
                    <td width="32.5%" style="padding-top: 80px; text-align: right; padding-right:10px "><b>Suresh Vaghela</b></td>
                </tr>
            </table>
            <table>
                <tr><td style="padding-top: 80px"></td></tr>
            </table>
</body>
</html>
