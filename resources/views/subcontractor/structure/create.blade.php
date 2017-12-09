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
            <select class="form-control" id="subcontractor_id" name="subcontractor_id">
                @foreach($subcontractor as $sc)
                    <option value="{{$sc['id']}}">{{$sc['subcontractor_name']}}</option>
                @endforeach
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
            <label for="summary_id" class="control-label">Select Summary:</label>
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
            <input type="text" class="form-control" id="rate" name="rate" value="0">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-3" style="text-align: right">
            <label for="total_work_area" class="control-label">Total Work Area (Sq.Ft) :</label>
            <span>*</span>
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" id="total_work_area" name="total_work_area" value="0">
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
            <?php $count = 0;?>
            @foreach($ScStrutureTypes as $types)
            <label class="mt-radio" style="margin-left: 13px">
                <?php if ($count == 0) { ?>
                    <input type="radio" name="structure_type" id="{{$types['id']}}" checked="checked" value="{{$types['slug']}}"> {{$types['name']}}
                <?php } else { ?>
                    <input type="radio" name="structure_type" id="{{$types['id']}}" value="{{$types['slug']}}"> {{$types['name']}}
                <?php } ?>
                <span></span>
            </label>
            <?php $count++; ?>
            @endforeach
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
       /* $("#search-withfilter").on('click',function(){
            var client_id = $('#client_id').val();
            var project_id = $('#project_id').val();
            var site_id = $('#site_id').val();
            var year = $('#year').val();
            var month = $('#month').val();
            var status_id = $('#status_id').val();
            var search_name = $('#search_name').val();
            var emp_id = $('#emp_id').val();

            var postData =
                'client_id=>'+client_id+','+
                    'project_id=>'+project_id+','+
                    'site_id=>'+site_id+','+
                    'year=>'+year+','+
                    'month=>'+month;

            $("input[name='postdata']").val(postData);
            $("input[name='search_name']").val(search_name);
            $("input[name='search_employee_id']").val(emp_id);
            $(".filter-submit").trigger('click');
        });*/
        getProjects($('#client_id').val());
        getProjectSites($('#project_id').val());

        
        $("#client_id").on('change', function(){
            getProjects($('#client_id').val());
        });
        $("#project_id").on('change', function(){
            getProjectSites($('#project_id').val());
        });

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
    });
    function getProjects(client_id){
        $.ajax({
            url: '/subcontractor/projects/'+client_id,
            type: 'GET',
            async : false,
            success: function(data,textStatus,xhr){
                if(xhr.status == 200){
                    $('#project_id').html(data);
                    $('#project_id').prop('disabled',false);
                    getProjectSites($('#project_id').val());
                }
            },
            error: function(errorStatus,xhr){

            }
        });
    }

    function getProjectSites(project_id){
        $.ajax({
            url: '/subcontractor/project-sites/'+project_id,
            type: 'GET',
            async : false,
            success: function(data,textStatus,xhr){
                if(xhr.status == 200){
                    $('#site_id').html(data);
                    $('#site_id').prop('disabled',false);
                    $("#search-withfilter").trigger('click');
                }
            },
            error: function(errorStatus,xhr){

            }
        });
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

    function convert_number(number)
    {
        if ((number < 0) || (number > 999999999))
        {
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

        if (Gn>0)
        {
            res += (convert_number(Gn) + " CRORE");
        }
        if (kn>0)
        {
            res += (((res=="") ? "" : " ") +
                convert_number(kn) + " LAKH");
        }
        if (Hn>0)
        {
            res += (((res=="") ? "" : " ") +
                convert_number(Hn) + " THOUSAND");
        }

        if (Dn)
        {
            res += (((res=="") ? "" : " ") +
                convert_number(Dn) + " HUNDRED");
        }


        var ones = Array("", "ONE", "TWO", "THREE", "FOUR", "FIVE", "SIX","SEVEN", "EIGHT", "NINE", "TEN", "ELEVEN", "TWELVE", "THIRTEEN","FOURTEEN", "FIFTEEN", "SIXTEEN", "SEVENTEEN", "EIGHTEEN","NINETEEN");
        var tens = Array("", "", "TWENTY", "THIRTY", "FOURTY", "FIFTY", "SIXTY","SEVENTY", "EIGHTY", "NINETY");

        if (tn>0 || one>0)
        {
            if (!(res==""))
            {
                res += " AND ";
            }
            if (tn < 2)
            {
                res += ones[tn * 10 + one];
            }
            else
            {

                res += tens[tn];
                if (one>0)
                {
                    res += ("-" + ones[one]);
                }
            }
        }

        if (res=="")
        {
            res = "zero";
        }
        return res;
    }

</script>
@endsection