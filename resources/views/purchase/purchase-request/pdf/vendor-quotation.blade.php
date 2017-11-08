<html>
    <head>
        <style>
            #mainTable,#itemTable {
                border-collapse: collapse;
            }
            #mainTable{
                margin: 2% 10% 10%;
                width: 840px;
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
        <span style="text-align: center"></span>
        <table border="1" id="mainTable">
            <tr style="height: 100px">
                <td style="width: 50%;padding-left: 1%; padding-top: 0.5%; padding-bottom: 1%" >
                    <span><b>Invoice To :</b></span>
                    <div style="margin-left: 2%;">
                        <table id="innerTable" border="0" style="border: 0px solid black !important;">
                            <tr style="border: 0px solid black !important;">
                                <td style="border: 0px solid black !important;">
                                    <img height="50" width="100" src="http://mconstruction.co.in/assets/global/img/logo.jpg">
                                </td>
                                <td style="border: 0px solid black !important;">
                                    <div>
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
                                </td>
                            </tr>
                        </table>
                        
                    </div>
                </td>
                <td style="width: 50%;padding-left: 1%; padding-top: 0.5%; padding-bottom: 1%" >
                    <span><b>Destination</b></span>
                    <div style="margin-left: 2%;">
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
                    <span><b>Supplier</b></span>
                    <div style="margin-left: 2%;">
                        <div>
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
                    <span><b>Terms of Delivery</b></span>
                    <div style="margin-left: 2%;">
                        <div>
                            {{$projectSiteInfo['project_name']}}
                        </div>
                        <div>
                            {{$projectSiteInfo['project_site_name']}}
                        </div>
                        <div>
                            {{$projectSiteInfo['project_site_address']}}
                        </div>
                        <div>
                            {{$projectSiteInfo['project_site_city']}}
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table id="itemTable">
                        <tr style="text-align: center">
                            <th style="width: 8px">
                                Sr.no.
                            </th>
                            <th style="width: 450px">
                                Item Name - Description
                            </th>
                            @if(isset($pdfFlag) && $pdfFlag == 'after-purchase-order-create')
                                <th>
                                    GST
                                </th>
                                <th>
                                    HSN Code
                                </th>
                                <th>
                                    Rate
                                </th>
                            @endif
                            <th>
                                Quantity
                            </th>
                            <th>
                                Unit
                            </th>
                            @if(isset($pdfFlag) && $pdfFlag == 'after-purchase-order-create')
                                <th>
                                    Amount
                                </th>
                            @endif
                        </tr>
                        @for($iterator = 0 ; $iterator < count($vendorInfo['materials']); $iterator++)
                            <tr style="text-align: center">
                                <td>
                                    {!! $iterator + 1 !!}
                                </td>
                                <td>
                                    {{$vendorInfo['materials'][$iterator]['item_name']}}
                                </td>
                                @if(isset($pdfFlag) && $pdfFlag == 'after-purchase-order-create')
                                    <td>
                                        {{$vendorInfo['materials'][$iterator]['gst']}}
                                    </td>
                                    <td>
                                        {{$vendorInfo['materials'][$iterator]['hsn_code']}}
                                    </td>
                                    <td>
                                        {{$vendorInfo['materials'][$iterator]['rate']}}
                                    </td>
                                @endif
                                <td>
                                    {{$vendorInfo['materials'][$iterator]['quantity']}}
                                </td>
                                <td>
                                    {{$vendorInfo['materials'][$iterator]['unit']}}
                                </td>
                                @if(isset($pdfFlag) && $pdfFlag == 'after-purchase-order-create')
                                    <td>
                                        {!! $vendorInfo['materials'][$iterator]['rate']*$vendorInfo['materials'][$iterator]['quantity'] !!}
                                    </td>
                                @endif
                            </tr>
                        @endfor
                        @for($i = 0;$i < (15-(count($vendorInfo['materials'])));$i++)
                            <tr style="text-align: center">
                                <td>

                                </td>
                                <td>

                                </td>
                                @if(isset($pdfFlag) && $pdfFlag == 'after-purchase-order-create')
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                @endif
                                <td>

                                </td>
                                <td>

                                </td>
                                @if(isset($pdfFlag) && $pdfFlag == 'after-purchase-order-create')
                                    <td>

                                    </td>
                                @endif
                            </tr>
                        @endfor
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>