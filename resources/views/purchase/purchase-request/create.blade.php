@extends('layout.master')
@section('title','Constro | Manage Materials')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
    <form role="form" id="new_purchase_request" class="form-horizontal" action="/purchase/purchase-request/create" method="post">
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
                                    <h1>Create Purchase Request</h1>
                                </div>
                                <button type="submit"  class="btn red pull-right margin-top-15">
                                    <i class="fa fa-check" style="font-size: large"></i>
                                    Submit
                                </button>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN VALIDATION STATES-->
                                        <div class="portlet light ">
                                            <div class="portlet-body form">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control empty" id="clientSearchbox" name="client_name" placeholder="Enter client name" >
                                                            <div id="client-suggesstion-box"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control empty" id="projectSearchbox"  placeholder="Enter project name" >
                                                            <input type="hidden"  id="project_side_id" name="project_site_id">
                                                            <div id="project-suggesstion-box"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control empty" id="userSearchbox"  placeholder="Enter user name" >
                                                            <input type="hidden" name="user_id" id="user_id_">
                                                            <div id="user-suggesstion-box"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <a href="#" class="btn btn-set yellow pull-right"  id="assetBtn">
                                                            <i class="fa fa-plus" style="font-size: large"></i>
                                                            Asset&nbsp &nbsp &nbsp &nbsp
                                                        </a>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group " style="text-align: center">
                                                            <a href="#" class="btn btn-set yellow pull-left"  id="myBtn">
                                                                <i class="fa fa-plus" style="font-size: large"></i>
                                                                Material
                                                            </a>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="portlet-light">
                                        <div class="portlet-body-form">
                                            <div class="container">
                                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading" role="tab" id="headingOne">
                                                            <h4 class="panel-title" style="padding-bottom: 20px">
                                                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                                    <i class="more-less glyphicon glyphicon-plus"></i>
                                                                    <span style="float: left ;font-size: 20px">Added from indent</span>
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                                            <div class="panel-body">
                                                                <table class="table table-hover table-light">
                                                                    <thead>
                                                                    <tr>
                                                                        <th> # </th>
                                                                        <th> Material \ Asset Name </th>
                                                                        <th> Quantity </th>
                                                                        <th> Unit </th>
                                                                        <th> Action </th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @foreach($materialRequestList as $components)
                                                                    <tr>
                                                                        <td> <input type="checkbox"> </td>
                                                                        <td> <input type="text" value="{{$components['name']}}" readonly> </td>
                                                                        <td> <input type="text" value="{{$components['quantity']}}" readonly> </td>
                                                                        <td> <input type="text" value="{{$components['unit']}}" readonly> </td>
                                                                        <td>
                                                                            <div class="btn-group open">
                                                                                <button class="btn btn-xs green dropdown-toggle deleteRowButton" type="button" aria-expanded="true">
                                                                                    Remove
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="panel panel-default">
                                                        <div class="panel-heading" role="tab" id="headingTwo">
                                                            <h4 class="panel-title" style="padding-bottom: 20px">
                                                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                                    <i class="more-less glyphicon glyphicon-plus"></i>
                                                                    <span style="float: left;font-size: 20px">Added material list</span>
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                                            <div class="panel-body">
                                                                <table class="table table-hover table-light">
                                                                    <thead>
                                                                    <tr>
                                                                        <th> Material \ Asset Name </th>
                                                                        <th> Quantity </th>
                                                                        <th> Unit </th>
                                                                        <th> Action </th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody id="Materialrows">

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="panel panel-default">
                                                        <div class="panel-heading" role="tab" id="headingThree">
                                                            <h4 class="panel-title" style="padding-bottom: 20px">
                                                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                                    <i class="more-less glyphicon glyphicon-plus"></i>
                                                                    <span style="float: left ;font-size: 20px">Added asset list</span>
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                                                            <div class="panel-body">
                                                                <table class="table table-hover table-light">
                                                                    <thead>
                                                                    <tr>
                                                                        <th> Material \ Asset Name </th>
                                                                        <th> Quantity </th>
                                                                        <th> Unit </th>
                                                                        <th> Action </th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody id="Assetrows">

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div><!-- panel-group -->


                                            </div><!-- container -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="myModal" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-4"> Material</div>
                                                <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                            </div>
                                        </div>
                                        <div class="modal-body" style="padding:40px 50px;">
                                            <div class="form-group">
                                                <input type="text" class="form-control empty" id="searchbox"  placeholder="Enter material name" >

                                            </div>
                                            <div class="form-group">
                                                <input type="number" class="form-control empty" id="qty"  placeholder="Enter quantity">
                                            </div>
                                            <div class="form-group" id="unitDrpdn">

                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12"> </div>
                                                </div>
                                                <div id="tab_images_uploader_container" class="col-md-offset-5">
                                                    <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow">
                                                        Browse</a>
                                                    <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
                                                        <i class="fa fa-share"></i> Upload Files </a>
                                                </div>
                                                <table class="table table-bordered table-hover" style="width: 200px">
                                                    <thead>
                                                    <tr role="row" class="heading">
                                                        <th> Image </th>
                                                        <th> Action </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="show-product-images">
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="btn red pull-right" id="createMaterial"> Create</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="myModal1" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header" style="padding-bottom:10px">
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-4"> Asset</div>
                                                <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                            </div>
                                        </div>
                                        <div class="modal-body" style="padding:40px 50px;">
                                            <div class="form-group">
                                                <input type="text" class="form-control empty" id="Assetsearchbox"  placeholder="Enter asset name" >
                                                <div id="asset_suggesstion-box"></div>
                                            </div>
                                            <div class="form-group">
                                                <input type="number" class="form-control empty" id="Assetqty" value="1" readonly>
                                            </div>
                                            <div class="form-group">
                                                <input type="text" class="form-control empty" id="AssetUnitsearchbox"  value="Nos" readonly >
                                                <div id="asset_unit-suggesstion-box"></div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12"> </div>
                                                </div>
                                                <div id="tab_images_uploader_container" class="col-md-offset-5">
                                                    <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow">
                                                        Browse</a>
                                                    <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
                                                        <i class="fa fa-share"></i> Upload Files </a>
                                                </div>
                                                <table class="table table-bordered table-hover" style="width: 200px">
                                                    <thead>
                                                    <tr role="row" class="heading">
                                                        <th> Image </th>
                                                        <th> Action </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="show-product-images">
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="btn red pull-right" id="createAsset"> Create</div>
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
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <link rel="stylesheet"  href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css"/>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
    <link rel="stylesheet"  href="/assets/global/css/app.css"/>
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/custom/purchase/purchase-request/purchase-request-datatable.js" type="text/javascript"></script>
    <script src="/assets/custom/purchase/purchase-request/purchase-request-typeahead.js" type="text/javascript"></script>
    <script src="/assets/custom/purchase/purchase-request/purchase-request.js" type="text/javascript"></script>
@endsection
