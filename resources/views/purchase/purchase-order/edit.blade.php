@extends('layout.master')
@section('title','Constro | Manage Materials')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="page-wrapper-row full-height">
            <div class="page-wrapper-middle">
                <!-- BEGIN CONTAINER -->
                <div class="page-container">
                    <!-- BEGIN CONTENT -->
                    <div class="page-content-wrapper">
                        <div class="page-head">
                            <div class="container">
                                <!-- BEGIN PAGE TITLE -->
                                <div class="page-title">
                                    <h1>Edit Purchase Order</h1>
                                </div>
                                <div class="form-group " style="text-align: center">
                                    <a href="#" class="btn red pull-right margin-top-15">
                                        <i class="fa fa-check" style="font-size: large"></i>
                                        Submit
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <input type="hidden" id="po_id" value="{{$purchaseOrderList['purchase_order_id']}}">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN VALIDATION STATES-->
                                        <div class="portlet light ">
                                            <div class="portlet-body form">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                                <label style="color: darkblue;">Purchase Order Id</label>
                                                                <input type="text" class="form-control" name="po_id" value="{{$purchaseOrderList['purchase_order_format_id']}}" readonly tabindex="-1">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label style="color: darkblue;">Client Name</label>
                                                            <input type="text" class="form-control" name="client_name" value="{{$purchaseOrderList['client_name']}}" readonly tabindex="-1">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label style="color: darkblue;">Project Name</label>
                                                            <input type="text" class="form-control" name="project_name" value="{{$purchaseOrderList['project']}}" readonly tabindex="-1">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label style="color: darkblue;">Purchase Request</label>
                                                            <input type="text" class="form-control" name="client_name"  value="{{$purchaseOrderList['purchase_request_format_id']}}" readonly tabindex="-1">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label style="color: darkblue;">Vendor Name</label>
                                                            <input type="text" class="form-control" name="client_name"  value="{{$purchaseOrderList['vendor_name']}}" readonly tabindex="-1">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN VALIDATION STATES-->
                                        <div class="portlet light ">
                                            <div class="portlet-body">
                                                <div class="table-container">
                                                    <table class="table table-striped table-bordered table-hover order-column" id="purchaseRequest">
                                                        <thead>
                                                            <tr>
                                                                <th> Material Name </th>
                                                                <th> Quantity</th>
                                                                <th> Unit </th>
                                                                <th>Action</th>
                                                            </tr>
                                                            <tr class="filter">
                                                                <th><input type="text" class="form-control form-filter" name="search_name"></th>
                                                                <th><input type="text" class="form-control form-filter" name="search_name" > </th>
                                                                <th><input type="text" class="form-control form-filter" name="search_name"></th>
                                                                <th>
                                                                    <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                                    <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($materialList as $key => $materialData)
                                                                <tr>
                                                                    <td> {{$materialData['material_component_name']}} </td>
                                                                    <td>  {{$materialData['material_component_quantity']}} </td>
                                                                    <td> {{$materialData['material_component_unit_name']}} </td>
                                                                    <td><button class="image" value="{{$materialData['purchase_order_component_id']}}">View</button> <button class="transaction" value="{{$materialData['purchase_order_component_id']}}">
                                                                            <i class="fa fa-plus" style="font-size: large"></i>&nbsp;
                                                                            Transaction
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="modal fade" id="ImageUpload" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form class="modal-content">
                                                            <div class="modal-header" >
                                                                <div class="row">
                                                                    <div class="col-md-4"></div>
                                                                    <div class="col-md-4"> Material</div>
                                                                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form role="form" class="form-horizontal" method="post">
                                                                    <div class="form-body">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-12" style="text-align: right">
                                                                                <input type="text" class="form-control empty typeahead tt-input" id="material_name" placeholder="Enter material name" autocomplete="off" spellcheck="false" dir="auto" style="position: relative; vertical-align: top; background-color: transparent;" readonly>
                                                                                <br><input type="text" class="form-control empty typeahead tt-input" id="qty" placeholder="Enter Quantity" autocomplete="off" spellcheck="false" dir="auto" style="position: relative; vertical-align: top; background-color: transparent;" readonly>
                                                                                <br><input type="text" class="form-control empty typeahead tt-input" id="unit" placeholder="Enter Unit" autocomplete="off" spellcheck="false" dir="auto" style="position: relative; vertical-align: top; background-color: transparent;" readonly>
                                                                                <br><input type="hidden" class="form-control empty typeahead tt-input" id="searchbox" placeholder="Enter Rate" autocomplete="off" spellcheck="false" dir="auto" style="position: relative; vertical-align: top; background-color: transparent;" readonly>
                                                                                <br><input type="text" class="form-control empty typeahead tt-input" id="hsn_code" placeholder="Enter HSNCODE" autocomplete="off" spellcheck="false" dir="auto" style="position: relative; vertical-align: top; background-color: transparent;" >
                                                                               <br>
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-12">
                                                                                        Vendor Quotation Image
                                                                                        <div id="myCarousel" class="carousel slide" style="height: 150px" data-ride="carousel">
                                                                                            <!-- Indicators -->
                                                                                            <ol class="carousel-indicators">
                                                                                                <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                                                                                                <li data-target="#myCarousel" data-slide-to="1"></li>
                                                                                                <li data-target="#myCarousel" data-slide-to="2"></li>
                                                                                            </ol>

                                                                                            <!-- Wrapper for slides -->
                                                                                            <div class="carousel-inner">
                                                                                                <div class="item active">
                                                                                                    <img src="http://www.bollywoodlife.com/wp-content/uploads/2016/12/Shahrukh-2.jpg" alt="Los Angeles" style="width:100%;;height: 170px">
                                                                                                </div>

                                                                                                <div class="item">
                                                                                                    <img src="data:image/jpeg;base64,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" alt="Chicago" style="width:100%;height: 170px">
                                                                                                </div>

                                                                                                <div class="item">
                                                                                                    <img src="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxITEhUSEhIVFRUVFxUVFRUXFRUVFRUVFRUXFhUVFRcYHSggGBolGxUVITEhJSkrLi4uFx8zODMsNygtLisBCgoKDg0OGhAQGy0fHx0tLS0tLS0tLS0tLS0tLS0tLS8tLS0tLS0tLS0tLS0tKy0tLS0tLS0tLS0tLS0tLS0tLf/AABEIALEBHAMBIgACEQEDEQH/xAAcAAACAwEBAQEAAAAAAAAAAAADBAIFBgABBwj/xABCEAABAwIEAwUFBAoBAgcAAAABAAIRAyEEEjFBBVFhBiJxgZETMqGxwUJi0fAHFCMzUnKCkrLh8RWiFjRDY3Sz8v/EABkBAAMBAQEAAAAAAAAAAAAAAAECAwAEBf/EACURAAICAgICAgMAAwAAAAAAAAABAhEDMRIhBEFRYRMiMjPR8P/aAAwDAQACEQMRAD8A6qEElEquSlWpC4B6DBym1ySZWR2vWaFY0CvQgMeiylaEsYphTQWPRZQZgjUUMQAUxQdKARinTRfYpTFcSFEB1Sm4N/i1Z/c0HKf5svivR2gozDjlvlBOmbWHEWb6xzItL0/RVY5BH0kF9NWAc18lpzNEgwRaOaU4jVbSZmcHE6Na1pcXHYCNJJiTCR2Hg7oC5kCeQk9BzPIdTCz+N7UUmj9n3zeDcDyMfKdN0PjVarXblq1W0m6mi0Z3m8zUeDAPQTG4VM/CsYJ7xFhJkTfedZO/yVYQXsssSSOf2kxLj+9psHLKyfIwSfESjN4x/FiHuPjlF+UH6BU9Wi2SXE36gGeRn5JN7qWrgD6j5Gy6FFGNGOJxo+f6mu9ZR6XEw4XaD1ER5nQKhw2NwzR9noCX2PSZXuL4qHCA4dLkD0IEouJlIvBxmg0/vG72mR8OqJ/1ujtWZ4TBNtRPmsFicY4TMRzA+YSTzNyPPp4bhZY0Z5aPoVXtDQabPYT1BPkYCeods6os4hzbfamwiPeEj/lfNsIRID4yOsHmYaesbcx1kJ2rNN15ADoN/ddtcfZPPcSOaLxoT8p9g4NxWniBBcGuE62F4jXTQ30urTEYJ7LOF7+kwHA7jqvk3AuMEODXAZSBI/hcBqAfAeMRobfTuG8ZD3toCoTIBbOzyLtNiSA6CDEaHkozxfBkkx40RmNv9QNOqao04C72ce9YAATzsD3Z2M/mLTa+Tp6bLkbFkqJI9NqA0JhhgJbFGKaJCA169NVOhiVQquxLkWtXSD3SsKcVEMRKYlEDFgGCr14lVNbFyUHGYklJ5iulRBKRYMxCZp4hVbAm8G26zQllzRcmAUrRammtU2KTaUVrkJTCRmCFyawBkwkgFecCwwMh3u2kyQQZ1BFxogh4K3Re4CnTgtflk87SDqD8umvJYft/2eYyi51N0FoJYRJz0y6TTdE6OMg6AOPujS94hUpMIBqudF2tMkbg5XtEtOpmD8VjeM8XeA8NZDKma7pIGsOAGjrkkeBuqRekjvjjpfRlOEcZrUnRmLXiQdwY2cB4cuq0DONitarUew9IcD/KZEDrBWKxzXk5wDmaLySSBa5+7cEnaV7RxU62PrB19PD4rpcFLsTk0za/r1Np/ZubM2sRHUg6nbyQ6+PLgTVY1s6OZrPLLMC3gDKzlDFi0OJ8pbyi/u+a6rxAA+9fo6fkDCX8aC8jJVXQTfunYmZ5SQPkqzEYJhNqw84n0kfJFxnEHbG3g36n6KorVw7/APIHyP0VoojKQaphsu7Xf1XSxjYkdD9UAqKpRJyHcNU70O0NtJEbpr2OUi4gEw7UDmDzCrKfqFeYWgTDgZmN7O6mfdd1Ou6DCmK1KOU5h7joa9ovlJ3HS0j0VlgHsc2LOHuGTaIMMdymLO2cGmwlNYfhz3i4gwLGRMnQ3BGkz8di7h+zLi/NTDiDYtd3gfMBsbEWtAKRySGUG9GSqNIPdJBaRfQxbK6DpyI2Ww4LxEivSeHNZ3QcztomIjcCGgHcDqVYnsG98nNldFpv1h1rj881mauEq4eoWVRlcy2WJzCIBa61tfwskclLRRQcdn3LhGNGIYxxDnOyaw0DbQAS4bb29E2+lFjPgbxpf3ba6eHNYHsdx0sIJzGw7xdNgbB0CJ/DbVfSKXEM9MggERI/iAH2wYO07WIIk6LlnEacBMj82+ilmQmv15qYcucicXwhPxC8qOSNd6dBJVq8oLKyUqPlQY6CnoUtKVRMsqJCi9Msclo1HyaoF6ymiObdMUaa6mRYKnTTmFZCIxiapMSOQoei1MAITAiBSbMTa1SAXjUVgShRKky60nC6FhBjYTEE7SJk6aSN+hFHRCt6VIOaDlkiIIcWkbbG4vpB8FkXw/0L4mpQa406tMU3EWOUkHeAQBn5g2OggOsqniYpkFtPI0EwHus3MLODWkGXWNyALE3Qe0dFj2GnXLnht20g2KocLSwtaWtsdDHUAw5Yl/EatAubkqPpg90lrnEBpuHEADMIv4crKsY8tHc5cdifGqLabzkLqjryRfMSe8CBoOXTnMnP4hskxE8vlc7q9xXG6bmkMDhMyHX3JibH5i3pm8VVJuT4brpgn7IZHEK98CC7ysfiCgOqAbpUuUqbSTAVqIc/g9qEFQhOHBEJrDYCdAhzSD+NsrPZFGp4Nx2Wu4b2ac6JEBbDhfZdgi0nwU3mKxwHzXC8GcdAb+i1XAezrz3SLTt0X0PDcEY2LK0w+Ea3QAKbyNlVjiig4d2aAIJ20n82V9hOGtGydpsR2paGv4Oo4YclS9sOyNPGUoENqsk0n8idWn7p/A7K/ouunWiUyJSPzzh6lWg8seHBzCQR9ppBuQdbcwvonYnirag9m6Q6CadQXLSB7kfaBFwPugBD/Sh2cmMTTEHR8b8ifzyWK7O491Ou0EiZBBMxM79Of4oyVoC+D6s599fTSNi3kFwepcSAyseJGZt2OEOY6SHN0iAQfAzzSTKi8+SadMm3TGarrKsruKbq1bKurVEYitg3PQzUQ3vUMyqhRqjWT9OvZUbakFMsr21TUGzFzdM0Ck3PujUaisyI+03TdIpCmU7QUpCjLCiyhNRAVNmJtKZpJQJmmUAobpp/DVLQbg2INwRyIVaxyYpvTIax5+DcR+zfE/ZfcHwJDsup+zv5qm4j2cqvBhoadXMgFjoAuNmu2lobvNpVth66JxDjzaIg5nEtnu5QzcNDiSSL9NY5prOrFkk3Wz5j2j7MPpMdUqQ2ATBc6x1Ib3b6cyNbr5zXfJWz7adoMRinua8mATDALDUyd3QNz1KxJXVgTrsPkSV0jxNYAd4JVP8ADKcu6K0tHPDZeYfB5ytNwvhobshcLw4gQr/CsXHJnoQiNYSlAVthXpCiwJ+gAkKFpQBKOxhnolsPWCeZVaEyEdkw1EbCBUq2QaWObMEo3QvFssmQmKFcTEqlr8Ra0El3xWQ4v2rOjHQCYExMjWBuqR7JzpbPp2Kwra1NzHXDgROsdV8M4pwk0K8GJY+Dfa7j8APVb7gnaj2RBqODaZIn2jmtsZuzMQTtsqH9IFXD165dh69Kp7SmD+zqMcWva6DMGxLC0iRq0pmmTj2azjuIE5GEFnddGWHMe4Fz4dvJcSdpncFVHt4UuK0g15yHMyGhrhB0Y2xLbT8+qRL1xZFcuyEn2NOrSgVHKAevC9KkLyB1Cln1ExUck6ipENnGopNqlALVJjU5jMvClSKm5qkymqMQPScrPCaKvoslWOHbCnIDGQpgqAUlMBNpR6aAwIwWCgzSjNel2lECwRui+6Sf2W9s81cTiDGuVjcrGjQBs77bm6NTVjRbnbvImBrfKbgbm0DlOiaLfopjdM+W9vqzGN9hhqXsqAiTYmqRuXAm1hbwWAcvp36UcAKTWjNJOriZLnucXOAjYDKIiBA1svmJXZh/krm2jgrDhzrquTGFqQVSStE4Ps+hcDq21V/RqLM8DdDB1V7ScuGWz0IaLWnX5oreK0G6uE+IWN49xKpIo0GlzyJdAmB9Oc7Kh/VAfec57jqKbu6I51ND/SHDqqQx2rYmTLTpH0av2pw7ZGf4/JeUe22G0NT1Ov8AtfL8VUZTnuM5XzVD553FvwGiX/W2kGaTSBqQymI8YaFRYosi87R9ow3aJlQWcDyUsTipGZuvJfJeG8Q9mQWREA5czvqT9Fo+FY/HVZqYfDVKgb9v3mW+7lE+GZSlidnTjycloY4w+q95D6nsmj19J1/O4S/DnOFsPh3P/wDcc8NcTvlaY+gP8O6VdWdUbTe8lxqe1qVHHV1T9YrMjyaxltp6qyrmqxjcrsmYHSRcXALheYlU7SpHLkmlK2jR9muKMFYUyz2Tu7ma5oaZP2jA7w1uJBg3V7264ZUdhgGMZVLatOA6BaoTT97QAOcD5LI4XhdRxp5qxIDKVVjzLsrnznogkyNHSNCWAiJv9TxVL2uGfSF3ZCB/MBLD/cAlWx3VJo+VcF4G/CYnFU3xOSkAASRDnOLruAJyvpFocYkTIM2t3tTfFAf1gEkHNSqGQZzD2mGLDPhUPqgEKWTt9nPk7dixK8U3hRhTSJ0DKG9qJUC8AlOgpAg1Gp0DCJTpJ+lSELWMYjIptYmKdJMewCNkgVFkJlq7IuAQDQRrlNpQ2qbSg0Kw7URqE1yK1KAIxFahtCI0IDBGpnDOgpdoRmohRRfpD7OVa9E1GNLnNLiGg6tJLiRzIaB5TfY/FnNX6Xc51Si5jSQ6MzdNWx3b7HTbXVfAeP8ACn0qrmODpBvms6JtPXnc3XXhl6Ly/aKZSJjAsl4ChVZHlZO4LBvZlqPim03bm1ePuNF3eOnVWeicdm3wDAGibABLY3tE1oimC86SJyyf8vJDp4V9VsPdDTo0W8yf+Y5ojuHtaIYI66uPiTdctK+zuTddFTiuIB7gxpc1r2UzUZDgHVGh2eSbuEwYJgZjELm4kudAIDWXcTt90Dcr3HYcgCxlrtYsGv7pnzDPVJ08GQb2EyBr5qtpkeL7QxxOnmYX06Ya3NJJEk9RNmhVwpuYHjJnkZZuHAbkDnrY9FrOGva0XAP82noFajBmoB3coHJoaD5JlNI53hm+j51RnN7sDujlEkC8+K+4fotxBGFpNJ1bI8CSR8IXzvtDwttJktFy4H+0FwH92Qea23An+yFNjdGNa3+0AKWSdpM7fHxU2vosO13ZVgmtSYIzueW3yte8AVHADZ2Rpjnm/ihUNDDv0cyx5AEL6lRcKjIcLEQb/XY/JZLFYN9Bx9oJaDaq1vcI51Gj90eZ9zkROUZ/QEldMtcDSY3DNAEvMSTd0DQEnaIsLWVlw6rf0VJgsc0gEFpGzmkOEdCLFPUuISS2g0VamwB7jOtV+jRvGpiyydhlGkUvHQP1xzR/6dJrTyHtfZlrfECgD/UErCs+JcN9g+C7O+oBVq1IjPUdLXEDZoDGgDYABIOUsj/Y5JbEqjZUmsTQoqBppbQADmAqApopCiiA9p6p6m6yrQ9MU6lkGEz9NGUKQRFiRxKhmUnaJZ2qKGGgVIBDpozVmKydMJtjUKi1MtCUCJNaiALxqmFgnoC9XkrsyBhzBV8rgVRdv+yzK+XEMeym+Mrs0gOI0NgSTE89tpVk16z/AGyxL3PpUwTl9nmt1c4HTwHonhKjq8XE8uRQXsxeJ4Y6kC8U2mqD3XSDTiB+0ynVwiwP8UnRZXFufnLnklxuSTJJ8Vr8Y9w1JI+P+1WY7A5wSBLhe2/NdEMnyW8jxXjdFpwLGl1MXvEK5ovB2WQ7OYjL3TvcLUMMKc1TNjlaLVuCa9padHAtPg4Qfmq7CcI9oTTqWqs94aZ27VW82n4GQUxhcbBVpUq06jQHiS0y1wJa5h5sc0y0+BukTrpluN9oHg+C06ZnL5m/orVrmxA232SLA829qSOb2tcY8W5fiF5XptAl7nO+6Yy+jQJ85Wsbj9Ga49V9rWpx+7a4H+Yg6j7sgeNuS13DqgnS3NZjFYcucHnd7fTMPorXjXaehTblESLA6LNcugxfG2z6HwzFNygA+eyfxQc9gcy7mnSYkRsvkvCO1jTGkcxda13bD9XwznhuapUcynSB3Or3eDW/Egbpu0qZNxt3EcfhaD3FzqVNzpIdmpsLweRJErS8DYAAGwBsBYDwiypqZ9oQ5whzmsL7Ad7IJ/PQK94fRynw/wCSjDYmXpUJdq6MuYfukehB+qz5oLXdosOXNDgPdmecWkjwiVl6hUsqqRyCrhCE8L2u5BNVTQGePCBUXtSslalSVRCg3PumKdWyULV6wImoVpORcyTpuRCVqEoI9yDN1znobXXRGSGWlHplKsTVEIMzQ9RTDUrTKO1yQQO1dKHmUS9azBcyg+ohOqKBcgYIHpLjWFL2tqtu6kDI50zc/wBpk+BPJMFyJQqwVl0WwZnhyKa9GH4qQGgjeb+f+1X8CpH2rhMtLS6/MFo+q3HaHg1N9M1GgNEjO0aDNYPaNpNiNNI1WPdhHss2Q5oMGJBadfEK0ddHtucfJTlH/voSbwZ7axOjZkGfsm8ADy9Fdu0Q8PjA9omzhIjwMGOYEj1CMPz+fVO23s82uLoBpzR6b3Td1uiE4xrr4/H5Lw1PzzSspGRb0cUAPx/HdMUZfrpyVNhrm6vcI8DSPklfRVSshxSjNMgeX0XzzirS45XMOYE32X0+qQkMRwym4zlEpoSoTJ2qPn/BqTmVAQ2245hfbuzxoYmkyGABhDmtIDi0gEanXmslhuB0plzmsnTMQJnxWg4C2lhpAqZpn3TIjxVG0+yMYzro2r8OHQ77Q16hP4Rv0+HTwAVbw/FMfoSDyIgm022Nlb0QskTlJ6Z7jawY0vIkNBJH58VhapIW5xrZpvH3XfJYjEKeVWT9FfWqJKrWCnjDdVdZ5UlEVh6lZeByTD0xTcnoyGGhEZTUGFMMQHSMuHqZrpQvQn1E/EmkOmopscq0VU/hVmqDQ5TTlJLUgmaaRiyGWFFaUBiK0pBCcrxcvAUDHhC8UypBiYIEqDXph1JAfRIRGSGKNcXDhLXAtcObTYjx5HYgHZVtXhVai+JlpHdLhq0iWuHwttcbIlR0JoY3NTa0+9TzBsmA5pJOUk2aReJgRyRS6OrxsvB8Xpmb4rgS8TMObdp2B/DbzQzhKrBTNRsCowVGHUOaeR5gyCNQfJWuOFR/dDWsG7nPbEcwAS53g0E9FoajcOaFNgqZ6TWik8kQaVWXOZXy6hrpdI5AjUABo2dfkPH0o1Z8/wASEu111c8ZwLqbi1wgixCp8qc5UOYd0Kxov0KqaJhWNN6RoqpDj8R+eap+J9oPZCGwX9dB1KYxTS6wPzSR7O0yc7y5zuUw0finjS2Km2yhdxGrVdIa+o47gE+ELQ8BwmOeSMopiwzPkkDeGhPUK1GlYTOwDhHxHRL4njtRrrACI6p7XpFlJLbNfg+ztdxYaWIyuBl5yT4RBGU+vwX0LhL3ZQ1/vACTz8PRYvsjx32jO8L9Pz4LZYWuDCHs5ssuRYYp4FN5Oga6fDKVi61KNfI7EcwVedrcd7LCuIN3uaxvmczv+1rlncJjGuAa46zlNrGfkf8Aa6F47yY3JbRwzyqM0mVXERdVNdqtuItOYg6iyqqy4lssAyo9Fqg0Jik1FmC0wmqeiWACIxyUzkZGoEq8pyoEq5t1QVEaYurbDMSNKkrnD07ISYWwtJqMAotCIApMRnoKICogKUICkgV6CowuaVjBQj02oDSmKZWCFAUhSlSYEVoSsJW4vBSLKmqUi0wVrHsVZxDCyEYy7DZTpzhFQBxY49yoPZu6SQWv/pcGnwB5pMs2RqdNVToZP2TxTDVaaFQRXpAhs6vYyZafvsA82j7l8liWFpMrY9o6JqMZiGk5hDXkSHCoyC10jQloa6ebXclna7xWHfgPH2o97qQNCnOi7VorW10VuII/5SWJpFqG2vzRo1lvSr7o7qrnaGPHRVVGsOatMI4G5ICVoKke4fhT3OBjdaPBdk6bhNRxvqBZLYPGtZoB1TP/AIhGg+iysfov+G9n6dI/syRzErR4CmZ9PTdYrg3aBpd3j0PmVpMV2io0KL6zjIa2QNydA0dSbeaeKsjNlH+kTigdWp0AZFIZ3/zP90HrlE+D1UYLEyI9Fmf199Wo+q/36ji50czsOgsPAKwp14he5hjwgonk5ZcpNmvoV21QGVLECGu5bgEwSWztqJMToq7iWCcwwRG+oIg3BBFiI3SP61AaRzurvDY0Pa2nUu0udF+80d27XbH57rm8nw1P9obHxZ3Dp6KINR2aJ3G8Ke2cvfAJHc7xgXzFrZLRHOND0lEtLTlIIIkEGxkbQvJlGS2js5J6JAozCggqbUoDMVkOgySjVGSi4ViZsxOjSVjSbZDp00w1qmzNkwFIKIU2pRSQCkAvWBEDVgEIQnNTWVDe1YIJrk1Seli1eNfCxqLamUw1VmHrp5r0rDQWUGsLIgKFUKQNFLiacOUqYU8U3vKdNoVr6NYTC1GjMyp+7qDK865d21I5tN+okbrKcUwbqNVzHCC0kH/XMfitBxDGtw9KpXcGn2TSWtdcOqu7tFpE3GeHEfwscsbw/tE7Esayuc1VgIFQnv1KckjPzc2YnllGyrCL42UxS9Bazgdfz6KrrUxsU9WEH5b7JPGA6jqmTLUKEkKbMY4bperUS76wTpWKWn/U3G0o2ErEmZVCMQ0a+g/FeVuIGIHdHIanxOp+XRNxFs0bOKtpvuZcTAY25nrsPMhE4pxKpVIafdaZyciLSZ9468um5Od7O0pqF50Zf6x6D4q0BJ18jv6rt8fCl+zOPNlbfFDtLom2YmyrmO5+RRGnYrsRzMtBivjsrShiCMkdPifwhZ3DtJI+e1+afNe/wHSbD/tB9E6kK4l+2uTfMbnMJNoLiddjGWye4ZjnuJa95cBch0vaY0s6RaN9FTiu0DUesdExgXiXOGhsjJJ9MGtFt+rUXCWl9MmwGUvZO95zAevnqudw5wsH0j/XljoQ+D8N12AaZI0m5PIbDx/FWbcLT/gHmLlcc/DxN/BVZ5o+eblHw+q8XLxmdzLCmjtXLlNis9UmrlyAGGpowXLljHqG9cuWCCchOXLlgoNh1ZU1y5LIIVQcuXJQlZX95SprlyoTM/8ApG/8gP8A5LP/AKK6+d4D98zwP+BXLl1w/wAZSH+jTYj3Qg8Q93yP0XLlNHUU+I38/mkai5crQEkBChVXq5WIy0W/Afcd4n/FWNLX0XLl34v4RwZP6YwF6Pd8ly5UQrG8D748lM/vG+I/xK5cszIs6m/52TnB/eHj9V6uToSWi7p+8/8AnH+ZT68XLMVH/9k=" alt="New york" style="width:100%;height: 170px">
                                                                                                </div>
                                                                                            </div>

                                                                                            <!-- Left and right controls -->
                                                                                            <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                                                                                                <span class="glyphicon glyphicon-chevron-left"></span>
                                                                                                <span class="sr-only">Previous</span>
                                                                                            </a>
                                                                                            <a class="right carousel-control" href="#myCarousel" data-slide="next">
                                                                                                <span class="glyphicon glyphicon-chevron-right"></span>
                                                                                                <span class="sr-only">Next</span>
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <br>
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-12">
                                                                                        Client Approval Note Image
                                                                                        <div id="myCarousel" class="carousel slide" style="height: 150px" data-ride="carousel">
                                                                                            <!-- Indicators -->
                                                                                            <ol class="carousel-indicators">
                                                                                                <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                                                                                                <li data-target="#myCarousel" data-slide-to="1"></li>
                                                                                                <li data-target="#myCarousel" data-slide-to="2"></li>
                                                                                            </ol>

                                                                                            <!-- Wrapper for slides -->
                                                                                            <div class="carousel-inner">
                                                                                                <div class="item active">
                                                                                                    <img src="http://www.bollywoodlife.com/wp-content/uploads/2016/12/Shahrukh-2.jpg" alt="Los Angeles" style="width:100%;;height: 170px">
                                                                                                </div>

                                                                                                <div class="item">
                                                                                                    <img src="data:image/jpeg;base64,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" alt="Chicago" style="width:100%;height: 170px">
                                                                                                </div>

                                                                                                <div class="item">
                                                                                                    <img src="data:image/jpeg;base64,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" alt="New york" style="width:100%;height: 170px">
                                                                                                </div>
                                                                                            </div>

                                                                                            <!-- Left and right controls -->
                                                                                            <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                                                                                                <span class="glyphicon glyphicon-chevron-left"></span>
                                                                                                <span class="sr-only">Previous</span>
                                                                                            </a>
                                                                                            <a class="right carousel-control" href="#myCarousel" data-slide="next">
                                                                                                <span class="glyphicon glyphicon-chevron-right"></span>
                                                                                                <span class="sr-only">Next</span>
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div style="padding-bottom: 5px;padding-left: 3px">
                                                                        <button type="submit" class="btn blue" >Approve</button>
                                                                        <button type="submit" class="btn blue">disapprove</button>
                                                                    </div>
                                                                </form>
                                                            </div>

                                                    </div>
                                                </div>
                                                <div class="modal fade" id="paymentModal" role="dialog">
                                                    <div class="modal-dialog">
                                                        <!-- Modal content-->
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="row">
                                                                    <div class="col-md-4"></div>
                                                                    <div class="col-md-4" style="font-size: 18px"> Payment</div>
                                                                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body" style="padding:40px 50px;">
                                                                <div class="form-group row">
                                                                    <input type="number" class="form-control" id="usrname" placeholder="Enter User Name">
                                                                </div>
                                                                <div class="form-group row">
                                                                    <input type="number" class="form-control" id="usrname" placeholder="Enter Amount">
                                                                </div>
                                                                <div class="form-group row">
                                                                    <input type="text" class="form-control" id="usrname" placeholder="Enter Transaction details">
                                                                </div>
                                                                <div class="form-group row">Quotation images
                                                                    <div id="myCarousel" class="carousel slide" style="height: 150px" data-ride="carousel">
                                                                        <!-- Indicators -->
                                                                        <ol class="carousel-indicators">
                                                                            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                                                                            <li data-target="#myCarousel" data-slide-to="1"></li>
                                                                            <li data-target="#myCarousel" data-slide-to="2"></li>
                                                                        </ol>

                                                                        <!-- Wrapper for slides -->
                                                                        <div class="carousel-inner">
                                                                            <div class="item active">
                                                                                <img src="la.jpg" alt="Los Angeles" style="width:100%;">
                                                                            </div>

                                                                            <div class="item">
                                                                                <img src="chicago.jpg" alt="Chicago" style="width:100%;">
                                                                            </div>

                                                                            <div class="item">
                                                                                <img src="ny.jpg" alt="New york" style="width:100%;">
                                                                            </div>
                                                                        </div>

                                                                        <!-- Left and right controls -->
                                                                        <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                                                                            <span class="glyphicon glyphicon-chevron-left"></span>
                                                                            <span class="sr-only">Previous</span>
                                                                        </a>
                                                                        <a class="right carousel-control" href="#myCarousel" data-slide="next">
                                                                            <span class="glyphicon glyphicon-chevron-right"></span>
                                                                            <span class="sr-only">Next</span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <a href="#" class="btn btn-set red pull-right">
                                                                    <i class="fa fa-check" style="font-size: large"></i>
                                                                    Add &nbsp; &nbsp; &nbsp;
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="transactionModal" role="dialog">
                                                    <form action="/purchase/purchase-order/create-transaction" method="post">
                                                        <input type="hidden" name="type" value="upload_bill">
                                                        <input type="hidden" name="purchase_order_component_id" id="po_component_id">
                                                        <input type="hidden" name="unit_id" id="unit_id">
                                                    <div class="modal-dialog">
                                                        <!-- Modal content-->
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="row">
                                                                    <div class="col-md-4"></div>
                                                                    <div class="col-md-4" style="font-size: 18px"> Purchase Bill Upload</div>
                                                                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body" style="padding:40px 50px;">
                                                                <div class="form-body">
                                                                    <div class="form-group row">
                                                                        <label>Material Name</label>
                                                                       <input type="text" class="form-control" id="material" name="material" placeholder="Enter material" readonly>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label>Quantity</label>
                                                                        <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity" readonly>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label>Unit Name</label>
                                                                        <input type="text" class="form-control" id="unit_name" name="unit_name" placeholder="Enter Unit" readonly>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label>Vendor Name</label>
                                                                        <input type="text" class="form-control" id="vendor" name="vendor_name" placeholder="Enter Vendor Name" readonly>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <table class="table table-bordered table-hover">
                                                                            <thead>
                                                                            <tr role="row" class="heading">
                                                                                <th>Bill Image </th>
                                                                                <th> Action </th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody id="show-product-images">
                                                                            <tr>
                                                                                <td>
                                                                                    <a target="_blank" class="fancybox-button" data-rel="fancybox-button">
                                                                                        <img class="img-responsive" alt="" style="width:100px; height:100px;"> </a>
                                                                                    <input type="hidden" class="work-order-image-name">
                                                                                </td>
                                                                                <td>
                                                                                    <a href="javascript:;" class="btn btn-default btn-sm">
                                                                                        <i class="fa fa-times"></i> Remove </a>
                                                                                </td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <div class="form-group row">Quotation images
                                                                        <div id="myCarousel" class="carousel slide" style="height: 150px" data-ride="carousel">
                                                                            <!-- Indicators -->
                                                                            <ol class="carousel-indicators">
                                                                                <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                                                                                <li data-target="#myCarousel" data-slide-to="1"></li>
                                                                                <li data-target="#myCarousel" data-slide-to="2"></li>
                                                                            </ol>

                                                                            <!-- Wrapper for slides -->
                                                                            <div class="carousel-inner">
                                                                                <div class="item active">
                                                                                    <img src="la.jpg" alt="Los Angeles" style="width:100%;">
                                                                                </div>

                                                                                <div class="item">
                                                                                    <img src="chicago.jpg" alt="Chicago" style="width:100%;">
                                                                                </div>

                                                                                <div class="item">
                                                                                    <img src="ny.jpg" alt="New york" style="width:100%;">
                                                                                </div>
                                                                            </div>

                                                                            <!-- Left and right controls -->
                                                                            <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                                                                                <span class="glyphicon glyphicon-chevron-left"></span>
                                                                                <span class="sr-only">Previous</span>
                                                                            </a>
                                                                            <a class="right carousel-control" href="#myCarousel" data-slide="next">
                                                                                <span class="glyphicon glyphicon-chevron-right"></span>
                                                                                <span class="sr-only">Next</span>
                                                                            </a>
                                                                    </div>
                                                                </div>
                                                                    <div class="form-group row">
                                                                        <input type="text" class="form-control" name="bill_number" placeholder="Enter Bill Number">
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <input type="text" class="form-control" name="bill_amount" placeholder="Enter Bill Amount">
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <input type="text" class="form-control" name="vehicle_number" placeholder="Enter Vehicle Number">
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <input type="datetime-local"   class="form-control" name="in_time" placeholder="Enter In Time">
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <input type="datetime-local" class="form-control" name="out_time" placeholder="Enter Out Time">
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <input type="text" class="form-control" name="remark" placeholder="Enter Remark">
                                                                    </div>
                                                                <button type="submit" class="btn btn-set red pull-right">
                                                                    <i class="fa fa-check" style="font-size: large"></i>
                                                                    Save&nbsp; &nbsp; &nbsp;
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-12">
                                           <!-- BEGIN VALIDATION STATES-->
                                            <div class="portlet light ">
                                                <div class="portlet-body form">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <table class="table table-striped table-bordered table-hover order-column" id="purchaseRequest">
                                                                <thead>
                                                                <tr>
                                                                    <th> GRN</th>
                                                                    <th> Material Name </th>
                                                                    <th> Qty </th>
                                                                    <th> Unit </th>
                                                                    <th>Vendor</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                                <tr class="filter">
                                                                    <th><input type="text" class="form-control form-filter" name="search_name"></th>
                                                                    <th><input type="text" class="form-control form-filter" name="search_name" > </th>
                                                                    <th><input type="text" class="form-control form-filter" name="search_name"></th>
                                                                    <th><input type="text" class="form-control form-filter" name="search_name" > </th>
                                                                    <th><input type="text" class="form-control form-filter" name="search_name" > </th>
                                                                    <th>
                                                                        <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                                        <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                                    </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tr>
                                                                    <td> ABC </td>
                                                                    <td> 5</td>
                                                                    <td> Kg </td>
                                                                    <td> Vendor lmn </td>
                                                                    <td>
                                                                        <select class="table-group-action-input form-control input-inline input-small input-sm">
                                                                            <option value="">Select...</option>
                                                                            <option value="Cancel">Approve</option>
                                                                            <option value="Cancel">Disapprove</option>
                                                                        </select>
                                                                    </td>
                                                                    <td> <button id="image">Upload</button> <button id="payment">Make Payment</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td> ABC </td>
                                                                    <td> 5</td>
                                                                    <td> Kg </td>
                                                                    <td> Vendor lmn </td>
                                                                    <td>
                                                                        <select class="table-group-action-input form-control input-inline input-small input-sm">
                                                                            <option value="">Select...</option>
                                                                            <option value="Cancel">Approve</option>
                                                                            <option value="Cancel">Disapprove</option>
                                                                        </select>
                                                                    </td>
                                                                    <td> <button id="image">Upload</button><button id="image">Make Payment</button> </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <link rel="stylesheet"  href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css"/>
    <link rel="stylesheet"  href="/assets/global/css/app.css"/>
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/custom/purchase/purchase-order/purchase-order.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
    <style>
        @-webkit-keyframes zoom {
            from {
                -webkit-transform: scale(1, 1);
            }
            to {
                -webkit-transform: scale(1.5, 1.5);
            }
        }

        @keyframes zoom {
            from {
                transform: scale(1, 1);
            }
            to {
                transform: scale(1.7, 1.7);
            }
        }

        .carousel-inner .item > img {
            -webkit-animation: zoom 20s;
            animation: zoom 20s;
        }
    </style>
@endsection
