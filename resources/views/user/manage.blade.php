@extends('layout.master')
@section('title','Constro | Manage User')
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
                                <h1>Manage User</h1>
                            </div>
                            @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-manage-user'))
                                <div id="sample_editable_1_new" class="btn yellow" style="margin-left: 82%;margin-top: 1%">
                                    <a href="/user/create" style="color: white">
                                        <i class="fa fa-plus"></i> User
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="page-content">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                    <div class="portlet light ">
                                        {!! csrf_field() !!}
                                        <div class="portlet-body">
                                            <div class="table-toolbar">
                                                <div class="row" style="text-align: right">
                                                    <div class="col-md-12">
                                                        <div class="btn-group">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="userTable">
                                                <thead>
                                                <tr>
                                                    <th style="width:30%"> Name </th>
                                                    <th> Email </th>
                                                    <th> Contact Number </th>
                                                    <th> Role </th>
                                                    <th> Status </th>
                                                    <th> Created On </th>
                                                    <th> Actions </th>
                                                </tr>
                                                <tr class="filter">
                                                    <th style="width: 30%"> <input type="text" class="form-control form-filter" name="search_name"> </th>
                                                    <th> <input type="text" class="form-control form-filter" name="search_email"> </th>
                                                    <th> <input type="text" class="form-control form-filter" name="search_mobile"> </th>
                                                    <th> <input type="text" class="form-control form-filter" name="search_role"> </th>
                                                    <th> {{--<input type="text" class="form-control form-filter" readonly>--}} </th>
                                                    <th> {{--<input type="text" class="form-control form-filter" readonly>--}} </th>
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
<script src="/public/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="/assets/custom/user/user.js" type="application/javascript"></script>
<script src="/assets/custom/user/manage-datatable.js" type="application/javascript"></script>
<link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#userTable').DataTable();
        $(".form-filter").on('keyup',function(){
            $(".filter-submit").trigger('click');
        });
    });
</script>
@endsection
