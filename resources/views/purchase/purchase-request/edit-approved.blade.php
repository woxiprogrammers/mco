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
                                    <h1>Edit Purchase Request</h1>
                                </div>
                                <div class="form-group " style="float: right;margin-top:1%">
                                    {!! csrf_field() !!}
                                </div>
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
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <input type="hidden" id="purchaseRequestId" value="{{$purchaseRequest->id}}">
                                        <div class="portlet-body form">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" name="client_name" value="{{$purchaseRequest->projectSite->project->client->company}}" readonly tabindex="-1">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="btn-group bootstrap-select bs-select form-control dropup">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="project_sites_name" value="{{$purchaseRequest->projectSite->name}}" readonly tabindex="-1">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="btn-group bootstrap-select bs-select form-control dropup">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" id="on_behalf_of" value="{{$purchaseRequest->onBehalfOfUser->first_name}} {{$purchaseRequest->onBehalfOfUser->last_name}}" readonly tabindex="-1">
                                                            </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        @if($userRole == 'superadmin')
                                            {{--<div class="row">
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
                                            </div>--}}
                                        @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <div class="portlet light ">
                                                <div class="portlet-title">
                                                    @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-vendor-assignment'))
                                                        <button class="btn btn-xs green  pull-right" type="button" aria-expanded="true" id="previewBtn">
                                                            Preview
                                                        </button>
                                                    @endif
                                                    <div class="caption">
                                                        <i class="fa fa-bars font-red"></i>&nbsp
                                                        <span class="caption-subject font-red sbold uppercase">Material / Asset List</span>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <table class="table table-hover table-light" style="overflow-y: scroll" id="componentTable">
                                                        <thead>
                                                        <tr>
                                                            <th> ID </th>
                                                            <th> Name </th>
                                                            <th> Quantity </th>
                                                            <th>Action</th>
                                                            <th> Unit </th>
                                                            @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-vendor-assignment'))
                                                                <th width="50%"> Action </th>
                                                            @endif
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            @for($iterator = 0 ; $iterator < count($materialRequestComponentDetails); $iterator++)
                                                                <tr>
                                                                    <td> {{$materialRequestComponentDetails[$iterator]['id']}} </td>
                                                                    <td> {{$materialRequestComponentDetails[$iterator]['name']}} </td>
                                                                    <td> <a href="javascript:void(0);" onclick="editQuantity({{$materialRequestComponentDetails[$iterator]['id']}})" id="componentQuantity-{{$materialRequestComponentDetails[$iterator]['id']}}">{{$materialRequestComponentDetails[$iterator]['quantity']}}</a> </td>
                                                                    <td> <a href="javascript:void(0);"  onclick="checkQuantity({{$materialRequestComponentDetails[$iterator]['id']}})"> Check Quantity </a></td>
                                                                    <td> {{$materialRequestComponentDetails[$iterator]->unit->name}} </td>
                                                                    @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-vendor-assignment'))
                                                                        <td>
                                                                            <div id="select-vendor-{{$materialRequestComponentDetails[$iterator]['id']}}">
                                                                                <select class="form-control input-lg select2-multiple" name="material_vendors[{{$materialRequestComponentDetails[$iterator]['id']}}][]" multiple="multiple" style="overflow:hidden" data-placeholder="Select Vendor">
                                                                                    @for($iterator1 = 0 ; $iterator1 < count($materialRequestComponentDetails[$iterator]['vendors']); $iterator1++)
                                                                                        @if(array_key_exists('is_client',$materialRequestComponentDetails[$iterator]['vendors'][$iterator1]) && $materialRequestComponentDetails[$iterator]['vendors'][$iterator1]['is_client'] == true)

                                                                                            @if(in_array($materialRequestComponentDetails[$iterator]['vendors'][$iterator1]['id'],$assignedClientData[$materialRequestComponentDetails[$iterator]['id']]))
                                                                                                <option value="client_{{$materialRequestComponentDetails[$iterator]['vendors'][$iterator1]['id']}}" selected>{{$materialRequestComponentDetails[$iterator]['vendors'][$iterator1]['company']}}</option>
                                                                                            @else
                                                                                                <option value="client_{{$materialRequestComponentDetails[$iterator]['vendors'][$iterator1]['id']}}">{{$materialRequestComponentDetails[$iterator]['vendors'][$iterator1]['company']}}</option>
                                                                                            @endif
                                                                                        @else
                                                                                            @if(in_array($materialRequestComponentDetails[$iterator]['vendors'][$iterator1]['id'],$assignedVendorData[$materialRequestComponentDetails[$iterator]['id']]))
                                                                                                <option value="{{$materialRequestComponentDetails[$iterator]['vendors'][$iterator1]['id']}}" selected>{{$materialRequestComponentDetails[$iterator]['vendors'][$iterator1]['company']}}</option>
                                                                                            @else
                                                                                                <option value="{{$materialRequestComponentDetails[$iterator]['vendors'][$iterator1]['id']}}">{{$materialRequestComponentDetails[$iterator]['vendors'][$iterator1]['company']}}</option>
                                                                                            @endif
                                                                                        @endif
                                                                                    @endfor
                                                                                </select>
                                                                            </div>
                                                                        </td>
                                                                    @endif
                                                                </tr>
                                                            @endfor
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
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
                                                    <label><input type="checkbox" value="">Is it a diesel ?</label>
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
                                            <form role="form">
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="vendorPreviewModal" role="dialog">
                                <div class="modal-dialog" style="width: 80% !important;">
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
                                            <form role="form" id="vendorAssignmentForm" action="/purchase/purchase-request/assign-vendors" method="post">
                                                {!! csrf_field() !!}
                                                <input type="hidden" name="is_mail" id="is_mail" value="1">
                                                <input type="hidden" name="purchase_request_id" value="{{$purchaseRequest['id']}}">
                                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

                                                </div><!-- panel-group -->
                                                <button type="submit" class="btn btn-set yellow" id="submitVendorAssignmentForm">
                                                    <i class="fa fa-check" style="font-size: large"></i>
                                                    Send mail to vendors&nbsp; &nbsp; &nbsp; &nbsp;
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="inventoryQuantityModal" role="dialog">
                                <div class="modal-dialog" style="width: 60% !important;">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-4" style="font-size: 18px"> Inventory Quantities </div>
                                                <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                            </div>
                                        </div>
                                        <div class="modal-body" style="padding:40px 50px;">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="editQuantityModal" role="dialog">
                                <div class="modal-dialog" style="width: 60% !important;">                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-4" style="font-size: 18px"> Edit Quantity </div>
                                                <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                            </div>
                                        </div>
                                        <div class="modal-body" style="padding:40px 50px;">
                                            <input type="hidden" id="editQuantityMaterialId">
                                            <div class="form-group row">
                                                <div class="col-md-3">
                                                    <label class="control-label pull-right">
                                                        Quantity :
                                                    </label>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="quantity" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-3">
                                                    <label class="control-label pull-right">
                                                        Remark :
                                                    </label>
                                                </div>
                                                <div class="col-md-6">
                                                    <textarea class="form-control" name="remark" required></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-6 col-md-offset-3">
                                                    <a href="javascript:void(0);" class="btn red" onclick="submitEditQuantity()">Submit</a>
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
    <link rel="stylesheet"  href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css"/>
    <link rel="stylesheet"  href="/assets/global/css/app.css"/>
    <link rel="stylesheet" href="/assets/global/plugins/bootstrap/css/bootstrap.min.css" type="text/css"/>
<link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/assets/global/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="/assets/global/plugins/bootstrap-multiselect/css/bootstrap-multiselect.css" type="text/css"/>
    <script>
        $(document).ready(function(){

            $('#submitVendorAssignmentForm').click(function(){
                $("button[type='submit']").prop('disabled', true);
                $('#vendorAssignmentForm').submit();
            });

            $('.example-getting-started').multiselect();
            $("#myBtn").click(function(){
                $("#myModal").modal();
            });
            $("#assetBtn").click(function(){
                $("#myModal1").modal();
            });

            $("#previewBtn").click(function(){
                var vendor = [];
                $(".select2-selection--multiple .select2-selection__choice").each(function(){
                    var vendorName = $(this).attr('title');
                    var vendorId;
                    $("#select2-multiple-input-lg option").each(function(){
                        if (vendorName == $(this).text()) {
                            vendorId = $(this).attr('value');
                        }
                    });


                    var materialId = $(this).closest('tr').find('td:nth-child(1)').text();
                    var materialName = $(this).closest('tr').find('td:nth-child(2)').text();
                    var materialQuantity = $(this).closest('tr').find('td:nth-child(3)').text();
                    var materialUnit = $(this).closest('tr').find('td:nth-child(4)').text();
                    if(vendor.length > 0){
                        var found = false;
                        $.each(vendor,function(i,v){
                            if(vendor[i].id == vendorId){
                                var newVendorMaterial = {
                                    id: materialId,
                                    name: materialName,
                                    quantity: materialQuantity,
                                    unit: materialUnit
                                };
                                vendor[i].material.push(newVendorMaterial);
                                found = true;
                                return true;
                            }
                        });
                        if(found == false){
                            var newVendor = {
                                id: vendorId,
                                name: vendorName,
                                material:[
                                    {
                                        id: materialId,
                                        name: materialName,
                                        quantity: materialQuantity,
                                        unit: materialUnit
                                    }
                                ]
                            };
                            vendor.push(newVendor);
                        }
                    }else{
                        var newVendor = {
                            id: vendorId,
                            name: vendorName,
                            material:[
                                {
                                    id: materialId,
                                    name: materialName,
                                    quantity: materialQuantity,
                                    unit: materialUnit
                                }
                            ]
                        };
                        vendor.push(newVendor);
                    }
                });
                console.log(vendor);
                var modalBodyString = '';
                $.each(vendor, function(i,v){
                    modalBodyString += '<div class="panel panel-default">\n' +
            '            <div class="panel-heading" role="tab" id="headingOne">\n' +
'                                                        <h4 class="panel-title">\n' +
'                                                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_'+i+'" aria-expanded="true" aria-controls="collapseOne">\n' +
                        '                                                                <span style="font-size: 16px;text-align: left !important;">'+vendor[i].name+'</span>\n' +
                            '&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp<button class="btn btn-dark" onclick="downloadPdf(this,'+vendor[i].id+')">Download PDF</button>' +
                        '                                                                <i class="more-less glyphicon glyphicon-plus"></i>\n' +
'                                                            </a>\n' +
'                                                        </h4>\n' +
'                                                    </div>\n' +
'                                                    <div id="collapse_'+i+'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">\n' +
'                                                        <div class="panel-body">\n' +
'                                                            <table class="table table-hover table-light">\n' +
'                                                                <tbody>' +
'<tr>\n' +
'                                                                    <th> Send mail </th>\n' +
'                                                                    <th> Material \\ Asset Name </th>\n' +
'                                                                    <th> Quantity </th>\n' +
'                                                                    <th> Unit </th>\n' +
'                                                                </tr>';
                    $.each(vendor[i].material,function(j,w){
                        modalBodyString += '<tr>\n' +
                            '                                                                    <td><input type="checkbox" name="checked_vendor_materials['+vendor[i].id+'][]" value="'+vendor[i].material[j].id+'"><input type="hidden" name="vendor_materials['+vendor[i].id+'][]" value="'+vendor[i].material[j].id+'"> </td>\n' +
                            '                                                                    <td> '+vendor[i].material[j].name+' </td>\n' +
                            '                                                                    <td> '+vendor[i].material[j].quantity+' </td>\n' +
                            '                                                                    <td> '+vendor[i].material[j].unit+' </td>\n' +
                            '                                                                </tr>';
                    });
                    modalBodyString += '</tbody>\n' +
                        '                                                            </table>\n' +
                        '\n' +
                        '                                                        </div>\n' +
                        '                                                    </div>\n' +
                        '                                                </div>';
                });
                $("#vendorPreviewModal .modal-body .panel-group").html(modalBodyString);
                $("#vendorPreviewModal").modal('show');
            });
        });
    </script>
    <script>
        function downloadPdf(element,vendorId){
            $('#is_mail').val(0);
            var divId = $(element).closest(".panel-heading").next('.panel-collapse').attr('id');
            $("#vendorPreviewModal form .panel-collapse[id!="+divId+"]").each(function(){
                $(this).remove();
            });
            $("#vendorPreviewModal form").submit();
        }

        function checkQuantity(materialRequestComponentId){
            $.ajax({
                url: '/purchase/purchase-request/get-material-inventory-quantity',
                type: 'POST',
                data: {
                    _token: $("input[name='_token']").val(),
                    material_request_component_id: materialRequestComponentId
                },
                success: function(data,textStatus,xhr ){
                    if(xhr.status == 201){
                        alert(data.message);
                    }else{
                        $("#inventoryQuantityModal .modal-body").html(data);
                        $("#inventoryQuantityModal").modal('show');
                    }
                },
                error: function (errorData){
                    alert('Something went wrong');
                }
            })
        }

        function editQuantity(materialRequestComponentId){
            $("#editQuantityMaterialId").val(materialRequestComponentId);
            $("#editQuantityModal").modal('show');
        }

        function submitEditQuantity(){
            var quantity =($("#editQuantityModal input[name='quantity']").val());
            var remark = $("#editQuantityModal textarea[name='remark']").val();
            if(!(typeof quantity == 'undefined' || quantity < 0 || isNaN(quantity) || !($.isNumeric(quantity)))){
                var a = confirm('Do you want to edit quantity ?');
                if(a == true){
                    var materialRequestComponentId = $("#editQuantityMaterialId").val();
                    $.ajax({
                        url: '/purchase/purchase-request/edit-quantity',
                        type: 'POST',
                        data:{
                            _token: $("input[name='_token']").val(),
                            quantity: quantity,
                            remark: remark,
                            material_request_component_id: materialRequestComponentId
                        },
                        success: function(data, textStatus, xhr){
                            if(data.quantity <= 0){
                                $("#select-vendor-"+materialRequestComponentId).hide();
                            }else{
                                $("#select-vendor-"+materialRequestComponentId).show();
                            }
                            $("#componentQuantity-"+materialRequestComponentId).html(data.quantity);
                            alert(data.message);
                            if(data.quantity <= 0){
                                $("#select-vendor-"+materialRequestComponentId).hide();
                            }else{
                                $("#select-vendor-"+materialRequestComponentId).show();
                            }
                            $('#editQuantityModal').modal('toggle');

                        },
                        error: function(errorData){
                            alert('Something went wrong');
                        }

                    });
                }
            }else{
                $("#editQuantityModal input[name='quantity']").closest('.form-group').addClass('has-error');
            }
        }
    </script>
@endsection
