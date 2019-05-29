@extends('layout.master')
@section('title','Constro | Manage Labour')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>

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
                                    <h1>Manage Employee</h1>
                                </div>
                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-manage-user'))
                                    <div class="btn-group" style="float: right;margin-top:1%">
                                        <div id="sample_editable_1_new" class="btn yellow" ><a href="/labour/create" style="color: white"> Employee
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </div>
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
                                                <table class="table table-striped table-bordered table-hover" id="labourTable">
                                                    <thead>
                                                    <tr>
                                                        <th> Employee Id </th>
                                                        <th> Profile Image </th>
                                                        <th> Employee Name </th>
                                                        <th> Contact No </th>
                                                        <th> Per Day wages </th>
                                                        <th> Monthly wages(30 days) </th>
                                                        <th> Project Name </th>
                                                        <th> Status </th>
                                                        <th> Actions </th>
                                                    </tr>
                                                    <tr class="filter">
                                                        <th style="width: 10%"> <input type="text" class="form-control form-filter" name="employee_id" id="employee_id"> </th>
                                                        <th style="width: 10%">  </th>
                                                        <th style="width: 20%"> <input type="text" class="form-control form-filter" name="employee_name" id="employee_name"> </th>
                                                        <th> <input type="text" class="form-control form-filter" name="employee_contact" id="employee_contact"> </th>
                                                        <th> <input type="text" class="form-control form-filter" name="employee_wages" id="employee_wages"> </th>
                                                        <th>
                                                            <input type="text" class="form-control form-filter" name="employee_monthly_wages" id="employee_monthly_wages" >
                                                            {{-- <input type="text" class="form-control form-filter" disabled="" >--}} 
                                                        </th>
                                                        <th> <input type="text" class="form-control form-filter" name="employee_project" id="employee_project"> </th>
                                                        <th style="width: 15%">
                                                            <select id="emp_status" name="emp_status" class="form-control form-filter" style="width: 80%;margin-left: 10%;">
                                                                <option value="1">Enabled</option>
                                                                <option value="0">Disabled</option>
                                                                <option value="2">All</option>
                                                            </select>
                                                            {{--<input type="text" class="form-control form-filter" name="search_created_on" readonly>--}} 
                                                        </th>
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
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/custom/labour/manage-datatable.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('#labourTable').DataTable();
            $(".form-filter").on('keyup',function(){
                $(".filter-submit").trigger('click');
            });

            $("#emp_status").on('change',function(){
                var employee_id = $('#employee_name').val();
                var employee_name = $('#employee_name').val();
                var employee_contact = $('#employee_contact').val();
                var employee_wages = $('#employee_wages').val();
                var employee_monthly_wages = $('#employee_monthly_wages').val();
                var employee_project = $('#employee_project').val();
                var emp_status = $('#emp_status').val();
                $("input[name='employee_id']").val(employee_id);
                $("input[name='employee_name']").val(employee_name);
                $("input[name='employee_contact']").val(employee_contact);
                $("input[name='employee_wages']").val(employee_wages);
                $("input[name='employee_monthly_wages']").val(employee_monthly_wages);
                $("input[name='employee_project']").val(employee_project);
                $("input[name='emp_status']").val(emp_status);
                $(".filter-submit").trigger('click');
            });
        });
    </script>
@endsection
