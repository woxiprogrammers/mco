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
                                <a href="javascript:window.open('/reports/get-report/{{$reportType}}/{{$project_site_id}}/{{$downloadButton['start_date']}}/{{$downloadButton['end_date']}}/{{$subcontractorId}}');" style="color: white"> Download
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $iterator++;?>
            @endforeach
        @endif

