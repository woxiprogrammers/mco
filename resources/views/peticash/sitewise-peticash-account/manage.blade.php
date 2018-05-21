@extends('layout.master')
@section('title','Constro | Manage Sitewise Peticash Account')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
<style>
    .statistic-view-modal{
        position: fixed;
        width: 60%;
        top: 0;
        left: 0;
        right: 0;
        background-color: rgba(0,0,0,.2);
        z-index: 2;
        cursor: pointer;
        overflow: scroll;
    }
</style>
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
                            <div class="page-title">
                                <h1>Manage Sitewise Peticash Account</h1>
                            </div>
                            <div class="pull-right">

                                <div class="form-group " style="text-align: center">
                                    <a href="javascript:void(0);" class="btn yellow" id="statistics" >
                                        Statistics
                                    </a>
                                    @if($user->hasPermissionTo('create-sitewise-account') || ($user->roles[0]->role->slug == 'admin') || ($user->roles[0]->role->slug == 'superadmin'))
                                        <a href="createpage" id="sample_editable_1_new" class="btn yellow" style="margin: 20px">
                                            <i class="fa fa-plus" style="font-size: large"></i>&nbsp;
                                            Create
                                        </a>
                                    @endif
                                </div>

                            </div>
                            <!-- BEGIN PAGE TITLE -->
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
                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="sitewisePeticashTable">
                                                <thead>
                                                <tr>
                                                    <th> Transaction Id </th>
                                                    <th> From </th>
                                                    <th> To </th>
                                                    <th> Project Name </th>
                                                    <th> Amount </th>
                                                    <th> Type </th>
                                                    <th> Remark </th>
                                                    <th> Txn Date </th>
                                                    <th> Action </th>
                                                </tr>
                                                <tr class="filter">
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th> <input type="text" class="form-control form-filter" name="search_name"> </th>
                                                    <th></th>
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
                                                    <th colspan="4" style="text-align:right">Total Page Wise: </th>
                                                    <th></th>
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
                            <div class="modal fade" id="statisticsModel" role="dialog">
                                <div class="modal-dialog statistic-view-modal">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header" style="padding-bottom:10px">
                                            <div class="row">
                                                <div class="col-md-4 col-md-offset-5">
                                                    <h3><b>Statistics</b></h3>
                                                </div>
                                                <div class="col-md-3"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                            </div>
                                        </div>
                                        <div class="modal-body">
                                            {{--<div class="row">
                                                <div class="col-md-5 col-md-offset-1">
                                                    <label class="control-label pull-right">
                                                            Allocated Amount :
                                                    </label>
                                                </div>
                                                <div class="col-md-6">
                                                   <label class="control-label pull-left">
                                                           {{$allocatedAmount}}
                                                       </label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-5 col-md-offset-1">
                                                    <label class="control-label pull-right">
                                                        Balance Amount :
                                                    </label>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="control-label pull-left">
                                                        {{$remainingAmount}}
                                                    </label>
                                                </div>
                                            </div>--}}
                                            <table class="table table-striped table-bordered table-hover" id="projectTable">
                                                <thead>
                                                <th scope="col" style="width:250px !important">
                                                    Project
                                                </th>
                                                <th>
                                                    Allocated Amount
                                                </th>
                                                <th>
                                                    Balance Amount
                                                </th>
                                                </thead>
                                                <tbody>
                                                @foreach($statistics as $statistic)
                                                    <tr>
                                                        <td>
                                                            {{$statistic['project']}}
                                                        </td>
                                                        <td>
                                                            {{$statistic['allocatedAmount']}}
                                                        </td>
                                                        <td>
                                                            {{$statistic['remainingAmount']}}
                                                        </td>
                                                    </tr>
                                                @endforeach
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
<script src="/assets/custom/peticash/peticash.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $("#statistics").click(function(){
            $("#statisticsModel").modal('show');
        });
        sitewiseAccountListing.init();
        $('#sitewisePeticashTable').DataTable();
        $("input[name='search_name']").on('keyup',function(){
            $(".filter-submit").trigger('click');
        });
    });
</script>
@endsection
