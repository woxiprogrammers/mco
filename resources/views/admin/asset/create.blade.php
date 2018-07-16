@extends('layout.master')
@section('title','Constro | Create Asset')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />

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
                                    <h1>Create Asset</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/asset/manage">Manage Asset</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Create Asset</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <form role="form" id="create-asset" class="form-horizontal" method="post" action="/asset/create">
                                                {!! csrf_field() !!}
                                                <input type="hidden"  id="csrf-token" name="csrf-token" value="{{ csrf_token() }}">
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="model_number" class="control-label">Model Number</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="model_number" name="model_number">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Asset Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="name" name="name">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="diesel" class="control-label">Asset type</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" name="asset_type" id="select-type">
                                                                <option value="">Select Option</option>
                                                               @foreach($asset_types as $asset_type)
                                                                <option value="{{$asset_type['id']}}">{{$asset_type['name']}}</option>
                                                               @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" id="espu">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="espu" class="control-label">Electricity per unit</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control" id="electricity_per_unit" name="electricity_per_unit">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row"  id="lpu">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="lpu" class="control-label">Litre per unit</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control" id="litre_per_unit" name="litre_per_unit">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="qty" class="control-label">Quantity</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" name="qty" id="qty">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" id="exp_date">
                                                        <div class="col-md-3" style="text-align: right" >
                                                            <label for="date" class="control-label ">Expiry Date</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-4 date date-picker">
                                                            <input type="text"   style="width: fit-content" name="expiry_date" placeholder="Select Expiry Date" id="date"/>
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="number" class="control-label">Price</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control" id="number" name="price">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="number" class="control-label">Rent</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control" id="rent_per_day" name="rent_per_day">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" id="maintenancePeriodDiv" hidden>
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="number" class="control-label">Maintenance Period</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="number" class="form-control" id="maintenance_period" name="maintenance_period">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <select class="form-control" id="maintenance_period_type" name="maintenance_period_type">
                                                                <option value=""> Select type </option>
                                                                <option value="day_wise">Day</option>
                                                                <option value="hour_wise">Hrs</option>
                                                            </select>
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
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="/assets/custom/user/user.js" type="application/javascript"></script>
    <script src="/assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
    <script src="/assets/custom/admin/asset/image-datatable.js"></script>
    <script src="/assets/custom/admin/asset/image-upload.js"></script>
    <script src="/assets/custom/admin/asset/asset.js" type="application/javascript"></script>
    <script>
        $(document).ready(function() {

            $('#espu').hide();
            $('#lpu').hide();
            CreateAsset.init();
            $('#select-litre').change(function(){
                var selected = $(this).val();
                if(selected == 'true'){
                    $('#Litre').show();
                }else{
                    $('#Litre').hide();
                }
            });
            $('#electricity_per_unit').rules('remove');
            $('#litre_per_unit').rules('remove');
        });
    </script>
    <script>
        var date=new Date();
        $('#expiry_date').val((date.getMonth()+1)+"/"+date.getDate()+"/"+date.getFullYear());
    </script>
    <script>
        $('#select-type').change(function(){
           var asset_type = $(this).val();
           if(asset_type == 1){
               $('#lpu').show();
               $('#espu').hide();
               $('#litre_per_unit').rules('add', {
                   required: true   // set a new rule
               });
               $('#electricity_per_unit').rules('remove');
               $('#qty').val(1);
               $('#qty').prop('readonly',true);
               $('#exp_date').show();
               $('#exp_date input[name="expiry_date"]').rules('add', {
                   required: true   // set a new rule
               });
               $("#maintenancePeriodDiv").show();
               $("#maintenance_period").rules('add',{
                   required: true
               });
               $("#maintenance_period_type").rules('add',{
                   required: true
               });
               $("#maintenance_period").val('');
           }else if(asset_type == 2){
               $('#espu').show();
               $('#lpu').hide();
               $('#electricity_per_unit').rules('add', {
                   required: true   // set a new rule
               });
               $('#litre_per_unit').rules('remove');
               $('#qty').val(1);
               $('#qty').prop('readonly',true);
               $('#exp_date').show();
               $('#exp_date  input[name="expiry_date"]').rules('add', {
                   required: true   // set a new rule
               });
               $("#maintenancePeriodDiv").show();
               $("#maintenance_period").rules('add',{
                   required: true
               });
               $("#maintenance_period_type").rules('add',{
                   required: true
               });
               $("#maintenance_period").val('');
           }else if(asset_type == 3){
               $('#espu').show();
               $('#electricity_per_unit').rules('add', {
                   required: true   // set a new rule
               });
               $('#litre_per_unit').rules('add', {
                   required: true   // set a new rule
               });
               $('#lpu').show();
               $('#exp_date').show();
               $('#exp_date  input[name="expiry_date"]').rules('add', {
                   required: true   // set a new rule
               });
               $('#qty').val(1);
               $('#qty').prop('readonly',true);
               $("#maintenancePeriodDiv").show();
               $("#maintenance_period").rules('add',{
                   required: true
               });
               $("#maintenance_period_type").rules('add',{
                   required: true
               });
               $("#maintenance_period").val('');
           }else if(asset_type == 4){
               $('#espu').hide();
               $('#electricity_per_unit').rules('remove');
               $('#litre_per_unit').rules('remove');
               $('#lpu').hide();
               $('#exp_date').hide();
               $('#exp_date  input[name="expiry_date"]').rules('remove');
               $('#qty').val('');
               $('#qty').prop('readonly',false);
               $("#maintenancePeriodDiv").hide();
               $("#maintenance_period").rules('remove');
               $("#maintenance_period_type").rules('remove');
               $("#maintenance_period").val('');
           }else{
               $('#qty').prop('readonly',false);
               $('#electricity_per_unit').rules('remove');
               $('#litre_per_unit').rules('remove');
               $('#espu').hide();
               $('#lpu').hide();
               $('#exp_date').hide();
               $('#exp_date').rules('remove');
               $("#maintenancePeriodDiv").hide();
               $("#maintenance_period_type").rules('remove');
               $("#maintenance_period_type").rules('remove');
               $("#maintenance_period").val('');
           }
        })
    </script>
@endsection
