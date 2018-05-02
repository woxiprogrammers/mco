@extends('layout.master')
@section('title','Constro | Manage Subcontractor Structure')
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
                                <h1>Manage Subcontractor Structure</h1>
                            </div>
                            @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-subcontractor-structure'))
                            <div class="btn-group" style="float: right;margin-top:1%">
                                <div id="sample_editable_1_new" class="btn yellow" ><a href="/subcontractor/subcontractor-structure/create" style="color: white"> Subcontractor Structure
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
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
                                        <span style="color: red">(Note : All Sites data displayed)</span>
                                        {!! csrf_field() !!}
                                        <div class="portlet-body">
                                            <div class="row">
                                            <table class="table table-striped table-bordered table-hover" id="subcontractorStructureTable">
                                                <thead>
                                                <tr>
                                                    <th data-width="15%"> Subcontractor Name </th>
                                                    <th data-width="15%"> Project Name </th>
                                                    <th> Summary Name </th>
                                                    <th> Contract type </th>
                                                    <th> Rate </th>
                                                    <th> Total Work Area </th>
                                                    <th> Total Amount </th>
                                                    <th> Bill Amount </th>
                                                    <th> Paid Amount </th>
                                                    <th> Balance Amount</th>
                                                    <th> Created On </th>
                                                    <th> Actions </th>
                                                </tr>
                                                <tr class="filter">
                                                    <th> <input type="text" class="form-control form-filter" name="subcontractor_name" id="subcontractor_name"> </th>
                                                    <th> <input type="text" class="form-control form-filter" name="project_name" id="project_name"></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th>
                                                        <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                        <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <th colspan="6" style="text-align:right">Total Page Wise: </th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                </tfoot>
                                            </table>
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
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/custom/subcontractor/subcontractor.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#subcontractorStructureTable').DataTable();

        $("#client_id").on('change', function(){
            getProjects($('#client_id').val());
        });
        $("#project_id").on('change', function(){
            getProjectSites($('#project_id').val());
        });

        $("input[name='subcontractor_name']").on('keyup',function(){
            $(".filter-submit").trigger('click');
        });

        $("input[name='project_name']").on('keyup',function(){
            $(".filter-submit").trigger('click');
        });
    });

    function getProjects(client_id){
        $.ajax({
            url: '/subcontractor/projects/'+client_id,
            type: 'GET',
            async : false,
            success: function(data,textStatus,xhr){
                if(xhr.status == 200){
                    $('#project_id').html(data);
                    $('#project_id').prop('disabled',false);
                    getProjectSites($('#project_id').val());
                }
            },
            error: function(errorStatus,xhr){

            }
        });
    }

    function getProjectSites(project_id){
        $.ajax({
            url: '/subcontractor/project-sites/'+project_id,
            type: 'GET',
            async : false,
            success: function(data,textStatus,xhr){
                if(xhr.status == 200){
                    $('#site_id').html(data);
                    $('#site_id').prop('disabled',false);
                    $("#search-withfilter").trigger('click');
                }
            },
            error: function(errorStatus,xhr){

            }
        });
    }
</script>
@endsection
