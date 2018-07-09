<?php
    /**
     * Created by Harsha.
     * User: harsha
     * Date: 6/7/18
     * Time: 3:37 PM
     */
    ?>

@extends('layout.master')
@section('title','Constro | Manage Cash Transaction')
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
                                    <h1>Manage Cash Transaction</h1>
                                </div>
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
                                                        <table class="table table-striped table-bordered table-hover order-column" id="cashTransactionManageTable">
                                                            <thead>
                                                                <tr>
                                                                    <th> ID </th>
                                                                    <th> Site </th>
                                                                    <th> Name </th>
                                                                    <th> Amount </th>
                                                                    <th> Date  </th>
                                                                </tr>
                                                                {{--<tr class="filter">--}}
                                                                    {{--<th> --}}{{--<input type="text" class="form-control form-filter" name="search_id" hidden>--}}{{-- </th>--}}
                                                                    {{--<th> --}}{{--<input type="text" class="form-control form-filter" name="search_name" id="search_name">--}}{{-- </th>--}}
                                                                    {{--<th> --}}{{--<input type="text" class="form-control form-filter" name="search_name" hidden>--}}{{-- </th>--}}
                                                                    {{--<th> --}}{{--<input type="text" class="form-control form-filter" name="search_type" hidden>--}}{{-- </th>--}}
                                                                    {{--<th> --}}{{--<input type="text" class="form-control form-filter" name="search_amount" hidden>--}}{{-- </th>--}}
                                                                {{--</tr>--}}
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                            {{--<tfoot>
                                                                <tr>
                                                                    <th colspan="4" style="text-align:right">Total Page Wise: </th>
                                                                    <th></th>
                                                                    <th colspan="5"></th>
                                                                </tr>
                                                            </tfoot>--}}
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
    </div>
@endsection
@section('javascript')
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/custom/peticash/cash-transaction-manage-datatable.js"></script>
    <link rel="stylesheet"  href="/assets/global/css/app.css"/>
    <script>
        $(document).ready(function(){
            cashTransactionListing.init();
        });
    </script>
@endsection


