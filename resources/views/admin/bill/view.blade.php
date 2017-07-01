@extends('layout.master')
@section('title','Constro | Create Bill')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
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
                                <h1>View Bill</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container" style="width: 100%">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/bill/manage">Manage Bill</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">View Bill</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body">
                                        <input type="hidden" id="billId" value="{{$selectedBillId}}">
                                      <div class="tab-content">
                                        <div class="tab-pane fade in">
                                            @if($bills != NULL)
                                            <div class="col-md-offset-6 table-actions-wrapper" style="margin-bottom: 20px; text-align: right">
                                                <select class="table-group-action-input form-control input-inline input-small input-sm" name="change_bill" id="change_bill">
                                                    @for($i = 0 ; $i < count($bills); $i++)
                                                        <option value="{{$bills[$i]['id']}}">R.A Bill {{$i+1}}</option>
                                                    @endfor
                                                </select>
                                                @if($bill->bill_status->slug != 'paid')
                                                    <a class="btn green-meadow" id="approve" data-toggle="tab" href="#billApproveTab" style="margin-left: 10px">
                                                        Approve
                                                    </a>
                                                @endif
                                                <a href="/bill/current/invoice/{{$selectedBillId}}" class="btn btn-info btn-icon" style="margin-left: 10px">
                                                    <i class="fa fa-download"></i>
                                                    Current Bill
                                                </a>
                                                <a href="/bill/cumulative/invoice/{{$selectedBillId}}" class="btn btn-info btn-icon" style="margin-left: 10px">
                                                    <i class="fa fa-download"></i> Cumulative Bill
                                                </a>
                                            </div>
                                            @endif
                                            <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="createBillTable">
                                                <tr style="text-align: center">
                                                    <th width="3%"> Item no </th>
                                                    <th width="15%"> Item Description </th>
                                                    <th width="6%" class="numeric"> UOM </th>
                                                    <th width="6%" class="numeric"> Rate </th>
                                                    <th width="7%" class="numeric"> BOQ Quantity </th>
                                                    <th width="10%" class="numeric"> W.O Amount </th>
                                                    <th width="7%" class="numeric"> Previous Quantity </th>
                                                    <th width="7%" class="numeric"> Current Quantity </th>
                                                    <th width="10%" class="numeric"> Cumulative Quantity </th>
                                                    <th width="10%" class="numeric"> Previous. Bill Amount </th>
                                                    <th width="10%" class="numeric"> Current Bill Amount </th>
                                                    <th width="10%" class="numeric"> Cumulative Bill Amount </th>
                                                </tr>
                                                @for($iterator = 0; $iterator < count($billQuotationProducts); $iterator++)
                                                <tr>
                                                    <td>
                                                        <span id="quotation_product_id">{{$iterator + 1}}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{$billQuotationProducts[$iterator]['productDetail']['name']}}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{$billQuotationProducts[$iterator]['unit']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="rate_per_unit_{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}">{{$billQuotationProducts[$iterator]['rate']}}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{$billQuotationProducts[$iterator]['quotationProducts']['quantity']}}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{$billQuotationProducts[$iterator]['quotationProducts']['rate_per_unit'] * $billQuotationProducts[$iterator]['quotationProducts']['quantity']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="previous_quantity_{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}">{{$billQuotationProducts[$iterator]['previous_quantity']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="current_quantity_{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}">{{$billQuotationProducts[$iterator]['quantity']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="cumulative_quantity_{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}">{{$billQuotationProducts[$iterator]['cumulative_quantity']}}</span>
                                                    </td>
                                                    <td>
                                                        <span class="previous_bill_amount" id="previous_bill_amount_{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}">{{$billQuotationProducts[$iterator]['previous_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span class="current_bill_amount" id="current_bill_amount_{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}">{{$billQuotationProducts[$iterator]['current_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span class="cumulative_bill_amount" id="cumulative_bill_amount_{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}">{{$billQuotationProducts[$iterator]['cumulative_bill_amount']}}</span>
                                                    </td>
                                                </tr>
                                                @endfor
                                                <tr>
                                                    <td colspan="9" style="text-align: right; padding-right: 30px;"><b>Total</b></td>
                                                    <td>
                                                        <span id="total_previous_bill_amount">{{$total['previous_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="total_current_bill_amount">{{$total['current_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="total_cumulative_bill_amount">{{$total['cumulative_bill_amount']}}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="9" style="text-align: right; padding-right: 30px;"><b>Total Round</b></td>
                                                    <td>
                                                        <span id="rounded_off_previous_bill_amount">{{$total_rounded['previous_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="rounded_off_current_bill_amount">{{$total_rounded['current_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="rounded_off_cumulative_bill_amount">{{$total_rounded['cumulative_bill_amount']}}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5"><b>Tax Name</b></td>
                                                    <td colspan="4"><b>Tax Rate</b></td>
                                                    <td colspan="3"></td>
                                                </tr>
                                                @for($j = 0 ; $j < count($taxes); $j++)
                                                <tr>
                                                    <td colspan="5" style="text-align: center">{{$taxes[$j]['taxes']['name']}}</td>
                                                    <td colspan="4" style="text-align: center"><span id="percentage">{{abs($taxes[$j]['percentage'])}}</td>
                                                    <td>
                                                        <span id="tax_previous_bill_amount_{{$taxes[$j]['id']}}">{{$taxes[$j]['previous_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="tax_current_bill_amount_{{$taxes[$j]['id']}}">{{$taxes[$j]['current_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="tax_cumulative_bill_amount_{{$taxes[$j]['id']}}">{{$taxes[$j]['cumulative_bill_amount']}}</span>
                                                    </td>

                                                </tr>
                                                @endfor
                                                <tr>
                                                    <td colspan="9" style="text-align: right; padding-right: 30px;"><b>Final Total</b></td>
                                                    <td>
                                                        <span id="final_previous_bill_total">{{$final['previous_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="final_current_bill_total">{{$final['current_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="final_cumulative_bill_total">{{$final['cumulative_bill_amount']}}</span>
                                                    </td>
                                                </tr>

                                            </table>
                                        </div>
                                        <div class="tab-pane fade in active" id="billApproveTab">
                                            <form id="approve" action="/bill/approve" method="post">
                                                {!! csrf_field() !!}
                                                <input type="hidden" name="bill_id" value="{{$selectedBillId}}">
                                                <div class="col-md-offset-2">
                                                    <div class="form-group">
                                                        <div class="col-md-3">
                                                            <label for="remark" class="control-form pull-right">
                                                                Remark:
                                                            </label>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <textarea class="form-control" name="remark" id="remark"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12"> </div>
                                                        </div>
                                                        <div id="tab_images_uploader_container" class="col-md-offset-5">
                                                            <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow">
                                                                Browse</a>
                                                            <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
                                                                <i class="fa fa-share"></i> Upload Files </a>
                                                        </div>
                                                        <table class="table table-bordered table-hover" style="width: 700px">
                                                            <thead>
                                                            <tr role="row" class="heading">
                                                                <th> Image </th>
                                                                <th> Action </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="show-product-images">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-2 col-md-offset-4">
                                                            <button type="submit" class="btn btn-success">
                                                                Submit
                                                            </button>
                                                        </div>
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
                <input type="hidden" id="path" name="path" value="">
                <input type="hidden" id="max_files_count" name="max_files_count" value="20">
        </div>
    </div>
</div>
</div>

@endsection
@section('javascript')
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script>
<script src="/assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
<script src="/assets/custom/bill/image-datatable.js"></script>
<script src="/assets/custom/bill/image-upload.js"></script>
<script>
    $(document).ready(function (){
        $("#change_bill").on('change', function(){
            var bill_id = $(this).val();
            window.location.href = "/bill/view/"+bill_id;
        });
        $('select[name="change_bill"]').find('option[value={{$selectedBillId}}]').attr("selected",true);
    });
</script>
@endsection



