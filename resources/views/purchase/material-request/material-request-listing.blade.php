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
                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-material-request') || $user->customHasPermission('approve-material-request'))
                                    <div class="btn-group"  style="float: right;margin-top:1%">
                                        <div id="sample_editable_1_new" class="btn yellow" ><a href="/purchase/material-request/create" style="color: white">
                                                <i class="fa fa-plus"></i>
                                                Material Request
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
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" id="materialWiseListing" value=""><span style="color: salmon">Materialwise Listing</span>
                                                </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                                        </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" id="materialRequestWiseListing" value=""><span style="color: salmon">Material Requestwise Listing</span>
                                                </label>
                                                <hr/>
                                                <div class="portlet-body">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <label>Select Year :</label>
                                                            <select class="form-control" id="year" name="year">
                                                                <option value="0">ALL</option>
                                                                <option value="2017">2017</option>
                                                                <option value="2018">2018</option>
                                                                <option value="2019">2019</option>
                                                                <option value="2020">2020</option>
                                                                <option value="2021">2021</option>
                                                                <option value="2022">2022</option>
                                                                <option value="2023">2023</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label>Select Month :</label>
                                                            <select class="form-control" id="month" name="month">
                                                                <option value="0">ALL</option>
                                                                <option value="01">Jan</option>
                                                                <option value="02">Feb</option>
                                                                <option value="03">Mar</option>
                                                                <option value="04">Apr</option>
                                                                <option value="05">May</option>
                                                                <option value="06">Jun</option>
                                                                <option value="07">Jul</option>
                                                                <option value="08">Aug</option>
                                                                <option value="09">Sep</option>
                                                                <option value="10">Oct</option>
                                                                <option value="11">Nov</option>
                                                                <option value="12">Dec</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <label>MR Id :</label>
                                                            <input  class="form-control" type="number" id="mr_count" name="mr_count"/>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <label>&nbsp;</label>
                                                            <div class="btn-group">
                                                                <div id="search-withfilter" class="btn blue" >
                                                                    <a href="#" style="color: white"> Submit
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr/>
                                                    <div class="table-container">
                                                        <table class="table table-striped table-bordered table-hover table-checkable order-column" id="materialRequestWise">
                                                            <thead>
                                                            <tr>
                                                                <th style="width: 20%"> MR Id </th>
                                                                <th> Client Name </th>
                                                                <th> Project Name - Site Name  </th>
                                                                <th> Action </th>
                                                            </tr>
                                                            <tr class="filter">
                                                                <th><input type="text" class="form-control form-filter" name="mr_name" id="mr_name" readonly></th>
                                                                <th> <input type="hidden" class="form-control form-filter" name="postdata" id="postdata"></th>
                                                                <th> </th>
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
                                                    <div class="modal fade" id="remarkModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form class="modal-content">
                                                                <div class="modal-header">
                                                                    <div class="row">
                                                                        <div class="col-md-4"></div>
                                                                        <div class="col-md-4"><center><h4 class="modal-title" id="exampleModalLongTitle">REMARK</h4></center></div>
                                                                        <div class="col-md-4"><button type="button" class="btn btn-warning pull-right" data-dismiss="modal"><i class="fa fa-close" style="font-size: medium"></i></button></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-body">
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
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn blue">Approve</button>
                                                                    <button type="submit" class="btn blue">disapprove</button>
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
<script src="/assets/custom/purchase/manage-materialRequestWiseListing-datatable.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#materialRequestWise').DataTable();
        $('[data-toggle="tooltip"]').tooltip();
        $('#materialRequestWiseListing').attr ( "checked" ,"checked" );

        $('#materialWiseListing').click(function(){
            window.location.replace("/purchase/material-request/manage");
        });

        $("#search-withfilter").on('click',function(){
            var site_id = $('#globalProjectSite').val();
            var year = $('#year').val();
            var month = $('#month').val();
            var mr_name = $('#mr_name').val();
            var mr_count = $('#mr_count').val();
            var postData =
                    'site_id=>'+site_id+','+
                    'year=>'+year+','+
                    'month=>'+month+','+
                    'mr_count=>'+mr_count;

            $("input[name='postdata']").val(postData);
            $("input[name='mr_name']").val(mr_name);
            $(".filter-submit").trigger('click');
        });
    });
</script>
@endsection
