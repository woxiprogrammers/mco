@extends('layout.master')
@section('title','Constro | Edit Extra Item')
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
                                    <h1>Edit Extra Item {{$extraItem['name']}}

                                    </h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/extra-item/manage">Manage Extra Item</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Edit Extra Item</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">

                                        <div class="portlet-body form">
                                            <input type="hidden" id="extra_item_id" value="{{$extraItem['id']}}">
                                            <form role="form" id="edit-extra-item" class="form-horizontal" method="post" action="/extra-item/edit/{{$extraItem['id']}}">
                                                {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="name" name="name" value="{{$extraItem['name']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="rate" class="control-label">Rate</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control" id="rate" name="rate" value="{{$extraItem['rate']}}">
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('edit-manage-extra-items'))
                                                    <div class="form-actions noborder row">
                                                        <div class="col-md-offset-3">
                                                            <button type="submit" id="edit-submit" class="btn red" style="padding-left: 6px"><i class="fa fa-check"></i> Submit</button>
                                                        </div>
                                                    </div>
                                                @endif

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
    <script src="/assets/custom/admin/extra-item/extra-item.js" type="application/javascript"></script>
    <script>
        $(document).ready(function() {
            EditExtraItem.init();
        });
    </script>
@endsection
