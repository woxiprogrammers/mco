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
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                        <div class="portlet-body flip-scroll">
                                            @if($bills != NULL)
                                                <div class="col-md-offset-7 table-actions-wrapper" style="margin-bottom: 20px">
                                                    <label class="control-label">Select Bill</label>
                                                    <select class="table-group-action-input form-control input-inline input-small input-sm" name="change_bill" id="change_bill">
                                                        @for($i = 0 ; $i < count($bills); $i++)
                                                            <option value="{{$bills[$i]['id']}}">Bill Array {{$i+1}}</option>
                                                        @endfor
                                                    </select>
                                                    <button class="btn btn-info btn-icon" style="margin-left: 50px">Download</button>

                                                </div>
                                            @endif
                                           <input type="hidden" id="project_id" name="project_id" value="{{$project_site['id']}}">
                                            <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="createBillTable">
                                                <tr>
                                                    <th width="1%">
                                                        <input type="checkbox" class="group-checkable" disabled="disabled" >
                                                    </th>
                                                    <th width="10%"> Item no </th>
                                                    <th width="90%" style="text-align: center"> Item Description </th>
                                                    <th width="40%" class="numeric"> UOM </th>
                                                    <th width="30%" class="numeric"> Rate </th>
                                                    <th width="30%" class="numeric"> BOQ Quantity </th>
                                                    <th width="30%" class="numeric"> W.O Amount </th>
                                                    <th width="30%" class="numeric"> Previous Quantity </th>
                                                    <th width="30%" class="numeric"> Current Quantity </th>
                                                    <th width="40%" class="numeric"> Cumulative Quantity </th>
                                                    <th width="40%" class="numeric"> Previous. Bill Amount </th>
                                                    <th width="40%" class="numeric"> Current Bill Amount </th>
                                                    <th width="40%" class="numeric"> Cumulative Bill Amount </th>

                                                </tr>
                                                @for($iterator = 0; $iterator < count($quotationProducts); $iterator++)
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" id="id_{{$quotationProducts[$iterator]['id']}}" name="id_{{$quotationProducts[$iterator]['id']}}" value="{{$quotationProducts[$iterator]['id']}}" onclick="selectedProducts({{$quotationProducts[$iterator]['id']}})">
                                                        </td>
                                                        <td>
                                                            <span>{{$quotationProducts[$iterator]['id']}}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{$quotationProducts[$iterator]['product_detail']['name']}}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{$quotationProducts[$iterator]['unit']}}</span>
                                                        </td>
                                                        <td>
                                                            <span id="rate_per_unit_{{$quotationProducts[$iterator]['id']}}">{{$quotationProducts[$iterator]['rate_per_unit']}}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{$quotationProducts[$iterator]['quantity']}}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{$quotationProducts[$iterator]['rate_per_unit'] * $quotationProducts[$iterator]['quantity']}}</span>
                                                        </td>
                                                        <td>
                                                            <span id="previous_quantity_{{$quotationProducts[$iterator]['id']}}">0</span>
                                                        </td>
                                                        <td>
                                                            <input class="form-control" type="text" id="current_quantity_{{$quotationProducts[$iterator]['id']}}" name="current_quantity" disabled>
                                                        </td>
                                                        <td>
                                                            <span id="cumulative_quantity_{{$quotationProducts[$iterator]['id']}}"></span>
                                                        </td>
                                                        <td>
                                                            <span id="previous_bill_amount_{{$quotationProducts[$iterator]['id']}}"></span>
                                                        </td>
                                                        <td>
                                                            <span id="current_bill_amount_{{$quotationProducts[$iterator]['id']}}"></span>
                                                        </td>
                                                        <td>
                                                            <span id="cumulative_bill_amount_{{$quotationProducts[$iterator]['id']}}"></span>
                                                        </td>
                                                    </tr>
                                                @endfor
                                                    <tr>
                                                        <td colspan="10" style="text-align: right; padding-right: 30px;">Total</td>
                                                        <td>
                                                            <span id="total_previous_bill_amount"></span>
                                                        </td>
                                                        <td>
                                                            <span id="total_current_bill_amount"></span>
                                                        </td>
                                                        <td>
                                                            <span id="total_cumulative_bill_amount"></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="10" style="text-align: right; padding-right: 30px;">Total Round</td>
                                                        <td>
                                                            <span id="rounded_off_previous_bill_amount"></span>
                                                        </td>
                                                        <td>
                                                            <span id="rounded_off_current_bill_amount"></span>
                                                        </td>
                                                        <td>
                                                            <span id="rounded_off_cumulative_bill_amount"></span>
                                                        </td>
                                                    </tr>
                                                <tr>
                                                    <td colspan="6" >Tax Name</td>
                                                    <td colspan="4">Tax Rate</td>
                                                    <td colspan="3"></td>
                                                </tr>
                                                @for($j = 0 ; $j < count($taxes); $j++)
                                                     <tr>
                                                         <td colspan="6" style="text-align: center">{{$taxes[$j]['name']}}</td>
                                                         <td colspan="4" style="text-align: right"><input class="form-control" type="number" id="tax_percentage_{{$taxes[$j]['id']}}" name="tax_percentage_{{$taxes[$j]['id']}}" value="{{$taxes[$j]['base_percentage']}}"></td>
                                                         <td>
                                                             <span id="tax_previous_bill_amount_{{$taxes[$j]['id']}}"></span>
                                                         </td>
                                                         <td>
                                                             <span id="tax_current_bill_amount_{{$taxes[$j]['id']}}"></span>
                                                         </td>
                                                         <td>
                                                             <span id="tax_cumulative_bill_amount_{{$taxes[$j]['id']}}"></span>
                                                         </td>

                                                     </tr>
                                                @endfor
                                                <tr>
                                                    <td colspan="10" style="text-align: right; padding-right: 30px;">Final Total</td>
                                                    <td>
                                                        <span id="final_previous_bill_total"></span>
                                                    </td>
                                                    <td>
                                                        <span id="final_current_bill_total"></span>
                                                    </td>
                                                    <td>
                                                        <span id="final_cumulative_bill_total"></span>
                                                    </td>

                                                </tr>

                                            </table>
                                            <div class="form-group">
                                                <div class="col-md-offset-11">
                                                    <button type="submit" class="btn btn-success"> Submit </button>
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
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/custom/bill/bill-manage-datatable.js" type="text/javascript"></script>
<script>
    function selectedProducts(id){
        $('input[name="id_'+id+'"]:checked').each(function(){
            $('#current_quantity_'+id).prop('disabled',false);
            var typingTimer;
            var doneTypingInterval = 500;
            var input = $('#current_quantity_'+id);
            input.on('keyup', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(doneTyping, doneTypingInterval);
            });
            input.on('keydown', function () {
                clearTimeout(typingTimer);
            });
            function doneTyping () {
                getcal(input.val(),id);
        }
        });
    }

    function getcal(current_quantity,id){
        var total = 0;
        var cumulative_quantity = parseFloat($('#previous_quantity_'+id).text()) + parseFloat(current_quantity);
        var prev_bill_amount = parseFloat($('#previous_quantity_'+id).text()) * parseFloat($('#rate_per_unit_'+id).text());
        var current_bill_amount = parseFloat(current_quantity) * parseFloat($('#rate_per_unit_'+id).text());
        var cumulative_bill_amount = prev_bill_amount + current_bill_amount;
        $('#cumulative_quantity_'+id).text(cumulative_quantity);
        $('#previous_bill_amount_'+id).text(prev_bill_amount);
        $('#current_bill_amount_'+id).text(current_bill_amount);
        $('#cumulative_bill_amount_'+id).text(cumulative_bill_amount);
        getTotal();
    }

    function getTotal(){
        var total_previous_bill_amount = 0;
        var total_current_bill_amount = 0;
        var total_cumulative_bill_amount = 0;
        var selected_product_length = $('input:checked').length;
        if(selected_product_length > 0){
            $('input:checked').each(function(){
                var id = $(this).val();

                var previous_bill_amount = parseFloat($('#previous_bill_amount_'+id).text());
                total_previous_bill_amount = total_previous_bill_amount + previous_bill_amount;
                $('#total_previous_bill_amount').text(total_previous_bill_amount);

                var current_bill_amount = parseFloat($('#current_bill_amount_'+id).text());
                total_current_bill_amount = total_current_bill_amount + current_bill_amount;
                $('#total_current_bill_amount').text(total_current_bill_amount);

                var cumulative_bill_amount = parseFloat($('#cumulative_bill_amount_'+id).text());
                total_cumulative_bill_amount = total_cumulative_bill_amount + cumulative_bill_amount;
                $('#total_cumulative_bill_amount').text(total_cumulative_bill_amount);
            });
        }
    }

    $(document).ready(function (){
        $('input[type="checkbox"]').click(function(){
            if($(this).prop("checked") == false){
                var id = $(this).val();
                $('#current_quantity_'+id).prop('disabled',true);
                $('#current_quantity_'+id).val('');
                $('#cumulative_quantity_'+id).text("");
                $('#previous_bill_amount_'+id).text("");
                $('#current_bill_amount_'+id).text("");
                $('#cumulative_bill_amount_'+id).text("");
                getTotal();
            }
        });
    });

</script>
@endsection



