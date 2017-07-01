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
            <hr>
            <p style="text-align: center">BASIC MATERIAL RATES</p>
        </td></tr>
</table>
<div>
@if ($materialData != NULL)
    @foreach ($materialData as $key => $category)
    <br>
    <table width="80%" border="1" align="center">
        <tr>
            <td colspan="3" style="text-align: center">Category Name : {{$key}}  </td>
        </tr>
        <tr>
            <th>Material Name</th>
            <th>Rate</th>
            <th>Unit</th>
        </tr>
        @foreach ($category as $material)
            <tr>
                <td>{{$material['material_name']}}</td>
                <td>{{$material['rate']}}</td>
                <td>{{$material['unit_name']}}</td>
            </tr>
        @endforeach
    </table>
    <br>
    @endforeach
@endif
</div>
</body>
</html>