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
                                                    <div class="row form-group">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="description" class="control-label">Select Subcontractor : </label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <select class="form-control" id="subcontractor_id" name="subcontractor_id">
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
                                                        </div>
                                                        &nbsp;&nbsp;&nbsp;
                                                        <div class="col-md-6 mt-radio-inline">
                                                            @foreach($ScStrutureTypes as $type)
                                                                <label class="mt-radio" style="margin-left: 13px">
                                                                    <input type="radio" name="structure_type" id="{{$type['id']}}" value="{{$type['slug']}}" onchange="structureTypeChange()"> {{$type['name']}}
                                                                    <span></span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-12">
                                                            <table class="table table-striped table-bordered table-hover">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 5%">  </th>
                                                                        <th style="width: 25%"> Summary </th>
                                                                        <th style="width: 15%"> Rate </th>
                                                                        <th style="width: 15%"> Work Area (Sq.ft.)</th>
                                                                        <th style="width: 15%"> Total Amount </th>
                                                                        <th style="width: 25%"> Total Amount (Words)</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td> x </td>
                                                                        <td> Brickwork </td>
                                                                        <td> <input type="text" name="rate[0]" class="form-control rate"> </td>
                                                                        <td> <input type="text" name="total_work_area[0]" class="form-control total_work_area"> </td>
                                                                        <td> <input type="text" class="form-control total_amount" readonly> </td>
                                                                        <td> <textarea class="form-control total_amount_inwords" readonly rows="3"></textarea> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td> x </td>
                                                                        <td> Plaster </td>
                                                                        <td> <input type="text" name="rate[0]" class="form-control rate"> </td>
                                                                        <td> <input type="text" name="total_work_area[0]" class="form-control total_work_area"> </td>
                                                                        <td> <input type="text" class="form-control total_amount" readonly> </td>
                                                                        <td> <textarea class="form-control total_amount_inwords" readonly rows="3"></textarea> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
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
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/custom/subcontractor/subcontractor.js" type="application/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $(".rate").on('keyup', function(){
                var rate = $(this).val();
                var total_work_area = $(this).closest('tr').find('.total_work_area').val();
                var total_amount = parseFloat(rate)*parseFloat(total_work_area);
                $(this).closest('tr').find('.total_amount').val(parseFloat(total_amount).toFixed(3));
                $(this).closest('tr').find('.total_amount_inwords').val(number2text(total_amount));
            });

            $(".total_work_area").on('keyup', function(){
                var rate = $(this).closest('tr').find('.rate').val();
                var total_work_area = $(this).val();
                var total_amount = parseFloat(rate)*parseFloat(total_work_area);
                $(this).closest('tr').find('.total_amount').val(parseFloat(total_amount).toFixed(3));
                $(this).closest('tr').find('.total_amount_inwords').val(number2text(total_amount));
            });
        });
        function structureTypeChange(){
            console.log(1234);
            var structureTypeSlug = $("input[name='structure_type']:checked").val();
            if(structureTypeSlug == 'itemwise'){

            }else{

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
    </script>
@endsection
