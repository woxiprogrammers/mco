<?php
    /**
     * Created by Harsha.
     * User: manoj
     * Date: 28/2/18
     * Time: 12:09 PM
     */
?>

@extends('layout.master')
@section('title','Constro | Create Peticash Purchase Transaction')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />

    <!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
    <input type="hidden" id="unitOptions" value="{{$unitOptions}}">
    <input id="nosUnit" type="hidden" value="{{$nosUnit}}">
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
                                    <h1>Create Peticash Purchase Transaction</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/peticash/peticash-management/purchase/manage">Manage Peticash Purchase Transaction</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Create Peticash Purchase Transaction</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <form role="form" id="create-peticash-purchase-transaction" class="form-horizontal" method="post" action="/peticash/peticash-management/purchase/transaction/create">
                                                {!! csrf_field() !!}
                                                <input type="hidden"  id="csrf-token" name="csrf-token" value="{{ csrf_token() }}">
                                                <input type="hidden" id="purchase_peticash_transaction_id" name="purchase_peticash_transaction_id">
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="structure_type" class="control-label">Select Component :</label>
                                                            <span>*</span>
                                                        </div>
                                                        &nbsp;&nbsp;&nbsp;
                                                        <div class="col-md-6 mt-radio-inline">
                                                            <label class="mt-radio" style="margin-left: 13px">
                                                                <input type="radio" name="component_type" id="material" value="material" > Material
                                                                <span></span>
                                                            </label>
                                                            <label class="mt-radio" style="margin-left: 13px">
                                                                <input type="radio" name="component_type" id="asset" value="asset"> Asset
                                                                <span></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="model_number" class="control-label">Shop Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="source_name" name="source_name">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Component Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="hidden" id="component_id" name="component_id">
                                                            <input type="text" class="form-control empty typeahead" id="component_name" name="component_name">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="espu" class="control-label">Quantity</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control" id="quantity" name="quantity" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="lpu" class="control-label">Unit</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="unit" name="unit" disabled>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row"  id="category_select" hidden>
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="qty" class="control-label">Select Category</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" name="miscellaneous_category_id" id="miscellaneous_category">
                                                                @foreach($miscellaneousCategories as $key => $category)
                                                                    <option value="{{$category['id']}}">{{$category['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right" >
                                                            <label for="date" class="control-label ">Challan Number</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text"  class="form-control"  name="challan_number" id="challan_number"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="number" class="control-label">Bill Amount</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control" id="bill_amount" name="bill_amount">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12" style="margin-left: 20%"> </div>
                                                        </div>
                                                        <div id="tab_images_uploader_container" class="col-md-offset-5">
                                                            <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow" style="margin-left: 26%">
                                                                Browse</a>
                                                            <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
                                                                <i class="fa fa-share"></i> Upload Files </a>
                                                        </div>
                                                        <table class="table table-bordered table-hover" style="width: 554px; margin-left: 26%; margin-top: 1%">
                                                            <thead>
                                                            <tr role="row" class="heading">
                                                                <th> Image </th>
                                                                <th> Action </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="show-product-images">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="col-md-offset-3 btn-group generate-grn-button">
                                                        <div class=" btn red" style="margin-left: 26%">
                                                            <a href="javascript:void(0)" style="color: white" onclick="generateGRN()"><i class="fa fa-check"></i> Generate GRN</a>
                                                        </div>
                                                    </div>
                                                    <div class="post-grn" hidden>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="number" class="control-label">GRN</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="grn" name="grn" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="number" class="control-label">Reference Number</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="reference_number" name="reference_number">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="row">
                                                                <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12" style="margin-left: 20%"> </div>
                                                            </div>
                                                            <div id="tab_images_uploader_container" class="col-md-offset-5">
                                                                <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow" style="margin-left: 26%">
                                                                    Browse</a>
                                                                <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
                                                                    <i class="fa fa-share"></i> Upload Files </a>
                                                            </div>
                                                            <table class="table table-bordered table-hover" style="width: 554px; margin-left: 26%; margin-top: 1%">
                                                                <thead>
                                                                <tr role="row" class="heading">
                                                                    <th> Image </th>
                                                                    <th> Action </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody id="show-product-images">

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="form-actions noborder row">
                                                            <div class="col-md-offset-3" style="margin-left: 26%">
                                                                <button type="submit" class="btn red" style=" padding-left: 6px"><i class="fa fa-check"></i> Submit</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <input type="hidden" id="path" name="path" value="">
                                            <input type="hidden" id="max_files_count" name="max_files_count" value="20">
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
    <script src="/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="/assets/custom/user/user.js" type="application/javascript"></script>
    <script src="/assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
    <script src="/assets/custom/admin/asset/image-datatable.js"></script>
    <script src="/assets/custom/admin/asset/image-upload.js"></script>
    <script src="/assets/custom/admin/asset/asset.js" type="application/javascript"></script>
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
<script>
    $(document).ready(function() {
        $("input[type='radio']").change(function(){
            $('#component_name').val('');
            $('#category_select').hide();
            $('#component_name').typeahead('destroy');
            var searchIn = $(this).val();
            var site_name = $("#globalProjectSite").val();
            var validSearchIn = '';
            var componentID = 0;
            switch(searchIn){
                case 'material':
                    $("#component_id").val(4);
                    componentID = 4;
                    $('#category_select').show();
                    validSearchIn = true;
                    break;

                case 'asset':
                    $("#component_id").val(6);
                    $('#category_select').hide();
                    componentID = 6;
                    validSearchIn = true;
                    break;

                default:
                    validSearchIn = false;
            }
            if(validSearchIn == true){
                var materialList = new Bloodhound({
                    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    remote: {
                        url: '/purchase/material-request/get-items?project_site_id='+site_name+'&search_in='+searchIn+'&keyword=%QUERY',
                        filter: function(x) {
                            if($(window).width()<420){
                                $("#header").addClass("fixed");
                            }

                            return $.map(x, function (data) {
                                if(searchIn == 'material'){
                                    return {
                                        name:data.material_name,
                                        unit:data.unit_quantity,
                                        component_type_id:data.material_request_component_type_id
                                    };
                                }else{
                                    return {
                                        name:data.asset_name,
                                        unit:data.asset_unit,
                                        unit_id:data.asset_unit_id,
                                        component_type_id:data.material_request_component_type_id
                                    };
                                }

                            });
                        },
                        wildcard: "%QUERY"
                    }
                });
                materialList.initialize();
                $('.typeahead').typeahead(null, {
                    displayKey: 'name',
                    engine: Handlebars,
                    source: materialList.ttAdapter(),
                    limit: 30,
                    templates: {
                        empty: [
                            '<div class="empty-suggest">',
                            'Unable to find any Result that match the current query',
                            '</div>'
                        ].join('\n'),
                        suggestion: Handlebars.compile('<div class="autosuggest"><strong>@{{name}}</strong></div>')
                    },
                })
                    .on('typeahead:selected', function (obj, datum) {
                        var POData = datum.unit;
                        var componentTypeId = datum.component_type_id;
                        $('#component_id').val(componentTypeId);
                        var options = '';
                        if(searchIn == 'material'){
                            $.each( POData, function( key, value ) {
                                var unitId = value.unit_id;
                                var unitName = value.unit_name;
                                options =  options+ '<option value="'+unitId +'">'+unitName +'</option>'
                            });
                        }else{
                            var unitId = datum.unit_id;
                            var unitName = POData;
                            options =  options+ '<option value="'+unitId +'">'+unitName +'</option>'
                        }

                        $('#unit').html(options);
                        $('#category_select').hide();
                    })
                    .on('typeahead:open', function (obj, datum) {
                        $("#component_id").val(componentID);
                        if(searchIn == 'material') {
                            $("#unit").html($("#unitOptions").val());
                            $('#category_select').show();
                        }else{
                            $("#unit").html($("#nosUnit").val());
                        }

                    });
            }
            $('#quantity').prop('disabled',false);
            $('#unit').prop('disabled',false);
        });
    });

    function generateGRN(){
        $.ajax({
            url: '/peticash/peticash-management/purchase/transaction/generate-grn',
            type: 'POST',
            async: true,
            data: {
                _token: $("input[name='_token']").val(),
                component_type : $("input[name='component_type']:checked").val(),
                source_name : $('#source_name').val(),
                component_id : $('#component_id').val(),
                component_name : $('#component_name').val(),
                quantity : $('#quantity').val(),
                unit : $('#unit').val(),
                miscellaneous_category_id : $('#miscellaneous_category').val(),
                challan_number : $('#challan_number').val(),
                bill_amount : $('#bill_amount').val()
            },
            success: function(data,textStatus,xhr){
                $('.post-grn').show();
                $('.generate-grn-button').hide();
                $('#grn').val(data.purchase_transaction.grn);
                $('#purchase_peticash_transaction_id').val(data.purchase_transaction.id);
            },
            error: function(){

            }
        });
    }
</script>
@endsection