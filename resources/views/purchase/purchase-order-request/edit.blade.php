<?php
/**
 * Created by Ameya Joshi.
 * Date: 10/1/18
 * Time: 5:01 PM
 */
?>
@extends('layout.master')
@section('title','Constro | Edit Purchase Order Request')
@include('partials.common.navbar')
@section('css')

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
                                    <h1>Edit Purchase Order Request</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/purchase/purchase-order-request/manage">Manage Purchase Order Request</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Edit Purchase Order Request</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <form role="form" id="editPurchaseOrderRequest" class="form-horizontal" method="post" action="/purchase/purchase-order-request/edit">
                                                {!! csrf_field() !!}
                                                <div class="panel-group accordion" id="accordion1">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading row" style="background-color: cornflowerblue">
                                                            <div class="pull-left" style="padding: 1% 1% 1%">
                                                                <input type="checkbox" onclick="accordionTitleSelect(this)">
                                                            </div>
                                                            <h4 class="panel-title" style="margin-left: 2%">
                                                                <a class="accordion-toggle accordion-toggle-styled" data-parent="#accordion3" href="#collapse_1_1" style="font-size: 16px;color: white">
                                                                    <b> Material 1 </b><br>
                                                                    100 KG
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapse_1_1" class="panel-collapse collapse">
                                                            <div class="panel-body" style="overflow:auto;">
                                                                <div class="form-group">
                                                                    <table class="table table-striped table-bordered table-hover">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>

                                                                                </th>
                                                                                <th>
                                                                                    Vendor Name
                                                                                </th>
                                                                                <th>
                                                                                    Rate w/o Tax
                                                                                </th>
                                                                                <th>
                                                                                    Rate w/ Tax
                                                                                </th>
                                                                                <th>
                                                                                    Total w/ Tax
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <input type="radio" name="checkbox">
                                                                                </td>
                                                                                <td>
                                                                                    Manisha Construction
                                                                                </td>
                                                                                <td>
                                                                                    100
                                                                                </td>
                                                                                <td>
                                                                                    120
                                                                                </td>
                                                                                <td>
                                                                                    12000
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <input type="radio"  name="checkbox">
                                                                                </td>
                                                                                <td>
                                                                                    Manisha Suppliers
                                                                                </td>
                                                                                <td>
                                                                                    100
                                                                                </td>
                                                                                <td>
                                                                                    105
                                                                                </td>
                                                                                <td>
                                                                                    10500
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <input type="radio" name="checkbox">
                                                                                </td>
                                                                                <td>
                                                                                    Woxi Construction
                                                                                </td>
                                                                                <td>
                                                                                    100
                                                                                </td>
                                                                                <td>
                                                                                    130
                                                                                </td>
                                                                                <td>
                                                                                    13000
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="panel-group accordion" id="accordion2">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading row" style="background-color: cornflowerblue">
                                                            <div class="pull-left" style="padding: 1% 1% 1%">
                                                                <input type="checkbox" onclick="accordionTitleSelect(this)">
                                                            </div>
                                                            <h4 class="panel-title" style="margin-left: 2%">
                                                                <a class="accordion-toggle accordion-toggle-styled" data-parent="#accordion3" href="#collapse_2_1" style="font-size: 16px;color: white">
                                                                    <b> Material 2 </b><br>
                                                                    150 Ltr.
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapse_2_1" class="panel-collapse collapse">
                                                            <div class="panel-body" style="overflow:auto;">
                                                                <div class="form-group">
                                                                    <table class="table table-striped table-bordered table-hover">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>

                                                                            </th>
                                                                            <th>
                                                                                Vendor Name
                                                                            </th>
                                                                            <th>
                                                                                Rate w/o Tax
                                                                            </th>
                                                                            <th>
                                                                                Rate w/ Tax
                                                                            </th>
                                                                            <th>
                                                                                Total w/ Tax
                                                                            </th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <input type="radio" name="checkbox">
                                                                            </td>
                                                                            <td>
                                                                                Manisha Construction
                                                                            </td>
                                                                            <td>
                                                                                100
                                                                            </td>
                                                                            <td>
                                                                                120
                                                                            </td>
                                                                            <td>
                                                                                12000
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <input type="radio"  name="checkbox">
                                                                            </td>
                                                                            <td>
                                                                                Manisha Suppliers
                                                                            </td>
                                                                            <td>
                                                                                100
                                                                            </td>
                                                                            <td>
                                                                                105
                                                                            </td>
                                                                            <td>
                                                                                10500
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <input type="radio" name="checkbox">
                                                                            </td>
                                                                            <td>
                                                                                Woxi Construction
                                                                            </td>
                                                                            <td>
                                                                                100
                                                                            </td>
                                                                            <td>
                                                                                130
                                                                            </td>
                                                                            <td>
                                                                                13000
                                                                            </td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="panel-group accordion" id="accordion3">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading row" style="background-color: cornflowerblue">
                                                            <div class="pull-left" style="padding: 1% 1% 1%">
                                                                <input type="checkbox" onclick="accordionTitleSelect(this)">
                                                            </div>
                                                            <h4 class="panel-title" style="margin-left: 2%">
                                                                <a class="accordion-toggle accordion-toggle-styled" data-parent="#accordion3" href="#collapse_3_1" style="font-size: 16px;color: white">
                                                                    <b> Material 3 </b><br>
                                                                    200 Bags
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapse_3_1" class="panel-collapse collapse">
                                                            <div class="panel-body" style="overflow:auto;">
                                                                <div class="form-group">
                                                                    <table class="table table-striped table-bordered table-hover">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>

                                                                            </th>
                                                                            <th>
                                                                                Vendor Name
                                                                            </th>
                                                                            <th>
                                                                                Rate w/o Tax
                                                                            </th>
                                                                            <th>
                                                                                Rate w/ Tax
                                                                            </th>
                                                                            <th>
                                                                                Total w/ Tax
                                                                            </th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <input type="radio" name="checkbox">
                                                                            </td>
                                                                            <td>
                                                                                Manisha Construction
                                                                            </td>
                                                                            <td>
                                                                                100
                                                                            </td>
                                                                            <td>
                                                                                120
                                                                            </td>
                                                                            <td>
                                                                                12000
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <input type="radio"  name="checkbox">
                                                                            </td>
                                                                            <td>
                                                                                Manisha Suppliers
                                                                            </td>
                                                                            <td>
                                                                                100
                                                                            </td>
                                                                            <td>
                                                                                105
                                                                            </td>
                                                                            <td>
                                                                                10500
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <input type="radio" name="checkbox">
                                                                            </td>
                                                                            <td>
                                                                                Woxi Construction
                                                                            </td>
                                                                            <td>
                                                                                100
                                                                            </td>
                                                                            <td>
                                                                                130
                                                                            </td>
                                                                            <td>
                                                                                13000
                                                                            </td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
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
@endsection
@section('javascript')
    <script>
        function accordionTitleSelect(element){
            if($(element).is(":checked") == true){
                $(element).closest('.panel-heading').find('.accordion-toggle').attr('data-toggle','collapse');
            }else{
                $(element).closest('.panel-heading').find('.accordion-toggle').removeAttr('data-toggle');
                $(element).closest('.panel-default').find('.panel-collapse').removeClass('in');
            }
        }

        $(document).ready(function(){

        });
    </script>
@endsection

