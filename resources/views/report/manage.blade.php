<?php
    /**
     * Created by PhpStorm.
     * User: manoj
     * Date: 6/9/18
     * Time: 4:15 PM
     */
    ?>
        @if(count($downloadButtonDetails) == 0)
            <div class="col-md-6" style="text-align: right;color: red">
                <label for="name" class="control-label">No Data Found</label>
            </div>
        @else
            <?php $iterator = 1;?>
            @foreach($downloadButtonDetails as $downloadButton)
                {!! csrf_field() !!}
                @if($reportType == 'sitewise_subcontractor_report')
                    <div class="form-body">
                        <div class="form-group row">
                            <div class="col-md-3" style="text-align: right">
                                <label for="name" class="control-label">Excel Sheet {{$iterator}}</label>
                                <span>*</span>
                            </div>
                            <div class="col-md-4" style="text-align: center">
                                <label for="name" class="control-label"><b>Type - {{$downloadButton['type']}} : Summary - {{$downloadButton['summary_name']}}</b>  ( Created On <i>{{date('d M Y',strtotime($downloadButton['created_at']))}}</i> )</label>
                            </div>
                            <div class="btn-group">
                                <div class="btn blue">
                                    <a href="javascript:window.open('/reports/get-report/{{$reportType}}/{{$project_site_id}}/{{$downloadButton['id']}}/null/null');" style="color: white"> Download
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($reportType == 'sitewise_indirect_expenses_report')
                    <div class="form-body">
                        <div class="form-group row">
                            <div class="btn-group">
                                <div class="col-md-offset-3 btn blue" style="margin-left: 350%">
                                    <a href="javascript:window.open('/reports/get-report/{{$reportType}}/{{$project_site_id}}/{{$downloadButton['start_month_id']}}/{{$downloadButton['end_month_id']}}/{{$downloadButton['year_id']}}');" style="color: white"> Download
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($reportType == 'sitewise_purchase_report')
                    <div class="form-body">
                        <div class="form-group row">
                            <div class="col-md-3" style="text-align: right">
                                <label for="name" class="control-label">Excel Sheet {{$iterator}}</label>
                                <span>*</span>
                            </div>
                            @if(array_key_exists('start_limit',$downloadButton))
                                <div class="col-md-4" style="text-align: center">
                                    <label for="name" class="control-label"><b>Records {{$downloadButton['start_limit']}} - {{$downloadButton['end_limit']}}</b> </label>
                                </div>
                            @endif
                            <div class="btn-group">
                                <div class="btn blue">
                                    <a href="javascript:window.open('/reports/get-report/{{$reportType}}/{{$project_site_id}}/{{$downloadButton['start_date']}}/{{$downloadButton['end_date']}}/{{$downloadButton['button_no']}}');" style="color: white"> Download
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="form-body">
                        <div class="form-group row">
                            <div class="col-md-3" style="text-align: right">
                                <label for="name" class="control-label">Excel Sheet {{$iterator}}</label>
                                <span>*</span>
                            </div>
                            @if(array_key_exists('start_limit',$downloadButton))
                                <div class="col-md-4" style="text-align: center">
                                    <label for="name" class="control-label"><b>Records {{$downloadButton['start_limit']}} - {{$downloadButton['end_limit']}}</b>  ( From <i>{{date('d M Y',strtotime($downloadButton['end_date']))}}</i> - <i>{{date('d M Y',strtotime($downloadButton['start_date']))}}</i> )</label>
                                </div>
                            @endif
                            <div class="btn-group">
                                <div class="btn blue">
                                    @if($reportType == 'sitewise_subcontractor_summary_report')
                                        <a href="javascript:window.open('/reports/get-report/{{$reportType}}/{{$project_site_id}}/null/null/null');" style="color: white"> Download
                                            <i class="fa fa-download"></i>
                                        </a>
                                    @else
                                        <a href="javascript:window.open('/reports/get-report/{{$reportType}}/{{$project_site_id}}/{{$downloadButton['start_date']}}/{{$downloadButton['end_date']}}/null');" style="color: white"> Download
                                            <i class="fa fa-download"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <?php $iterator++;?>
            @endforeach
        @endif

