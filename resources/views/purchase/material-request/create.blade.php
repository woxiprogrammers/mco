@extends('layout.master')
@section('title','Constro | Manage Materials')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
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
                            <div class="form-group " style="float: right;margin-top:1%">
                                <button type="submit" class="btn btn-set red pull-right">
                                    <i class="fa fa-check"></i>
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
                                                        <input type="text" class="form-control empty" id="clientSearchbox" name="client_name" placeholder="Enter client name" >
                                                        <div id="client-suggesstion-box"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control empty" id="projectSearchbox"  placeholder="Enter project name" >
                                                        <input type="hidden"  id="project_side_id" name="project_site_id">
                                                        <div id="project-suggesstion-box"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control empty" id="userSearchbox"  placeholder="Enter user name" >
                                                        <input type="hidden" name="user_id" id="user_id_">
                                                        <div id="user-suggesstion-box"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
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
                                                                <th> Action </th>
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
                                                                <th> Action </th>
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
                                            <div id="asset_unit-suggesstion-box"></div>
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
<script>
    $(document).ready(function(){
        var site_name = '';
        var search_in = '';
        $( "#assetBtn" ).hide();
        $( "#myBtn" ).hide();
        var iterator = parseInt(0);
        $('#iterator').val(iterator);


        $("#myBtn").click(function(){
            $("#myModal").modal();
        });
        $("#assetBtn").click(function(){
            $("#myModal1").modal();
        });

        $("#Unitsearchbox").keyup(function(){
            if($(this).val().length > 0){
                $.ajax({
                    type: "POST",
                    url: "/purchase/material-request/get-units",
                    data:'keyword='+$(this).val(),
                    beforeSend: function(){
                        $.LoadingOverlay("hide");
                        $("#unit-suggesstion-box").css({"background": "palegreen", "font-size": "initial" , "color":"brown"});
                    },
                    success: function(data){
                        console.log(data);
                        $("#unit-suggesstion-box").show();
                        $("#unit-suggesstion-box").html(data);
                        $("#Unitsearchbox").css("background-color","#FFF");
                    }
                });
            }else{
                $("#unit-suggesstion-box").hide();
            }

        });
        $("#AssetUnitsearchbox").keyup(function(){
            if($(this).val().length > 0){
                $.ajax({
                    type: "POST",
                    url: "/purchase/material-request/get-units",
                    data:'keyword='+$(this).val(),
                    beforeSend: function(){
                        $.LoadingOverlay("hide");
                        $("#unit-suggesstion-box").css({"background": "palegreen", "font-size": "initial" , "color":"brown"});
                    },
                    success: function(data){
                        console.log(data);
                        $("#unit-suggesstion-box").show();
                        $("#unit-suggesstion-box").html(data);
                        $("#Unitsearchbox").css("background-color","#FFF");
                    }
                });
            }else{
                $("#unit-suggesstion-box").hide();
            }
        });
    });
</script>
<script>
    function selectAsset(id) {
        $("#searchbox").val(id);
        $("#suggesstion-box").hide();
    }
</script>
<script>
    function selectAssetUnit(id) {
        $("#AssetUnitsearchbox").val(id);
        $("#asset_suggesstion-box").hide();
    }
</script>
<script>
        $("#clientSearchbox").keyup(function(){
            if($(this).val().length > 0){
                $.ajax({
                    type: "POST",
                    url: "/purchase/material-request/get-clients",
                    data:'keyword='+$(this).val(),
                    beforeSend: function(){
                        $.LoadingOverlay("hide");
                        $("#client-suggesstion-box").css({"background": "palegreen", "font-size": "initial" , "color":"brown"});
                    },
                    success: function(data){
                        console.log(data);
                        $("#client-suggesstion-box").show();
                        $("#client-suggesstion-box").html(data);
                        $("#clientSearchbox").css("background-color","#FFF");
                    }
                });
            }else{
                $("#client-suggesstion-box").hide();
            }
        });
    </script>
    <script>
        $("#projectSearchbox").keyup(function(){
            if($(this).val().length > 0){
                $.ajax({
                    type: "POST",
                    url: "/purchase/material-request/get-projects",
                    data:'keyword='+$(this).val(),
                    beforeSend: function(){
                        $.LoadingOverlay("hide");
                        $("#project-suggesstion-box").css({"background": "palegreen", "font-size": "initial" , "color":"brown"});
                    },
                    success: function(data){
                        console.log(data);
                        $("#project-suggesstion-box").show();
                        $("#project-suggesstion-box").html(data);
                        $("#projectSearchbox").css("background-color","#FFF");
                    }
                });
            }else{
                $("#project-suggesstion-box").hide();
            }

        });
    </script>
    <script>
        $("#userSearchbox").keyup(function(){
            if($(this).val().length > 0){
                $.ajax({
                    type: "POST",
                    url: "/purchase/material-request/get-users",
                    data:'keyword='+$(this).val(),
                    beforeSend: function(){
                        $.LoadingOverlay("hide");
                        $("#user-suggesstion-box").css({"background": "palegreen", "font-size": "initial" , "color":"brown"});
                    },
                    success: function(data){
                        $("#user-suggesstion-box").show();
                        $("#user-suggesstion-box").html(data);
                        $("#userSearchbox").css("background-color","#FFF");
                    }
                });
            }else{
                $("#user-suggesstion-box").hide();
            }
        });
    </script>
<script>
    function selectClient(id) {
        $("#clientSearchbox").val(id);
        $("#client-suggesstion-box").hide();
    }
</script>
<script>
    function selectProject(nameProject,id) {
        $( "#assetBtn" ).show();
        $( "#myBtn" ).show();
        var search_in = 'asset';
        var site_name = nameProject;
        var project_site_id = id;
        $('#project_side_id').val(project_site_id);
        $("#projectSearchbox").val(nameProject);
        $("#project-suggesstion-box").hide();
        var assetList = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '/purchase/material-request/get-items?site='+site_name+'&search_in='+search_in+'&keyword=%QUERY',
                filter: function(x) {
                    if($(window).width()<420){
                        $("#header").addClass("fixed");
                    }
                    return $.map(x, function (data) {
                        return {
                            name:data.asset_name,
                            unit:data.asset_unit,
                            component_type_id:data.material_request_component_type_id,
                        };
                    });
                },
                wildcard: "%QUERY"
            }
        });
        $('#Assetsearchbox').addClass('assetTypeahead');
        assetList.initialize();
        $('.assetTypeahead').typeahead(null, {
            displayKey: 'name',
            engine: Handlebars,
            source: assetList.ttAdapter(),
            limit: 30,
            templates: {
                empty: [
                    '<div class="empty-suggest">',
                    'Unable to find any Result that match the current query',
                    '</div>'
                ].join('\n'),
                suggestion: Handlebars.compile('<div class="autosuggest"><strong>@{{name}}</strong></div>')
            },
        }).on('typeahead:selected', function (obj, datum) {
            var POData = datum.unit;
            var componentTypeId = datum.component_type_id;
            $('#component_id').val(componentTypeId);
            var options = '';
            $.each( POData, function( key, value ) {
                var unitId = value.unit_id;
                var unitName = value.unit_name;
                options =  options+ '<option value="'+unitId +'">'+unitName +'</option>'
            });
            $('#unitDrpdn').html('');
            var str1 = '<select id="materialUnit" style="width: 80%;height: 20px;text-align: center">'+options+ '</select>';
            $('#unitDrpdn').append(str1);
            $('#component_type_id').val();
        })
            .on('typeahead:open', function (obj, datum) {
            });
        var search_in = 'material';
        var materialList = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '/purchase/material-request/get-items?site='+site_name+'&search_in='+search_in+'&keyword=%QUERY',
                filter: function(x) {
                    if($(window).width()<420){
                        $("#header").addClass("fixed");
                    }
                    return $.map(x, function (data) {
                        return {
                            name:data.material_name,
                            unit:data.unit_quantity,
                            component_type_id:data.material_request_component_type_id,
                        };
                    });
                },
                wildcard: "%QUERY"
            }
        });
            $('#searchbox').addClass('typeahead');
            materialList.initialize();
            $('.typeahead').typeahead(null, {
                displayKey: 'name',
                engine: Handlebars,
                source: materialList.ttAdapter(),
                limit: 30,
                templates: {
                    empty: [
                        '<div class="empty-suggest">',
                        'Unable to find any Result that match the current query',
                        '</div>'
                    ].join('\n'),
                    suggestion: Handlebars.compile('<div class="autosuggest"><strong>@{{name}}</strong></div>')
                },
            }).on('typeahead:selected', function (obj, datum) {
                var POData = datum.unit;
                var componentTypeId = datum.component_type_id;
                $('#component_id').val(componentTypeId);
                var options = '';
                $.each( POData, function( key, value ) {
                                    var unitId = value.unit_id;
                                    var unitName = value.unit_name;

                    options =  options+ '<option value="'+unitId +'">'+unitName +'</option>'
                           });
                  $('#unitDrpdn').html('');
                var str1 = '<select id="materialUnit" style="width: 80%;height: 20px;text-align: center">'+options+ '</select>';
                  $('#unitDrpdn').append(str1);
                  $('#component_type_id').val();
            })
                .on('typeahead:open', function (obj, datum) {
                });
    }
</script>
<script>
    function selectUser(id,id1) {
        $('#user_id_').val(id1);
        $("#userSearchbox").val(id);
        $("#user-suggesstion-box").hide();
    }
</script>
<script>
    $('#createMaterial').click(function(){
        $('#searchbox').html('');
        $('#qty').html('');
        $('#unitDrpdn').html('');
        var material_name = $('#searchbox').val();
        var quantity = $('#qty').val();
        var unit = $('#materialUnit').val();
        var componentTypeId = $('#component_id').val();
        var iterator = $('#iterator').val();
        var materials = '<td><input type="hidden" name="item_list['+iterator+'][name]" value="'+material_name+'">'+' <input type="hidden" name="item_list['+iterator+'][quantity_id]" value="'+quantity+'">'+'<input type="hidden" name="item_list['+iterator+'][unit_id]" value="'+unit+'">'+'<input type="hidden" name="item_list['+iterator+'][component_type_id]" value="'+componentTypeId+'">'+material_name+'</td>'+'<td>'+quantity+'</td>'+'<td>'+unit+'</td>';
        var rows = '<tr>'+materials+'</tr>';
        $('#myModal').modal('hide');
        $('#Materialrows').append(rows);
        var iterator = parseInt(iterator) + 1;
        $('#iterator').val(iterator);
        $('#component_id').val(null);
    })
</script>
<script>
    $('#createAsset').click(function(){
        $('#searchbox').html('');
        $('#qty').html('');
        $('#unitDrpdn').html('');
        var asset_name = $('#Assetsearchbox').val();
        var quantity = $('#Assetqty').val();
        var unit = $('#AssetUnitsearchbox').val();
        var componentTypeId = $('#component_id').val();
        var iterator = $('#iterator').val();
        var assets = '<td><input type="hidden" name="item_list['+iterator+'][name]" value="'+asset_name+'">'+' <input type="hidden" name="item_list['+iterator+'][quantity_id]" value="'+quantity+'">'+'<input type="hidden" name="item_list['+iterator+'][unit_id]" value="'+unit+'">'+'<input type="hidden" name="item_list['+iterator+'][component_type_id]" value="'+componentTypeId+'">'+asset_name+'</td>'+'<td>'+quantity+'</td>'+'<td>'+unit+'</td>';
        var rows = '<tr>'+assets+'</tr>';
        $('#myModal1').modal('hide');
        $('#Assetrows').append(rows);
        var iterator = parseInt(iterator) + 1;
        $('#iterator').val(iterator);
        $('#component_id').val(null);
    })
</script>
@endsection
