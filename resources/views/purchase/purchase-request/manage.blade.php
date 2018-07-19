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
                                    <h1>Manage Purchase Request</h1>
                                </div>
                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-purchase-request') || $user->customHasPermission('create-purchase-request'))
                                    <div class="btn-group pull-right margin-top-15">
                                        <div id="sample_editable_1_new" class="btn yellow" ><a href="/purchase/purchase-request/create" style="color: white"> Purchase Request
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
                                                        <label>PR Id :</label>
                                                        <input  class="form-control" type="number" id="pr_count" name="pr_count"/>
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
                                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="purchaseRequestTable">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:20%;"> PR Id </th>
                                                                <th> Client Name</th>
                                                                <th> Project Name - Site Name </th>
                                                                <th> Created At</th>
                                                                <th> Status  </th>
                                                                <th> Material Status  </th>
                                                                <th> Action </th>
                                                            </tr>
                                                            <tr class="filter">
                                                                <th><input type="text" class="form-control form-filter" name="pr_name" id="pr_name"></th>
                                                                <th> <input type="hidden" class="form-control form-filter" name="postdata" id="postdata"></th>
                                                                <th> </th>
                                                                <th> </th>
                                                                <th>
                                                                    <select class="form-control" id="status_id" name="status_id">
                                                                            <option value="0">ALL</option>
                                                                            @foreach($purchaseStatus as $status)
                                                                                <option value="{{$status['id']}}">{{$status['name']}}</option>
                                                                            @endforeach
                                                                    </select>
                                                                    <input type="hidden" class="form-control form-filter" name="status" id="status">
                                                                </th>
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
                                            </div>
                                            <div class="modal fade" id="remarkModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form class="modal-content" method="post">
                                                        {!! csrf_field() !!}
                                                        <input type="hidden" name="purchaseRequestId" id="purchaseRequestId">
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
                                                            <a class="btn blue approve-modal-footer-buttons">Approve</a>
                                                            <a class="btn blue approve-modal-footer-buttons">Disapprove</a>
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
                <div class="modal fade" id="purchaseRequestDetailModel" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header" style="padding-bottom:10px">
                                <div class="row">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-6"><center><h4 class="modal-title" id="exampleModalLongTitle">Purchase Request Details</h4></center></div>
                                    <div class="col-md-3"><button type="button" class="close" data-dismiss="modal"><i class="fa fa-close" style="font-size: medium"></i></button></div>
                                </div>
                            </div>
                            <div class="modal-body" style="padding:40px 50px; font-size: 15px">

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
    <script src="/assets/custom/purchase/purchase-request/manage-datatable.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $(".approve-modal-footer-buttons").on('click',function(){
                var buttonType = $(this).text();
                if(buttonType == 'Approve'){
                    var action = "/purchase/purchase-request/change-status/approved";
                }else{
                    if(buttonType == 'Disapprove'){
                        var action = "/purchase/purchase-request/change-status/disapproved"
                    }
                }
                $(this).closest('form').attr('action',action);
                $(this).closest('form').submit();
            });

            $("#client_id").on('change', function(){
                getProjects($('#client_id').val());
            });
            $("#project_id").on('change', function(){
                getProjectSites($('#project_id').val());
            });


            $("#pr_name").on('keyup',function(){
                var site_id = $('#globalProjectSite').val();
                var year = $('#year').val();
                var month = $('#month').val();
                var status_id = $('#status_id').val();
                var pr_name = $('#pr_name').val();
                var pr_count = $('#pr_count').val();

                var postData =
                    'site_id=>'+site_id+','+
                    'year=>'+year+','+
                    'month=>'+month+','+
                    'pr_count=>'+pr_count;

                $("input[name='postdata']").val(postData);
                $("input[name='pr_name']").val(pr_name);
                $("input[name='status']").val(status_id);
                $(".filter-submit").trigger('click');
            });

            $("#status_id").on('change',function(){
                var site_id = $('#globalProjectSite').val();
                var year = $('#year').val();
                var month = $('#month').val();
                var status_id = $('#status_id').val();
                var pr_name = $('#pr_name').val();
                var pr_count = $('#pr_count').val();

                var postData =
                        'site_id=>'+site_id+','+
                        'year=>'+year+','+
                        'month=>'+month+','+
                        'pr_count=>'+pr_count;

                $("input[name='postdata']").val(postData);
                $("input[name='pr_name']").val(pr_name);
                $("input[name='status']").val(status_id);
                $(".filter-submit").trigger('click');
            });

            $("#search-withfilter").on('click',function(){
                var client_id = $('#client_id').val();
                var project_id = $('#project_id').val();
                var site_id = $('#site_id').val();
                var year = $('#year').val();
                var month = $('#month').val();
                var status_id = $('#status_id').val();
                var pr_name = $('#pr_name').val();
                var pr_count = $('#pr_count').val();

                var postData =
                    'client_id=>'+client_id+','+
                        'project_id=>'+project_id+','+
                        'site_id=>'+site_id+','+
                        'year=>'+year+','+
                        'month=>'+month+','+
                        'pr_count=>'+pr_count;

                $("input[name='postdata']").val(postData);
                $("input[name='pr_name']").val(pr_name);
                $("input[name='status']").val(status_id);
                $(".filter-submit").trigger('click');
            });
        });

        function openApproveModal(purchaseRequestId){
            $("#remarkModal #purchaseRequestId").val(purchaseRequestId);
            $("#remarkModal").modal('show');
        }

        function getProjects(client_id){
            $.ajax({
                url: '/purchase/projects/'+client_id,
                type: 'GET',
                async : false,
                success: function(data,textStatus,xhr){
                    if(xhr.status == 200){
                        $('#project_id').html(data);
                        $('#project_id').prop('disabled',false);
                        getProjectSites($('#project_id').val());
                    }
                },
                error: function(errorStatus,xhr){

                }
            });
        }

        function getProjectSites(project_id){
            $.ajax({
                url: '/purchase/project-sites/'+project_id,
                type: 'GET',
                async : false,
                success: function(data,textStatus,xhr){
                    if(xhr.status == 200){
                        $('#site_id').html(data);
                        $('#site_id').prop('disabled',false);
                    }
                },
                error: function(errorStatus,xhr){

                }
            });
        }

        function openDetails(purchaseRequestId){
            $.ajax({
                url: '/purchase/purchase-request/get-detail/'+purchaseRequestId+'?_token='+$("input[name='_token']").val(),
                type: 'GET',
                async: true,
                success: function(data,textStatus,xhr){
                    $("#purchaseRequestDetailModel .modal-body").html(data);
                    $("#purchaseRequestDetailModel").modal('show');
                },
                error:function(errorData){
                    alert(errorData);
                }

            });
        }
    </script>
@endsection
