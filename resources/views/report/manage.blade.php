<?php
    /**
     * Created by PhpStorm.
     * User: manoj
     * Date: 6/9/18
     * Time: 4:15 PM
     */
    ?>
        <?php $iterator = 1;?>
        @foreach($downloadButtonDetails as $downloadButton)
            {!! csrf_field() !!}
            <div class="form-body">
                <div class="form-group row">
                    <div class="col-md-3" style="text-align: right">
                        <label for="name" class="control-label">Excel Sheet {{$iterator}}</label>
                        <span>*</span>
                    </div>
                    <div class="col-md-2" style="text-align: center">
                        <label for="name" class="control-label">From {{$downloadButton['start_limit']}} - {{$downloadButton['end_limit']}}</label>
                    </div>
                    <div class="btn-group">
                        <div class="btn blue">
                            <a href="javascript:window.open('/reports/get-report/{{$reportType}}/{{$project_site_id}}/{{$downloadButton['start_date']}}/{{$downloadButton['end_date']}}');" style="color: white"> Download
                                <i class="fa fa-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php $iterator++;?>
    @endforeach
