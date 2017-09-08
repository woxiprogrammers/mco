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
                                    <h1>Create Purchase Order</h1>
                                </div>
                                <div class="form-group " style="text-align: center">
                                    <a href="#" class="btn red pull-right margin-top-15">
                                        <i class="fa fa-check" style="font-size: large"></i>
                                        Submit
                                    </a>
                                </div>
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
                                                            <div class="btn-group bootstrap-select bs-select form-control dropup">
                                                                <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" title="Afghanistan" aria-expanded="false"><span class="filter-option pull-left">Purchase Request Number</span>&nbsp;<span class="caret"></span></button>
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
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4"><div class="form-group">
                                                            <div class="btn-group bootstrap-select bs-select form-control dropup">
                                                                <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" title="Afghanistan" aria-expanded="false"><span class="filter-option pull-left">For which have to request</span>&nbsp;<span class="caret"></span></button>
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

                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4"></div>
                                                    <div class="col-md-4"></div>
                                                    <div class="col-md-4">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN VALIDATION STATES-->
                                        <div class="portlet light ">
                                            <div class="portlet-body">
                                                <div class="table-container">
                                                    <table class="table table-striped table-bordered table-hover order-column" id="purchaseRequest">
                                                        <thead>
                                                        <tr>
                                                            <th> Material Name </th>
                                                            <th> Quantity</th>
                                                            <th> Unit </th>
                                                            <th> Vendor </th>
                                                            <th>Status</th>
                                                            <th>Action</th>
                                                        </tr>
                                                        <tr class="filter">
                                                            <th><input type="text" class="form-control form-filter" name="search_name"></th>
                                                            <th><input type="text" class="form-control form-filter" name="search_name" > </th>
                                                            <th><input type="text" class="form-control form-filter" name="search_name"></th>
                                                            <th><input type="text" class="form-control form-filter" name="search_name" > </th>
                                                            <th><input type="text" class="form-control form-filter" name="search_name" > </th>
                                                            <th>
                                                                <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                                <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td> ABC </td>
                                                            <td> 5</td>
                                                            <td> Kg </td>
                                                            <td> Vendor lmn </td>
                                                            <td>
                                                                <select class="table-group-action-input form-control input-inline input-small input-sm">
                                                                    <option value="">Select...</option>
                                                                    <option value="Cancel">Approve</option>
                                                                    <option value="Cancel">Disapprove</option>
                                                                </select>
                                                            </td>
                                                            <td> <button id="image">Upload</button>  </td>
                                                        </tr>
                                                        <tr>
                                                            <td> ABC </td>
                                                            <td> 5</td>
                                                            <td> Kg </td>
                                                            <td> Vendor lmn </td>
                                                            <td>
                                                                <select class="table-group-action-input form-control input-inline input-small input-sm">
                                                                    <option value="">Select...</option>
                                                                    <option value="Cancel">Approve</option>
                                                                    <option value="Cancel">Disapprove</option>
                                                                </select>
                                                            </td>
                                                            <td> <button id="image">Upload</button> </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="modal fade" id="ImageUpload" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form class="modal-content">
                                                            <div class="modal-header" >
                                                                <div class="row">
                                                                    <div class="col-md-4"></div>
                                                                    <div class="col-md-4"> Material</div>
                                                                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form role="form" class="form-horizontal" method="post">
                                                                    <div class="form-body">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-12" style="text-align: right">
                                                                                <table class="table table-bordered table-hover">
                                                                                    <thead>
                                                                                    <tr role="row" class="heading">
                                                                                        <th> Image </th>
                                                                                        <th> Action </th>
                                                                                    </tr>
                                                                                    </thead>
                                                                                    <tbody id="show-product-images">
                                                                                    <tr >
                                                                                        <td>
                                                                                            <a  target="_blank" class="fancybox-button" data-rel="fancybox-button">
                                                                                                <img class="img-responsive"  alt="" style="width:100px; height:100px;"> </a>
                                                                                            <input type="hidden" class="work-order-image-name"   />
                                                                                        </td>
                                                                                        <td>
                                                                                            <a href="javascript:;" class="btn btn-default btn-sm">
                                                                                                <i class="fa fa-times"></i> Remove </a>
                                                                                        </td>
                                                                                    </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div style="padding-bottom: 5px;padding-left: 3px">
                                                                        <button type="submit" class="btn blue" >Approve</button>
                                                                        <button type="submit" class="btn blue">disapprove</button>
                                                                    </div>
                                                                </form>
                                                            </div>

                                                    </div>
                                                </div>
                                                <div class="modal fade" id="approveModal" role="dialog">
                                                    <div class="modal-dialog">
                                                        <!-- Modal content-->
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="row">
                                                                    <div class="col-md-4"></div>
                                                                    <div class="col-md-4" style="font-size: 18px"> Approve</div>
                                                                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body" style="padding:40px 50px;">
                                                                <div class="form-body">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-3" style="text-align: right">
                                                                            <label for="name" class="control-label">Quotation Image</label>
                                                                            <span>*</span>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="file" class="form-control" id="name" name="name">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-body">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-3" style="text-align: right">
                                                                            <label for="name" class="control-label">Client approval note image</label>
                                                                            <span>*</span>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="file" class="form-control" id="name" name="name">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-body">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-3" style="text-align: right">
                                                                            <label for="name" class="control-label">Delivery date</label>
                                                                            <span>*</span>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="name" name="name">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-body">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-3" style="text-align: right">
                                                                            <label for="name" class="control-label">Created Date</label>
                                                                            <span>*</span>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="name" name="name">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-body">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-3" style="text-align: right">
                                                                            <label for="name" class="control-label">Rate</label>
                                                                            <span>*</span>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="name" name="name">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-body">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-3" style="text-align: right">
                                                                            <label for="name" class="control-label">GST Y</label>
                                                                            <span>*</span>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="name" name="name">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-body">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-3" style="text-align: right">
                                                                            <label for="name" class="control-label">HSN code</label>
                                                                            <span>*</span>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="name" name="name">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <a href="#" class="btn btn-set yellow pull-right">
                                                                    <i class="fa fa-check" style="font-size: large"></i>
                                                                    Approve &nbsp; &nbsp; &nbsp;
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="disapproveModal" role="dialog">
                                                    <div class="modal-dialog">
                                                        <!-- Modal content-->
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="row">
                                                                    <div class="col-md-4"></div>
                                                                    <div class="col-md-4" style="font-size: 18px"> Vendor assignment</div>
                                                                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body" style="padding:40px 50px;">
                                                                <div class="form-body">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-3" style="text-align: right">
                                                                            <label for="name" class="control-label">Quotation image</label>
                                                                            <span>*</span>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="file" class="form-control" id="name" name="name">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-3" style="text-align: right">
                                                                            <label for="name" class="control-label">Client approval image</label>
                                                                            <span>*</span>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="file" class="form-control" id="name" name="name">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-3" style="text-align: right">
                                                                            <label for="name" class="control-label">Remark</label>
                                                                            <span>*</span>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="name" name="name">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <a href="#" class="btn btn-set yellow pull-right">
                                                                    <i class="fa fa-check" style="font-size: large"></i>
                                                                    Disapprove&nbsp; &nbsp; &nbsp;
                                                                </a>
                                                            </div>
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
    <script>
        $(document).ready(function(){
            $("#image").click(function(){
                $("#ImageUpload").modal();
            })
        });
    </script>
@endsection
