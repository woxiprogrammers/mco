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
                                                                                <th style="width: 25%"> Summary </th>
                                                                                <th style="width: 15%"> Rate </th>
                                                                                <th style="width: 15%"> Work Area (Sq.ft.)</th>
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
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="tab-pane fade in" id="extraItemsTab">
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
@endsection
@section('javascript')
    <script src="/assets/custom/subcontractor/validations.js"></script>
    <script>
        $(document).ready(function() {
            CreateSubcontractorStructure.init();
            $(".extra_items").each(function(){
                $(this).rules('add', {
                    required: true
                });
            });
            onSummaryChange($('#summaryTable tbody .summary'));
        });

        function rateKeyUp(element){
            var rate = $(element).val();
            var total_work_area = $(element).closest('tr').find('.total_work_area').val();
            var total_amount = parseFloat(rate)*parseFloat(total_work_area);
            $(element).closest('tr').find('.total_amount').val(parseFloat(total_amount).toFixed(3));
            $(element).closest('tr').find('.total_amount_inwords').val(number2text(total_amount));
        }

        function workAreaKeyUp(element){
            var rate = $(element).closest('tr').find('.rate').val();
            var total_work_area = $(element).val();
            var total_amount = parseFloat(rate)*parseFloat(total_work_area);
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
        }

        function removeSummary(element){
            $(element).closest('tr').remove();
        }
    </script>
@endsection
