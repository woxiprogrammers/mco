@extends('layout.master')
@section('title','Constro | Edit Vendor')
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
                                    <h1>Edit Vendor</h1>
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
                                        <a href="javascript:void(0);">Edit Vendor</a>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <form role="form" id="edit-vendor" class="form-horizontal" method="post" action="/vendors/edit/{{$vendor->id}}">
                                            {!! csrf_field() !!}
                                            <input type="hidden" name="_method" value="put">
                                            @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('edit-manage-user'))
                                                <div class="pull-right">
                                                    <button type="submit" class="btn red" style=" padding-left: 6px"><i class="fa fa-check"></i> Submit</button>
                                                </div>
                                            @endif

                                            <div class="table-toolbar">
                                                <ul class="nav nav-tabs nav-tabs-lg">
                                                    <li class="active">
                                                        <a href="#generaltab" data-toggle="tab"> General </a>
                                                    </li>
                                                    @if($isTransportationVendor != true)
                                                        <li>
                                                            <a href="#materialassigntab" data-toggle="tab"> Material Assign </a>
                                                        </li>
                                                    @endif

                                                </ul>
                                            </div>
                                            <div class="portlet-body form">
                                                <div class="tab-content">
                                                    <div class="tab-pane fade in active" id="generaltab">
                                                        @if($vendor->for_transportation == true)
                                                            <div class="form-group row">
                                                                <div class="col-md-5" style="text-align: right">

                                                                </div>
                                                                <div class="col-md-5">
                                                                    <label for="name" class="control-label"><b>Transportation Vendor</b></label>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="name" class="control-label">Name</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="name" name="name" value="{{$vendor->name}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-body">
                                                            <div class="form-group row">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label for="company" class="control-label">Company</label>
                                                                    <span>*</span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control" id="company" name="company" value="{{$vendor->company}}">
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
                                                                    <input type="number" class="form-control" id="mobile" name="mobile"  value="{{$vendor->mobile}}">
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
                                                                    <input type="text" class="form-control" id="email" name="email"  value="{{$vendor->email}}">
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
                                                                    <input type="text" class="form-control" id="gstin" name="gstin"  value="{{$vendor->gstin}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-body">
                                                            <div class="form-group row">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label for="alternate_contact" class="control-label">Alternate Contact Number</label>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control" id="alternate_contact" name="alternate_contact"  value="{{$vendor->alternate_contact}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-body">
                                                            <div class="form-group row">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label for="alternate_email" class="control-label">Alternate Email Address </label>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control" id="alternate_email" name="alternate_email"  value="{{$vendor->alternate_email}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-body">
                                                            <div class="form-group row">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label class="control-label">City Name</label>
                                                                    <span>*</span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-control product-material-select" style="height: 150px;" >
                                                                        <ul id="cityList" class="list-group">
                                                                            @foreach($cityArray as $city)
                                                                                @if(in_array($city['id'],$vendorCities))
                                                                                    <li><input type="checkbox" value={{$city['id']}} name="cities[]" class="cities" checked> <span>{{$city['name']}}</span> </li>
                                                                                @else
                                                                                    <li><input type="checkbox" value={{$city['id']}} name="cities[]" class="cities"> <span>{{$city['name']}}</span> </li>
                                                                                @endif
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
                                                                    <input class="form-control typeahead" name="material">
                                                                </div>
                                                                <div id="removeMaterial">
                                                                    <a class="btn pull-right blue" id="removeMaterialButton">Remove Material</a>
                                                                </div>
                                                            </div>
                                                            <table border="1" id="materialCityTable" class="table table-striped table-bordered table-hover table-checkable order-column">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 10%">Remove</th>
                                                                        <th style="width: 30%">Material</th>
                                                                        <th>City</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($vendorMaterialInfo as $materialId => $materialInfo)
                                                                        <tr>
                                                                            <td>
                                                                                <input type="checkbox" class="material-city-row">
                                                                            </td>
                                                                            <td>
                                                                                {{$materialInfo['name']}}
                                                                            </td>
                                                                            <td>
                                                                                @foreach($cityArray as $city)
                                                                                    @if(in_array($city['id'],$vendorCities))
                                                                                        @if(in_array($city['id'],$materialInfo['cities']))
                                                                                            <input type="checkbox" name="material_city[{{$materialId}}][]" value="{{$city['id']}}" checked><span>{{$city['name']}}</span><br>
                                                                                        @else
                                                                                            <input type="checkbox" name="material_city[{{$materialId}}][]" value="{{$city['id']}}"><span>{{$city['name']}}</span><br>
                                                                                        @endif
                                                                                    @endif
                                                                                @endforeach
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
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
@endsection
@section('javascript')
    <script src="/assets/custom/admin/vendor/vendor.js" type="application/javascript"></script>
    <script src="/assets/custom/admin/vendor/validation.js" type="application/javascript"></script>
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>

    <script>
        $(document).ready(function() {
            $("#categoryId").trigger('change');
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
                    if($("input[name='material_city["+POData.id+"][]']").length <= 0){
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
                        alert('You can not select same material more than once.');
                    }
                }else{
                    alert("Please select atleast one city first");
                }

            })
            .on('typeahead:open', function (obj, datum) {

                });
            EditVendor.init();
        });
    </script>
@endsection
