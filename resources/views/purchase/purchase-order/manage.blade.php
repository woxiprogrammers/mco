@extends('layout.master')
@section('title','Constro | Manage Purchase Order')
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
                                    <h1>Manage Purchase Order</h1>
                                </div>
                                {{--@if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-purchase-order'))
                                    <div class="btn-group pull-right margin-top-15">
                                        <div id="sample_editable_1_new" class="btn yellow" ><a href="/purchase/purchase-order/create" style="color: white"> <i class="fa fa-plus"></i>  &nbsp; Purchase Order
                                            </a>
                                        </div>
                                    </div>
                                @endif--}}
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
                                                            <label>PO Id :</label>
                                                            <input  class="form-control" type="number" id="po_count" name="po_count"/>
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
                                                        <table class="table table-striped table-bordered table-hover order-column" id="purchaseOrder">
                                                            <thead>
                                                            <tr>
                                                                <th style="width:15%;"> PO Id </th>
                                                                <th style="width:10%;"> PR Id </th>
                                                                <th style="width:10%;"> PO Status </th>
                                                                <th style="width:15%;"> Vendor Company Name </th>
                                                                <th style="width:5%;">Vendor Mobile No</th>
                                                                <th style="width:5%;"> Approved Quantity</th>
                                                                <th style="width:5%;"> Received  Quantity</th>
                                                                <th style="width:10%;"> Status </th>
                                                                <th style="width:10%;"> Created At</th>
                                                                <th style="width:15%;"> Action </th>
                                                            </tr>
                                                            <tr class="filter">
                                                                <th><input type="text" class="form-control form-filter" name="po_id"></th>
                                                                <th><input type="hidden" class="form-control form-filter" name="postdata" id="postdata"></th>
                                                                <th>
                                                                    <select class="form-control" id="po_status_id" name="po_status_id">
                                                                        @foreach($po_status as $status)
                                                                        <option value="{{$status['id']}}">{{$status['name']}}</option>
                                                                        @endforeach
                                                                        <option value="0">ALL</option>
                                                                    </select>
                                                                    <input type="hidden" class="form-control form-filter" name="po_status" id="po_status">
                                                                </th>
                                                                <th><input type="text" class="form-control form-filter" name="vendor_name"></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th>
                                                                    <select class="form-control" id="status_id" name="status_id">
                                                                        <option value="0">ALL</option>
                                                                        <option value="1">Approve</option>
                                                                        <option value="2">Disapprove</option>
                                                                    </select>
                                                                    <input type="hidden" class="form-control form-filter" name="status" id="status">
                                                                </th>
                                                                <th></th>
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
                    <div class="modal-body" style="padding:10px 10px; font-size: 15px">

                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="purchaseOrderDetailModel" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header" style="padding-bottom:10px">
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6"><center><h4 class="modal-title" id="exampleModalLongTitle">Purchase Order Details</h4></center></div>
                            <div class="col-md-3"><button type="button" class="close" data-dismiss="modal"><i class="fa fa-close" style="font-size: medium"></i></button></div>
                        </div>
                    </div>
                    <div class="modal-body" style="padding:10px 10px; font-size: 15px">

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
    <script src="/assets/custom/purchase/purchase-order/manage-datatables.js" type="text/javascript"></script>
    <script>

        $(document).ready(function() {
            $('#purchaseOrder').DataTable();

            $("input[name='po_id']").on('keyup',function(){
                $(".filter-submit").trigger('click');
            });

            $("input[name='vendor_name']").on('keyup',function(){
                $(".filter-submit").trigger('click');
            });

            $("#status_id").on('change',function(){
                var site_id = $('#globalProjectSite').val();
                var year = $('#year').val();
                var month = $('#month').val();
                var status_id = $('#status_id').val();
                var po_id = $('#po_id').val();
                var po_count = $('#po_count').val();
                var vendor_name = $('#vendor_name').val();
                var postData =
                        'site_id=>'+site_id+','+
                        'year=>'+year+','+
                        'month=>'+month+','+
                        'po_count=>'+po_count;

                $("input[name='postdata']").val(postData);
                $("input[name='po_id']").val(po_id);
                $("input[name='vendor_name']").val(vendor_name);
                $("input[name='status']").val(status_id);
                $(".filter-submit").trigger('click');
            });

            $("#po_status_id").on('change',function(){
                var site_id = $('#globalProjectSite').val();
                var year = $('#year').val();
                var month = $('#month').val();
                var status_id = $('#status_id').val();
                var po_status_id = $('#po_status_id').val();
                var po_id = $('#po_id').val();
                var po_count = $('#po_count').val();
                var vendor_name = $('#vendor_name').val();
                var postData =
                        'site_id=>'+site_id+','+
                        'year=>'+year+','+
                        'month=>'+month+','+
                        'po_count=>'+po_count;

                $("input[name='postdata']").val(postData);
                $("input[name='po_id']").val(po_id);
                $("input[name='vendor_name']").val(vendor_name);
                $("input[name='status']").val(status_id);
                $("input[name='po_status']").val(po_status_id);
                $(".filter-submit").trigger('click');
            });

            $("#search-withfilter").on('click',function(){
                var site_id = $('#globalProjectSite').val();
                var year = $('#year').val();
                var month = $('#month').val();
                var status_id = $('#status_id').val();
                var po_status_id = $('#po_status_id').val();
                var po_id = $('#po_id').val();
                var po_count = $('#po_count').val();
                var vendor_name = $('#vendor_name').val();
                var postData =
                        'site_id=>'+site_id+','+
                        'year=>'+year+','+
                        'month=>'+month+','+
                        'po_count=>'+po_count;

                $("input[name='postdata']").val(postData);
                $("input[name='po_id']").val(po_id);
                $("input[name='vendor_name']").val(vendor_name);
                $("input[name='status']").val(status_id);
                $("input[name='po_status']").val(po_status_id);
                $(".filter-submit").trigger('click');
            });
        });

        function openPurchaseRequestDetails(purchaseRequestId){
            $.ajax({
                url: '/purchase/purchase-request/get-detail/'+purchaseRequestId+'?_token='+$("input[name='_token']").val(),
                type: 'GET',
                async: true,
                success: function(data,textStatus,xhr){
                    $("#purchaseRequestDetailModel .modal-body").html(data);
                    $("#purchaseRequestDetailModel").modal('show');
                },
                error:function(errorData){
                    alert("Something went wrong");
                }

            });
        }

        function openPurchaseOrderDetails(purchaseOrderId){
            $.ajax({
                url: '/purchase/purchase-order/get-detail/'+purchaseOrderId+'?_token='+$("input[name='_token']").val(),
                type: 'GET',
                async: true,
                success: function(data,textStatus,xhr){
                    $("#purchaseOrderDetailModel .modal-body").html(data);
                    $("#purchaseOrderDetailModel").modal('show');
                },
                error:function(errorData){
                    alert("Something went wrong");
                }

            });
        }

    </script>
@endsection
