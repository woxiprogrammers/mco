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
                            <div class="row">
                                <div class="col-md-6">
                                    <div>
                                        <div class="page-title col-md-12">
                                            <h1>Manage Material</h1>
                                        </div>
                                    </div>
                                </div>
                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-material-request'))
                                    <div class="col-md-4" style="text-align: right">
                                        <div class="table-actions-wrapper" style="margin-top: 12px;">
                                            <span> </span>
                                            <form role="form" method="POST">
                                                {!! csrf_field() !!}
                                                <label>For Bulk Approval : </label>
                                                <select class="form-control input-inline" id="statusChangeDropdown">
                                                    <option value="">Select...</option>
                                                    <option value="approve">Approve</option>
                                                    <option value="disapprove">Disapprove</option>
                                                </select>
                                                <a href="javascript:void(0);" class="btn btn-sm green" id="multipleStatusChangeSubmit">
                                                    <i class="fa fa-check"></i> Submit
                                                </a>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-material-request') || $user->customHasPermission('create-material-request'))
                                    <div class="col-md-2" style="margin-top: 12px;">
                                        <div class="btn-group"  style="float: right;margin-top:1%">
                                            <div id="sample_editable_1_new" class="btn yellow" ><a href="/purchase/material-request/create" style="color: white">
                                                    <i class="fa fa-plus"></i>
                                                    Material Request
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
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
                                          <label class="checkbox-inline">
                                              <input type="checkbox" id="materialtWiseListing" value=""><span>Materialwise Listing</span>
                                          </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                                        </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                          <label class="checkbox-inline">
                                              <input type="checkbox" id="materialRequestWiseListing" value=""><span>Material Requestwise Listing</span>
                                          </label>
                                          &nbsp;&nbsp;&nbsp;<span style="color: red">(Note : Only Global Sites data displayed)</span>
                                          <hr/>
                                          <div class="portlet-body">
                                              <div class="row">
                                                  <div class="col-md-2">
                                                      <label>Select Year :</label>
                                                      <select class="form-control" id="year" name="year">
                                                          <option value="0">ALL</option>
                                                          <option value="2017">2017</option>
                                                          <option value="2018">2018</option>
                                                          <option value="2019">2019</option>
                                                          <option value="2020">2020</option>
                                                          <option value="2021">2021</option>
                                                          <option value="2022">2022</option>
                                                          <option value="2023">2023</option>
                                                      </select>
                                                  </div>
                                                  <div class="col-md-2">
                                                      <label>Select Month :</label>
                                                      <select class="form-control" id="month" name="month">
                                                          <option value="0">ALL</option>
                                                          <option value="01">Jan</option>
                                                          <option value="02">Feb</option>
                                                          <option value="03">Mar</option>
                                                          <option value="04">Apr</option>
                                                          <option value="05">May</option>
                                                          <option value="06">Jun</option>
                                                          <option value="07">Jul</option>
                                                          <option value="08">Aug</option>
                                                          <option value="09">Sep</option>
                                                          <option value="10">Oct</option>
                                                          <option value="11">Nov</option>
                                                          <option value="12">Dec</option>
                                                      </select>
                                                  </div>
                                                  <div class="col-md-2" style="display: show">
                                                      <label>MR Id :</label>
                                                      <input  class="form-control" type="number" id="m_count" name="m_count"/>
                                                  </div>
                                                  <div class="col-md-6" style="padding-top: 25px;">
                                                      <label>&nbsp;</label>
                                                      <div class="btn-group">
                                                          <div id="search-withfilter" class="btn blue" >
                                                              <a href="#" style="color: white"> Submit
                                                                  <i class="fa fa-plus"></i>
                                                              </a>
                                                          </div>
                                                          <a class="btn red" style="margin-left: 10px;" href="javascript:void(0);" id="bulkMultipleStatusChangeSubmit">
                                                              <i class="fa fa-check"></i>Bulk Move To Indent
                                                          </a>
                                                      </div>
                                                  </div>
                                              </div>
                                              <hr/>
                                          <div class="table-container">
                                          <table class="table table-striped table-bordered table-hover table-checkable order-column" id="materialRequest">
                                              <thead>
                                              <tr>
                                                  <th data-width="10%"> Bulk Approval</th>
                                                  <th> Bulk Move to Indent</th>
                                                  <th> MR Id </th>
                                                  <th> Material Name </th>
                                                  <th> Quantity</th>
                                                  <th> Unit </th>
                                                  <th> Created At</th>
                                                  <th> Status </th>
                                                  <th> Action </th>
                                                  <th> Detail </th>
                                              </tr>
                                              <tr class="filter">
                                                  <th></th>
                                                  <th><input type="hidden" class="form-control form-filter" name="postdata" id="postdata"></th>
                                                  <th><input type="text" class="form-control form-filter custom_filter" name="m_id" id="m_id" ></th>
                                                  <th><input type="text" class="form-control form-filter custom_filter" name="m_name" id="m_name" ></th>
                                                  <th> </th>
                                                  <th> </th>
                                                  <th> </th>
                                                  <th>
                                                      <select class="form-control" id="status_id" name="status_id">
                                                          <option value="0">ALL</option>
                                                          @foreach($purchaseStatus as $status)
                                                          <option value="{{$status['id']}}">{{$status['name']}}</option>
                                                          @endforeach
                                                      </select>
                                                      <input type="hidden" class="form-control form-filter" name="status" id="status">
                                                  </th>
                                                  <th>
                                                      <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                      <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                  </th>
                                                  <th></th>
                                              </tr>
                                              </thead>
                                              <tbody>

                                              </tbody>
                                          </table>
                                        </div>
                                        <div class="modal fade" id="remarkModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form class="modal-content" method="post">
                                                    {!! csrf_field() !!}
                                                    <input type="hidden" name="component_id[]" id="componentId">
                                                    <div class="modal-header">
                                                      <div class="row">
                                                          <div class="col-md-4"></div>
                                                          <div class="col-md-4"><center><h4 class="modal-title" id="exampleModalLongTitle">REMARK</h4></center></div>
                                                          <div class="col-md-4"><button type="button" class="btn btn-warning pull-right" data-dismiss="modal"><i class="fa fa-close" style="font-size: medium"></i></button></div>
                                                      </div>
                                                   </div>
                                                  <div class="modal-body">
                                                      <div class="form-body">
                                                          <div class="form-group row">
                                                              <div class="col-md-3" style="text-align: right">
                                                                  <label for="company" class="control-label">Quantity</label>
                                                              </div>
                                                              <div class="col-md-6">
                                                                  <input type="text" class="form-control" id="quantity" name="quantity">
                                                              </div>
                                                          </div>
                                                          <div class="form-group row">
                                                              <div class="col-md-3" style="text-align: right">
                                                                  <label for="company" class="control-label">Unit</label>
                                                              </div>
                                                              <div class="col-md-6">
                                                                  <select class="form-control" name="unit_id" id="unitId">
                                                                      @foreach($units as $unit)
                                                                          <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                                                                      @endforeach
                                                                  </select>
                                                              </div>
                                                          </div>
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
                                                  <div class="modal-footer">
                                                      <a class="btn blue approve-modal-footer-buttons">Approve</a>
                                                    <a class="btn blue approve-modal-footer-buttons">Disapprove</a>
                                                  </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="indentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                  <div class="modal-dialog">
                                                      <form class="modal-content" method="post">
                                                          {!! csrf_field() !!}
                                                          <input type="hidden" name="component_id" id="componentId">
                                                          <div class="modal-header">
                                                              <div class="row">
                                                                  <div class="col-md-4"></div>
                                                                  <div class="col-md-4"><center><h4 class="modal-title" id="exampleModalLongTitle">REMARK</h4></center></div>
                                                                  <div class="col-md-4"><button type="button" class="btn btn-warning pull-right" data-dismiss="modal"><i class="fa fa-close" style="font-size: medium"></i></button></div>
                                                              </div>
                                                          </div>
                                                          <div class="modal-body">
                                                              <div class="form-body">
                                                                  <div class="form-group row">
                                                                      <div class="col-md-3" style="text-align: right">
                                                                          <label for="company" class="control-label">Quantity</label>
                                                                      </div>
                                                                      <div class="col-md-6">
                                                                          <input type="text" class="form-control" id="quantity" name="quantity">
                                                                      </div>
                                                                  </div>
                                                                  <div class="form-group row">
                                                                      <div class="col-md-3" style="text-align: right">
                                                                          <label for="company" class="control-label">Unit</label>
                                                                      </div>
                                                                      <div class="col-md-6">
                                                                          <select class="form-control" name="unit_id" id="unitId">
                                                                              @foreach($units as $unit)
                                                                                  <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                                                                              @endforeach
                                                                          </select>
                                                                      </div>
                                                                  </div>
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
                                                          <div class="modal-footer">
                                                              <a class="btn blue approve-modal-footer-buttons">Move to Indent</a>
                                                          </div>
                                                      </form>
                                                  </div>
                                          </div>
                                              <div class="modal fade" id="BulkIndentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                  <div class="modal-dialog">
                                                      <form class="modal-content" method="post" id="bulkMoveToIndentFormId">
                                                          {!! csrf_field() !!}
                                                          <div class="modal-header">
                                                              <div class="row">
                                                                  <div class="col-md-8"><center><h4 class="modal-title" id="exampleModalLongTitle">BULK Move to Indent Form</h4></center></div>
                                                                  <div class="col-md-4"><button type="button" class="btn btn-warning pull-right" data-dismiss="modal"><i class="fa fa-close" style="font-size: medium"></i></button></div>
                                                              </div>
                                                          </div>
                                                          <div class="modal-body">
                                                              <div class="form-body">
                                                                  <div class="form-group row">
                                                                     <div class="form-group row">
                                                                      <div class="col-md-3" style="text-align: right">
                                                                          <label for="company" class="control-label">Remark</label>
                                                                      </div>
                                                                      <div class="col-md-6">
                                                                          <input type="text" class="form-control" id="remark" name="remark" required="required">
                                                                      </div>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                          <div class="modal-footer">
                                                              <a class="btn blue bulk-mti-modal-footer-buttons">Submit</a>
                                                          </div>
                                                      </form>
                                                  </div>
                                              </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          </div>
                            <div class="modal fade" id="purchaseDetailModel" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header" style="padding-bottom:10px">
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-4"><center><h4 class="modal-title" id="exampleModalLongTitle">Purchase Details</h4></center></div>
                                                <div class="col-md-4"><button type="button" class="close" data-dismiss="modal"><i class="fa fa-close" style="font-size: medium"></i></button></div>
                                            </div>
                                        </div>
                                        <div class="modal-body" style="padding:40px 50px; font-size: 15px">

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
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/custom/purchase/material-request/manage-materialRequest-datatable.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#materialRequest').DataTable();
        $('[data-toggle="tooltip"]').tooltip();
        $('#materialtWiseListing').attr ( "checked" ,"checked" );
        $('#materialRequestWiseListing').change(function(){
            window.location.replace("/purchase/material-request/material-requestWise-listing-view");
        });
        $(".approve-modal-footer-buttons").on('click',function(){
            var buttonType = $(this).text();
            if(buttonType == 'Approve'){
                var action = "/purchase/material-request/change-status/admin-approved";
            }else{
                if(buttonType == 'Disapprove'){
                    var action = "/purchase/material-request/change-status/admin-disapproved"
                }else{
                    if(buttonType == 'Move to Indent'){
                        var action = "/purchase/material-request/change-status/in-indent"
                    }
                }
            }
            $(this).closest('form').attr('action',action);
            $(this).closest('form').submit();
        });
        $("#statusChangeDropdown").on('change',function(){
            var statusChangeId = $(this).val();
            if(statusChangeId == 'approve'){
                var action = "/purchase/material-request/change-status/admin-approved";
                $("#statusChangeDropdown").closest('form').attr('action',action);
            }else{
                if(statusChangeId == 'disapprove'){
                    var action = "/purchase/material-request/change-status/admin-disapproved"
                    $("#statusChangeDropdown").closest('form').attr('action',action);
                }else{
                    $("#statusChangeDropdown").closest('form').removeAttr('action');
                }
            }
        });

        $('#multipleStatusChangeSubmit').on('click', function(){
            if($(".multiple-select-checkbox:checkbox:checked").length > 0){
                var selectComponentIds = [];
                $(".multiple-select-checkbox:checkbox:checked").each(function(){
                    $('#multipleStatusChangeSubmit').closest('form').append('<input type="hidden" name="component_id[]" value="'+$(this).val()+'">');
                });
                $('#multipleStatusChangeSubmit').closest('form').submit();
            }else{
                alert(' Please select atleast one material for Bulk Approve/Disapprove.!')
            }
        });

        $('#bulkMultipleStatusChangeSubmit').on('click', function(){
            if($(".multiple-select-checkbox-mti:checkbox:checked").length > 0){
                openBulkMoveToIndent();
                $(".multiple-select-checkbox-mti:checkbox:checked").each(function(){
                    $('#bulkMoveToIndentFormId').closest('form').append('<input type="hidden" name="bulk_component_id[]" value="'+$(this).val()+'">');
                });
               // $('#bulkMoveToIndentFormId').closest('form').submit();
            }else{
                alert(' Please select atleast one material for Bulk Move to Indent.!')
            }
        });

        $(".bulk-mti-modal-footer-buttons").on('click',function(){
            var action = "/purchase/material-request/change-status-mti";
            $(this).closest('form').attr('action',action);
            $(this).closest('form').submit();
        });

        $("#status_id").on('change',function(){
            var site_id = $('#globalProjectSite').val();
            var year = $('#year').val();
            var month = $('#month').val();
            var status_id = $('#status_id').val();
            var m_name = $('#m_name').val();
            var m_id = $('#m_id').val();
            var m_count = $('#m_count').val();

            var postData =
                    'site_id=>'+site_id+','+
                    'year=>'+year+','+
                    'month=>'+month+','+
                    'm_count=>'+m_count;

            $("input[name='postdata']").val(postData);
            $("input[name='m_name']").val(m_name);
            $("input[name='m_id']").val(m_id);
            $("input[name='status']").val(status_id);
            $(".filter-submit").trigger('click');
        });

        $(".custom_filter").on('keyup',function(){
            if ($("#m_name").val().length > 3 || $("#m_id").val().length > 3) {
                var site_id = $('#globalProjectSite').val();
                var year = $('#year').val();
                var month = $('#month').val();
                var status_id = $('#status_id').val();
                var m_name = $('#m_name').val();
                var m_id = $('#m_id').val();
                var m_count = $('#m_count').val();

                var postData =
                    'site_id=>'+site_id+','+
                        'year=>'+year+','+
                        'month=>'+month+','+
                        'm_count=>'+m_count;

                $("input[name='postdata']").val(postData);
                $("input[name='m_name']").val(m_name);
                $("input[name='m_id']").val(m_id);
                $("input[name='status']").val(status_id);
                $(".filter-submit").trigger('click');
            }
        });


        $("#search-withfilter").on('click',function(){
            var client_id = $('#client_id').val();
            var project_id = $('#project_id').val();
            var site_id = $('#site_id').val();
            var year = $('#year').val();
            var month = $('#month').val();
            var status_id = $('#status_id').val();
            var m_id = $('#m_id').val();
            var m_name = $('#m_name').val();
            var m_count = $('#m_count').val();

            var postData =
                'client_id=>'+client_id+','+
                    'project_id=>'+project_id+','+
                    'site_id=>'+site_id+','+
                    'year=>'+year+','+
                    'month=>'+month+','+
                    'm_count=>'+m_count;

            $("input[name='postdata']").val(postData);
            $("input[name='m_id']").val(m_id);
            $("input[name='m_name']").val(m_name);
            $("input[name='status']").val(status_id);
            $(".filter-submit").trigger('click');
        });
    });

    function openBulkMoveToIndent() {
        $("#BulkIndentModal").modal('show');
    }

    function openApproveModal(componentId){
        $.ajax({
            url: '/purchase/material-request/get-material-request-component-details/'+componentId+'?_token='+$("input[name='_token']").val(),
            type: 'GET',
            success: function(data,textStatus,xhr){
                $("#remarkModal #unitId").html(data.units);
                $("#remarkModal #quantity").val(data.quantity);
                $("#remarkModal #componentId").val(componentId);
            },
            error: function(errorData){

            }
        })
        $("#remarkModal").modal('show');
    }


    function submitIndentForm(element){
        var token = $('input[name="_token"]').val();
        $(element).next('input[name="_token"]').val(token);
        $(element).closest('form').submit();
    }

    function openIndentModal(componentId){
        $.ajax({
            url: '/purchase/material-request/get-material-request-component-details/'+componentId+'?_token='+$("input[name='_token']").val(),
            type: 'GET',
            success: function(data,textStatus,xhr){
                $("#indentModal #unitId").html(data.units);
                $("#indentModal #quantity").val(data.quantity);
                $("#indentModal #componentId").val(componentId);
            },
            error: function(errorData){

            }
        })
        $("#indentModal").modal('show');
    }

    function openDetails(materialRequestComponentId){
        $.ajax({
            url: '/purchase/get-detail/'+materialRequestComponentId+'?_token='+$("input[name='_token']").val(),
            type: 'GET',
            async: true,
            success: function(data,textStatus,xhr){
                $("#purchaseDetailModel .modal-body").html(data);
                $("#purchaseDetailModel").modal('show');
            },
            error:function(errorData){
                alert('Something went wrong');
            }

        });
    }
</script>
@endsection