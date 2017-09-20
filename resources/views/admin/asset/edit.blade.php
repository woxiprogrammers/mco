@extends('layout.master')
@section('title','Constro | Create User')
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
                                    <h1>Edit Asset</h1>
                                </div>
                                <div class="form-group " style="float: right;margin-top:1%">
                                    <a href="#" class="btn btn-set red pull-right">
                                        <i class="fa fa-check"></i>
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <form role="form" id="create-user" class="form-horizontal" method="post" action="/asset/edit">
                                                {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="model_number" class="control-label">Model Number</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="model_number" name="model_number">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="asset_name" class="control-label">Asset Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="last_name" name="last_name">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="expiry_date" class="control-label ">Expiry Date</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-4 date date-picker" data-date-end-date="0d">
                                                            <input type="text"  class="form-control" style="width: 30%" name="date" placeholder="Select Bill Date" id="date"/>
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="price" class="control-label">Price</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control" id="price" name="price">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="diesel" class="control-label">Is It a Diesel</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" name="is_fuel_dependent">
                                                                <option value="">Select Option</option>
                                                                <option value="true">Yes</option>
                                                                <option value="false">No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="liter_per_unit" class="control-label">Litre Per Unit</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control" id="number" name="litre_per_unit">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="row">
                                                            <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12"> </div>
                                                        </div>
                                                        <div id="tab_images_uploader_container" class="col-md-offset-5">
                                                            <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow">
                                                                Browse</a>
                                                            <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
                                                                <i class="fa fa-share"></i> Upload Files </a>
                                                        </div>
                                                        <center><table class="table table-bordered table-hover row" style="width: 200px">
                                                                <thead class="pull-right">
                                                                <tr role="row" class="heading">
                                                                    <th> Image </th>
                                                                    <th> Action </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody id="show-product-images">
                                                                </tbody>
                                                            </table></center>
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
    <script src="/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="/assets/custom/user/user.js" type="application/javascript"></script>
    <script>
        $(document).ready(function(){

        });
    </script>
@endsection
