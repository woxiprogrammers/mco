
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
                            component_type_id:data.material_request_component_type_id
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
                $('#component_id').val('');
                var options = $("#unitOptions").val();
                var str1 = '<select class="form-control" id="materialUnit"><option value="">Select Unit</option>'+options+ '</select>';
                $('#unitDrpdn').html(str1);
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
            }
        }).on('typeahead:selected', function (obj, datum) {
            var POData = datum.unit;
            var componentTypeId = datum.component_type_id;
            $('#component_id').val(componentTypeId);
            var options = '';
            var str1 = '<select id="materialUnit" style="width: 80%;height: 20px;text-align: center">'+options+ '</select>';
            $('#unitDrpdn').html(str1);
            $('#component_type_id').val();
        })
            .on('typeahead:open', function (obj, datum) {
                $('#component_id').val('');
            });
        $("#myModal1").modal();
    });

    $("#myModal").on("hidden.bs.modal", function () {
        $('#qty').val('');
        $('#files').val(null);
        $('#qty').removeClass('has-error');
        $('#qty').removeClass('has-success');
        $('#materialUnit option[value=""]').prop('selected', true);
        $(".typeahead").typeahead("destroy");
        $("#searchbox").removeClass('typeahead');
        $('#searchbox').removeClass('has-error').removeClass('has-success');
        $("#searchbox").val('');
        $("#myModal output").html('');
    });

    $("#myModal1").on("hidden.bs.modal", function () {
        $('#Assetsearchbox').val('');
        $('#Assetqty').removeClass('has-error');
        $('#Assetqty').removeClass('has-success');
        $(".assetTypeahead").typeahead('destroy');
        $("#Assetsearchbox").removeClass("assetTypeahead");
        $('#Assetsearchbox').removeClass('has-error').removeClass('has-success');
        $("#myModal1 output").html('');
        $('#filesAsset').val(null);
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
    var materialNameEncoded = material_name.replace(/"/g,'$!@#$');
    materialNameEncoded = materialNameEncoded.replace(/''/g,'$!@#$');
    var quantity = $('#qty').val();
    var unit = $('#materialUnit option:selected').text();
    var unitId = $('#materialUnit').val();
    var componentTypeId = $('#component_id').val();
    var validFlag = true;
    if(typeof componentTypeId == 'undefined' || componentTypeId == ''){
        $("#searchbox").closest('.form-group').addClass('has-error').removeClass('has-success');
        alert('Please select from dropdown');
        validFlag = false;
    }else{
        $("#searchbox").closest('.form-group').addClass('has-success').removeClass('has-error');
    }
    if(/^[^$!@#]*$/.test(material_name) == false) {
        validFlag = false;
        alert('Material name must not contain special characters like $ ! @ #');
    }
    if(typeof material_name == 'undefined' || material_name == ''){
        $("#searchbox").closest('.form-group').addClass('has-error').removeClass('has-success');
        validFlag = false;
    }else{
        $("#searchbox").closest('.form-group').addClass('has-success').removeClass('has-error');
    }
    if(typeof quantity == 'undefined' || quantity == ''){
        $("#qty").closest('.form-group').addClass('has-error').removeClass('has-success');
        validFlag = false;
    }else{
        $("#qty").closest('.form-group').addClass('has-success').removeClass('has-error');
    }
    if(typeof unitId == 'undefined' || unitId == '' || unitId == 'Select Unit'){
        $("#materialUnit").closest('.form-group').addClass('has-error').removeClass('has-success');
        validFlag = false;
    }else{
        $("#materialUnit").closest('.form-group').addClass('has-success').removeClass('has-error');
    }
    if(validFlag == true){
        var componentTypeId = $('#component_id').val();
        var iterator = $('#iterator').val();
        var materials = '<td><input type="hidden" name="item_list['+iterator+'][name]" value="'+materialNameEncoded+'">'+' <input type="hidden" name="item_list['+iterator+'][quantity_id]" value="'+quantity+'">'+'<input type="hidden" name="item_list['+iterator+'][unit_id]" value="'+unitId+'">'+'<input type="hidden" name="item_list['+iterator+'][component_type_id]" value="'+componentTypeId+'">';

        $('.img').each(function(i, el) {
            var imageSrc = $(el).attr('src');
            materials += '<input type="hidden" name="item_list['+iterator+'][images][]" value="'+imageSrc+'">'
        });
        materials += material_name+'</td>'+'<td>'+quantity+'</td>'+'<td>'+unit+'</td>'+'<td><a class="btn btn-xs green dropdown-toggle" id="deleteRowButton"  onclick="removeTableRow(this)">Remove</a></td>';
        var rows = '<tr>'+materials+'</tr>';
        $('#myModal').modal('hide');
        $("#myModal output").html('');
        $('#Materialrows').append(rows);
        var iterator = parseInt(iterator) + 1;
        $('#iterator').val(iterator);
        $('#deleteRowButton').click(DeleteRow);
        $('#component_id').val(null);
        $('#searchbox').html('');
        $('#qty').val('');
        $('#files').val(null);
    }else{
        alert('Please fill all fields');
    }
});
$('#createAsset').click(function(){
    var asset_name = $('#Assetsearchbox').val();
    var assetNameEncoded = asset_name.replace(/"/g,'$!@#$');
    assetNameEncoded = assetNameEncoded.replace(/''/g,'$!@#$');
    var quantity = $('#Assetqty').val();
    var unit = $('#AssetUnitsearchbox').val();
    var unitId = $('#AssetUnitId').val();
    var componentTypeId = $('#component_id').val();
    var validFlag = true;
    if(typeof componentTypeId == 'undefined' || componentTypeId == ''){
        $("#Assetsearchbox").closest('.form-group').addClass('has-error').removeClass('has-success');
        alert('Please select from dropdown');
        validFlag = false;
    }else{
        $("#Assetsearchbox").closest('.form-group').addClass('has-success').removeClass('has-error');
    }
    if(/^[^$!@#]*$/.test(asset_name) == false) {
        validFlag = false;
        alert('Asset name must not contain special characters like $ ! @ #');
    }
    if(typeof asset_name == 'undefined' || asset_name == ''){
        $("#Assetsearchbox").closest('.form-group').addClass('has-error').removeClass('has-success');
        validFlag = false;
    }else{
        $("#Assetsearchbox").closest('.form-group').addClass('has-success').removeClass('has-error');
    }
    if(typeof quantity == 'undefined' || quantity == ''){
        $("#Assetqty").closest('.form-group').addClass('has-error').removeClass('has-success');
        validFlag = false;
    }else{
        $("#Assetqty").closest('.form-group').addClass('has-success').removeClass('has-error');
    }
    if(validFlag == true){
        $('#searchbox').html('');
        var componentTypeId = $('#component_id').val();
        var iterator = $('#iterator').val();
        var assets = '<td><input type="hidden" name="item_list['+iterator+'][name]" value="'+assetNameEncoded+'">'+' <input type="hidden" name="item_list['+iterator+'][quantity_id]" value="'+quantity+'">'+'<input type="hidden" name="item_list['+iterator+'][unit_id]" value="'+unitId+'">'+'<input type="hidden" name="item_list['+iterator+'][component_type_id]" value="'+componentTypeId+'">';
        $('.assetImg').each(function(i, el) {
            var imageSrc = $(el).attr('src');
            assets += '<input type="hidden" name="item_list['+iterator+'][images][]" value="'+imageSrc+'">'
        });
        assets += asset_name+'</td>'+'<td>'+quantity+'</td>'+'<td>'+unit+'</td>'+'<td><a class="btn btn-xs green dropdown-toggle" id="deleteRowButton"  onclick="removeTableRow(this)">Remove</a></td>';
        var rows = '<tr>'+assets+'</tr>';
        $('#myModal1').modal('hide');
        $("#myModal1 output").html('');
        $('#Assetrows').append(rows);
        var iterator = parseInt(iterator) + 1;
        $('#iterator').val(iterator);
        $('#deleteAssetRowButton').click(DeleteRow);
        $('#component_id').val(null);
        $('#filesAsset').val(null);
    }
});

function removeTableRow(element){
    $(element).closest('tr').remove();
}
