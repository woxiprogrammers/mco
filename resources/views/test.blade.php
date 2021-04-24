<html>

<head>
    <style>
        table {
            border-collapse: collapse;
        }

        .column {
            float: left;
            width: 49.99%;
        }

        .row::after {
            clear: both;
            display: table;
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
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table border="1" style="padding-top: 20px; padding-bottom:20px " width="100%">
        <tr>
            <td colspan="10" style="text-align: center; background-color:#81A1D1; height: 4%; font-size: 18px;"><b>Transfer Challan </b></td>
        </tr>
        <tr>
            <td colspan=" 10" style="font-size: 16px"> Challan No - </td>
        </tr>
        <tr style="font-size: 14px">
            <td colspan="5" style="width: 50%;">GRN No-</td>
            <td colspan="5" style="width: 50%;">Date-</td>
        </tr>
        <tr style="font-size: 14px">
            <td colspan="5">Transporter Name-</td>
            <td colspan="5">Trip Rate-</td>
        </tr>
        <tr style="font-size: 14px">
            <td colspan="5">Source Name-</td>
            <td colspan="5">Destination Name-</td>
        </tr>
        <tr style="font-size: 14px">
            <td colspan="5">Source Address-</td>
            <td colspan="5">Destination Address-</td>
        </tr>
        <tr style="font-size: 14px">
            <td colspan="5"> &nbsp; </td>
            <td colspan="5"> &nbsp; </td>
        </tr>
        <tr style="font-size: 14px">
            <td colspan="5">Driver Name-</td>
            <td colspan="5">Mobile No-</td>
        </tr>
        <tr>
            <td colspan=" 10"> &nbsp; </td>
        </tr>

        @if(count($assets) > 0)
        <tr style="font-size: 16px">
            <td colspan="10" style="text-align: center; height: 3%; background-color:#81A1D1; "><b>Asset </b></td>
        </tr>
        <tr style="font-size: 14px;">
            <td colspan="2" style="width: 10%; text-align: center;"> <b> S. No. </b> </td>
            <td colspan="2" style="width: 40%; text-align: center;"> <b> Product Description </b> </td>
            <td colspan="2" style="width: 10%; text-align: center;"> <b> QTY </b> </td>
            <td colspan="2" style="width: 10%; text-align: center;"> <b> Unit </b> </td>
            <td colspan="2" style="width: 20%; text-align: center;"> <b> Rent </b> </td>
        </tr>
        @for($iterator = 0 ; $iterator < count($assets); $iterator++) <tr style="font-size: 14px;">
            <td colspan="2" style="text-align: center;">{{$iterator+1}}.</td>
            <td colspan="2">{{$assets[$iterator]['inventory_component_name']}}</td>
            <td style="text-align: right;">{{$assets[$iterator]['quantity']}}</td>
            <td style="text-align: center;">{{$assets[$iterator]['unit_name']}}</td>
            <td style="text-align: right;">{{$assets[$iterator]['rate_per_unit']}}</td>
            </tr>
            @endfor
            @endif
            @if(count($materials) > 0)
            <tr style="font-size: 16px">
                <td colspan="10" style="text-align: center; height: 3%; background-color:#81A1D1; "><b>Material</b></td>
            </tr>
            <tr style="text-align: center; font-size: 14px;">
                <td colspan="2"> <b> S. No. </b> </td>
                <td colspan="2"> <b> Product Description </b> </td>
                <td> <b> QTY </b> </td>
                <td> <b> Unit </b> </td>
                <td> <b> Rate </b> </td>
                <td> <b> GST </b> </td>
                <td colspan="2"> <b> Amount </b> </td>
            </tr>
            @for($iterator = 0 ; $iterator < count($materials); $iterator++) <tr style="font-size: 14px;">
                <td colspan="2" style="text-align: center;">{{$iterator+1}}.</td>
                <td colspan="2">{{$materials[$iterator]['inventory_component_name']}}</td>
                <td style="text-align: right;">{{$materials[$iterator]['quantity']}}</td>
                <td style="text-align: center;">{{$materials[$iterator]['unit_name']}}</td>
                <td style="text-align: right;">{{$materials[$iterator]['rate_per_unit']}}</td>
                <td style="text-align: right;">{{$materials[$iterator]['gst']}}</td>
                <td colspan="2" style="text-align: right;">{{$materials[$iterator]['total']}}</td>
                </tr>
                @endfor
                <tr style="font-size: 14px;">
                    <td colspan="4" style="text-align: center;"> <b> Total </b> </td>
                    <td style="text-align: right;"> <b> {{$materialTotal['quantity_total']}} </b> </td>
                    <td> </td>
                    <td style="text-align: right;"> <b> {{$materialTotal['rate_per_unit']}} </b> </td>
                    <td style="text-align: right;"> <b> {{$materialTotal['gst_total']}} </b> </td>
                    <td colspan="2" style="text-align: right;"> <b> {{$materialTotal['total']}} </b> </td>
                </tr>
                @endif
    </table>

</body>

</html>