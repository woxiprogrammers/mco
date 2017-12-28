
$(document).ready(function(){
    var iterator = parseInt(0);
    $('#iterator').val(iterator);
    var project_site_id = $("#project_site_id").val();
    $("#myBtn").click(function(){
        var materialList = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '/purchase/material-request/get-items?project_site_id='+project_site_id+'&search_in=material&keyword=%QUERY',
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
                suggestion: Handlebars.compile('<div class="autosuggest"><strong>{{name}}</strong></div>')
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
            var str1 = '<select id="materialUnit" style="width: 80%;height: 20px;text-align: center"><option>Select Unit</option>'+options+ '</select>';
            $('#unitDrpdn').html(str1);
            $('#component_type_id').val();
        })
            .on('typeahead:open', function (obj, datum) {
                $('#component_id').val(4);
            });
        $("#myModal").modal();
    });
    $("#assetBtn").click(function(){
        var project_site_id = $('#project_site_id').val();
        var assetList = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '/purchase/material-request/get-items?project_site_id='+project_site_id+'&search_in=asset&keyword=%QUERY',
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
        var unitName = "Nos";
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
                suggestion: Handlebars.compile('<div class="autosuggest"><strong>{{name}}</strong></div>')
            },
        }).on('typeahead:selected', function (obj, datum) {
            var POData = datum.unit;
            var componentTypeId = datum.component_type_id;
            $('#component_id').val(componentTypeId);
            var options = ''
            var str1 = '<select id="materialUnit" style="width: 80%;height: 20px;text-align: center">'+options+ '</select>';
            $('#unitDrpdn').html(str1);
            $('#component_type_id').val();
        })
            .on('typeahead:open', function (obj, datum) {
                $('#component_id').val(6);
            });
        $("#myModal1").modal();
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
$("#userSearchbox").keyup(function(){
    if($(this).val().length > 0){
        $.ajax({
            type: "POST",
            url: "/purchase/material-request/get-users?_token="+$('input[name="_token"]').val(),
            data:'keyword='+$(this).val()+'&project_site_id='+$("#project_site_id").val()+'&module=purchase-request',
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
function selectUser(name,id) {
    $('#user_id_').val(id);
    $("#userSearchbox").val(name);
    $("#user-suggesstion-box").hide();
}
$('#createMaterial').click(function(){
    var material_name = $('#searchbox').val();
    var quantity = $('#qty').val();
    var unit = $('#materialUnit option:selected').text();
    var unitId = $('#materialUnit').val();
    var componentTypeId = $('#component_id').val();
    var iterator = $('#iterator').val();
    var materials = '<td><input type="hidden" name="item_list['+iterator+'][name]" value="'+material_name+'">'+' <input type="hidden" name="item_list['+iterator+'][quantity_id]" value="'+quantity+'">'+'<input type="hidden" name="item_list['+iterator+'][unit_id]" value="'+unitId+'">'+'<input type="hidden" name="item_list['+iterator+'][component_type_id]" value="'+componentTypeId+'">';

    $('.img').each(function(i, el) {
        var imageSrc = $(el).attr('src');
        materials += '<input type="hidden" name="item_list['+iterator+'][images][]" value="'+imageSrc+'">'
    });
    materials += material_name+'</td>'+'<td>'+quantity+'</td>'+'<td>'+unit+'</td>'+'<td><a class="btn btn-xs green dropdown-toggle" id="deleteRowButton"  onclick="removeTableRow(this)">Remove</a></td>';
    var rows = '<tr>'+materials+'</tr>';
    $('#myModal').modal('hide');
    $('#Materialrows').append(rows);
    var iterator = parseInt(iterator) + 1;
    $('#iterator').val(iterator);
    $('#deleteRowButton').click(DeleteRow);
    $('#component_id').val(null);
    $('#searchbox').html('');
    $('#qty').html('');
})
$('#createAsset').click(function(){
    $('#searchbox').html('');
    $('#qty').html('');
    var asset_name = $('#Assetsearchbox').val();
    var quantity = $('#Assetqty').val();
    var unit = $('#AssetUnitsearchbox').val();
    var unitId = $('#AssetUnitId').val();
    var componentTypeId = $('#component_id').val();
    var iterator = $('#iterator').val();
    var assets = '<td><input type="hidden" name="item_list['+iterator+'][name]" value="'+asset_name+'">'+' <input type="hidden" name="item_list['+iterator+'][quantity_id]" value="'+quantity+'">'+'<input type="hidden" name="item_list['+iterator+'][unit_id]" value="'+unitId+'">'+'<input type="hidden" name="item_list['+iterator+'][component_type_id]" value="'+componentTypeId+'">';
    $('.img').each(function(i, el) {
        var imageSrc = $(el).attr('src');
        assets += '<input type="hidden" name="item_list['+iterator+'][images][]" value="'+imageSrc+'">'
    })
    assets += asset_name+'</td>'+'<td>'+quantity+'</td>'+'<td>'+unit+'</td>'+'<td><a class="btn btn-xs green dropdown-toggle" id="deleteRowButton"  onclick="removeTableRow(this)">Remove</a></td>';
    var rows = '<tr>'+assets+'</tr>';
    $('#myModal1').modal('hide');
    $('#Assetrows').append(rows);
    var iterator = parseInt(iterator) + 1;
    $('#iterator').val(iterator);
    $('#deleteAssetRowButton').click(DeleteRow);
    $('#component_id').val(null);
});

function removeTableRow(element){
    $(element).closest('tr').remove();
}
