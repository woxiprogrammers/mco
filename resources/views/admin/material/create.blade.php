@extends('layout.master')
@section('title','Constro | Create Material')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link rel="stylesheet"  href="/assets/global/plugins/typeahead/typeahead.css"/>
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
                                <h1>Create Material</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/material/manage">Manage Material</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Create Material</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <form role="form" id="create-material" class="form-horizontal" action="/material/create" method="post">
                                            {!! csrf_field() !!}
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Category Name</label>
                                                    <div class="col-md-6">
                                                        <select class="form-control" id="category_id" name="category_id">
                                                            @foreach($categories as $category)
                                                                <option value="{{$category['id']}}"> {{$category['name']}} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Is Material already created</label>
                                                    <div class="col-md-6">
                                                        <div class="mt-checkbox-list">
                                                            <label class="mt-checkbox">
                                                                <input type="checkbox" id="is_present" name="is_present">
                                                                <span></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Material Name</label>
                                                    <div class="col-md-6">
                                                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter Material Name">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Rate</label>
                                                    <div class="col-md-6">
                                                        <input type="number" id="rate_per_unit" name="rate_per_unit" class="form-control" placeholder="Enter Rate">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Unit</label>
                                                    <div class="col-md-6">
                                                        <select class="form-control" id="unit" name="unit">
                                                            @foreach($units as $unit)
                                                                <option value="{{$unit['id']}}"> {{$unit['name']}} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">GST</label>
                                                    <div class="col-md-6">
                                                        <input type="number" id="gst" name="gst" class="form-control" placeholder="Enter GST">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">HSN Code</label>
                                                    <div class="col-md-6">
                                                        <input type="text" id="hsn_code" name="hsn_code" class="form-control" placeholder="Enter HSN Code">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions noborder row">
                                                <div class="col-md-offset-3" style="margin-left: 26%">
                                                    <button type="submit" class="btn red btn-md"><i class="fa fa-check"></i> Submit</button>
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
<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
<script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
<script src="/assets/custom/admin/material/material.js" type="application/javascript"></script>
<script>
    $(document).ready(function() {
       CreateMaterial.init();
        $("#name").rules('add',{
            remote: {
                url: "/material/check-name",
                type: "POST",
                data: {
                    name: function() {
                        return $( "#name" ).val();
                    }
                }
            }
        });
        $('#is_present').on('click',function(){
            if($(this).prop('checked') == true){
                $('#name').rules('remove', 'remote');
                $('#name').addClass('typeahead');
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
                    var POData = $.parseJSON(JSON.stringify(datum));
                    POData.name = POData.name.replace(/\&/g,'%26');
                    $("#rate_per_unit").val(POData.rate_per_unit);
                    $("#rate_per_unit").prop('disabled', true);
                    $("#unit option[value='"+POData.unit_id+"']").prop('selected', true);
                    $("#unit").prop('disabled', true);
                    $("#name").val(POData.name);
                    $("#name").prop('disabled', true);
                    $("#create-material .form-body").append($("<input>", {'id': "material_id",
                        'type': 'hidden',
                        'value': POData.id,
                        'name': "material_id"
                    }));
                })
                .on('typeahead:open', function (obj, datum) {
                });
            }else{
                $('#name').removeClass('typeahead');
                $("#create-material input[name='material_id']").remove();
                $('#name').typeahead('destroy');
                $("#rate_per_unit").prop('disabled', false);
                $("#name").prop('disabled', false);
                $("#unit").prop('disabled', false);
                $("#name").rules('add',{
                    remote: {
                        url: "/material/check-name",
                        type: "POST",
                        data: {
                            name: function() {
                                return $("#name" ).val();
                            }
                        }
                    }
                });
            }
        });
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
                            material_version_id:data.material_version_id,
                            unit_id:data.unit_id,
                            unit:data.unit,
                            rate_per_unit:data.rate_per_unit
                        };
                    });
                },
                wildcard: "%QUERY"
            }
        });
    });
</script>
@endsection
