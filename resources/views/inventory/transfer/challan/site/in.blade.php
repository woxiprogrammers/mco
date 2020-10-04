@extends('layout.master')
@section('title','Constro | Site In Challan')
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
                                <h1>Site In Challan</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/inventory/transfer/challan/manage">Manage Challan</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Create Site In</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <form role="form" id="createTransferSiteIn" class="form-horizontal" method="post" action="/inventory/transfer/challan/site/in">
                                            {!! csrf_field() !!}
                                            <div class="form-actions noborder row">
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <label class="control-label pull-right">
                                                            Challan Number :
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control" id="challan_number" name="challan_id" onchange="getChallanDetails()">
                                                            <option value="default">Select Challan</option>
                                                            @foreach($challans as $challan)
                                                            <option value="{{$challan['id']}}">{{$challan['challan_number']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div id="siteInDetailForm">

                                                </div>
                                                <div class="form-group row postImageUpload" hidden>
                                                    <label class="control-label">Select Images :</label>
                                                    <input id="postImageUpload" type="file" class="btn blue" multiple />
                                                    <br />
                                                    <div class="row">
                                                        <div id="postPreviewImage" class="row">

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-offset-3 site-in-submit" style="margin-left: 26%" hidden>
                                                    <button type="submit" class="btn red" id="submitSiteInTransfer"><i class="fa fa-check"></i> Submit</button>
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
<div class="modal fade " id="detailsModal" role="dialog">
    <div class="modal-dialog" style="width: 98%; height: 800px">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4" style="font-size: 21px"> Details </div>
                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                </div>
            </div>
            <input type="hidden" id="modalComponentID">
            <form id="componentDetailForm">
                {!! csrf_field() !!}
                <div class="modal-body">

                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script>
    function getChallanDetails() {
        var challan_id = $('#challan_number').val();
        if (challan_id == 'default') {
            $('.grnDetail').hide();
        } else {
            $.ajax({
                url: '/inventory/transfer/challan/detail/' + challan_id,
                type: 'GET',
                async: true,
                data: {
                    _token: $("input[name='_token']").val(),
                    challan_id: challan_id
                },
                success: function(data, textStatus, xhr) {
                    if (typeof data.error != 'undefined') {
                        alert(data.message);
                    } else {
                        $("#siteInDetailForm").html(data);
                        $(".postImageUpload").show();
                        $(".site-in-submit").show();
                    }
                },
                error: function(errorData) {
                    alert('Something went wrong');
                }
            });
        }


    }
    $(document).ready(function() {

        $("#postImageUpload").on('change', function() {
            var countFiles = $(this)[0].files.length;
            var imgPath = $(this)[0].value;
            var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
            var image_holder = $("#postPreviewImage");
            image_holder.empty();
            if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                if (typeof(FileReader) != "undefined") {
                    for (var i = 0; i < countFiles; i++) {
                        var reader = new FileReader()
                        reader.onload = function(e) {
                            var imagePreview = '<div class="col-md-2"><input type="hidden" name="post_grn_image[]" value="' + e.target.result + '"><img src="' + e.target.result + '" class="thumbimage" /></div>';
                            image_holder.append(imagePreview);
                        };
                        image_holder.show();
                        reader.readAsDataURL($(this)[0].files[i]);
                    }
                } else {
                    alert("It doesn't supports");
                }
            } else {
                alert("Select Only images");
            }
        });

    });
</script>
@endsection