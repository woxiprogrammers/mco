@extends('layout.master')
@section('title','Constro | Manage Bill')
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
                                <h1>Manage Bill</h1>
                            </div>
                            @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-billing'))
                                <div id="sample_editable_1_new" class="btn yellow" style="margin-left: 84%; margin-top: 1%">
                                    <a href="/bill/create" style="color: white">
                                        <i class="fa fa-plus"></i> Bill
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
                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="projectSiteTable">
                                                <thead>
                                                    <tr>
                                                        {{--<th width="10%"> Company Name </th>--}}
                                                        <th width="10%"> Project Name </th>
                                                        {{--<th width="10%"> Site Name </th>--}}
                                                        <th width="10%"> Type </th>
                                                        <th width="10%"> Bill Amount </th>
                                                        <th width="10%"> Paid Amount </th>
                                                        <th width="10%"> Balance Amount </th>
                                                        <th width="10%"> Actions </th>
                                                    </tr>
                                                    <tr class="filter">
                                                        <th> <input type="text" class="form-control form-filter" name="project_name" id="project_name"></th>
                                                        <th>
                                                            <div>
                                                                <select class="form-control form-filter" name="contract_type_id">
                                                                    <option value="">Select contract type</option>
                                                                    @foreach($contractTypes as $contractType)
                                                                        <option value="{{$contractType['id']}}"> {{$contractType['name']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </th>

                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th>
                                                            <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                            <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="2" style="text-align:right">Total Page Wise: </th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
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
<!--<script src="/assets/custom/bill/bill.js" type="application/javascript"></script>-->
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/custom/bill/manage-datatable.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#projectSiteTable').DataTable();
        
        $("#project_name").on('keyup',function(){
            if ($("#project_name").val().length > 3 ) {
                $(".filter-submit").trigger('click');
            }
        });

    });
</script>
@endsection
