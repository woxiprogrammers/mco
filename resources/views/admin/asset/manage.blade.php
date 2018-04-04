@extends('layout.master')
@section('title','Constro | Manage Asset')
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
                                    <h1>Manage Asset</h1>
                                </div>
                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-asset-management'))
                                    <div class="btn-group" style="float: right;margin-top:1%">
                                        <div id="sample_editable_1_new" class="btn yellow"><a href="/asset/create" style="color: white">                                         <i class="fa fa-plus"></i>
                                                Asset
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
                                                <div class="portlet-body">
                                                    <div class="table-container">
                                                        <table class="table table-striped table-bordered table-hover order-column" id="assetTable">
                                                            <thead>
                                                            <tr>
                                                                <th> Asset ID </th>
                                                                <th> Asset Name</th>
                                                                <th> Model Number</th>
                                                                <th> Qty </th>
                                                                <th> Price/Qty </th>
                                                                <th> Value (Qty*Price)</th>
                                                                <th> Rent Per Day </th>
                                                                <th> Asset Type </th>
                                                                <th> Status </th>
                                                                <th> Actions </th>
                                                            </tr>
                                                            <tr class="filter">
                                                                <th> </th>
                                                                <th> <input type="text" class="form-control form-filter" name="asset_name" id ="asset_name"> </th>
                                                                <th> </th>
                                                                <th> </th>
                                                                <th> </th>
                                                                <th> </th>
                                                                <th> </th>
                                                                <th> </th>
                                                                <th> </th>
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
                                                                    <th colspan="5" style="text-align:right">Total Page Wise:</th>
                                                                    <th colspan="5"></th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                    <div class="modal fade" id="remarkModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form class="modal-content">
                                                                <div class="modal-header" style="background-color:#00844d">
                                                                    <center><h4 class="modal-title" id="exampleModalLongTitle">ADD REMARK</h4></center>
                                                                    <button type="button" class="btn btn-warning pull-right" data-dismiss="modal"><i class="fa fa-close" style="font-size: medium"></i></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form role="form" class="form-horizontal" method="post">
                                                                        <div class="form-body">
                                                                            <div class="form-group row">
                                                                                <div class="col-md-3" style="text-align: right">
                                                                                    <label for="company" class="control-label">Remark</label>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <input type="text" class="form-control" id="remark" name="remark">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                <div class="modal-footer" style="background-color:#00844d">
                                                                    <button type="submit" class="btn blue">Approve</button>
                                                                    <button type="submit" class="btn blue">disapprove</button>
                                                                </div>
                                                            </form>
                                                        </div>
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
    <script src="/assets/custom/admin/asset/manage-datatable.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('#assetTable').DataTable();
            $("input[name='asset_name']").on('keyup',function(){
                $(".filter-submit").trigger('click');
            });
        });
    </script>
@endsection
