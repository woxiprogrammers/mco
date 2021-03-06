@extends('layout.master')
@section('title','Constro | Create Vendor')
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
                                    <h1>Create Vendor</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/vendors/manage">Manage Vendor</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Create Vendor</a>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <form role="form" id="create-vendor" class="form-horizontal" method="post" action="/vendors/create">
                                                {!! csrf_field() !!}
                                            <div class="pull-right">
                                                <button type="submit" class="btn red" style=" padding-left: 6px"><i class="fa fa-check"></i> Submit</button>
                                            </div>
                                            <div class="table-toolbar">
                                                <ul class="nav nav-tabs nav-tabs-lg">
                                                    <li class="active">
                                                        <a href="#generaltab" data-toggle="tab"> General </a>
                                                    </li>
                                                    <li class="materialTab">
                                                        <a href="#materialassigntab" data-toggle="tab"> Material Assign </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="portlet-body form">
                                                <div class="tab-content">
                                                <div class="tab-pane fade in active" id="generaltab">
                                                    <div class="form-group row">
                                                        <div class="col-md-4" style="text-align: right">

                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="checkbox" id="transportationVendor" name="transportation_vendor">
                                                            <label for="name" class="control-label">Is transportation provided</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Name</label>

                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="name" name="name">
                                                        </div>
                                                    </div>
                                                    <div class="form-body">
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="company" class="control-label">Company</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="company" name="company">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-body">
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="mobile" class="control-label">Mobile Number</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number" class="form-control" id="mobile" name="mobile">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-body">
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="email" class="control-label">Email Address</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="email" name="email">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-body">
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="gstin" class="control-label">GSTIN</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="gstin" name="gstin">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-body">
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="alternate_contact" class="control-label">Alternate Contact Number</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="alternate_contact" name="alternate_contact">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-body">
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="alternate_email" class="control-label">Alternate Email Address </label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="alternate_email" name="alternate_email">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-body">
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="city" class="control-label">City Name</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-control product-material-select" style="height: 150px;" >
                                                                    <ul id="cityList" class="list-group">
                                                                        @foreach($cityArray as $city)
                                                                            <li><input class="cities" type="checkbox" id="city" value="{{$city['id']}}" name="cities[]" > <span>{{$city['name']}}</span> </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade in" id="materialassigntab">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="col-md-3 control-label">Material</label>
                                                            <div class="col-md-6">
                                                                <input class="form-control typeahead" id="material" name="material">
                                                            </div>
                                                            <div id="removeMaterial" hidden>
                                                                <a class="btn pull-right blue" id="removeMaterialButton">Remove Material</a>
                                                            </div>
                                                        </div>
                                                            <thead>
                                                            <table border="1" id="materialCityTable" class="table table-striped table-bordered table-hover table-checkable order-column" hidden>
                                                                <tr>
                                                                    <th style="width:10%" ></th>
                                                                    <th style="width: 30%">Material</th>
                                                                    <th>City</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            </div>
                                        </form>
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
    <script src="/assets/custom/admin/vendor/vendor.js" type="application/javascript"></script>
    <script src="/assets/custom/admin/vendor/validation.js" type="application/javascript"></script>
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#transportationVendor').on('change',function(){
                if($(this).is(':checked') == true){
                    $("a[href='#materialassigntab']").remove();
                }else{
                    $('.materialTab').append('<a href="#materialassigntab" data-toggle="tab"> Material Assign </a>');
                }
            });
            CreateVendor.init();
            var citiList = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: "/material/auto-suggest/%QUERY",
                    filter: function(x) {
                        if($(window).width()<420){
                            $("#header").addClass("fixed");
                        }
                        return $.map(x, function (data) {
                            return {
                                id:data.id,
                                name:data.name,
                            };
                        });
                    },
                    wildcard: "%QUERY"
                }
            });
            citiList.initialize();
            $('.typeahead').typeahead(null, {
                displayKey: 'name',
                engine: Handlebars,
                source: citiList.ttAdapter(),
                limit: 30,
                templates: {
                    empty: [
                        '<div class="empty-suggest">',
                        'Unable to find any Result that match the current query',
                        '</div>'
                    ].join('\n'),
                    suggestion: Handlebars.compile('<div class="autosuggest"><strong>@{{name}}</strong></div>')
                },
            }).on('typeahead:selected', function (obj, datum) {
                if($("#cityList input:checkbox:checked").length > 0){
                    var POData = $.parseJSON(JSON.stringify(datum));
                    var materialTrString = '<tr><td><input type="checkbox" class="material-city-row"></td><td>'+POData.name+'</td><td>';
                    $("#cityList input:checkbox:checked").each(function(){
                        var cityName = $(this).next().text();
                        var cityId = $(this).val();
                        materialTrString += '<input type="checkbox" name="material_city['+POData.id+'][]" value="'+cityId+'"><span>'+cityName+'</span><br>';
                    });
                    materialTrString += '</td></tr>';
                    $("#materialCityTable").append(materialTrString);
                    $("#materialCityTable").show();
                    $("#removeMaterial").show();
                }else{
                    alert("Please select atleast one city first");
                }
            })
            .on('typeahead:open', function (obj, datum) {

            });
        });
    </script>
    <script>

    </script>
@endsection
