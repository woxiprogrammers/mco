<?php
    /**
     * Created by PhpStorm.
     * User: manoj
     * Date: 6/9/18
     * Time: 4:15 PM
     */
    ?>
{{--<tr>
    <th> Name </th>

</tr>
@for($iterator = 0 ; $iterator < $noOfButtons; $iterator++)
    <tr>
        --}}{{--<td>Excel Sheet</td>
        <td>From 1 - 1000</td>--}}{{--
        <td>
            <form role="form" id="download-excel" class="form-horizontal" method="post" action="/reports/get-report">
                {!! csrf_field() !!}
                <div class="form-body">
                    <div class="form-group row">
                        <div class="col-md-3" style="text-align: right">
                            <label for="name" class="control-label">Excel Sheet {{$iterator+1}}</label>
                            <span>*</span>
                        </div>
                        <div class="col-md-2" style="text-align: center">
                            <label for="name" class="control-label">From 1 - 1000</label>
                            <span>*</span>
                        </div>
                        <input type="hidden" name="report_type" value="{{$reportType}}">
                        <input type="hidden" name="project_site_id" value="{{$project_site_id}}">
                        <input type="hidden" name="start_date" value="{{$start_date}}">
                        <input type="hidden" name="end_date" value="{{$endDate}}">
                        <div class="col-md-2" style="text-align: center">
                            <button type="submit" class="btn red"><i class="fa fa-check"></i> Download</button>
                        </div>
                    </div>
                </div>
            </form>
        </td>
    </tr>
@endfor--}}

    @for($iterator = 0 ; $iterator < 2; $iterator++)
        {{--<form role="form" id="download-excel" class="form-horizontal" method="post" action="/reports/get-report">--}}
            {!! csrf_field() !!}
            <div class="form-body">
                <div class="form-group row">
                    <div class="col-md-3" style="text-align: right">
                        <label for="name" class="control-label">Excel Sheet {{$iterator+1}}</label>
                        <span>*</span>
                    </div>
                    <div class="col-md-2" style="text-align: center">
                        <label for="name" class="control-label">From 1 - 1000</label>
                        <span>*</span>
                    </div>
                    <input type="hidden" name="report_type" id="report_type" value="{{$reportType}}">
                    <input type="hidden" name="project_site_id" id="project_site_id" value="{{$project_site_id}}">
                    <input type="hidden" name="start_date" id="start_date" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_date" value="{{$endDate}}">
                    <a href="javascript:window.open('/reports/get-report/sitewise_purchase_report/{{$project_site_id}}/{{$start_date}}/{{$endDate}}');" >
                        download
                    </a>
                    <div class="col-md-2" style="text-align: center">
                        <button type="button" onclick="downloadReport()" class="btn red"><i class="fa fa-check"></i> Download</button>
                    </div>
                </div>
            </div>
        {{--</form>--}}
    @endfor

<script>
    function downloadReport(){
        $.ajax({
            type : "POST",
            url : "/reports/get-report",
            data : {
                _token : $('input[name="_token"]').val(),
                report_type : $('#report_type').val(),
                project_site_id : $('#project_site_id').val(),
                start_date : $('#start_date').val(),
                end_date : $('#end_date').val()
            },
            success : function(data,textStatus,xhr){
                alert('done');
            },
            error : function(errorData){

            }
        });
    }
</script>
