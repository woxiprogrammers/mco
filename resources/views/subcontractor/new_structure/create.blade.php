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
                                        <a href="/subcontractor/structure/manage">Manage Subcontractor Structure</a>
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
                                            <form id="createSubcontractorStructure" class="form-horizontal" action="/subcontractor/structure/create" method="post">
                                                {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <div class="row">
                                                        <div class="col-md-offset-9 col-md-3">
                                                            <button type="submit" class="btn red pull-right" id="labour_submit"><i class="fa fa-check"></i> Create Structure</button>
                                                        </div>
                                                    </div>

                                                    <ul class="nav nav-tabs nav-tabs-lg">
                                                        <li class="active">
                                                            <a href="#generalTab" data-toggle="tab">General</a>
                                                        </li>
                                                        <li>
                                                            <a href="#extraItemsTab" data-toggle="tab">Extra Items</a>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content">
                                                        <div class="tab-pane fade in active" id="generalTab">
                                                            <div class="row form-group">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label for="description" class="control-label">Select Subcontractor : </label>
                                                                    <span>*</span>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <select class="form-control" id="subcontractor_id" name="subcontractor_id">
                                                                        <option value="">Please select subcontractor</option>
                                                                        @foreach($subcontractors as $subcontractor)
                                                                            <option value="{{$subcontractor['id']}}">{{$subcontractor['subcontractor_name']}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label for="structure_type" class="control-label">Structure Type :</label>
                                                                    <span>*</span>
                                                                </div>                                                        &nbsp;&nbsp;&nbsp;
                                                                <div class="col-md-6 mt-radio-inline">
                                                                    @foreach($ScStrutureTypes as $type)
                                                                        <label class="mt-radio" style="margin-left: 13px">
                                                                            <input type="radio" name="structure_type" id="{{$type['id']}}" value="{{$type['slug']}}" onchange="structureTypeChange()"> {{$type['name']}}
                                                                            <span></span>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <fieldset id="summariesFieldset" hidden>
                                                                <legend>
                                                                    Summaries
                                                                    <a class="btn yellow btn-md col-md-offset-8" href="javascript:void(0);" id="addSummaryBtn" onclick="addSummary()">
                                                                        <i class="fa fa-plus"></i>
                                                                        Summary
                                                                    </a>
                                                                </legend>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <table class="table table-striped table-bordered table-hover" id="summaryTable">
                                                                            <thead>
                                                                            <tr>
                                                                                <th style="width: 15%"> Summary </th>
                                                                                <th style="width: 20%"> Description </th>
                                                                                <th style="width: 15%"> Rate </th>
                                                                                <th style="width: 10%"> Unit </th>
                                                                                <th style="width: 10%"> Work Area (Sq.ft.)</th>
                                                                                <th style="width: 15%"> Total Amount </th>
                                                                                <th style="width: 25%"> Total Amount (Words)</th>
                                                                                <th style="width: 5%"> Action </th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <select class="summary form-control" onchange="onSummaryChange(this)" name="summaries[]">
                                                                                        @foreach($summaries as $summary)
                                                                                            <option value="{{$summary['id']}}"> {{$summary['name']}} </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="form-group" style="width: 90%; margin-left: 5%">
                                                                                        <textarea class="form-control description" rows="3"></textarea>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="form-group" style="width: 90%; margin-left: 5%">
                                                                                        <input type="text" class="form-control rate" onkeyup="rateKeyUp(this)" min="1" required>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="form-group" style="width: 90%; margin-left: 5%">
                                                                                        <select  class="unit form-control" required>
                                                                                            <option value="">
                                                                                                Select Unit
                                                                                            </option>
                                                                                            @foreach($units as $unit)
                                                                                                <option value="{{$unit['id']}}">
                                                                                                    {{$unit['name']}}
                                                                                                </option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="form-group"  style="width: 90%; margin-left: 5%">
                                                                                        <input type="text" class="form-control total_work_area" onkeyup="workAreaKeyUp(this)"  min="1" required>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="form-group"  style="width: 90%; margin-left: 5%">
                                                                                        <input type="text" class="form-control total_amount" readonly>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="form-group"  style="width: 90%; margin-left: 5%">
                                                                                        <textarea class="form-control total_amount_inwords" readonly rows="3"></textarea>
                                                                                    </div>
                                                                                </td>
                                                                                <td>

                                                                                </td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="tab-pane fade in" id="extraItemsTab">
                                                            <fieldset>
                                                                <legend>
                                                                    <span>
                                                                        <a href="#extraItemModal" data-toggle="modal" class="btn yellow pull-right">
                                                                            <i class="fa fa-plus"> </i>
                                                                            Extra Item
                                                                        </a>
                                                                    </span>
                                                                </legend>
                                                                @foreach($extraItems as $extraItem)
                                                                    <div class="form-group">
                                                                        <div class="col-md-3">
                                                                            <label class="control-label pull-right">
                                                                                {{$extraItem['name']}}
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control extra_items" name="extra_items[{{$extraItem['id']}}]" value="{{$extraItem['rate']}}">
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                                <div id="newExtraItemSection">

                                                                </div>
                                                            </fieldset>
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
    </div>
    <div id="extraItemModal" class="modal fade" role="dialog">
        <div class="modal-dialog" style="width: 70%;">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" style="text-align: center"> <b>Create extra item </b> </h4>
                </div>
                <div class="modal-body form">
                    <form role="form" id="create-extra-item" class="form-horizontal" method="post">
                        {!! csrf_field() !!}
                        <div class="form-body">
                            <div class="form-group row">
                                <div class="col-md-3" style="text-align: right">
                                    <label for="name" class="control-label">Name</label>
                                    <span>*</span>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Rate</label>
                            <div class="col-md-6">
                                <input type="number" id="rate" name="rate" class="form-control" placeholder="Enter Rate">
                            </div>
                        </div>
                        <div class="form-actions noborder row">
                            <div class="col-md-offset-3" style="margin-left: 26%">
                                <button type="submit" class="btn red" id="submit"><i class="fa fa-check"></i> Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('javascript')
    <script src="/assets/custom/subcontractor/validations.js"></script>
    <script>
        $(document).ready(function() {
            CreateSubcontractorStructure.init();
            CreateExtraItem.init();
            $(".extra_items").each(function(){
                $(this).rules('add', {
                    required: true
                });
            });
            onSummaryChange($('#summaryTable tbody .summary'));
        });

        function rateKeyUp(element){
            var rate = parseInt($(element).val());
            var total_work_area = parseInt($(element).closest('tr').find('.total_work_area').val());
            if (isNaN(rate)){
                rate = 0;
            }
            if(isNaN(total_work_area)){
                total_work_area = 0;
            }
            var total_amount = (rate)*(total_work_area);
            $(element).closest('tr').find('.total_amount').val(parseFloat(total_amount).toFixed(3));
            $(element).closest('tr').find('.total_amount_inwords').val(number2text(total_amount));
        }

        function workAreaKeyUp(element){
            var rate = parseFloat($(element).closest('tr').find('.rate').val());
            var total_work_area = parseFloat($(element).val());
            if (isNaN(rate)){
                rate = 0;
            }
            if( isNaN(total_work_area)){
                total_work_area = 0;
            }
            var total_amount = (rate)*(total_work_area);
            $(element).closest('tr').find('.total_amount').val(parseFloat(total_amount).toFixed(3));
            $(element).closest('tr').find('.total_amount_inwords').val(number2text(total_amount));
        }

        function structureTypeChange(){
            $("#summariesFieldset").show();
            var structureTypeSlug = $("input[name='structure_type']:checked").val();
            if(structureTypeSlug == 'itemwise'){
                $("#addSummaryBtn").show();
            }else{
                $("#addSummaryBtn").hide();
                $("#summaryTable tbody tr:not(:first)").each(function(key, element){
                    $(element).remove()
                });
            }
        }

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

        function addSummary(){
            var newRow = $("#summaryTable tbody").children("tr:first").clone();

            newRow.find('td:last').html('<a class="btn red btn-xs" href="javascript:void(0);" onclick="removeSummary(this)">\n' +
                                            '<i class="fa fa-times"></i>\n' +
                                        '</a>\n');
            $("#summaryTable tbody").append(newRow);
            onSummaryChange(newRow.find('.summary'));
        }

        function onSummaryChange(element){
            var summaryId = $(element).val();
            $(element).closest('tr').find('.rate').attr('name', 'rate['+summaryId+']');
            $(element).closest('tr').find('.rate').val('');
            $(element).closest('tr').find('.rate').rules('add',{
                required: true
            });
            $(element).closest('tr').find('.total_work_area').attr('name', 'total_work_area['+summaryId+']');
            $(element).closest('tr').find('.total_work_area').val('');
            $(element).closest('tr').find('.total_work_area').rules('add',{
                required: true
            });
            $(element).closest('tr').find('.total_amount').attr('name', 'total_amount['+summaryId+']');
            $(element).closest('tr').find('.total_amount').val('');
            $(element).closest('tr').find('.total_amount_inwords').attr('name', 'total_amount_inwords['+summaryId+']');
            $(element).closest('tr').find('.total_amount_inwords').val('');
            $(element).closest('tr').find('.description').attr('name', 'description['+summaryId+']');
            $(element).closest('tr').find('.description').val('');
            $(element).closest('tr').find('.unit').attr('name', 'unit['+summaryId+']');
            $(element).closest('tr').find('.unit').val('');

        }

        function removeSummary(element){
            $(element).closest('tr').remove();
        }

        function removeExtraItem(element){
            $(element).closest('.form-group').remove();
        }
    </script>
@endsection
