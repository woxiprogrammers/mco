$(document).ready(function(){
    var site_name = '';
    var search_in = '';
    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
    var iterator = parseInt(0);
    $('#iterator').val(iterator);
    $("#myBtn").click(function(){
       // $('#component_id').val(4);
        var site_name = $("#globalProjectSite").val();
        var search_in = 'material';
        var materialList = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '/purchase/material-request/get-items?project_site_id='+site_name+'&search_in='+search_in+'&keyword=%QUERY',
                filter: function(x) {
                    if($(window).width()<420){
                        $("#header").addClass("fixed");
                    }
                    return $.map(x, function (data) {
                        return {
                            name:data.material_name,
                            unit:data.unit_quantity,
                            component_type_id:data.material_request_component_type_id,
                            component_type_slug:data.material_request_component_type_slug
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
                suggestion: Handlebars.compile('<div class="autosuggest"><strong>{{name}}</strong></div>')
            }
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
            $('#materialUnit').html(options);
            $("#materialModalComponentSlug").val(datum.component_type_slug);
        })
            .on('typeahead:open', function (obj, datum) {
               // $('#component_id').val(4);
                $("#materialUnit").html($("#unitOptions").val());
                $("#materialModalComponentSlug").val('');
            });
        $("#myModal").modal();
    });
    $("#assetBtn").click(function(){
        //$('#component_id').val(6);
        var search_in = 'asset';
        var site_name = $("#globalProjectSite").val();
        var assetList = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '/purchase/material-request/get-items?project_site_id='+site_name+'&search_in='+search_in+'&keyword=%QUERY',
                filter: function(x) {
                    if($(window).width()<420){
                        $("#header").addClass("fixed");
                    }
                    return $.map(x, function (data) {
                        return {
                            name:data.asset_name,
                            unit:data.asset_unit,
                            component_type_id:data.material_request_component_type_id
                        };
                    });
                },
                wildcard: "%QUERY"
            }
        });
        $('#Assetsearchbox').addClass('assetTypeahead');
       // $('#component_id').val(6);
        assetList.initialize();
        var unitName = "Nos";
        $('.assetTypeahead').typeahead(null, {
            displayKey: 'name',
            engine: Handlebars,
            source: assetList.ttAdapter(),
            limit: 30,
            templates: {
                /*empty: [
                    '<div class="empty-suggest">',
                    'Unable to find any Result that match the current query',
                    '</div>'
                ].join('\n'),*/
                suggestion: Handlebars.compile('<div class="autosuggest"><strong>{{name}}</strong></div>')
            },
        }).on('typeahead:selected', function (obj, datum) {
            var POData = datum;
            var componentTypeId = datum.component_type_id;
            $('#component_id').val(componentTypeId);
        })
            .on('typeahead:open', function (obj, datum) {
               // $('#component_id').val(6);
            });
        $("#myModal1").modal();

    });
    $("#userSearchbox").keyup(function(){
        if($(this).val().length > 0){
            $.ajax({
                type: "POST",
                url: "/purchase/material-request/get-users?_token="+$('input[name="_token"]').val(),
                data:'keyword='+$(this).val()+'&project_site_id='+$("#project_site_id").val()+'&module=material-request',
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

    $('#createMaterial').click(function(){
        var material_name = $('#searchbox').val();
        var quantity = $('#qty').val();
        var unitId = $('#materialUnit').val();
        var unitName = $('#materialUnit option[value="'+unitId+'"]').text();
        var componentTypeId = $('#component_id').val();
        var validFlag = true;
        if(typeof componentTypeId == 'undefined' || componentTypeId == ''){
            $("#searchbox").closest('.form-group').addClass('has-error').removeClass('has-success');
            alert('Please select from dropdown');
            validFlag = false;
        }else{
            $("#searchbox").closest('.form-group').addClass('has-success').removeClass('has-error');
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
        if(typeof unitId == 'undefined' || unitId == ''){
            $("#materialUnit").closest('.form-group').addClass('has-error').removeClass('has-success');
            validFlag = false;
        }else{
            $("#materialUnit").closest('.form-group').addClass('has-success').removeClass('has-error');
        }
        if(validFlag == true){
            var componentSlug = $("#materialModalComponentSlug").val();
            if(componentSlug == 'quotation-material'){
                $.ajax({
                    url:'/purchase/material-request/validate-quantity',
                    type: "POST",
                    data:{
                        _token: $("input[name='_token']").val(),
                        quantity: quantity,
                        unit_id: unitId,
                        material_name: material_name,
                        project_site_id: $("#globalProjectSite").val()
                    },
                    success: function(data,textStatus, xhr){
                        if(xhr.status == 203){
                            alert(data.message);
                        }
                        $('#searchbox').html('');
                        $('#qty').html('');
                        var componentTypeId = $('#component_id').val();
                        var images = [];
                        var iterator = $('#iterator').val();
                        var materials = '<td><input type="hidden" name="item_list['+iterator+'][name]" value="'+material_name+'">'+' <input type="hidden" name="item_list['+iterator+'][quantity_id]" value="'+quantity+'">'+'<input type="hidden" name="item_list['+iterator+'][unit_id]" value="'+unitId+'">'+'<input type="hidden" name="item_list['+iterator+'][component_type_id]" value="'+componentTypeId+'">';
                        $('.img').each(function(i, el) {
                            var imageSrc = $(el).attr('src');
                            materials += '<input type="hidden" name="item_list['+iterator+'][images][]" value="'+imageSrc+'">'
                        });
                        materials += material_name+'</td>'+'<td>'+quantity+'</td>'+'<td>'+unitName+'</td>';
                        var rows = '<tr>'+materials+'</tr>';
                        $('#myModal').modal('hide');
                        $('#myModal output').html('');
                        $('#Materialrows').append(rows);
                        var iterator = parseInt(iterator) + 1;
                        $('#iterator').val(iterator);
                        $('#component_id').val(null);
                    },
                    error: function(errorData){

                    }
                });
            }else{
                $('#searchbox').html('');
                $('#qty').html('');
                var componentTypeId = $('#component_id').val();
                var images = [];
                var iterator = $('#iterator').val();
                var materials = '<td><input type="hidden" name="item_list['+iterator+'][name]" value="'+material_name+'">'+' <input type="hidden" name="item_list['+iterator+'][quantity_id]" value="'+quantity+'">'+'<input type="hidden" name="item_list['+iterator+'][unit_id]" value="'+unitId+'">'+'<input type="hidden" name="item_list['+iterator+'][component_type_id]" value="'+componentTypeId+'">';
                $('.img').each(function(i, el) {
                    var imageSrc = $(el).attr('src');
                    materials += '<input type="hidden" name="item_list['+iterator+'][images][]" value="'+imageSrc+'">'
                });
                materials += material_name+'</td>'+'<td>'+quantity+'</td>'+'<td>'+unitName+'</td>';
                var rows = '<tr>'+materials+'</tr>';
                $('#myModal').modal('hide');
                $('#myModal output').html('');
                $('#Materialrows').append(rows);
                var iterator = parseInt(iterator) + 1;
                $('#iterator').val(iterator);
                $('#component_id').val(null);
            }
        }
    });

    $('#createAsset').click(function(){
        var asset_name = $('#Assetsearchbox').val();
        var quantity = $('#Assetqty').val();
        var unit = $('#AssetUnitsearchbox').val();
        var unitId = $('#nosUnitId').val();
        var componentTypeId = $('#component_id').val();
        var validFlag = true;
        if(typeof componentTypeId == 'undefined' || componentTypeId == ''){
            $("#Assetsearchbox").closest('.form-group').addClass('has-error').removeClass('has-success');
            alert('Please select from dropdown');
            validFlag = false;
        }else{
            $("#Assetsearchbox").closest('.form-group').addClass('has-success').removeClass('has-error');
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
        if(validFlag ==  true){
            $('#searchbox').html('');
            $('#qty').html('');
            var componentTypeId = $('#component_id').val();
            var iterator = $('#iterator').val();
            var assets = '<td><input type="hidden" name="item_list['+iterator+'][name]" value="'+asset_name+'">'+' <input type="hidden" name="item_list['+iterator+'][quantity_id]" value="'+quantity+'">'+'<input type="hidden" name="item_list['+iterator+'][unit_id]" value="'+unitId+'">'+'<input type="hidden" name="item_list['+iterator+'][component_type_id]" value="'+componentTypeId+'">';
            $('.assetImg').each(function(i, el) {
                var imageSrc = $(el).attr('src');
                assets += '<input type="hidden" name="item_list['+iterator+'][images][]" value="'+imageSrc+'">'
            })
            assets += asset_name+'</td>'+'<td>'+quantity+'</td>'+'<td>'+unit+'</td>';
            var rows = '<tr>'+assets+'</tr>';
            $('#myModal1').modal('hide');
            $('#myModal1 output').html('');
            $('#Assetrows').append(rows);
            var iterator = parseInt(iterator) + 1;
            $('#iterator').val(iterator);
            $('#component_id').val(null);
        }
    });

    $("#myModal").on("hidden.bs.modal", function () {
        $('#qty').val('');
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
function selectUser(id,id1) {
    $('#user_id_').val(id1);
    $("#userSearchbox").val(id);
    $("#user-suggesstion-box").hide();
}

