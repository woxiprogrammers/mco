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
                                <h1>Manage Inventory</h1>
                            </div>
                            @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-inventory-in-out-transfer'))
                            <div id="sample_editable_1_new" style="margin-top: 1%; margin-left: 70%">
                                <a href="javascript:void(0);" style="color: white" id="generateChallan" class="btn yellow">
                                    <!-- <i class="fa fa-plus"></i> --> Generate Challan
                                </a>
                                <a href="javascript:void(0);" style="color: white" id="createInventoryComponent" class="btn yellow">
                                    <i class="fa fa-plus"></i> Inventory Component
                                </a>
                            </div>
                            @endif

                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                    <div class="portlet light ">
                                        {!! csrf_field() !!}
                                        <div class="portlet-body">
                                            <div class="portlet-body">
                                                <div class="table-container">
                                                    <div class="row">
                                                        <div class="col-md-2 pull-right">
                                                            <button type="button" class="btn btn-small blue" id="updateCart"> Cart </button>
                                                        </div>
                                                    </div>
                                                    <table class="table table-striped table-bordered table-hover order-column" id="inventoryListingTable">
                                                        <thead>
                                                            <tr>
                                                                <th> </th>
                                                                <th> Material Name </th>
                                                                <th> In</th>
                                                                <th> Out </th>
                                                                <th> Available </th>
                                                                <th> Type </th>
                                                                <th> Action </th>
                                                            </tr>
                                                            <tr class="filter">
                                                                <th> <input type="hidden" class="form-control form-filter search_filter" name="search_component_id" id="search_component_id"> </th>
                                                                <th> <input type="text" class="form-control form-filter search_filter" name="search_name" id="search_name"> </th>
                                                                <th> </th>
                                                                <th> </th>
                                                                <th> </th>
                                                                <th> </th>
                                                                <th>
                                                                    <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                                    <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="inventoryComponentModal" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header" style="padding-bottom:10px">
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-4"> Inventory Component</div>
                                                <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                            </div>
                                        </div>
                                        <div class="modal-body" style="padding:40px 50px;">
                                            <form role="form" action="/inventory/component/create" method="POST" id="createComponentForm">
                                                {!! csrf_field() !!}
                                                <div class="form-group row">
                                                    <div class="col-md-4" style="text-align: right">
                                                        <label for="name" class="control-label">Inventory Type: </label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select class="form-control" id="inventory_type" name="inventory_type">
                                                            <option value="">Select Inventory Type</option>
                                                            <option value="material">Material</option>
                                                            <option value="asset">Asset</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-4" style="text-align: right">
                                                        <label for="name" class="control-label">Name : </label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" id="name" name="name">
                                                    </div>
                                                </div>
                                                <input type="hidden" class="form-control" id="reference_id" name="reference_id">
                                                <div class="form-group row">
                                                    <div class="col-md-4" style="text-align: right">
                                                        <label for="name" class="control-label">Opening Stock : </label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" id="opening_stock" name="opening_stock">
                                                    </div>
                                                </div>
                                                <button type="button" class="btn red pull-right" id="createComponentButton" hidden> Create</button>
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
</div>
@endsection
@section('javascript')
<link rel="stylesheet" href="/assets/global/plugins/datatables/datatables.min.css" />
<script src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
<script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
<script src="/assets/custom/inventory/manage-datatable.js" type="text/javascript"></script>
<script>

</script>
@endsection