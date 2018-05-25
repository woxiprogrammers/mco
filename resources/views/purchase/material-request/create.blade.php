@extends('layout.master')
@section('title','Constro | Manage Materials')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
    <input type="hidden" id="unitOptions" value="{{$unitOptions}}">
    <input id="nosUnitId" type="hidden" value="{{$nosUnitId}}">
    <form role="form" id="new_material_request" class="form-horizontal" action="/purchase/material-request/create" method="post">
    <input type="hidden" id="component_id">
    <input type="hidden" id="iterator">
        {!! csrf_field() !!}
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
                            <div class="pull-right">
                                <a href="/purchase/material-request/manage" class="btn btn-secondary-outline margin-top-15">< Back</a>
                                <button type="submit"  class="btn red margin-top-15">
                                    <i class="fa fa-check" style="font-size: large"></i>
                                    Submit
                                </button>
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
                                                        <label>Client Name : </label>
                                                        <input type="text" class="form-control empty" id="clientSearchbox" name="client_name" value="{{$globalProjectSite->project->client->company}}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Site Name : </label>
                                                        <input type="text" class="form-control empty" id="projectSearchbox" value="{{$globalProjectSite->project->name}} - {{$globalProjectSite->name}}" readonly>
                                                        <input type="hidden"  id="project_site_id" name="project_site_id" value="{{$globalProjectSite->id}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>User Name : </label>
                                                        <!--<input type="text" class="form-control empty" id="userSearchbox"  placeholder="Enter user name" name="user_name">-->
                                                        <input type="text" class="form-control empty" value="{{$userData['username']}}" readonly name="user_name">
                                                        <input type="hidden" name="user_id" id="user_id_" value="{{$userData['id']}}">
                                                        <div id="user-suggesstion-box"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <a href="#" class="btn btn-set yellow pull-right" style="margin-left: 10px;" id="assetBtn">
                                                        <i class="fa fa-plus" style="font-size: large"></i>
                                                        Asset&nbsp &nbsp &nbsp &nbsp
                                                    </a>
                                                    <a href="#" class="btn btn-set yellow pull-right"  id="myBtn">
                                                        <i class="fa fa-plus" style="font-size: large"></i>
                                                        Material
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <div class="portlet light ">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-bars font-red"></i>&nbsp
                                                        <span class="caption-subject font-red sbold uppercase">Material List</span>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-scrollable">
                                                        <table class="table table-hover table-light" >
                                                            <thead>
                                                            <tr>
                                                                <th> Name </th>
                                                                <th> Quantity </th>
                                                                <th> Unit </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="Materialrows">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-bars font-red"></i>&nbsp
                                                        <span class="caption-subject font-red sbold uppercase">Asset List</span>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-scrollable">
                                                        <table class="table table-hover table-light" >
                                                            <thead>
                                                            <tr>
                                                                <th> Name </th>
                                                                <th> Quantity </th>
                                                                <th> Unit </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="Assetrows">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
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
                                    <div class="modal-header">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"> Material</div>
                                            <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                        </div>
                                    </div>
                                    <div class="modal-body" style="padding:40px 50px;">
                                            <div class="form-group">
                                                <input type="text" class="form-control empty" id="searchbox"  placeholder="Enter material name" >
                                            </div>
                                            <div class="form-group">
                                                <input type="number" class="form-control empty" id="qty"  placeholder="Enter quantity">
                                            </div>
                                            <div class="form-group" id="unitDrpdn">
                                                <select id="materialUnit" style="width: 80%;height: 20px;text-align: center">
                                                    @foreach($units as $unit)
                                                        <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        <article>
                                            <label for="files">Select multiple files:</label>
                                            <input id="files" type="file" multiple="multiple" />
                                            <output id="result" />
                                        </article>
                                           <div class="btn red pull-right" id="createMaterial"> Create</div>
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
                                        <div class="form-group">
                                            <input type="text" class="form-control empty" id="Assetsearchbox"  placeholder="Enter asset name" >
                                            <div id="asset_suggesstion-box"></div>
                                        </div>
                                        <div class="form-group">
                                            <input type="number" class="form-control empty" id="Assetqty" value="1" readonly>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control empty" id="AssetUnitsearchbox"  value="Nos" readonly >
                                        </div>
                                        <article>
                                            <label for="filesAsset">Select multiple files:</label>
                                            <input id="filesAsset" type="file" multiple="multiple" />
                                            <output id="resultAsset" />
                                        </article>
                                        <div class="btn red pull-right" id="createAsset"> Create</div>
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
    </form>
@endsection
@section('javascript')
<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
<script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
<link rel="stylesheet"  href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css"/>
<link rel="stylesheet"  href="/assets/global/css/app.css"/>
<link rel="stylesheet"  href="/assets/custom/purchase/material-request/material-request.css"/>
<script src="/assets/custom/purchase/material-request/material-request.js" type="text/javascript"></script>
<script src="/assets/custom/purchase/validations.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        CreateMaterialRequest.init();
    });
</script>
    <script>
        function handleFileSelect() {
            //Check File API support
            if (window.File && window.FileList && window.FileReader) {
                var files = event.target.files; //FileList object
                var output = document.getElementById("result");
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    //Only pics
                    if (!file.type.match('image')) continue;
                    var picReader = new FileReader();
                    picReader.addEventListener("load", function (event) {
                        var picFile = event.target;
                        var div = document.createElement("div");
                        div.innerHTML = "<img class='thumbnail img' src='" + picFile.result + "'" + "title='" + picFile.name + "'/>";
                        output.insertBefore(div, null);
                    });
                    //Read the image
                    picReader.readAsDataURL(file);
                }
            } else {
                console.log("Your browser does not support File API");
            }
        }
        document.getElementById('files').addEventListener('change', handleFileSelect, false);
    function handleFileSelectForAsset() {
        //Check File API support
        if (window.File && window.FileList && window.FileReader) {
            var files = event.target.files; //FileList object
            var output = document.getElementById("result");
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                //Only pics
                if (!file.type.match('image')) continue;
                var picReader = new FileReader();
                picReader.addEventListener("load", function (event) {
                    var picFile = event.target;
                    var div = document.createElement("div");
                    div.innerHTML = "<img class='thumbnail assetImg' src='" + picFile.resultAsset + "'" + "title='" + picFile.name + "'/>";
                    output.insertBefore(div, null);
                });
                //Read the image
                picReader.readAsDataURL(file);
            }
        } else {
            alert("Your browser does not support File API");
        }
    }
    document.getElementById('filesAsset').addEventListener('change', handleFileSelectForAsset, false);
</script>
@endsection
