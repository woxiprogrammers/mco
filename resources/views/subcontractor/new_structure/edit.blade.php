<?php
/**
 * Created by PhpStorm.
 * User: ameya
 * Date: 1/11/18
 * Time: 9:47 PM
 */
?>

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
                                        <a href="javascript:void(0);">Edit Subcontractor Structure</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <form id="editSubcontractorStructure" class="form-horizontal" action="/subcontractor/structure/edit/{{$subcontractorStructure->id}}" method="post">
                                                {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <div class="row">
                                                        <div class="col-md-offset-9 col-md-3">
                                                            <button type="submit" class="btn red pull-right" id="labour_submit"><i class="fa fa-check"></i> Edit Structure</button>
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
                                                                    <label for="description" class="control-label"> Project Site : </label>
                                                                    <span>*</span>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="control-label"> {!! $subcontractorStructure->projectSite->name !!}</label>
                                                                </div>
                                                            </div>
                                                            <div class="row form-group">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label for="description" class="control-label"> Subcontractor : </label>
                                                                    <span>*</span>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="control-label"> {!! $subcontractorStructure->subcontractor->company_name !!}</label>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label for="structure_type" class="control-label">Structure Type :</label>
                                                                    <span>*</span>
                                                                </div>                                                        &nbsp;&nbsp;&nbsp;
                                                                <div class="col-md-6 mt-radio-inline">
                                                                    <label class="control-label"> {!! $subcontractorStructure->contractType->name !!}</label>
                                                                </div>
                                                            </div>
                                                            <fieldset id="summariesFieldset">
                                                                <legend>
                                                                    Summaries
                                                                    @if($subcontractorStructure->contractType->slug == 'itemwise')
                                                                        <a class="btn yellow btn-md col-md-offset-8" href="javascript:void(0);" id="addSummaryBtn" onclick="addSummary()">
                                                                            <i class="fa fa-plus"></i>
                                                                            Summary
                                                                        </a>
                                                                    @endif
                                                                </legend>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <table class="table table-striped table-bordered table-hover" id="summaryTable">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th style="width: 15%"> Summary </th>
                                                                                    <th style="width: 20%"> Description </th>

                                                                                    <th style="width: 15%"> Rate </th>
                                                                                    <th style="width: 15%"> Work Area (Sq.ft.)</th>
                                                                                    <th style="width: 15%"> Total Amount </th>
                                                                                    <th style="width: 25%"> Total Amount (Words)</th>
                                                                                    <th style="width: 5%"> Action </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($subcontractorStructure->summaries as $subcontractorStructureSummary)
                                                                                    <tr>
                                                                                        <td>
                                                                                            <label class="control-label"> {!! $subcontractorStructureSummary->summary->name !!}</label>
                                                                                        </td>
                                                                                        <td>
                                                                                            <div class="form-group" style="width: 90%; margin-left: 5%">
                                                                                                <textarea class="form-control description" rows="3">{{$subcontractorStructureSummary->description}}</textarea>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td>
                                                                                            <div class="form-group" style="width: 90%; margin-left: 5%">
                                                                                                <input type="text" class="form-control rate" onkeyup="rateKeyUp(this)" value="{{$subcontractorStructureSummary->rate}}">
                                                                                            </div>
                                                                                        </td>
                                                                                        <td>
                                                                                            <div class="form-group"  style="width: 90%; margin-left: 5%">
                                                                                                <input type="text" class="form-control total_work_area" onkeyup="workAreaKeyUp(this)" value="{{$subcontractorStructureSummary->total_work_area}}">
                                                                                            </div>
                                                                                        </td>
                                                                                        <td>
                                                                                            <div class="form-group"  style="width: 90%; margin-left: 5%">
                                                                                                <input type="text" class="form-control total_amount" value="{!! $subcontractorStructureSummary->rate * $subcontractorStructureSummary->total_work_area !!}" readonly>
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
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="tab-pane fade in" id="extraItemsTab">
                                                            @foreach($subcontractorStructure->extraItems as $subcontractorStructureExtraItem)
                                                                <div class="form-group">
                                                                    <div class="col-md-3">
                                                                        <label class="control-label pull-right">
                                                                            {{$subcontractorStructureExtraItem->extraItem['name']}}
                                                                        </label>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" class="form-control extra_items" name="extra_items[{{$subcontractorStructureExtraItem->extraItem['id']}}]" value="{{$subcontractorStructureExtraItem['rate']}}">
                                                                    </div>
                                                                </div>
                                                            @endforeach
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
    @if($subcontractorStructure->contractType->slug == 'itemwise')
        <table id="tempSummaryTable" hidden>
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
                        <input type="text" class="form-control rate" onkeyup="rateKeyUp(this)">
                    </div>
                </td>
                <td>
                    <div class="form-group"  style="width: 90%; margin-left: 5%">
                        <input type="text" class="form-control total_work_area" onkeyup="workAreaKeyUp(this)">
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
        </table>
    @endif
@endsection
@section('javascript')
    <script src="/assets/custom/subcontractor/validations.js"></script>
    <script>
        $(document).ready(function() {
            EditSubcontractorStructure.init();
            $(".extra_items").each(function(){
                $(this).rules('add', {
                    required: true
                });
            });
            $(".total_work_area").each(function(){
                workAreaKeyUp(this);
            });
            // onSummaryChange($('#summaryTable tbody .summary'));
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
            var newRow = $("#tempSummaryTable").find("tr").clone();

            newRow.find('td:last').html('<a class="btn red btn-xs" href="javascript:void(0);" onclick="removeSummary(this)">\n' +
                '<i class="fa fa-times"></i>\n' +
                '</a>\n');
            console.log('new row', newRow);
            $("#summaryTable tbody").append(newRow);
            console.log(newRow.find('.summary').val());
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

        }

        function removeSummary(element){
            $(element).closest('tr').remove();
        }
    </script>
@endsection

