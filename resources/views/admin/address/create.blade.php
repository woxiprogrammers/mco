@extends('layout.master')
@section('title','Constro | Create Address')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->

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
                                    <h1>Create Address</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/address/manage">Manage Address</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Create Address</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <form role="form" id="create-address" class="form-horizontal" method="post" action="/address/create">
                                                {!! csrf_field() !!}
                                                <input type="hidden"  id="csrf-token" name="csrf-token" value="{{ csrf_token() }}">
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="model_number" class="control-label">Address</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="address" name="address">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="diesel" class="control-label">Country</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" name="country_id" id="country_id" onchange="getStates()">
                                                                <option value="">Select Country</option>
                                                                @foreach($countries as $country)
                                                                    <option value="{{$country['id']}}">{{$country['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="state" class="control-label">State</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" name="state_id" id="state" onchange="getCity()" disabled>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">City</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" name="city_id" id="city" disabled>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="diesel" class="control-label">Pin-code</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="pincode" name="pincode">
                                                        </div>
                                                    </div>

                                                    <div class="form-actions noborder row">
                                                        <div class="col-md-offset-3" style="margin-left: 26%">
                                                            <button type="submit" class="btn red" style=" padding-left: 6px"><i class="fa fa-check"></i> Submit</button>
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
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script>
        function getStates(){
            var country_id = $('#country_id').val();
            $.ajax({
                url : '/address/get-states',
                type : 'POST',
                async : false,
                data : {
                    'country_id' : country_id
                },
                success : function(data,textStatus,xhr){
                    if(xhr.status == 200){
                        $('#state').prop('disabled',false);
                        $('#state').html(data.states);
                        getCity();
                    }
                },
                error : function (data, textStatus, xhr){

                }
            });
        }

        function getCity(){
            var state_id = $('#state').val();
            $.ajax({
                url : '/address/get-cities',
                type : 'POST',
                async : false,
                data : {
                    'state_id' : state_id
                },
                success : function(data, textStatus, xhr){
                    if(xhr.status == 200){
                        console.log(data.cities);
                        $('#city').prop('disabled',false);
                        $('#city').html(data.cities);
                    }
                },
                error : function (data, textStatus, xhr){

                }
            });
        }
    </script>
@endsection
