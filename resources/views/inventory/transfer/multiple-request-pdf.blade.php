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
        <td style="width: 10%">
            <img style="margin-left: 30%" src="http://mconstruction.co.in/assets/global/img/logo.jpg" height="90px" width="160px">
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
                    {!! $data['project_site_to'] !!},
                </div>
                <div>
                    {!! $data['project_site_to_address'] !!}
                </div>
            </div>
        </td>
        <td style="width: 50%;padding-left: 1%; padding-top: 0.5%; padding-bottom: 1%" >
            <span><b>Supplier : </b></span>
            <div style="margin-left: 12%;font-size: 14px;">
                <div>
                    {!! $data['project_site_from'] !!} ,
                </div>
                <div>
                    {!! $data['project_site_from_address'] !!}
                </div>
            </div>
        </td>
    </tr>
    @foreach($data['data'] as $value)
    <tr>
        <td style="width: 50%; padding-left: 5%;" ><b>Component Name</b></td>
        <td style="text-align: left; padding-left: 5%;">{!! $value['component_name'] !!}</td>
    </tr>
    <tr>
        <td style="width: 50%;padding-left: 5%;"><b>GRN</b></td>
        <td style="text-align: left;padding-left: 5%;">{!! $value['grn'] !!}</td>
    </tr>
    <tr>
        <td style="width: 50%;padding-left: 5%;"><b>Quantity</b></td>
        <td style="text-align: left;padding-left: 5%;">{!! $value['quantity'] !!}</td>
    </tr>
    <tr>
        <td style="width: 50%;padding-left: 5%;"><b>Unit</b></td>
        <td style="text-align: left;padding-left: 5%;">{!! $value['unit'] !!}</td>
    </tr>
    @if($value['is_material'] == true)
        <tr>
            <td style="padding-left: 5%;width: 50%;"><b>Rate</b></td>
            <td style="padding-left: 5%;text-align: left;">{!! $value['rate_per_unit'] !!}</td>
        </tr>
        <tr>
            <td style="padding-left: 5%;width: 50%;"><b>CGST ({!! $value['cgst_percentage'] !!} %)</b></td>
            <td style="padding-left: 5%;text-align: left;">{!! $value['cgst_amount'] !!}</td>
        </tr>
        <tr>
            <td style="padding-left: 5%;width: 50%;"><b>SGST ({!! $value['sgst_percentage'] !!} %)</b></td>
            <td style="padding-left: 5%;text-align: left;">{!! $value['sgst_amount'] !!}</td>
        </tr>
        <tr>
            <td style="padding-left: 5%;width: 50%;"><b>IGST ({!! $value['igst_percentage'] !!} %)</b></td>
            <td style="padding-left: 5%;text-align: left;">{!! $value['igst_amount'] !!}</td>
        </tr>
        <tr>
            <td style="padding-left: 5%;width: 50%;"><b>Total Amount</b></td>
            <td style="padding-left: 5%;text-align: left;">{!! $value['total'] !!}</td>
        </tr>
    @else
        <tr>
            <td style="padding-left: 5%;width: 50%;"><b>Rent Per Day</b></td>
            <td style="padding-left: 5%;text-align: left;">{!! $value['rate_per_unit'] !!}</td>
        </tr>
    @endif
    <tr>
        <td style="padding-left: 5%;width: 50%;"><b>Transportation Amount</b></td>
        <td style="padding-left: 5%;text-align: left;">{!! $value['transportation_amount'] !!}</td>
    </tr>
    <tr>
        <td style="padding-left: 5%;width: 50%;"><b>Transportation CGST ({!! $value['transportation_cgst_percent'] !!} %)</b></td>
        <td style="padding-left: 5%;text-align: left;">{!! $value['transportation_cgst_amount'] !!}</td>
    </tr>
    <tr>
        <td style="padding-left: 5%;width: 50%;"><b>Transportation SGST ({!! $value['transportation_sgst_percent'] !!} %)</b></td>
        <td style="padding-left: 5%;text-align: left;">{!! $value['transportation_sgst_amount'] !!}</td>
    </tr>
    <tr>
        <td style="padding-left: 5%;width: 50%;"><b>Transportation IGST ({!! $value['transportation_igst_percent'] !!} %)</b></td>
        <td style="padding-left: 5%;text-align: left;">{!! $value['transportation_igst_amount'] !!}</td>
    </tr>
        <tr>
        <td style="padding-left: 5%;width: 50%;"><b>Driver Name</b></td>
        <td style="padding-left: 5%;text-align: left;">{!! $value['driver_name'] !!}</td>
    </tr>
    <tr>
        <td style="padding-left: 5%;width: 50%;"><b>Company Name</b></td>
        <td style="padding-left: 5%;text-align: left;">{!! $value['company_name'] !!}</td>
    </tr>
    <tr>
        <td style="padding-left: 5%;width: 50%;"><b>Mobile no</b></td>
        <td style="padding-left: 5%;text-align: left;">{!! $value['mobile'] !!}</td>
    </tr>
    <tr>
        <td style="padding-left: 5%;width: 50%;"><b>Vehicle number</b></td>
        <td style="padding-left: 5%;text-align: left;">{!! $value['vehicle_number'] !!}</td>
    </tr>
    <tr>
        <td style="padding-left: 5%;width: 50%;"><b>Transaction Date</b></td>
        <td style="padding-left: 5%;text-align: left;">{!! $value['created_at'] !!}</td>
    </tr>
    @endforeach
</table>
</body>
</html>
