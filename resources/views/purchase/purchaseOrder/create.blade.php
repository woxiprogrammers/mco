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
                                    <h1>Create Material Request</h1>
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
                                                            <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" title="Afghanistan" aria-expanded="false"><span class="filter-option pull-left">Select site Name</span>&nbsp;<span class="caret"></span></button>
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
                                                    <div class="form-group">
                                                        <div class="btn-group bootstrap-select bs-select form-control dropup">
                                                            <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" title="Afghanistan" aria-expanded="false"><span class="filter-option pull-left">Purchase request number</span>&nbsp;<span class="caret"></span></button>
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
                                                    <div class="form-group " style="text-align: center">
                                                        <a href="#" class="btn btn-lg yellow pull-right">
                                                            <i class="fa fa-plus" style="font-size: large"></i>
                                                            Submit
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body">
                                            <div class="table-container">
                                               <table class="table table-striped table-bordered table-hover table-checkable order-column" id="purchaseRequest">
                                                    <thead>
                                                    <tr>
                                                        <th><input type="checkbox"></th>
                                                        <th> Material Name </th>
                                                        <th> Quantity</th>
                                                        <th> Unit </th>
                                                        <th> Vendor </th>
                                                        <th> Image </th>
                                                        <th> Is approved </th>
                                                    </tr>
                                                    <tr class="filter">
                                                        <th style="text-align: center"><input type="checkbox"></th>
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
                                                        <td><input type="checkbox"></td>
                                                        <td> ABC </td>
                                                        <td> 5</td>
                                                        <td> Kg </td>
                                                        <td> Vendor lmn </td>
                                                        <td> <button>Browse</button> <button>Upload</button> </td>
                                                        <td> Is approved </td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="checkbox"></td>
                                                        <td> ABC </td>
                                                        <td> 5</td>
                                                        <td> Kg </td>
                                                        <td> Vendor lmn </td>
                                                        <td> <button>Browse</button> <button>Upload</button> </td>
                                                        <td> Is approved </td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="checkbox"></td>
                                                        <td> ABC </td>
                                                        <td> 5</td>
                                                        <td> Kg </td>
                                                        <td> Vendor lmn </td>
                                                        <td> <button>Browse</button> <button>Upload</button> </td>
                                                        <td> Is approved </td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="checkbox"></td>
                                                        <td> ABC </td>
                                                        <td> 5</td>
                                                        <td> Kg </td>
                                                        <td> Vendor lmn </td>
                                                        <td> <button>Browse</button> <button>Upload</button> </td>
                                                        <td> Is approved </td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="checkbox"></td>
                                                        <td> ABC </td>
                                                        <td> 5</td>
                                                        <td> Kg </td>
                                                        <td> Vendor lmn </td>
                                                        <td> <button>Browse</button> <button>Upload</button> </td>
                                                        <td> Is approved </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal fade" id="remarkModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form class="modal-content">
                                                        <div class="modal-header" style="background-color:#00844d">
                                                            <center><h4 class="modal-title" id="exampleModalLongTitle">ADD REMARK</h4></center>
                                                            <button type="button" class="btn btn-warning pull-right" data-dismiss="modal"><i class="fa fa-close" style="font-size: medium"></i></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form role="form" class="form-horizontal" method="post">
                                                                <div class="form-body">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-3" style="text-align: right">
                                                                            <label for="company" class="control-label">Remark</label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="remark" name="remark">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                        </div>
                                                        <div class="modal-footer" style="background-color:#00844d">
                                                            <button type="submit" class="btn blue">Approve</button>
                                                            <button type="submit" class="btn blue">disapprove</button>
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
            TableData.init();
        });
    </script>
@endsection
