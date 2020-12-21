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
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <p style="text-align: center"><b>Value Sheet</b></p>
    <table border="1" width="100%" style="font-size: 12px;">
        @foreach($value_sheet as $key => $data)
        <tr>
            @foreach($data as $key => $value)
            <td style="text-align: center;height: 25px;">{!! $value !!}</td>
            @endforeach
        </tr>
        @endforeach
    </table>
    @if($set_drought_sheet)
    <p style="text-align: center"><b>Drought Sheet</b></p>
    <table border="1" width="100%" style="font-size: 12px;">
        <tr>
            @foreach($drought_titles as $title)
            <td style="width: 50%;text-align: center">{!! $title !!}</td>
            @endforeach
        </tr>
        @foreach($drought_sheet as $key => $data)
        <tr>
            @foreach($data as $key => $value)
            <td style="text-align: center;height: 25px;">{!! $value !!}</td>
            @endforeach
        </tr>
        @endforeach
    </table>
    @endif
    @if($set_vci_sheet)
    <p style="text-align: center"><b>VCI Sheet</b></p>
    <table border="1" width="100%" style="font-size: 12px;">
        <tr>
            @foreach($vci_titles as $title)
            <td style="width: 50%;text-align: center">{!! $title !!}</td>
            @endforeach
        </tr>
        @foreach($vci_sheet as $key => $data)
        <tr>
            @foreach($data as $key => $value)
            <td style="text-align: center;height: 25px;">{!! $value !!}</td>
            @endforeach
        </tr>
        @endforeach
    </table>
    @endif
</body>

</html>