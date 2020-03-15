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
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                        <div class="portlet light ">
                                            {!! csrf_field() !!}
                                            <div class="portlet-body">
                                                <div class="table-container">
                                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="vendorMailTable">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:20%"> Seq. No. </th>
                                                                <th> Vendor/Client Name</th>
                                                                <th> Mailed For</th>
                                                                <th> Mail Sent Date</th>
                                                                <th> PDF </th>
                                                            </tr>
                                                            <tr>
                                                                <th  style="width: 5%"> </th>
                                                                <th style="width: 25%"> <input type="text" class="form-control form-filter" name="vendor_name" id="vendor_name"> </th>
                                                                <th>
                                                                    <select class="form-control" name="status" id="status">
                                                                        <option value="all">All</option>
                                                                        <option value="for-purchase-order">For Purchase Order</option>
                                                                        <option value="for-quotation">For Quotation</option>
                                                                    </select>
                                                                </th>
                                                                <th> <input type="hidden" class="form-control form-filter" name="status_id" id="status_id">  </th>
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
    <script src="/assets/custom/purchase/vendor-email-datatable.js"></script>
    <script>
        $(document).ready(function() {
            $("input[name='vendor_name']").on('keyup',function(){
                if($("input[name='vendor_name']").val().length > 3) {
                    $(".filter-submit").trigger('click');
                }
            });

            $("#status").on('change',function(){
                var status_id = $('#status').val();
                $("input[name='status_id']").val(status_id);
                $(".filter-submit").trigger('click');
            });
        });
    </script>
@endsection
