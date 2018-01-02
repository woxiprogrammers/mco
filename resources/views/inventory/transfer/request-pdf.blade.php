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
            <tr>
                <td style="width: 50%;"><b>Component Name</b></td>
                <td style="text-align: left;">{!! $component_name !!}</td>

            </tr>
            <tr>
                <td style="width: 50%;"><b>Quantity</b></td>
                <td style="text-align: left;">{!! $quantity !!}</td>
            </tr>
            <tr>
                <td style="width: 50%;"><b>Unit</b></td>
                <td style="text-align: left;">{!! $unit !!}</td>
            </tr>
            @if($is_material == true)
                <tr>
                    <td style="width: 50%;"><b>Rate</b></td>
                    <td style="text-align: left;">{!! $rate !!}</td>
                </tr>
                <tr>
                    <td style="width: 50%;"><b>Tax</b></td>
                    <td style="text-align: left;">{!! $tax !!}</td>
                </tr>
                <tr>
                    <td style="width: 50%;"><b>Total Amount</b></td>
                <td style="text-align: left;">{!! $total_amount !!}</td>
                </tr>
            @else
                <tr>
                    <td style="width: 50%;"><b>Rent Per Day</b></td>
                    <td style="text-align: left;">{!! $rent !!}</td>
                </tr>
            @endif
        </table>
</body>
</html>
