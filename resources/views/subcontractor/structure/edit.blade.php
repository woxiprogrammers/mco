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
<form role="form" id="editSubcontractor" class="form-horizontal" method="post" action="/subcontractor/subcontractor-structure/edit/{{$subcontractor_struct['id']}}">
    {!! csrf_field() !!}
    <div class="form-body">
        <div class="row form-group">
            <div class="col-md-3" style="text-align: right">
                <label for="description" class="control-label">Subcontractor Name : </label>
                <span>*</span>
            </div>
            <div class="col-md-3">
                <label class="control-label">{{$subcontractor[0]['subcontractor_name']}}</label>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: right">
                <label for="description" class="control-label">Description</label>
                <span>*</span>
            </div>
            <div class="col-md-6">
                <textarea class="form-control" id="description" name="description" readonly>{{$subcontractor_struct['description']}}</textarea>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: right">
                <label for="summary" class="control-label">Summary:</label>
                <span>*</span>
            </div>
            <div class="col-md-3">
                <div class="col-md-6">
                    <label class="control-label">{{$summary[0]['name']}}</label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: right">
                <label for="rate" class="control-label">Rate :</label>
                <span>*</span>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="rate" name="rate" value="{{$subcontractor_struct['rate']}}">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: right">
                <label for="total_work_area" class="control-label">Total Work Area :</label>
                <span>*</span>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="total_work_area" name="total_work_area" value="{{$subcontractor_struct['total_work_area']}}">
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
                    <input type="radio" name="structure_type" id="sqft" value="2"> SQFT
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
