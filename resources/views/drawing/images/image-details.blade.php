@extends('layout.master')
@section('title','Constro | Manage Category')
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
                                    <h1>Image Details</h1>
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
                                            <div class="card">
                                                <div class="form-group " style="float: right;margin-top:1%">
                                                    <button id="addCommentBtnId" class="btn btn-set red pull-right">
                                                        <i class="fa fa-plus-circle"></i>
                                                        Add Comment
                                                    </button>
                                                </div>
                                                <img src="{{$image_src}}" height="400px" width="100%">
                                            </div>
                                                <br>
                                                <input type="hidden" id="image_id" value="{{$id}}">
                                                <div class="tabbable-custom nav-justified">
                                                    <ul class="nav nav-tabs nav-justified">
                                                        <li class="active" >
                                                            <a href="#comments" id="commentsId" data-toggle="tab" style="font-size: 18px"><b>COMMENTS</b>  </a>
                                                        </li>
                                                        <li >
                                                            <a href="#versions" id="versionsId" data-toggle="tab" style="font-size: 14px"> <b>VERSIONS</b>  </a>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content">
                                                        <div class="tab-pane active" id="comments">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <ul class="list-group">
                                                                        @foreach($comments as $comment)
                                                                        <li class="list-group-item">{{$comment['comment']}}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane active" id="versions">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <ul class="list-group" id="versinListingId">

                                                                    </ul>
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
                        <div class="modal fade" id="myModal1" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header" style="padding-bottom:10px">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"> Comment</div>
                                            <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                        </div>
                                    </div>
                                    <div class="modal-body" style="padding:40px 50px;">
                                        <form action="/drawing/images/add-comment" method="post">
                                            {!! csrf_field() !!}
                                            <input type="hidden" name="drawing_image_version_id" value="{{$id}}">
                                            <div class="form-group">
                                                <input type="text" class="form-control empty" name="comment"  placeholder="Enter Comment" required>
                                            </div>
                                            <input type="submit" class="btn red pull-right" id="createAsset">
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
@endsection
@section('javascript')
            <script type="text/javascript">

                $("#addCommentBtnId").click(function(){
                    $("#myModal1").modal();
                });

                $('#versionsId').click(function(){
                    var image_id = $('#image_id').val();
                    $.ajax({
                        url: '/drawing/images/get-versions/',
                        type: 'POST',
                        async: false,
                        data: {
                            'id' : image_id,
                        },
                        success: function(data,textStatus,xhr){
                            var option = '';
                            $.each(data, function( index, value ) {
                                console.log(value);
                                option += '<a href="/drawing/images/get-details/'+value.id +'"><li class="list-group-item"> Version '+ (parseInt(index)+1) +'</li></a>';
                            });
                            $('#versinListingId').html(option);
                        },
                        error: function(data, textStatus, xhr){
                        }
                    });
                })
            </script>
@endsection