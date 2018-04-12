@extends('layout.master')
@section('title','Constro | Manage Materials')
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
                                    <h1>Create Purchase Request</h1>
                                </div>
                                <a href="#" class="btn red pull-right margin-top-15">
                                    <i class="fa fa-check" style="font-size: large"></i>
                                    Submit
                                </a>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/purchase/purchase-request/manage">Manage Purchase Request</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Edit Purchase Request</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN VALIDATION STATES-->
                                        <div class="portlet light ">
                                            <div class="portlet-body form">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="btn-group bootstrap-select bs-select form-control dropup">
                                                                <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" title="Afghanistan" aria-expanded="false"><span class="filter-option pull-left">Select Client Name</span>&nbsp;<span class="caret"></span></button>
                                                                <div class="dropdown-menu open" style="max-height: 314px; overflow: hidden;"><div class="bs-searchbox"><input type="text" class="form-control" autocomplete="off"></div>
                                                                    <ul class="dropdown-menu inner" role="menu" style="max-height: 272px; overflow-y: auto;"><li data-original-index="0" class="selected active"><a tabindex="0" class="" style="" data-tokens="null">
                                                                                <span class="text">Algeria</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="3"><a tabindex="0" class="" style="" data-tokens="null">
                                                                                <span class="text">American Samoa</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="4"><a tabindex="0" class="" style="" data-tokens="null"></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="btn-group bootstrap-select bs-select form-control dropup">
                                                                <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" title="Afghanistan" aria-expanded="false"><span class="filter-option pull-left">Select Project Name</span>&nbsp;<span class="caret"></span></button>
                                                                <div class="dropdown-menu open" style="max-height: 314px; overflow: hidden;"><div class="bs-searchbox"><input type="text" class="form-control" autocomplete="off"></div>
                                                                    <ul class="dropdown-menu inner" role="menu" style="max-height: 272px; overflow-y: auto;"><li data-original-index="0" class="selected active"><a tabindex="0" class="" style="" data-tokens="null">
                                                                                <span class="text">Algeria</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="3"><a tabindex="0" class="" style="" data-tokens="null">
                                                                                <span class="text">American Samoa</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="4"><a tabindex="0" class="" style="" data-tokens="null"></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="btn-group bootstrap-select bs-select form-control dropup">
                                                                <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" title="Afghanistan" aria-expanded="false"><span class="filter-option pull-left">Select User Name</span>&nbsp;<span class="caret"></span></button>
                                                                <div class="dropdown-menu open" style="max-height: 314px; overflow: hidden;"><div class="bs-searchbox"><input type="text" class="form-control" autocomplete="off"></div>
                                                                    <ul class="dropdown-menu inner" role="menu" style="max-height: 272px; overflow-y: auto;"><li data-original-index="0" class="selected active"><a tabindex="0" class="" style="" data-tokens="null">
                                                                                <span class="text">Algeria</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="3"><a tabindex="0" class="" style="" data-tokens="null">
                                                                                <span class="text">American Samoa</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="4"><a tabindex="0" class="" style="" data-tokens="null"></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6"><a href="#" class="btn btn-set yellow pull-right"  id="assetBtn">
                                                            <i class="fa fa-plus" style="font-size: large"></i>
                                                            Asset&nbsp &nbsp &nbsp &nbsp
                                                        </a></div>
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
                                                            <h4 class="panel-title">
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
                                                                    <tr>
                                                                        <td> 1 </td>
                                                                        <td> Mark </td>
                                                                        <td> Otto </td>
                                                                        <td> makr124 </td>
                                                                        <td>
                                                                            <div class="btn-group open">
                                                                                <button class="btn btn-xs green dropdown-toggle" type="button"  aria-expanded="true">
                                                                                    Remove
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td> 2 </td>
                                                                        <td> Jacob </td>
                                                                        <td> Nilson </td>
                                                                        <td> jac123 </td>
                                                                        <td>
                                                                            <div class="btn-group open">
                                                                                <button class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true">
                                                                                    Remove
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td> 3 </td>
                                                                        <td> Larry </td>
                                                                        <td> Cooper </td>
                                                                        <td> lar </td>
                                                                        <td>
                                                                            <div class="btn-group open">
                                                                                <button class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true">
                                                                                    Remove
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td> 4 </td>
                                                                        <td> Sandy </td>
                                                                        <td> Lim </td>
                                                                        <td> sanlim </td>
                                                                        <td>
                                                                            <div class="btn-group open">
                                                                                <button class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true">
                                                                                    Remove
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="panel panel-default">
                                                        <div class="panel-heading" role="tab" id="headingTwo">
                                                            <h4 class="panel-title">
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
                                                                        <th> MR Id </th>
                                                                        <th> Material Id </th>
                                                                        <th> Material \ Asset Name </th>
                                                                        <th> Quantity </th>
                                                                        <th> Unit </th>
                                                                        <th> Action </th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <tr>
                                                                        <td> 1 </td>
                                                                        <td> 5 </td>
                                                                        <td> Mark </td>
                                                                        <td> Otto </td>
                                                                        <td> makr124 </td>
                                                                        <td>
                                                                            <div class="btn-group open">
                                                                                <button class="btn btn-xs green dropdown-toggle" type="button"  aria-expanded="true">
                                                                                    Remove
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td> 1 </td>
                                                                        <td> 8 </td>
                                                                        <td> Jacob </td>
                                                                        <td> Nilson </td>
                                                                        <td> jac123 </td>
                                                                        <td>
                                                                            <div class="btn-group open">
                                                                                <button class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true">
                                                                                    Remove
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td> 1 </td>
                                                                        <td> 7 </td>
                                                                        <td> Larry </td>
                                                                        <td> Cooper </td>
                                                                        <td> lar </td>
                                                                        <td>
                                                                            <div class="btn-group open">
                                                                                <button class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true">
                                                                                    Remove
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td> 2 </td>
                                                                        <td> 2 </td>
                                                                        <td> Sandy </td>
                                                                        <td> Lim </td>
                                                                        <td> sanlim </td>
                                                                        <td>
                                                                            <div class="btn-group open">
                                                                                <button class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true">
                                                                                    Remove
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="panel panel-default">
                                                        <div class="panel-heading" role="tab" id="headingThree">
                                                            <h4 class="panel-title">
                                                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                                    <i class="more-less glyphicon glyphicon-plus"></i>
                                                                    <span style="float: left;font-size: 20px">Added asset list</span>
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
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
                                                                    <tr>
                                                                        <td> 1 </td>
                                                                        <td> Mark </td>
                                                                        <td> Otto </td>
                                                                        <td> makr124 </td>
                                                                        <td>
                                                                            <div class="btn-group open">
                                                                                <button class="btn btn-xs green dropdown-toggle" type="button"  aria-expanded="true">
                                                                                    Remove
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td> 2 </td>
                                                                        <td> Jacob </td>
                                                                        <td> Nilson </td>
                                                                        <td> jac123 </td>
                                                                        <td>
                                                                            <div class="btn-group open">
                                                                                <button class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true">
                                                                                    Remove
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td> 3 </td>
                                                                        <td> Larry </td>
                                                                        <td> Cooper </td>
                                                                        <td> lar </td>
                                                                        <td>
                                                                            <div class="btn-group open">
                                                                                <button class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true">
                                                                                    Remove
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td> 4 </td>
                                                                        <td> Sandy </td>
                                                                        <td> Lim </td>
                                                                        <td> sanlim </td>
                                                                        <td>
                                                                            <div class="btn-group open">
                                                                                <button class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true">
                                                                                    Remove
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
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
                                        <div class="modal-header" style="padding-bottom:10px">
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-4"> Material</div>
                                                <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                            </div>
                                        </div>
                                        <div class="modal-body" style="padding:40px 50px;">
                                            <form role="form">
                                                <div class="checkbox">
                                                    <label><input type="checkbox"  value="">Is it a diesel ?</label>
                                                </div>
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="usrname" placeholder="Enter material name">
                                                </div>
                                                <div class="form-group">
                                                    <input type="number" class="form-control" id="psw" placeholder="Enter quantity">
                                                </div>
                                                <div class="form-group">
                                                    <div class="btn-group bootstrap-select bs-select form-control dropup">
                                                        <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" title="Afghanistan" aria-expanded="false"><span class="filter-option pull-left">Select Unit</span>&nbsp;<span class="caret"></span></button>
                                                        <div class="dropdown-menu open" style="max-height: 314px; overflow: hidden;"><div class="bs-searchbox"><input type="text" class="form-control" autocomplete="off"></div>
                                                            <ul class="dropdown-menu inner" role="menu" style="max-height: 272px; overflow-y: auto;"><li data-original-index="0" class="selected active"><a tabindex="0" class="" style="" data-tokens="null">
                                                                        <span class="text">Kg</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="3"><a tabindex="0" class="" style="" data-tokens="null">
                                                                        <span class="text">Ltr</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="4"><a tabindex="0" class="" style="" data-tokens="null"></a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
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
                                                <button type="submit" class="btn red pull-right"> Create</button>
                                            </form>
                                        </div></div>
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
                                            <form role="form">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="usrname" placeholder="Enter asset name">
                                                </div>
                                                <div class="form-group">
                                                    <input type="number" class="form-control" id="psw" placeholder="Enter quantity">
                                                </div>
                                                <div class="form-group">
                                                    <div class="btn-group bootstrap-select bs-select form-control dropup">
                                                        <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" title="Afghanistan" aria-expanded="false"><span class="filter-option pull-left">Select Unit</span>&nbsp;<span class="caret"></span></button>
                                                        <div class="dropdown-menu open" style="max-height: 314px; overflow: hidden;"><div class="bs-searchbox"><input type="text" class="form-control" autocomplete="off"></div>
                                                            <ul class="dropdown-menu inner" role="menu" style="max-height: 272px; overflow-y: auto;"><li data-original-index="0" class="selected active"><a tabindex="0" class="" style="" data-tokens="null">
                                                                        <span class="text">Kg</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="3"><a tabindex="0" class="" style="" data-tokens="null">
                                                                        <span class="text">Ltr</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="4"><a tabindex="0" class="" style="" data-tokens="null"></a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
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
                                                <button type="submit" class="btn red pull-right"> Create</button>
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
    <link rel="stylesheet"  href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css"/>
    <link rel="stylesheet"  href="/assets/global/css/app.css"/>
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/custom/purchase/manage-datatable.js" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            $("#myBtn").click(function(){
                $("#myModal").modal();
            });
            $("#assetBtn").click(function(){
                $("#myModal1").modal();
            });
        });
    </script>
@endsection
