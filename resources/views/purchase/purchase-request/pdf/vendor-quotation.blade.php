<html>
    <head>
        <style>
            #mainTable,#itemTable {
                border-collapse: collapse;
            }
            #mainTable{
                margin: 2% 8% 0% 0%;
                width: 782px;
            }
            #itemTable{
                margin-top: 0.5%;
                width: 100%;
            }

            #mainTable,#mainTable td:not('#innerTable td'),#mainTable th:not('#innerTable th') {
                border: 1px solid black;
            }
            #itemTable,#itemTable td,#itemTable th {
                border: 1px solid black;
            }
            #itemTable td,#itemTable th{
                height: 30px;
            }
        </style>
    </head>
    <body>
    @php
        $totalQuantity = $totalRate = $grandTotal = 0;
        $totalSubtotal =  $totalCGSTAmount = $totalSGSTAmount = $totalIGSTAmount = 0;
    @endphp
        <span style="text-align: center; margin-left: 35%; font-size: 19px; font-weight: bold">{{$pdfTitle}}({{$formatId}})</span>
        <table border="1" id="mainTable">
            <tr style="height: 100px">
                <td style="width: 50%;padding-left: 1%; padding-top: 0.5%; padding-bottom: 1%" >
                    <span><b>Invoice To :</b></span>
                    <div style="margin-left: 2%;">
                        <table id="innerTable" border="0" style="border: 0px solid black !important;font-size: 12px;">
                            <tr style="border: 0px solid black !important;">
                                <td style="border: 0px solid black !important;">
                                    <img height="50px" width="100px" src="http://mconstruction.co.in/assets/global/img/logo.jpg">
                                </td>
                                <td style="border: 0px solid black !important;">
                                    <div style="font-weight: bold;font-size: 14px;">
                                        {!! env('COMPANY_NAME') !!}
                                    </div>
                                    <div>
                                        {!! env('DESIGNATION') !!}
                                    </div>
                                    <div>
                                        {!! env('ADDRESS') !!}
                                    </div>
                                    <div>
                                        {!! env('CONTACT_NO') !!}
                                    </div>
                                    <div>
                                        {!! env('GSTIN_NUMBER') !!}
                                    </div>
                                </td>
                            </tr>
                        </table>
                        
                    </div>
                </td>
                <td style="width: 50%;padding-top: 0px;" >
                    <span><b>Destination : </b></span>
                    <div style="margin-left: 2%;font-size: 12px;">
                        <div>
                            {{$projectSiteInfo['project_site_address']}}
                        </div>
                        <div>

                        </div>
                        <div>

                        </div>
                        <div>

                        </div>
                    </div>

                </td>
            </tr>
            <tr  style="height: 100px">
                <td style="width: 50%;padding-left: 1%; padding-top: 0.5%; padding-bottom: 1%" >
                    <span><b>Supplier : </b></span>
                    <div style="margin-left: 2%;font-size: 12px;">
                        <div style="font-weight: bold;font-size: 14px;">
                            {{$vendorInfo['company']}}
                        </div>
                        <div>
                            Contact: {{$vendorInfo['mobile']}}
                        </div>
                        <div>
                            Email: {{$vendorInfo['email']}}
                        </div>
                        <div>
                            GSTIN: {{$vendorInfo['gstin']}}
                        </div>
                    </div>
                </td>
                <td style="width: 50%;padding-left: 1%; padding-top: 0.5%; padding-bottom: 1%" >
                    <span><b>Terms of Delivery :</b></span>
                    <div style="margin-left: 2%;font-size: 12px;">
                        <div>
                            {{$projectSiteInfo['delivery_address']}}
                        </div>
                        <div>

                        </div>
                        <div>

                        </div>
                        <div>

                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <table id="itemTable" style="font-size: 12px;">
            <tr style="text-align: center">
                <th style="width: 8px">
                    Sr.no.
                </th>
                @if(isset($pdfFlag) && ($pdfFlag == 'after-purchase-order-create' || $pdfFlag == 'purchase-order-listing-download'))
                    <th style="width: 150px;">
                        Item Name - Description
                    </th>
                    <th>
                        Quantity
                    </th>
                    <th>
                        Unit
                    </th>
                    <th>
                        Rate
                    </th>
                    <th>
                        Subtotal
                    </th>
                    <th>
                        CGST
                    </th>
                    <th>
                        SGST
                    </th>
                    <th>
                        IGST
                    </th>
                    <th>
                        Total
                    </th>
                @else
                    <th style="width: 380px">
                        Item Name - Description
                    </th>
                    <th style="width: 80px">
                        HSN code
                    </th>
                    <th>
                        Quantity
                    </th>
                    <th>
                        Unit
                    </th>
                @endif
            </tr>
            @for($iterator = 0 ; $iterator < count($vendorInfo['materials']); $iterator++)
                <tr style="text-align: center">
                    <td>
                        {!! $iterator + 1 !!}
                    </td>
                    @if(isset($pdfFlag) && ($pdfFlag == 'after-purchase-order-create' || $pdfFlag == 'purchase-order-listing-download'))
                        <td style="text-align: left;padding-left: 5px" >
                            {{ucwords($vendorInfo['materials'][$iterator]['item_name'])}}
                            <br>
                            <span style="font-size: 12px"> {{$vendorInfo['materials'][$iterator]['due_date']}} </span>
                        </td>
                        <td>
                            {{$vendorInfo['materials'][$iterator]['quantity']}}
                            @php
                                $totalQuantity += $vendorInfo['materials'][$iterator]['quantity'];
                            @endphp
                        </td>
                        <td>
                            {{$vendorInfo['materials'][$iterator]['unit']}}
                        </td>
                        <td>
                            {{$vendorInfo['materials'][$iterator]['rate']}}
                            @php
                                $totalRate += $vendorInfo['materials'][$iterator]['rate'];
                            @endphp
                        </td>
                        <td>
                            {{$vendorInfo['materials'][$iterator]['subtotal']}}
                            @php
                                $totalSubtotal += $vendorInfo['materials'][$iterator]['subtotal'];
                            @endphp
                        </td>
                        <td>
                            {{$vendorInfo['materials'][$iterator]['cgst_amount']}}({{$vendorInfo['materials'][$iterator]['cgst_percentage']}}%)
                            @php
                                $totalCGSTAmount += $vendorInfo['materials'][$iterator]['cgst_amount'];
                            @endphp
                        </td>
                        <td>
                            {{$vendorInfo['materials'][$iterator]['sgst_amount']}}({{$vendorInfo['materials'][$iterator]['sgst_percentage']}}%)
                            @php
                                $totalSGSTAmount += $vendorInfo['materials'][$iterator]['sgst_amount'];
                            @endphp
                        </td>
                        <td>
                            {{$vendorInfo['materials'][$iterator]['igst_amount']}}({{$vendorInfo['materials'][$iterator]['igst_percentage']}}%)
                            @php
                                $totalIGSTAmount += $vendorInfo['materials'][$iterator]['igst_amount'];
                            @endphp
                        </td>
                        <td>
                            {{$vendorInfo['materials'][$iterator]['total']}}
                            @php
                                $grandTotal += $vendorInfo['materials'][$iterator]['total'];
                            @endphp
                        </td>
                    @else
                        <td style="text-align: left;padding-left: 5px" >
                            {{$vendorInfo['materials'][$iterator]['item_name']}}
                        </td>
                        <td>

                        </td>
                        <td>
                            {{$vendorInfo['materials'][$iterator]['quantity']}}
                            @php
                                $totalQuantity += $vendorInfo['materials'][$iterator]['quantity'];
                            @endphp
                        </td>
                        <td>
                            {{$vendorInfo['materials'][$iterator]['unit']}}
                        </td>
                    @endif
                </tr>
                @if(isset($pdfFlag) && ($pdfFlag == 'after-purchase-order-create' || $pdfFlag == 'purchase-order-listing-download'))
                    <tr style="text-align: center">
                        <td>

                        </td>
                        <td>
                            Transportation
                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>
                            {{$vendorInfo['materials'][$iterator]['transportation_amount']}}
                            @php
                                $totalSubtotal += $vendorInfo['materials'][$iterator]['transportation_amount'];
                            @endphp
                        </td>
                        <td>
                            {{$vendorInfo['materials'][$iterator]['transportation_cgst_amount']}} ({{$vendorInfo['materials'][$iterator]['transportation_cgst_percentage']}}%)
                            @php
                                $totalCGSTAmount += $vendorInfo['materials'][$iterator]['transportation_cgst_amount'];
                            @endphp
                        </td>
                        <td>
                            {{$vendorInfo['materials'][$iterator]['transportation_sgst_amount']}} ({{$vendorInfo['materials'][$iterator]['transportation_sgst_percentage']}}%)
                            @php
                                $totalSGSTAmount += $vendorInfo['materials'][$iterator]['transportation_sgst_amount'];
                            @endphp
                        </td>
                        <td>
                            {{$vendorInfo['materials'][$iterator]['transportation_igst_amount']}} ({{$vendorInfo['materials'][$iterator]['transportation_igst_percentage']}}%)
                            @php
                                $totalIGSTAmount += $vendorInfo['materials'][$iterator]['transportation_igst_amount'];
                            @endphp
                        </td>
                        <td>
                            {{$vendorInfo['materials'][$iterator]['transportation_total_amount']}}
                            @php
                                $grandTotal += $vendorInfo['materials'][$iterator]['transportation_total_amount'];
                            @endphp
                        </td>
                    </tr>
                @endif
            @endfor
            @for($i = 0;$i < (11-(count($vendorInfo['materials'])));$i++)
                <tr style="text-align: center">
                    <td>
                    </td>
                    @if(isset($pdfFlag) && ($pdfFlag == 'after-purchase-order-create' || $pdfFlag == 'purchase-order-listing-download'))
                        <td style="text-align: left;padding-left: 5px" >

                        </td>

                        <td>

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                    @else
                        <td style="text-align: left;padding-left: 5px" >

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                    @endif
                </tr>
            @endfor
            <tr style="text-align: center">
                <td>
                </td>
                @if(isset($pdfFlag) && ($pdfFlag == 'after-purchase-order-create' || $pdfFlag == 'purchase-order-listing-download'))
                    <td style="text-align: left;padding-left: 5px" >
                        Total
                    </td>

                    <td>
                        {{$totalQuantity}}
                    </td>
                    <td>

                    </td>
                    <td>
                        {{$totalRate}}
                    </td>
                    <td>
                        {{$totalSubtotal}}
                    </td>
                    <td>
                        {{$totalCGSTAmount}}
                    </td>
                    <td>
                        {{$totalSGSTAmount}}
                    </td>
                    <td>
                        {{$totalIGSTAmount}}
                    </td>
                    <td>
                        {{$grandTotal}}
                    </td>
                @else
                    <td style="text-align: left;padding-left: 5px" >
                        Total
                    </td>
                    <td>

                    </td>
                    <td>
                        {{$totalQuantity}}
                    </td>
                    <td>

                    </td>
                @endif
            </tr>
        </table>
    </body>
</html>