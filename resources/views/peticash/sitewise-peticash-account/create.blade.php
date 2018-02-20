@extends('layout.master')
@section('title','Constro | Add Amount to Master Peticash Account')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
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
                                <h1>Allocate Amount to Sitewise Peticash Account</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/peticash/sitewise-peticash-account/manage">Manage Sitewise Peticash Account</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Allocate Amount to Sitewise Peticash Account</a>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <form role="form" id="create-sitewise-account" class="form-horizontal" method="post" action="/peticash/sitewise-peticash-account/create">
                                            {!! csrf_field() !!}
                                            <div class="form-body">
                                                <fieldset>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="type" class="control-label">Sitename</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="project_site_id" name="project_site_id">
                                                                @foreach($sites as $site)
                                                                <option value="{{$site['id']}}">{{$site['name']}}-{{$site['address']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="type" class="control-label">Assign To</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="to_userid" name="to_userid">
                                                                @foreach($users as $user)
                                                                <option value="{{$user['id']}}">{{$user['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">

                                                            <label for="name" class="control-label">Amount</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="amount" name="amount" required="required">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="type" class="control-label">Payment Type</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="payment_type" name="payment_type">
                                                                @foreach($paymenttypes as $type)
                                                                <option value="{{$type['id']}}">{{$type['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">

                                                            <label for="name" class="control-label">Remark</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="remark" name="remark" required="required">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">

                                                            <label for="name" class="control-label">Transaction Date</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input name="date" class="form-control" id="date" type="text">
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-md-offset-8">
                                                    <button type="submit" class="btn btn-success"> Submit </button>
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
<script src="/assets/custom/peticash/peticash.js" type="text/javascript"></script>
<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
<script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
<script>
    $(document).ready(function() {
        AddAmtToSitewiseAccount.init();
        var date=new Date();
        $('#date').val((date.getMonth()+1)+"/"+date.getDate()+"/"+date.getFullYear());

        $("#project_site_id").on('change', function(){
            getProjects($('#project_site_id').val());
        });

        function getProjects(client_id){
            $.ajax({
                url: '/peticash/sitewise-peticash-account/getuserlistbysite/'+client_id,
                type: 'GET',
                async : false,
                success: function(data,textStatus,xhr){
                    if(xhr.status == 200){
                        $('#to_userid').html(data);
                    }
                },
                error: function(errorStatus,xhr){

                }
            });
        }
    });
</script>
@endsection
