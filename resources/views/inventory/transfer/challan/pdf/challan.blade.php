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
            <td colspan=" 10" style="font-size: 16px"> Challan No - {{$challan['challan_number']}}</td>
        </tr>
        <tr style="font-size: 14px">
            <td colspan="10">Date - {{$other_data['date']}}</td>
        </tr>
        <tr style="font-size: 14px">
            <td colspan="4">Transporter Name - {{$other_data['vendor_name']}}</td>
            <td colspan="6">Trip Rate - {{$other_data['transportation_total']}}</td>
        </tr>
        <tr style="font-size: 14px">
            <td colspan="4">Source Name - {{$challan->projectSiteOut->project->name}}</td>
            <td colspan="6">Destination Name - {{$challan->projectSiteIn->project->name}}</td>
        </tr>
        <tr style="font-size: 14px">
            <td colspan="4">Source Address - {{$challan->projectSiteOut->address}}</td>
            <td colspan="6">Destination Address - {{$challan->projectSiteIn->address}}</td>
        </tr>
        <tr style="font-size: 14px">
            <td colspan="4"> &nbsp; </td>
            <td colspan="6"> &nbsp; </td>
        </tr>
        <tr style="font-size: 14px">
            <td colspan="4">Driver Name - {{$other_data['driver_name']}}</td>
            <td colspan="6">Mobile No - {{$other_data['mobile']}}</td>
        </tr>

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
        <tr style="font-size: 14px;">
            <td colspan="2" style="width: 10%; text-align: center;">1.</td>
            <td colspan="2" style="width: 40%; text-align: center;">A</td>
            <td colspan="2" style="width: 10%; text-align: center;">1.00</td>
            <td colspan="2" style="width: 10%; text-align: center;">Nos</td>
            <td colspan="2" style="width: 20%; text-align: center;">0.25</td>
        </tr>
        <tr style="font-size: 14px;">
            <td colspan="2" style="width: 10%; text-align: center;">2.</td>
            <td colspan="2" style="width: 40%; text-align: center;">B</td>
            <td colspan="2" style="width: 10%; text-align: center;">1.00</td>
            <td colspan="2" style="width: 10%; text-align: center;">Nos</td>
            <td colspan="2" style="width: 20%; text-align: center;">0.25</td>
        </tr>
        <tr style="font-size: 14px;">
            <td colspan="2" style="width: 10%; text-align: center;">3.</td>
            <td colspan="2" style="width: 40%; text-align: center;">C</td>
            <td colspan="2" style="width: 10%; text-align: center;">1.00</td>
            <td colspan="2" style="width: 10%; text-align: center;">Nos</td>
            <td colspan="2" style="width: 20%; text-align: center;">0.25</td>
        </tr>
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
        <tr style="font-size: 14px;">
            <td colspan="2" style="text-align: center;">1.</td>
            <td colspan="2">A</td>
            <td style="text-align: right;">1.00</td>
            <td style="text-align: center;">MT</td>
            <td style="text-align: right;">10.00</td>
            <td style="text-align: right;">1.80</td>
            <td colspan="2" style="text-align: right;">11.80</td>
        </tr>
        <tr style="font-size: 14px;">
            <td colspan="2" style="text-align: center;">1.</td>
            <td colspan="2">B</td>
            <td style="text-align: right;">1.00</td>
            <td style="text-align: center;">MT</td>
            <td style="text-align: right;">10.00</td>
            <td style="text-align: right;">1.80</td>
            <td colspan="2" style="text-align: right;">11.80</td>
        </tr>
        <tr style="font-size: 14px;">
            <td colspan="2" style="text-align: center;">1.</td>
            <td colspan="2">C</td>
            <td style="text-align: right;">1.00</td>
            <td style="text-align: center;">MT</td>
            <td style="text-align: right;"> 10.00</td>
            <td style="text-align: right;">1.80</td>
            <td colspan="2" style="text-align: right;">11.80</td>
        </tr>
        <tr style="font-size: 14px;">
            <td colspan="4" style="text-align: center;"> <b> Total </b> </td>
            <td style="text-align: right;"> <b> 39.00 </b> </td>
            <td> </td>
            <td style="text-align: right;"> <b> 51.00 </b> </td>
            <td style="text-align: right;"> <b> 9.18 </b> </td>
            <td colspan="2" style="text-align: right;"> <b> 60.18 </b> </td>
        </tr>
    </table>

</body>

</html>