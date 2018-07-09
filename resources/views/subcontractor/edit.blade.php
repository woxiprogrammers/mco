@extends('layout.master')
@section('title','Constro | Edit Subcontractor')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link href="/assets/global/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
    <input type="hidden" id="subcontractorId" value="{{$subcontractor['id']}}">
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
                                <h1>Edit Subcontractor</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/subcontractor/manage">Manage Subcontractor</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                        <a href="javascript:void(0);">Edit Subcontractor</a>
                                        <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                            <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <ul class="nav nav-tabs nav-tabs-lg">
                                            <li class="active">
                                                <a href="#generalInfoTab" data-toggle="tab"> General Information </a>
                                            </li>
                                            <li>
                                                <a href="#dprCategoryTab" data-toggle="tab"> Assign DPR Category </a>
                                            </li>
                                            <li>
                                                <a href="#advancePaymentTab" data-toggle="tab"> Advance Payment </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane fade in active" id="generalInfoTab">
                                                <form role="form" id="editSubcontractor" class="form-horizontal" method="post" action="/subcontractor/edit/{{$subcontractor['id']}}">
                                                    {!! csrf_field() !!}
                                                    <div class="form-body">
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="subcontractor_name" class="control-label">Subcontractor Name</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="subcontractor_name" name="subcontractor_name" required="required" value="{{$subcontractor['subcontractor_name']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="company_name" class="control-label">Company Name</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="company_name" name="company_name" required="required" value="{{$subcontractor['company_name']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="category" class="control-label">Category Name</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="category" name="category" value="{{$subcontractor['category']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="subcategory" class="control-label">Subcategory Name</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="subcategory" name="subcategory" value="{{$subcontractor['subcategory']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="desc_prod_service" class="control-label">Description of Service</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="desc_prod_service" name="desc_prod_service" value="{{$subcontractor['desc_prod_service']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="nature_of_work" class="control-label">Nature Of Work</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="nature_of_work" name="nature_of_work" value="{{$subcontractor['nature_of_work']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="sc_turnover_pre_yr" class="control-label">Turnover Per Year</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="sc_turnover_pre_yr" name="sc_turnover_pre_yr" value="{{$subcontractor['sc_turnover_pre_yr']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="sc_turnover_two_fy_ago" class="control-label">Turnover Two FY Ago</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="sc_turnover_two_fy_ago" name="sc_turnover_two_fy_ago" value="{{$subcontractor['sc_turnover_two_fy_ago']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="primary_cont_person_name" class="control-label">Primary Contact Person Name</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="primary_cont_person_name" name="primary_cont_person_name" value="{{$subcontractor['primary_cont_person_name']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="primary_cont_person_mob_number" class="control-label">Primary Contact Person Mobile No</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="primary_cont_person_mob_number" name="primary_cont_person_mob_number" value="{{$subcontractor['primary_cont_person_mob_number']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="primary_cont_person_email" class="control-label">Primary Contact Person Email</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="primary_cont_person_email" name="primary_cont_person_email" value="{{$subcontractor['primary_cont_person_email']}}">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="escalation_cont_person_name" class="control-label">Escalation Contact Person Name</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="escalation_cont_person_name" name="escalation_cont_person_name" value="{{$subcontractor['escalation_cont_person_name']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="escalation_cont_person_mob_number" class="control-label">Escalation Contact Person Mobile No</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="escalation_cont_person_mob_number" name="escalation_cont_person_mob_number" value="{{$subcontractor['escalation_cont_person_mob_number']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="sc_pancard_no" class="control-label">PAN Card Number</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="sc_pancard_no" name="sc_pancard_no" value="{{$subcontractor['sc_pancard_no']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="sc_service_no" class="control-label">Service Tax Number</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="sc_service_no" name="sc_service_no" value="{{$subcontractor['sc_service_no']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="sc_vat_no" class="control-label">VAT Number</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="sc_vat_no" name="sc_vat_no" value="{{$subcontractor['sc_vat_no']}}">
                                                            </div>
                                                        </div>
                                                        @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('edit-manage-user'))
                                                            <div class="form-actions noborder row">
                                                                <div class="col-md-offset-3" style="margin-left: 26%">
                                                                    <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
                                                                </div>
                                                            </div>
                                                        @endif

                                                    </div>
                                                </form>
                                            </div>
                                            <div class="tab-pane fade in" id="dprCategoryTab">
                                                <form role="form" id="assignDprCategoryForm" class="form-horizontal" method="post" action="/subcontractor/dpr/assign-categories/{{$subcontractor['id']}}">
                                                    {!! csrf_field() !!}
                                                    <div class="form-body">
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="subcontractor_name" class="control-label">Search DPR category :</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control typeahead" id="searchDprCategory">
                                                            </div>
                                                        </div>
                                                        @if(count($subcontractor->dprCategoryRelations) > 0)
                                                            <div class="row"  style="margin-top: 2%">
                                                        @else
                                                            <div class="row"  style="margin-top: 2%" hidden>
                                                        @endif
                                                            <div class="col-md-3">
                                                                <a class="btn blue pull-right" id="removeButton" >Remove DPR Category</a>
                                                            </div>
                                                        </div>
                                                        <div class="row"  style="margin-top: 0.5%">
                                                            <div class="col-md-8 col-md-offset-2">
                                                                @if(count($subcontractor->dprCategoryRelations) > 0)
                                                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dprCategoryTable">
                                                                @else
                                                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dprCategoryTable" hidden>
                                                                @endif
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="width: 10%;">Remove</th>
                                                                            <th> DPR Category</th>
                                                                        </tr>
                                                                    </thead>

                                                                    <tbody>
                                                                        @if(count($subcontractor->dprCategoryRelations) > 0)
                                                                            @foreach($subcontractor->dprCategoryRelations as $dprCategoryRelation)
                                                                                <tr>
                                                                                    <td style="width: 10%;">
                                                                                        <input type="checkbox" class="remove-category-checkbox">
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="hidden" name="dpr_categories[]" value="{{$dprCategoryRelation->dprMainCategory->id}}">
                                                                                        {{$dprCategoryRelation->dprMainCategory->name}}
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="form-actions noborder row">
                                                            <div class="col-md-offset-3" style="margin-left: 26%">
                                                                <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="tab-pane fade in" id="advancePaymentTab">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label class="control-label pull-right">Total Advance Paid Amount</label>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" value="{{$subcontractor->total_advance_amount}}" readonly>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="control-label pull-right">Balance Advance Amount</label>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control"  value="{{$subcontractor->balance_advance_amount}}" readonly>
                                                    </div>
                                                </div>
                                                <div class="btn-group pull-right margin-top-15">
                                                    <a id="sample_editable_1_new" class="btn yellow" href="#paymentModal" data-toggle="modal" >
                                                        <i class="fa fa-plus"></i>  &nbsp; Advance Payment
                                                    </a>
                                                </div>
                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="subcontractorAdvancePaymentTable">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 25%"> Date </th>
                                                        <th style="width: 25%"> Project Name </th>
                                                        <th style="width: 25%"> Amount </th>
                                                        <th style="width: 25%"> Payment Method </th>
                                                        <th style="width: 25%"> Reference Number </th>
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
<div class="modal fade " id="paymentModal"  role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="add_payment_form" action="/subcontractor/advance-payment/add" method="post">
                {!! csrf_field() !!}
                <input type="hidden" name="subcontractor_id" value="{{$subcontractor['id']}}">
                <div class="modal-header">
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4" style="font-size: 18px"> Payment</div>
                        <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                    </div>
                </div>

                <div class="modal-body" style="padding:40px 50px;">
                    <div class="form-group row" id="paidFromSlug">
                        <select class="form-control" id="paid_from_slug" name="paid_from_slug" onchange="changePaidFrom(this)">
                            <option value="bank">Bank</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>

                    <div class="form-group row">
                        <select class="form-control" id="project_site_id" name="project_site_id">
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{$project['id']}}">{{$project['name']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="bankData">
                        <div class="form-group row" id="bankSelect">
                            <select class="form-control" id="bank_id" name="bank_id">
                                <option value="">Select Bank</option>
                                @foreach($banks as $bank)
                                    <option value="{{$bank['id']}}">{{$bank['bank_name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group row">
                            <select class="form-control" name="payment_id">
                                @foreach($transaction_types as $type)
                                    <option value="{{$type['id']}}">{{$type['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <input type="hidden" id="allowedAmount">
                    <input type="hidden" id="cashAllowedAmount" value="{{$cashAllowedLimit}}">

                    @foreach($banks as $bank)
                        <input type="hidden" id="balance_amount_{{$bank['id']}}" value="{{$bank['balance_amount']}}">
                    @endforeach

                    <div class="form-group row">
                        <input type="number" class="form-control" id="bilAmount" name="amount" placeholder="Enter Amount" onkeyup="calculateTotal()">
                    </div>

                    <div class="form-group row">
                        <input type="number" class="form-control"  name="reference_number" placeholder="Enter Reference Number" >
                    </div>

                    <button class="btn btn-set red pull-right" type="submit">
                        <i class="fa fa-check" style="font-size: large"></i>
                        Add &nbsp; &nbsp; &nbsp;
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/custom/subcontractor/subcontractor.js" type="application/javascript"></script>
<script src="/assets/custom/subcontractor/subcontractor-advance-payment-datatable.js" type="application/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
<script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>

<script>
    $(document).ready(function() {
        EditSubcontractor.init();
        PaymentCreate.init();
        $("#removeButton").on('click',function(){
            if($("#dprCategoryTable tbody input:checkbox:checked").length > 0){
                $("#dprCategoryTable tbody input:checkbox:checked").each(function(){
                    $(this).closest('tr').remove();
                });
            }
            if($("#dprCategoryTable tbody input:checkbox").length <= 0){
                $("#removeButton").closest('.row').hide();
                $("#dprCategoryTable").hide();
            }
        });
        var citiList = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: "/subcontractor/dpr/auto-suggest/%QUERY",
                filter: function(x) {
                    if($(window).width()<420){
                        $("#header").addClass("fixed");
                    }
                    return $.map(x, function (data) {
                        return {
                            dpr_category_id:data.dpr_category_id,
                            dpr_category_name:data.dpr_category_name,
                        };
                    });
                },
                wildcard: "%QUERY"
            }
        });
        citiList.initialize();
        $('.typeahead').typeahead(null, {
            displayKey: 'name',
            engine: Handlebars,
            source: citiList.ttAdapter(),
            limit: 30,
            templates: {
                empty: [
                    '<div class="empty-suggest">',
                    'Unable to find any Result that match the current query',
                    '</div>'
                ].join('\n'),
                suggestion: Handlebars.compile('<div class="autosuggest"><strong>@{{dpr_category_name}}</strong></div>')
            },
        })
            .on('typeahead:selected', function (obj, datum) {
                var POData = $.parseJSON(JSON.stringify(datum));
                var trString = '<tr>' +
                    '           <td style="width: 10%;"><input type="checkbox" class="remove-category-checkbox"></td>\n' +
                    '           <td>'+POData.dpr_category_name+
                    '<input type="hidden" name="dpr_categories[]" value="'+POData.dpr_category_id+'">' +
                    '</td>' +
                    '</tr>';
                console.log(trString);
                $("#dprCategoryTable tbody").append(trString);
                $("#removeButton").closest('.row').show();
                $("#dprCategoryTable").show();
            })
            .on('typeahead:open', function (obj, datum) {

            });
    });

    function changePaidFrom(element){
        var paidFromSlug = $(element).val();
        if(paidFromSlug == 'cash'){
            $('#bankData').hide();
        }else{
            $('#bankData').show();
        }
    }

    function calculateTotal(){
        var bilAmount = parseFloat($('#bilAmount').val());
        if(typeof bilAmount == 'undefined' || bilAmount == '' || bilAmount == null || isNaN(bilAmount)){
            bilAmount = 0;
        }
        var paidFromSlug = $('#paid_from_slug').val();
        if(paidFromSlug == 'bank'){
            var selectedBankId = $('#bank_id').val();
            if(selectedBankId == ''){
                alert('Please select Bank');
            }else{
                var allowedAmount = parseFloat($('#balance_amount_'+selectedBankId).val());
                $("input[name='amount']").rules('add',{
                    max: allowedAmount
                });
            }
        }else{
            var cashAllowedAmount = parseFloat($('#cashAllowedAmount').val());
            $("input[name='amount']").rules('add',{
                max: cashAllowedAmount
            });
        }
    }
</script>
@endsection
