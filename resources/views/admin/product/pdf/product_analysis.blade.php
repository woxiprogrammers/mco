<html>
<head>
    <style>
        table {
            border-collapse: collapse;
        }
        td {
            padding: 3px;
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
            <hr>
            <p style="text-align: center;font-weight: bold;">PRODUCT ANALYSIS - {{$product->name}}</p>
        </td></tr>
</table>
<div>

    <br>
    <table width="95%" border="1" align="center">
        <tr>
            <td colspan="5" style="text-align: center;font-weight: bold;font-size: 12px;font-style: italic">DESCRIPTION : {{$product->description}}</td>
        </tr>
        <tr>
            <td colspan="5">
                &nbsp;
            </td>
        </tr>
        <tr>
            <th>Material Name</th>
            <th>Unit</th>
            <th>Rate</th>
            <th>Quantity</th>
            <th>Amount</th>
        </tr>
        @foreach ($productMaterialVersions as $material)
        <tr>
            <td>{{$material['name']}}</td>
            <td>{{$material['unit']}}</td>
            <td>{{round($material['rate_per_unit'],3)}}</td>
            <td>{{round($material['quantity'],3)}}</td>
            <td>{!! round(($material['quantity']*$material['rate_per_unit']),3) !!}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="4" style="text-align: right;font-weight: bold;">Sub Total:</td>
            <td >{{$subtotal}}</td>
        </tr>
        <tr>
            <td colspan="5">
                &nbsp;
            </td>
        </tr>
        @if($productProfitMargins != NULL)
            @foreach ($productProfitMargins as $pm)
            <tr>
                <td colspan="3">{{$pm['pm_name']}}</td>
                <td>{{$pm['percentage']}}</td>
                <td>{{$pm['total']}}</td>
            </tr>
            @endforeach
        @endif
        <tr>
            <td colspan="4" style="text-align: right;font-weight: bold;">Total:</td>
            <td >{{$finalAmount}} / <i style="font-size: 14px;font-weight: bold;">{{$product->unit->name}}</i></td>
        </tr>

    </table>
    <br>

</div>
</body>
</html>