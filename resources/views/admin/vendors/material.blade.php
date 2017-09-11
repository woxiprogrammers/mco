@extends('layout.master')
@section('title','Constro | Create Material')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
    <div class="page-wrapper" xmlns="http://www.w3.org/1999/html">
        <div class="page-wrapper-row full-height">
            <div class="page-wrapper-middle">
                <!-- BEGIN CONTAINER -->
                <div class="page-container">
                    <!-- BEGIN CONTENT -->
                    <div class="page-content-wrapper">
                        <div class="page-head">
                            <div class="container">
                                <!-- BEGIN PAGE TITLE -->

                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">

                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <form role="form" id="material-vendor" class="form-horizontal" method="post" action="/vendors/material">
                                                {!! csrf_field() !!}
                                                <div class="tab-content">
                                                    <div class="tab-pane fade in active" id="tab_general">
                                                        <fieldset>
                                                            <div class="form-group">
                                                                    <label class="col-md-3 control-label">Category</label>
                                                                    <div class="col-md-6">
                                                                        <select class="form-control" id="category_name" name="category_id">
                                                                            @foreach($categories as $category)
                                                                                <option value="{{$category['id']}}">{{$category['name']}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label">Material</label>
                                                                <div class="col-md-7">
                                                                    <div class="form-control product-material-select" >
                                                                        <ul id="material_id" class="list-group">

                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-md-offset-9">
                                                                    <a class="btn btn-success btn-md" id="next_btn">Next >></a>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <div class="materials-table-div" hidden>
                                                            <fieldset>
                                                                <legend> Material ID's</legend>
                                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="productMaterialTable">

                                                                </table>
                                                            </fieldset>
                                                            <fieldset>
                                                                <div class="form-body">
                                                                    <div class="form-group">
                                                                        <div class="col-md-3 col-md-offset-4" style="margin-left: 78%">
                                                                            <button type="submit" class="btn red" id="submit"><i class="fa fa-check"></i> Submit </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </fieldset>
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
    <script src="/assets/custom/admin/vendors/vendor.js" type="application/javascript"></script>
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
    <script>
    $(document).ready(function() {
        getMaterials($("#category_name").val());
        CreateVendor.init();
        $('#submit').css("padding-left", '6px');
        var citiList = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: "/product/auto-suggest/%QUERY",
                filter: function (x) {
                    if ($(window).width() < 420) {
                        $("#header").addClass("fixed");
                    }
                    return $.map(x, function (data) {
                        return {
                            id: data.id,
                            name: data.name,
                        };
                    });
                },
                wildcard: "%QUERY"
            }

        });
    });

</script>
@endsection
