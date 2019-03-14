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
        <br/>
        <fieldset>
            <legend>Remark :</legend>
            <span style="font-weight: bolder;font-size: 12px;">{{array_key_exists("por_remarks",$projectSiteInfo) ? $projectSiteInfo['por_remarks'] : "-"}}</span>
        </fieldset>
    </body>
</html>