<html>
<head>
    <style>
        table {
            border-collapse: collapse;
        }
    </style>
</head>
<body>
<table border="1" style="padding-top: 20px; padding-bottom:20px " width="100%">
    <tr><td>
            <table width="100%" style="text-align: center; ">
                <tr>
                    <td style="font-size: 30px"><i>Manisha Construction</i></td>
                </tr>
                <tr>
                    <td style="font-size: 20px"><i>CIVIL CONTRACTOR</i></td>
                </tr>
                <tr>
                    <td style="font-size: 15px"><i>SIDDHI TOWER ABOVE RUPEE BANK, KONDHWA,PUNE - 411048</i></td>
                </tr>
                <tr>
                    <td style="font-size: 15px">Ph 26831325 /26</td>
                </tr>
            </table>
            <hr>
            <table width="100%" style="text-align: center" border="1">
                <tr>
                    <td style="background-color: #808080">BILL OF QUANTITIES</td>
                </tr>
            </table>
            <br>
            <div>
                <table width="100%" border="1">
                    <tr style="text-align: center">
                        <th style="width: 5%; "><b>Sr.no</b></th>
                        <th style="width: 25%"><b>Description</b></th>
                        <th style="width: 20%"><b>Qty</b></th>
                        <th style="width: 10%"><b>Unit</b></th>
                        <th style="width: 15%"><b>Rate</b></th>
                        <th style="width: 15%"><b>Amt</b></th>
                    </tr>
                    @for($iterator = 0 ; $iterator < 5 ; $iterator++)
                        <tr >
                            <td style="text-align: center">{!! $iterator + 1 !!}</td>
                            <td style="text-align: left; padding-left: 10px">mkgrgbrd</td>
                            <td style="text-align: right; padding-right: 10px">3.4</td>
                            <td style="text-align: right; padding-right: 10px">KG</td>
                            <td style="text-align: right; padding-right: 10px">123.67</td>
                            <td style="text-align: right; padding-right: 10px">123456</td>
                        </tr>
                    @endfor
                    <tr>
                        <td colspan="5" style="text-align: right; padding-right:10px;background-color: #808080 ">Total=</td>
                        <td style="background-color: #808080">1234567</td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: right; padding-right:10px;background-color: #808080 ">Say=</td>
                        <td style="background-color: #808080">1234565</td>
                    </tr>
                    <tr>
                        <td colspan="6" style="background-color: #808080"><b>Rs. Three Lakh Fifty Thousand Two Hundred And Six Only.</b></td>
                    </tr>
                </table>
            </div>
            <br>
            <table style="font-size:15px" width="100%" border="1">
                <tr>
                    <th width="65%" colspan="2" style="background-color: #aaa7a7; padding-left: 10px"><b>For Company PVT. LTD.</b></th>
                    <th width="35%" style="background-color: #aaa7a7; text-align: right; padding-right: 10px"><b>For Manisha Construction</b></th>
                </tr>
                <tr >
                    <td width="32.5%" style="padding-top: 80px"><b>Head-Engineering</b></td>
                    <td width="32.5%" style="padding-top: 80px; text-align: center"><b>Authorised signatory</b></td>
                    <td width="32.5%" style="padding-top: 80px; text-align: right; padding-right:10px "><b>Suresh Vaghela</b></td>
                </tr>
            </table>
        </td></tr>
    <tr><td style="padding-top: 80px"></td></tr>
</table>
</body>
</html>
