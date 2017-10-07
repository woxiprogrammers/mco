
$(document).ready(function(){
   // $('.deleteRowButton').click(DeleteRow);
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
function selectAsset(id) {
    $("#searchbox").val(id);
    $("#suggesstion-box").hide();
}
function selectAssetUnit(id) {
    $("#AssetUnitsearchbox").val(id);
    $("#asset_suggesstion-box").hide();
}
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
                $("#client-suggesstion-box").show();
                $("#client-suggesstion-box").html(data);
                $("#clientSearchbox").css("background-color","#FFF");
            }
        });
    }else{
        $("#client-suggesstion-box").hide();
    }
});
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
                $("#project-suggesstion-box").show();
                $("#project-suggesstion-box").html(data);
                $("#projectSearchbox").css("background-color","#FFF");
            }
        });
    }else{
        $("#project-suggesstion-box").hide();
    }

});
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
function selectClient(id) {
    $("#clientSearchbox").val(id);
    $("#client-suggesstion-box").hide();
}
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
        var str1 = '<select id="materialUnit" style="width: 80%;height: 20px;text-align: center"><option>Select Unit</option>'+options+ '</select>';
        $('#unitDrpdn').append(str1);
        $('#component_type_id').val();
    })
        .on('typeahead:open', function (obj, datum) {
        });
}
function selectUser(id,id1) {
    $('#user_id_').val(id1);
    $("#userSearchbox").val(id);
    $("#user-suggesstion-box").hide();
}
$('#createMaterial').click(function(){
    var material_name = $('#searchbox').val();
    var quantity = $('#qty').val();
    var unit = $('#materialUnit option:selected').text();
    var unitId = $('#materialUnit').val();
    var componentTypeId = $('#component_id').val();
    var iterator = $('#iterator').val();
    var materials = '<td><input type="hidden" name="item_list['+iterator+'][name]" value="'+material_name+'">'+' <input type="hidden" name="item_list['+iterator+'][quantity_id]" value="'+quantity+'">'+'<input type="hidden" name="item_list['+iterator+'][unit_id]" value="'+unitId+'">'+'<input type="hidden" name="item_list['+iterator+'][component_type_id]" value="'+componentTypeId+'">'+material_name+'</td>'+'<td>'+quantity+'</td>'+'<td>'+unit+'</td>'+'<td><a class="btn btn-xs green dropdown-toggle" id="deleteRowButton"  onclick="removeTableRow(this)">Remove</a></td>';
    var rows = '<tr>'+materials+'</tr>';
    $('#myModal').modal('hide');
    $('#Materialrows').append(rows);
    var iterator = parseInt(iterator) + 1;
    $('#iterator').val(iterator);
    $('#deleteRowButton').click(DeleteRow);
    $('#component_id').val(null);
    $('#searchbox').html('');
    $('#qty').html('');
    $('#unitDrpdn').html('');
})
$('#createAsset').click(function(){
    $('#searchbox').html('');
    $('#qty').html('');
    $('#unitDrpdn').html('');
    var asset_name = $('#Assetsearchbox').val();
    var quantity = $('#Assetqty').val();
    var unit = $('#AssetUnitsearchbox').val();
    var unitId = $('#AssetUnitId').val();
    var componentTypeId = $('#component_id').val();
    var iterator = $('#iterator').val();
    var assets = '<td><input type="hidden" name="item_list['+iterator+'][name]" value="'+asset_name+'">'+' <input type="hidden" name="item_list['+iterator+'][quantity_id]" value="'+quantity+'">'+'<input type="hidden" name="item_list['+iterator+'][unit_id]" value="'+unitId+'">'+'<input type="hidden" name="item_list['+iterator+'][component_type_id]" value="'+componentTypeId+'">'+asset_name+'</td>'+'<td>'+quantity+'</td>'+'<td>'+unit+'</td>'+'<td><a class="btn btn-xs green dropdown-toggle" id="deleteRowButton"  onclick="removeTableRow(this)">Remove</a></td>';
    var rows = '<tr>'+assets+'</tr>';
    $('#myModal1').modal('hide');
    $('#Assetrows').append(rows);
    var iterator = parseInt(iterator) + 1;
    $('#deleteAssetRowButton').click(DeleteRow);
    $('#iterator').val(iterator);
    $('#component_id').val(null);
});

function removeTableRow(element){
    $(element).closest('tr').remove();
}
