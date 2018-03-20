@extends('layout.master')
@section('title','Constro | Create Subcontractor Structure')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
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
                                    <h1>Create Subcontractor Structure</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container" style="width: 100%">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/subcontractor/subcontractor-structure/manage">Manage Subcontractor Structure</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Create Subcontractor Structure</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <form id="createSubcontractorStructure" class="form-horizontal" action="/subcontractor/subcontractor-structure/create" method="post">
                                                {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <div class="row form-group">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="description" class="control-label">Select Subcontractor : </label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <select class="form-control" id="subcontractor_id" name="subcontractor_id">
                                                                @foreach($subcontractor as $sc)
                                                                    <option value="{{$sc['id']}}">{{$sc['subcontractor_name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="description" class="control-label">Description : </label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <textarea class="form-control" id="description" name="description"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="summary_id" class="control-label">Select Summary : </label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <select class="form-control" id="summary_id" name="summary_id">
                                                                @foreach($summary as $sum)
                                                                    <option value="{{$sum['id']}}">{{$sum['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="rate" class="control-label">Rate :</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" id="rate" name="rate" onchange="calculateBillAmounts(this)">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="total_work_area" class="control-label">Total Work Area (Sq.Ft) :</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" id="total_work_area" name="total_work_area" value="0" onchange="calculateBillAmounts(this)">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="total_amount" class="control-label">Total Amount : </label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" id="total_amount" name="total_amount" value="0" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="total_amount_inwords" class="control-label">Total Amount in words : </label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <textarea class="form-control" id="total_amount_inwords" name="total_amount_inwords" value="0" readonly >
                                                            </textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="structure_type" class="control-label">Structure Type :</label>
                                                            <span>*</span>
                                                        </div>
                                                        &nbsp;&nbsp;&nbsp;
                                                        <div class="col-md-6 mt-radio-inline">
                                                            @foreach($ScStrutureTypes as $type)
                                                                <label class="mt-radio" style="margin-left: 13px">
                                                                    <input type="radio" name="structure_type" id="{{$type['id']}}" value="{{$type['slug']}}" onchange="structureType()"> {{$type['name']}}
                                                                    <span></span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" id="floor_div" hidden>
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="no_of_floors" class="control-label">No of Floors : </label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="text" class="form-control" id="no_of_floors" name="no_of_floors" {{--onkeyup="getBillTable()"--}}>
                                                        </div>
                                                    </div>
                                                    <hr/>
                                                    {{--<div id="billTable">
                                                        <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="parentBillTable" hidden>
                                                            <thead>
                                                            <tr id="tableHeader">
                                                                <th width="10%" style="text-align: center"> Bill No  </th>
                                                                <th width="30%" style="text-align: center"> Description </th>
                                                                <th width="15%" class="numeric" style="text-align: center"> Quantity </th>
                                                                <th width="15%" class="numeric" style="text-align: center"> Rate </th>
                                                                <th width="15%" class="numeric" style="text-align: center"> Amount </th>
                                                                <th width="10%" class="numeric" style="text-align: center"> Taxes </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr id="tableData" class="billRow">
                                                                    <td>
                                                                        <input type="text" class="form-control bill_no" id="bill_no_0" value="R.A.1" disabled>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control description" id="description_0">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control quantity" id="quantity_0" onkeyup="calculateSubtotal(this)">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control rate" id="rate_0" disabled>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control amount" id="amount_0">
                                                                    </td>
                                                                    <td>
                                                                        <a class="btn btn-xs blue tax-button" onclick="addTax(this)">Add Tax</a>
                                                                    </td>
                                                                </tr>
                                                            </tbody>

                                                        </table>
                                                    </div>--}}
                                                </div>
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red" id="labour_submit"><i class="fa fa-check"></i> Create Structure</button>
                                                    </div>
                                                </div>
                                            </form>

                                            <div class="modal fade" id="taxModal" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header" style="padding-bottom:10px">
                                                            <div class="row">
                                                                <div class="col-md-4"></div>
                                                                <div class="col-md-4">Add Taxes</div>
                                                                <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                            </div>
                                                        </div>
                                                        <form id="addTaxForm">
                                                            <input type="hidden" id="modalSubtotal">
                                                            <input type="hidden" id="billRowId">
                                                            <div class="modal-body" >



                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="taxes" id="taxDiv" hidden>
                                                @foreach($taxes as $tax)
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label class="control-label pull-right">{!! $tax['name'] !!}</label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="input-group" >
                                                                <input type="text" class="form-control tax-modal-name" id="tax_id[{!! $tax['slug'] !!}]" name="{{$tax['id']}}" onchange="calculateTaxes()" onkeyup="calculateTaxes()" value="{!! $tax['base_percentage'] !!}">
                                                                <span class="input-group-addon">%</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="text" class="form-control tax-modal-value" name="tax_amount_{{$tax['id']}}" readonly>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <div class="row form-group">
                                                    <div class="col-md-5">
                                                        <a href="javascript:void(0)" class="btn red pull-right" onclick="submitTaxForm()">Submit</a>
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
<script src="/assets/custom/subcontractor/subcontractor.js" type="application/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script><script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>

<script>
    $(document).ready(function() {
        CreateSubcontractorStructure.init();
        $("#rate").on('keyup', function(){
            var rate = $('#rate').val();
            var total_work_area = $('#total_work_area').val();
            var total_amount = rate*total_work_area;
            $('#total_amount').val(total_amount);
            $('#total_amount_inwords').val(number2text(total_amount));
        });

        $("#total_work_area").on('keyup', function(){
            var rate = $('#rate').val();
            var total_work_area = $('#total_work_area').val();
            var total_amount = rate*total_work_area;
            $('#total_amount').val(total_amount);
            $('#total_amount_inwords').val(number2text(total_amount));
        });

        $('#taxModal').on('hidden.bs.modal', function (e) {
            $("#taxModal .modal-body").html('');
        })
    });

    function number2text(value) {
        var fraction = Math.round(frac(value)*100);
        var f_text  = "";

        if(fraction > 0) {
            f_text = "AND "+convert_number(fraction)+" PAISE";
        }
        if (convert_number(value) == 'NUMBER OUT OF RANGE!') {
            return convert_number(value);
        } else {
            return convert_number(value)+" RUPEE "+f_text+" ONLY";
        }
    }

    function frac(f) {
        return f % 1;
    }

    function convert_number(number){
        if ((number < 0) || (number > 999999999)){
            return "NUMBER OUT OF RANGE!";
        }
        var Gn = Math.floor(number / 10000000);  /* Crore */
        number -= Gn * 10000000;
        var kn = Math.floor(number / 100000);     /* lakhs */
        number -= kn * 100000;
        var Hn = Math.floor(number / 1000);      /* thousand */
        number -= Hn * 1000;
        var Dn = Math.floor(number / 100);       /* Tens (deca) */
        number = number % 100;               /* Ones */
        var tn= Math.floor(number / 10);
        var one=Math.floor(number % 10);
        var res = "";

        if (Gn>0){
            res += (convert_number(Gn) + " CRORE");
        }
        if (kn>0){
            res += (((res=="") ? "" : " ") +
                convert_number(kn) + " LAKH");
        }
        if (Hn>0){
            res += (((res=="") ? "" : " ") +
                convert_number(Hn) + " THOUSAND");
        }
        if (Dn){
            res += (((res=="") ? "" : " ") +
                convert_number(Dn) + " HUNDRED");
        }


        var ones = Array("", "ONE", "TWO", "THREE", "FOUR", "FIVE", "SIX","SEVEN", "EIGHT", "NINE", "TEN", "ELEVEN", "TWELVE", "THIRTEEN","FOURTEEN", "FIFTEEN", "SIXTEEN", "SEVENTEEN", "EIGHTEEN","NINETEEN");
        var tens = Array("", "", "TWENTY", "THIRTY", "FOURTY", "FIFTY", "SIXTY","SEVENTY", "EIGHTY", "NINETY");

        if (tn>0 || one>0){
            if (!(res=="")){
                res += " AND ";
            }
            if (tn < 2){
                res += ones[tn * 10 + one];
            }else{
                res += tens[tn];
                if (one>0){
                    res += ("-" + ones[one]);
                }
            }
        }

        if (res==""){
            res = "zero";
        }
        return res;
    }

    function structureType(){
        structureTypeSlug = $("input[name='structure_type']:checked").val();
        if(structureTypeSlug == 'amountwise'){
            $("#floor_div").show();
        }else{
            $("#floor_div").hide();
            $("#billTable table").hide();
        }
    }

    function getBillTable(){
        /*var total_amount = $('#total_amount').val();
        var noOfFloor = parseInt($('#no_of_floors').val());
        var quantity = parseFloat(1 / noOfFloor);
        var amount = quantity * parseFloat(total_amount);
        $('#quantity_0').val(quantity);
        $('#amount_0').val(amount);

        $("#billTable table tbody tr:not('#tableData')").each(function(){
           $(this).remove();
        });
        for(var iterator = 1 ; iterator < noOfFloor; iterator++){
            var newClone = $("#tableData").clone().show().attr('id', 'id_'+ iterator);
            $(newClone).find('.bill_no').attr("id","bill_no_"+iterator);
            $(newClone).find('.bill_no').attr("name","bills["+iterator+"][bill_no]");
            $(newClone).find('.bill_no').attr("value","R.A. "+(iterator+1));
            $(newClone).find('.description').attr("id","description_"+iterator);
            $(newClone).find('.description').attr("name","bills["+iterator+"][description]");
            $(newClone).find('.quantity').attr("id","quantity_"+iterator);
            $(newClone).find('.quantity').attr("name","bills["+iterator+"][quantity]");
            $(newClone).find('.quantity').attr("value",quantity);
            $(newClone).find('.rate').attr("id","rate_"+iterator);
            $(newClone).find('.rate').attr("name","bills["+iterator+"][rate]");
            $(newClone).find('.rate').attr("value",$('#rate').val());
            $(newClone).find('.amount').attr("id","amount_"+iterator);
            $(newClone).find('.amount').attr("name","bills["+iterator+"][amount]");
            $(newClone).find('.amount').attr("value",amount);
            $("#billTable tbody").append(newClone);
        }
        $("#billTable table").show();*/
    }

    function calculateSubtotal(element){
        var total_amount = $('#total_amount').val();
        var noOfFloor = parseInt($('#no_of_floors').val());
        var rowId = $(element).attr('id');
        var row = rowId.match(/\d+/)[0];
        var currentRow = parseInt(row) + 1;
        var quantity = $('#quantity_'+row).val();
        var belowRowCount = noOfFloor - (currentRow);
        var aboveRowCount =  noOfFloor - (belowRowCount + 1);
        var aboveRowQuantityAssigned = 0;
        for(var aboveRowId = 0 ; aboveRowId < aboveRowCount ; aboveRowId++){
            aboveRowQuantityAssigned += parseFloat($('#quantity_'+aboveRowId).val());
        }
        var remainingQuantity = 1 - (aboveRowQuantityAssigned + parseFloat(quantity));
        var quantityToBeAssigned = remainingQuantity / belowRowCount;

        for(var iterator = 0 ; iterator <= belowRowCount ; iterator++){
            $('#quantity_'+currentRow).val(quantityToBeAssigned);
            var currentAmount = parseFloat(quantityToBeAssigned) * parseFloat(total_amount);
            if(isNaN(currentAmount)){
                console.log(3);
                $('#amount_'+currentRow).val(0);
            }else{
                console.log(4);
                $('#amount_'+currentRow).val(currentAmount);
            }
            currentRow++;
        }
        var amount = parseFloat(quantity) * parseFloat(total_amount);
        if(isNaN(amount)){
            $('#amount_'+row).val(0);
        }else{
            $('#amount_'+row).val(amount);
        }

    }

    function calculateBillAmounts(element){
        var total_amount = $('#total_amount').val();
        $(".billRow").each(function(){
            $(this).find('.rate').val(total_amount);
            var quantityElement = $(this).find('.quantity');
            calculateSubtotal(quantityElement);
        });
    }

    function addTax(element){
        var amount = $(element).closest('tr').find('.amount').val();
        var rowID = $(element).closest('tr').attr('id');
        $("#modalSubtotal").val(amount);
        $("#taxModal #billRowId").val(rowID);
        $("#addTaxForm .modal-body").html($("#taxDiv").clone().attr('id','').show());
        $("#taxModal").modal('show');
        var hiddenTaxLength = $(element).closest('tr').find(".tax-modal-name:input:hidden").length;
        if(hiddenTaxLength > 0){
            $(element).closest('tr').find(".tax-modal-name:input:hidden").each(function () {
                var taxId = $(this).attr('id');
                $("#addTaxForm").find("input[name='"+ taxId +"']").val($(this).val());
            });
        }
        calculateTaxes();
    }

    function calculateTaxes(){
        var subtotal = parseFloat($("#modalSubtotal").val());
        $(".tax-modal-name").each(function(){
            var taxPercentage = parseFloat($(this).val());
            var taxAmount = subtotal * (taxPercentage / 100);
            $(this).closest('.row').find('.tax-modal-value').val(taxAmount);
        });
    }
    
    function submitTaxForm(){
        var formData = $("#addTaxForm").serializeArray();
        var rowId = $("#taxModal #billRowId").val();
        $("#"+rowId+" .tax-modal-name").each(function(){
            $(this).remove();
        });
        if(rowId == 'tableData'){
            var iterator = 0;
        }else{
            var iterator = rowId.match(/\d+/)[0];
        }
        var row = $("#"+rowId+" td:first-child");
        $.each(formData, function(key, value){
            var className = $("input[name='"+ value.name +"']").attr('class');
            var taxId = value.name.match(/\d+/)[0];
            if(className.indexOf('tax-modal-value') != -1){
                var inputData = '<input type="hidden" id = "'+taxId+'" name="bills['+iterator+'][taxes]['+taxId+'][amount]" class="'+className+'" value="'+value.value+'">'
            }else{
                var inputData = '<input type="hidden" id = "'+taxId+'" name="bills['+iterator+'][taxes]['+taxId+'][percentage]" class="'+className+'" value="'+value.value+'">'
            }
            row.append(inputData);
        });
        $("#addTaxForm .modal-body").html('');
        $("#taxModal").modal('toggle');
    }

</script>
@endsection
