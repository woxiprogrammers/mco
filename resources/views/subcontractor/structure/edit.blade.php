@extends('layout.master')
@section('title','Constro | Edit Subcontractor Structure')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link href="/assets/global/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css" />
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
            <h1>Edit Subcontractor Structure</h1>
        </div>
    </div>
</div>
<div class="page-content">
@include('partials.common.messages')
<div class="container">
<ul class="page-breadcrumb breadcrumb">
    <li>
        <a href="/subcontractor/subcontractor-structure/manage">Manage Subcontractor Structure</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
        <a href="javascript:void(0);">Edit Subcontractor Structure</a>
        <i class="fa fa-circle"></i>
    </li>
</ul>
<div class="col-md-12">
<!-- BEGIN VALIDATION STATES-->
<div class="portlet light ">
<div class="portlet-body form">
<form role="form" id="editSubcontractor" class="form-horizontal" method="post" action="/subcontractor/subcontractor-structure/edit/{{$labour['id']}}">
    {!! csrf_field() !!}
    <div class="form-body">
        <div class="row form-group">
            <div class="col-md-3">
                &nbsp;
            </div>
            <div class="col-md-2">
                <label>Select Client :</label>
                <select class="form-control" id="client_id" name="client_id">
                    @foreach($clients as $client)
                    <option value="{{$client['id']}}">{{$client['company']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Select Project :</label>
                <select class="form-control" id="project_id" name="project_id">
                </select>
            </div>
            <div class="col-md-2">
                <label>Select Site :</label>
                <select class="form-control" id="site_id" name="site_id">
                </select>
            </div>
            <div class="col-md-2">
                <label>Select Subcontractor :</label>
                <select class="form-control" id="year" name="subcontractor">
                    <option value="2017">SC1</option>
                    <option value="2018">SC2</option>
                    <option value="2019">SC3</option>
                    <option value="2020">SC4</option>
                    <option value="2021">Sc5</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: right">
                <label for="description" class="control-label">Description</label>
                <span>*</span>
            </div>
            <div class="col-md-6">
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: right">
                <label for="summary" class="control-label">Select Summary:</label>
                <span>*</span>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="year" name="summary">
                    <option value="2017">Summary 1</option>
                    <option value="2018">Summary 2</option>
                    <option value="2019">Summary 3</option>
                    <option value="2020">Summary 4</option>
                    <option value="2021">Summary 5</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: right">
                <label for="rate" class="control-label">Rate :</label>
                <span>*</span>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="rate" name="rate">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: right">
                <label for="total_work_area" class="control-label">Total Work Area :</label>
                <span>*</span>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="total_work_area" name="total_work_area">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: right">
                <label for="amount" class="control-label">Total Amount : </label>
                <span>*</span>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="total_amount" name="total_amount">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: right">
                <label for="structure_type" class="control-label">Structure Type :</label>
                <span>*</span>
            </div>
            &nbsp;&nbsp;&nbsp;
            <div class="col-md-6 mt-radio-inline">
                <label class="mt-radio" style="margin-left: 13px">
                    <input type="radio" name="structure_type" id="amountwise" value="1"> Amountwise
                    <span></span>
                </label>
                <label class="mt-radio">
                    <input type="radio" name="structure_type" id="areawise" value="2"> Areawise
                    <span></span>
                </label>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: right">
                <label for="no_of_floors" class="control-label">No of Floors : </label>
                <span>*</span>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" id="no_of_floors" name="no_of_floors">
            </div>
            <div class="col-md-2">
                <a id="next_btn" class="btn blue">Next</a>
            </div>
        </div>
        <hr/>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: center">
                <label>Bill No :</label>
            </div>
            <div class="col-md-3">
                <label>Description :</label>
            </div>
            <div class="col-md-2">
                <label>Quantity :</label>
            </div>
            <div class="col-md-2">
                <label>Rate :</label>
            </div>
            <div class="col-md-2">
                <label>Amount :</label>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: right">
                <input type="text" id="struct_bill_no" name="struct_bill_no" value="R.A 1" disabled>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="struct_desc" name="struct_desc">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" id="struct_qty" name="struct_qty">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" id="struct_rate" name="struct_rate">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" id="struct_amount" name="struct_amount">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: right">
                <input type="text" id="struct_bill_no" name="struct_bill_no" value="R.A 2" disabled>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="struct_desc" name="struct_desc">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" id="struct_qty" name="struct_qty">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" id="struct_rate" name="struct_rate">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" id="struct_amount" name="struct_amount">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: right">
                <input type="text" id="struct_bill_no" name="struct_bill_no" value="R.A 3" disabled>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="struct_desc" name="struct_desc">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" id="struct_qty" name="struct_qty">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" id="struct_rate" name="struct_rate">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" id="struct_amount" name="struct_amount">
            </div>
        </div>

    </div>
    <div class="form-actions noborder row">
        <div class="col-md-offset-3" style="margin-left: 26%">
            <button type="submit" class="btn red" id="labour_submit"><i class="fa fa-check"></i> Create Structure</button>
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
<script src="/assets/custom/subcontractor/subcontractor.js" type="application/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>

<script>
    $(document).ready(function() {
        EditSubcontractor.init();
    });
</script>
@endsection
