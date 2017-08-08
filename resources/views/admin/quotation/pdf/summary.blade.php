<html>
<head>
    <style>
        table {
            border-collapse: collapse;
        }
    </style>
</head>
<body>
<table style="border-left: 1px solid black; border-right: 1px solid black; border-top: 1px solid black" width="100%">
    <tr>
        <td style="width: 20%">
            <img style="margin-left: 10%" src="http://mconstruction.co.in/assets/global/img/logo.jpg" height="75px" width="120px">
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
<div>
    <table width="100%" border="1">
        <tr>
            <td style="text-align: right; padding: 5px;font-weight: bolder" colspan="3">Summary No : {!! $summary_no !!}</td>
        </tr>
        <tr>
            <td colspan="3">
                <table width="100%" style="text-align: center; ">

                    <tr>
                        <td><b>APPROX QUOTATION FOR  PROJECT {!! strtoupper($project_site['name']) !!} AT</b></td>
                    </tr>
                    <tr>
                        <td><b>{!! strtoupper($project_site['address']) !!}</b></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="text-align: center">
            <td colspan="3"><b>SUMMARY</b></td>
        </tr>
        <tr style="text-align: center">
            <td colspan="3"><b>(SLAB AREA CONSIDERED = {!! $quotation['built_up_area'] !!} SQFT)</b></td>
        </tr>
        <tr style="text-align: center">
            <td style="width: 10%; "><b>Sr.no</b></td>
            <td style="width: 50%"><b>Description</b></td>
            <td style="width: 40%"><b>Rate / SFT</b></td>
        </tr>
        @for($iterator = 0 ; $iterator < count($summaryData) ; $iterator++)
        <tr >
            <td style="text-align: center">{!! $iterator + 1 !!}</td>
            <td style="text-align: left; padding-left: 10px">{!! $summaryData[$iterator]['description'] !!}</td>
            <td style="text-align: center">{!! $summaryData[$iterator]['rate_per_sft'] !!}</td>
        </tr>
        @endfor
        @if(count($summaryData) < 15)
            @for($iterator = 0 ; $iterator < (15 - count($summaryData)) ; $iterator++)
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            @endfor
        @endif
        <tr>
            <td></td>
            <td style="text-align: center"><b>TOTAL RATE PER Sqft. BUILTUP AREA</b></td>
            <td style="text-align: center"><b>{!! $total['rate_per_sft'] !!}</b> </td>
        </tr>
    </table>
</div>
<br>
<br>

<div style="font-size: 14px;">
    <p><u>Note :-</u></p>
    <ul>
        <li>We have assumed 3.7kg of steel per B/UP area and concrete 0.035cu.m per B/UP area</li>
        <li>Above rate excluding all Govt. taxes.( as todays date)</li>
        <li>Basic rates are including all govt. tax and transportation charges ( it’s a onsite delivery rate)</li>
        <li>All rates are on basis of basic rates hence variation of material rates will be varaiation in rates of items</li>
        <li>Electric supply and Water supply are included in the above rate</li>
        <li>Labour hutment area shall be provided by client.</li>
    </ul>
</div>

</body>
</html>