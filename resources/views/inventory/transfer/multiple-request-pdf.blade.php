<html>
<head>
    <style>
        table {
            border-collapse: collapse;
        }
        .text_alignment{
            text-align: center;
        }
        .text_bold{
        font-weight: bold;
        }
    </style>
</head>
<body>
<table style="border-left: 1px solid black; border-right: 1px solid black; border-top: 1px solid black" width="100%">
    <tr>
        <td style="width: 10%">
            <img style="margin-left: 30%" src="https://mconstruction.co.in/assets/global/img/logo.jpg" height="90px" width="160px">
        </td>
        <td style="width: 80%">
            <table style="padding-top: 2px; padding-bottom:2px;" width="100%">
                <tr>
                    <td>
                        <table width="100%" style="text-align: center; ">
                            <tr>
                                <td style="font-size: 20px"><i>{!! env('COMPANY_NAME') !!}</i></td>
                            </tr>
                            <tr>
                                <td style="font-size: 14px"><i>{!! env('DESIGNATION') !!}</i></td>
                            </tr>
                            <tr>
                                <td style="font-size: 12px"><i>{!! env('ADDRESS') !!}</i></td>
                            </tr>
                            <tr>
                                <td style="font-size: 12px">{!! env('CONTACT_NO') !!}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 12px">{!! env('GSTIN_NUMBER') !!}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table width="100%" style="font-size: 15px;" border="1">
    <tr  style="height: 100px">
        <td style="width: 50%;padding-left: 1%; padding-top: 0.5%; padding-bottom: 1%" >
            <span><b>Destination : </b></span>
            <div style="margin-left: 12%;font-size: 14px;">
                <div>
                    {!! $project_site_to !!},
                </div>
                <div>
                    {!! $project_site_to_address !!}
                </div>
            </div>
        </td>
        <td style="width: 50%;padding-left: 1%; padding-top: 0.5%; padding-bottom: 1%" >
            <span><b>Supplier : </b></span>
            <div style="margin-left: 12%;font-size: 14px;">
                <div>
                    {!! $project_site_from !!} ,
                </div>
                <div>
                    {!! $project_site_from_address !!}
                </div>
            </div>
        </td>
    </tr>
</table>
<br>
<table width="100%" style="font-size: 9px;" border="1">
    <tr style="height: 100px">
        <td class="text_alignment"><b>Component Name</b></td>
        <td class="text_alignment"><b>GRN</b></td>
        <td class="text_alignment"><b>Quantity</b></td>
        <td class="text_alignment"><b>Unit</b></td>
        <td class="text_alignment"><b>Rate / Rent per day</b></td>
        <td class="text_alignment"><b>CGST %)</b></td>
        <td class="text_alignment"><b>SGST %)</b></td>
        <td class="text_alignment"><b>IGST %)</b></td>
        <td class="text_alignment"><b>Total Amount</b></td>
        <td class="text_alignment"><b>Transportation Amount</b></td>
        <td class="text_alignment"><b>Transportation CGST %)</b></td>
        <td class="text_alignment"><b>Transportation SGST %)</b></td>
        <td class="text_alignment"><b>Transportation IGST %)</b></td>
        <td class="text_alignment"><b>Driver Name</b></td>
        <td class="text_alignment"><b>Company Name</b></td>
        <td class="text_alignment"><b>Mobile no</b></td>
        <td class="text_alignment"><b>Vehicle number</b></td>
        <td class="text_alignment"><b>Transaction Date</b></td>
    </tr>
    @foreach($data1 as $key => $value)
    <tr>
        <td class="text_alignment text_bold">{!! $value['component_name'] !!}</td>
        <td class="text_alignment text_bold">{!! $value['grn'] !!}</td>
        <td class="text_alignment text_bold">{!! $value['quantity'] !!}</td>
        <td class="text_alignment">{!! $value['unit'] !!}</td>
    @if($value['is_material'] == true)
        <td class="text_alignment text_bold">{!! $value['rate_per_unit'] !!}</td>
        <td class="text_alignment">{!! $value['cgst_amount'] !!}</td>
        <td class="text_alignment">{!! $value['sgst_amount'] !!}</td>
        <td class="text_alignment">{!! $value['igst_amount'] !!}</td>
        <td class="text_alignment">{!! $value['total'] !!}</td>
    @else
        <td class="text_alignment text_bold">{!! $value['rate_per_unit'] !!}</td>
        <td class="text_alignment">-</td>
        <td class="text_alignment">-</td>
        <td class="text_alignment">-</td>
        <td class="text_alignment">-</td>
    @endif
        <td class="text_alignment">{!! $value['transportation_amount'] !!}</td>
        <td class="text_alignment">{!! $value['transportation_cgst_amount'] !!}</td>
        <td class="text_alignment">{!! $value['transportation_sgst_amount'] !!}</td>
        <td class="text_alignment">{!! $value['transportation_igst_amount'] !!}</td>
        <td class="text_alignment">{!! $value['driver_name'] !!}</td>
        <td class="text_alignment">{!! $value['company_name'] !!}</td>
        <td class="text_alignment">{!! $value['mobile'] !!}</td>
        <td class="text_alignment">{!! $value['vehicle_number'] !!}</td>
        <td class="text_alignment">{!! $value['created_at'] !!}</td>
    </tr>
    @endforeach
</table>
</body>
</html>
