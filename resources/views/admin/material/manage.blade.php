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
                                <h1>Manage Material</h1>
                            </div>
                            @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-material'))
                                <div id="sample_editable_1_new" class="btn yellow" style="margin-left: 77%; margin-top: 1%">
                                    <a href="/material/create" style="color: white">
                                        <i class="fa fa-plus"></i>
                                        Material
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                    <div class="portlet light ">
                                        <div class="portlet-body">
                                            <div class="table-toolbar">
                                                <div class="row" style="text-align: right">
                                                    <div class="col-md-8">
                                                        <div class="btn-group">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                            {!! csrf_field() !!}
                                        @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-material'))
                                            <div class="col-md-2 pull-right">
                                                <a class="btn btn-success btn-md pull-right" id="changeStatusButton">
                                                    Change status
                                                </a>
                                            </div>
                                        @endif

                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="materialTable">
                                                <thead>
                                                <tr>
                                                    <th  style="width: 5%"> </th>
                                                    <th> Image</th>
                                                    <th> Category</th>
                                                    <th style="width: 25%"> Name </th>
                                                    <th> Rate </th>
                                                    <th> Unit </th>
                                                    <th> Status </th>
                                                    <th> Created On </th>
                                                    <th style="width: 15%"> Actions </th>
                                                </tr>
                                                <tr>
                                                    <th  style="width: 5%"> </th>
                                                    <th> </th>
                                                    <th > <input type="text" class="form-control form-filter" name="search_name_cat"> </th>
                                                    <th style="width: 25%"> <input type="text" class="form-control form-filter" name="search_name"> </th>
                                                    <th> <input type="text" class="form-control form-filter search_filter" name="search_rate"> </th>
                                                    <th> {{--<input type="text" class="form-control form-filter" name="search_unit" readonly>--}} </th>
                                                    <th> {{--<input type="text" class="form-control form-filter" name="search_status" readonly>--}} </th>
                                                    <th> {{--<input type="text" class="form-control form-filter" name="search_created_on" readonly>--}} </th>
                                                    <th>
                                                        <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                        <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    {{--<div class="portlet light ">
                                       <div class="portlet-body" style="margin-bottom: 8%">
                                            @if($categories != NULL)
                                                <form role="form" id="create-material" class="form-horizontal" action="/material/basicrate_material" method="post" novalidate="novalidate">
                                                    {!! csrf_field() !!}
                                                    <div class="form-body">
                                                        <div class="col-md-3">
                                                            <select class="form-control" id="material_category_ids" name="material_category_ids[]" multiple="true" style="overflow: scroll" aria-invalid="false">
                                                                @foreach ($categories as $category)
                                                                    <option value="{{$category['id']}}"> {{$category['name']}}</option>
                                                                @endforeach
                                                                <option value="all">All Categories</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="btn-group">
                                                                <div id="basicreate_material_dwn_id">
                                                                    <button type="submit" class="btn btn-success btn-md">
                                                                        <i class="fa fa-download"></i> PDF
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            @endif
                                        </div>
                                    </div>--}}
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
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/custom/admin/material/manage-datatable.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        SelectMaterial.init();
        $("#material_category_ids").rules('add',{
            required: true
        });
    });
    var  SelectMaterial = function () {
        var handleCreate = function() {
            var form = $('#create-material');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {

                },
                messages: {

                },
                invalidHandler: function (event, validator) { //display error alert on form submit
                    success.hide();
                    error.show();
                },
                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').addClass('has-error'); // set error class to the control group
                },
                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
                },
                success: function (label) {
                    label
                        .closest('.form-group').addClass('has-success');
                },
                submitHandler: function (form) {
                    $("button[type='submit']").prop('disabled', true);
                    success.show();
                    error.hide();
                    form.submit();
                }
            });
        }
        return {
            init: function () {
                handleCreate();
            }
        };
    }();
</script>
@endsection
