@extends('layout.master')
@section('title','Constro | Create Bill')
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
                                <h1>Create Bill</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/bill/manage">Manage Bill</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Create Bill</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-11">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <ul class="nav nav-tabs">
                                            @foreach($categories as $category)
                                            <li>
                                                <a href="#tab_category_{{$category['id']}}" data-toggle="tab" id="tab_price_a"> {{$category['name']}} </a>
                                            </li>
                                            @endforeach
                                            <li>
                                                <a href="#tab_tax" data-toggle="tab" id="tab_tax_a"> Taxes </a>
                                            </li>
                                        </ul>
                                        <form role="form" id="create-product" class="form-horizontal" action="/bill/create" method="post">
                                            {!! csrf_field() !!}
                                            <div class="tab-content">
                                                <div class="tab-pane fade in active" id="tab_category_{{$category['id']}}">
                                                    <fieldset>
                                                        <legend style="font-size: 20px;"><input type="checkbox" id="check_1">&nbsp;&nbsp;&nbsp;Product Nameee</legend>
                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label">Product Description</label>
                                                            <div class="col-md-3">
                                                                <input type="text" id="description" name="description" class="form-control" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label">Product Unit</label>
                                                            <div class="col-md-3">
                                                                <input type="text" id="unit" name="unit" class="form-control" value="KG" readonly>
                                                            </div>
                                                            <label class="col-md-2 control-label">Product Rate</label>
                                                            <div class="col-md-3">
                                                                <input type="text" id="unit" name="unit" class="form-control" value="123" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label">BOQ Quantity</label>
                                                            <div class="col-md-3">
                                                                <input type="text" id="unit" name="unit" class="form-control" readonly>
                                                            </div>
                                                            <label class="col-md-2 control-label">W.O Amount</label>
                                                            <div class="col-md-3">
                                                                <input type="text" id="unit" name="unit" class="form-control" value="123" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label">Previous Quantity</label>
                                                            <div class="col-md-3">
                                                                <input type="text" id="unit" name="unit" class="form-control" value="2" readonly>
                                                            </div>
                                                            <label class="col-md-2 control-label">Amount</label>
                                                            <div class="col-md-3">
                                                                <input type="text" id="unit" name="unit" class="form-control" value="123" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label">Current Quantity</label>
                                                            <div class="col-md-3">
                                                                <input type="text" id="unit" name="unit" class="form-control">
                                                            </div>
                                                            <label class="col-md-2 control-label">Amount</label>
                                                            <div class="col-md-3">
                                                                <input type="text" id="unit" name="unit" class="form-control" value="123" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label">Cumulative Quantity</label>
                                                            <div class="col-md-3">
                                                                <input type="text" id="unit" name="unit" class="form-control" readonly>
                                                            </div>
                                                            <label class="col-md-2 control-label">Cumulative Amount</label>
                                                            <div class="col-md-3">
                                                                <input type="text" id="unit" name="unit" class="form-control" value="123" readonly>
                                                            </div>
                                                        </div>

                                                    </fieldset>
                                                </div>
                                                <div class="tab-pane fade in active" id="tab_tax">
                                                    <fieldset>

                                                    </fieldset>
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
<script src="/assets/custom/admin/category/category.js" type="application/javascript"></script>
<script>
    $(document).ready(function() {
        console.log('here');
        console.log($('#company').val());
        //getProjects($('#company').val());

    function getProjects(client_id){
        $.ajax({
            url: '/bill/get-projects/'+client_id,
            type: 'GET',
            async : false,
            success: function(data,textStatus,xhr){
                if(xhr.status == 200){
                    console.log(data);
                }else{

                }
            },
            error: function(errorStatus,xhr){

            }
        });
    }
    });
</script>
@endsection
