@extends('layout.master')
@section('title','Constro | Create Site Transfer')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="/assets/global/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/css/app.css" rel="stylesheet" type="text/css" />
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
                                    <h1>Create Site Transfer</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container" style="width: 100%">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/inventory/transfer/manage">Manage Site Transfer</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Create Site Transfer</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <form id="create-labour" class="form-horizontal" action="/inventory/transfer/create" method="post">
                                                {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Project Site From</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="project_site_from" name="project_site_from">
                                                                <option value="">Select Project Site</option>
                                                                @foreach($projectSites as $projectSite)
                                                                    <option value="{{$projectSite['id']}}">{{$projectSite['project_name']}} - {{$projectSite['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Project Site To</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="project_site_to" name="project_site_to">
                                                                <option value="">Select Project Site</option>
                                                                @foreach($projectSites as $projectSite)
                                                                    <option value="{{$projectSite['id']}}">{{$projectSite['project_name']}} - {{$projectSite['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Type</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="inventory_type" name="inventory_type">
                                                                <option value="">Select Inventory Type</option>
                                                                <option value="material">Material</option>
                                                                <option value="asset">Asset</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Name : </label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="name" name="name">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Quantity : </label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="quantity" name="quantity">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Unit : </label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="unit" name="unit" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Remark : </label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="remark" name="remark">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red" id="labour_submit"><i class="fa fa-check"></i> Submit</button>
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
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script><script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#inventory_type,#project_site_from").on('change',function(){
                var componentType = $("#inventory_type").val();
                var project_site_id = $('#project_site_from').val();
                if(typeof componentType != 'undefined' && componentType != '' && typeof project_site_id != 'undefined' && project_site_id != ''){
                    $('#name').removeClass('typeahead');
                    $('#name').typeahead('destroy');
                    $('#name').addClass('typeahead');
                    var citiList = new Bloodhound({
                        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                        remote: {
                            url: "/inventory/transfer/auto-suggest/"+project_site_id+"/"+componentType+"/%QUERY",
                            filter: function(x) {
                                if($(window).width()<420){
                                    $("#header").addClass("fixed");
                                }
                                return $.map(x, function (data) {
                                    return {
                                        name:data.name,
                                        unit:data.unit
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
                        var POData = $.parseJSON(JSON.stringify(datum));
                        POData.name = POData.name.replace(/\&/g,'%26');
                        $("#unit").val(POData.unit);
                        $("#name").val(POData.name);
                    })
                    .on('typeahead:open', function (obj, datum) {

                    });
                }else{
                    $('#name').removeClass('typeahead');
                    $('#name').typeahead('destroy');
                }
            });
        });
    </script>
@endsection