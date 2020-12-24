<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
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
    <br>
    <table border=" 1" width="100%" style="font-size: 12px;">
        <tr style="background-color:#81A1D1">
            <td colspan='8' style="text-align: center;height: 35px; font-weight: bold; font-size: 16px;"> Monthly Rent Bill </td>
        </tr>
        <tr>
            <td colspan='8' style="text-align: left;height: 30px; font-weight: bold; font-size: 14px;"> Billing for the month - {!! $bill_month !!}</td>
        </tr>
        <tr>
            <td colspan='8' style="text-align: left;height: 30px; font-weight: bold; font-size: 14px;"> Site Name - {!! $projectSite['name'] !!}</td>
        </tr>
        <tr>
            <td colspan='8' style="text-align: left;height: 30px; font-weight: bold; font-size: 14px;"> Site Address - {!! $projectSite['address'] !!} </td>
        </tr>
        <tr>
            <td colspan='3' style="text-align: left;height: 30px; font-weight: bold; font-size: 14px;"> Invoice No - Rent </td>
            <td colspan='5' style="text-align: left;height: 30px; font-weight: bold; font-size: 14px;"> Date - </td>
        </tr>
        <tr style="background-color:#81A1D1">
            <td style="text-align: center; height: 25px; font-weight: bold">Sr no.</td>
            <td style="text-align: center; height: 25px; font-weight: bold">Name</td>
            <td style="text-align: center; height: 25px; font-weight: bold">Transfer Date</td>
            <td style="text-align: center; height: 25px; font-weight: bold">Days</td>
            <td style="text-align: center; height: 25px; font-weight: bold">Rent</td>
            <td style="text-align: center; height: 25px; font-weight: bold">Qty</td>
            <td style="text-align: center; height: 25px; font-weight: bold">Unit</td>
            <td style="text-align: center; height: 25px; font-weight: bold">Amount</td>
        </tr>
        @foreach($rows as $key => $row)
        <tr>
            <?php
            if ($row['make_bold']) {
                $makeBold = true;
            } else {
                $makeBold = false;
            }
            unset($row['make_bold']);
            ?>
            @if ($makeBold)
            <td style="text-align: center; height: 20px; font-weight: bold">{!! $row[0] !!}</td>
            <td style="text-align: center; height: 20px; font-weight: bold">{!! $row[1] !!}</td>
            <td style="text-align: center; height: 20px; font-weight: bold">{!! $row[2] !!}</td>
            <td style="text-align: right; height: 20px; font-weight: bold">{!! $row[3] !!}</td>
            <td style="text-align: right; height: 20px; font-weight: bold">{!! $row[4] !!}</td>
            <td style="text-align: right; height: 20px; font-weight: bold">{!! $row[5] !!}</td>
            <td style="text-align: left; height: 20px; font-weight: bold">{!! $row[6] !!}</td>
            <td style="text-align: right; height: 20px; font-weight: bold">{!! $row[7] !!}</td>
            @else
            <td style="text-align: center; height: 20px">{!! $row[0] !!}</td>
            <td style="text-align: center; height: 20px">{!! $row[1] !!}</td>
            <td style="text-align: center; height: 20px">{!! $row[2] !!}</td>
            <td style="text-align: right; height: 20px;">{!! $row[3] !!}</td>
            <td style="text-align: right; height: 20px">{!! $row[4] !!}</td>
            <td style="text-align: right; height: 20px">{!! $row[5] !!}</td>
            <td style="text-align: left; height: 20px">{!! $row[6] !!}</td>
            <td style="text-align: right; height: 20px">{!! $row[7] !!}</td>
            @endif
        </tr>
        @endforeach
        <tr>
            <td colspan='7' style="text-align: center;height: 25px; font-weight: bold">
                Final Rent total
            </td>
            <td colspan='1' style="text-align: right;height: 25px; font-weight: bold">
                {!! $projectSiteRentTotal !!}
            </td>
        </tr>
    </table>
</body>

</html>