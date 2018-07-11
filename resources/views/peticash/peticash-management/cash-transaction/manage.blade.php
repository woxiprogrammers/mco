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
                                                                    <th> Sr No </th>
                                                                    <th> Site </th>
                                                                    <th> Vendor/Subcontractor/Project Name </th>
                                                                    <th> Amount </th>
                                                                    <th> Date  </th>
                                                                </tr>
                                                                <tr class="filter">
                                                                    <th> </th>
                                                                    <th> </th>
                                                                    <th> <input type="text" class="form-control form-filter" name="search_name" id="search_name"> </th>
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
                                                                    <th colspan="3" style="text-align:right">Total Page Wise: </th>
                                                                    <th></th>
                                                                    <th colspan="1"></th>
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
            $("input[name='search_name']").on('keyup',function(){
                var search_name = $('#search_name').val();
                $("input[name='search_name']").val(search_name);
                $(".filter-submit").trigger('click');
            });
        });
    </script>
@endsection


