@extends('layout.master')
@section('title','Constro | Edit Asset')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
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
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/asset/manage">Manage Asset</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Edit Asset</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <input type="hidden" id="vendors_id" value="{{$asset['id']}}">
                                            <form role="form" id="edit-asset" class="form-horizontal" method="post" action="/asset/edit/{{$asset['id']}}">
                                                {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="model_number" class="control-label">Model Number</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control" value="{{$asset['model_number']}}" id="model_number" name="model_number">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="asset_name" class="control-label">Asset Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="name" name="name" value="{{$asset['name']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="expiry_date" class="control-label ">Expiry Date</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6 date date-picker">
                                                            <input type="text"  style="width: fit-content" name="expiry_date" placeholder="Select Expiry Date" id="date" value="{{$asset['expiry_date']}}">
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
                                                            <input type="number" class="form-control" id="price" name="price" value="{{$asset['price']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="diesel" class="control-label">Is It a Diesel</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" name="is_fuel_dependent" id="select-litre">
                                                                @if(isset($asset['is_fuel_dependent']) && $asset['is_fuel_dependent'] == 'true')
                                                                    <option value="true" selected >Yes</option>
                                                                    <option value="false">No</option>
                                                                @else
                                                                    <option value="true">Yes</option>
                                                                    <option value="false" selected>No</option>
                                                                @endif

                                                            </select>
                                                        </div>
                                                    </div>
                                                    @if($asset['is_fuel_dependent'] == 'true')
                                                    <div class="form-group row" id="Litre">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="liter_per_unit" class="control-label">Litre Per Unit</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control" id="number" name="litre_per_unit" value="{{$asset['litre_per_unit']}}">
                                                        </div>
                                                    </div>
                                                    @else
                                                        <div class="form-group row" id="Litre" hidden>
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="liter_per_unit" class="control-label">Litre Per Unit</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number" class="form-control" id="number" name="litre_per_unit" value=" ">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12" style="margin-left: 20%"> </div>
                                                        </div>
                                                        <div id="tab_images_uploader_container" class="col-md-offset-5">
                                                            <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow" style="margin-left: 26%">
                                                                Browse</a>
                                                            <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
                                                                <i class="fa fa-share"></i> Upload Files </a>
                                                        </div>
                                                        <table class="table table-bordered table-hover" style="width: 554px; margin-left: 26%; margin-top: 1%">
                                                            <thead>
                                                            <tr role="row" class="heading">
                                                                <th> Image </th>
                                                                <th> Action </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="show-product-images">
                                                            @foreach($assetImage as $image)
                                                                <tr id="image-{{$image['id']}}">
                                                                    <td>
                                                                        <a href="{{$image['path']}}" target="_blank" class="fancybox-button" data-rel="fancybox-button">
                                                                            <img class="img-responsive" src="{{$image['path']}}" alt="" style="width:100px; height:100px;"> </a>
                                                                        <input type="hidden" class="work-order-image-name" name="asset_images[{{$image['id']}}][image_name]" id="work-order-image-{{$image['id']}}" value="{{$image['path']}}"/>
                                                                    </td>
                                                                    <td>
                                                                        <a href="javascript:;" class="btn btn-default btn-sm" onclick='removeAssetImage("#image-{{$image['id']}}","{{$image['path']}}",0);'>
                                                                            <i class="fa fa-times"></i> Remove </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="form-actions noborder row">
                                                        <div class="col-md-offset-3" style="margin-left: 26%">
                                                            <button type="submit" class="btn red" style=" padding-left: 6px"><i class="fa fa-check"></i> Submit</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <input type="hidden" id="path" name="path" value="">
                                            <input type="hidden" id="max_files_count" name="max_files_count" value="20">
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
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script><script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="/assets/custom/user/user.js" type="application/javascript"></script>
    <script src="/assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
    <script src="/assets/custom/admin/asset/image-datatable.js"></script>
    <script src="/assets/custom/admin/asset/image-upload.js"></script>
    <script src="/assets/custom/admin/asset/asset.js" type="application/javascript"></script>
    <script>
        $(document).ready(function(){
            EditAsset.init();
            $('#select-litre').change(function(){
                var selected = $(this).val();
                if(selected == 'true'){
                    $('#Litre').show();
                }else{
                    $('#Litre').hide();
                }
            });
        });
    </script>
    <script>
        var date=new Date();
        $('#expiry_date').val((date.getMonth()+1)+"/"+date.getDate()+"/"+date.getFullYear());
    </script>
@endsection
