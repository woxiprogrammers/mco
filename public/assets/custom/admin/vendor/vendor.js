/**
 * Created by Ameya Joshi on 16/9/17.
 */

$(document).ready(function(){
    $("#next").on('click', function(){
        if($("#material_id input:checkbox:checked").length > 0 && $("#cityList input:checkbox:checked").length > 0){
            var tableStr = '<thead><tr><th style="width: 30%">Material</th><th>City</th></tr></thead>';
            var rowSpanCount =  $("#cityList input:checkbox:checked").length + 1;
            $("#material_id input:checkbox:checked").each(function(){
                var materialName = $(this).next().text();
                var materialId = $(this).val();
                var materialTrString = '<tr><td rowspan="'+rowSpanCount+'">'+materialName+'</tr>';
                $("#cityList input:checkbox:checked").each(function(){
                    var cityName = $(this).next().text();
                    var cityId = $(this).val();
                    materialTrString += '<tr><td><input type="checkbox" name="material_city['+materialId+'][]" value="'+cityId+'"><span>'+cityName+'</span></td></tr>';
                });
                tableStr += materialTrString;
            });
            $('#materialCityTable').html(tableStr);
        }else{
            alert("Please check whether you have selected atleast one city and one material or not.");
        }
    });

    $("#categoryId").on('change', function () {
        $('#materialCityTable').html('');
        var category = $(this).val();
        $.ajax({
            url: '/vendors/get-materials/'+category,
            type: 'GET',
            async: false,
            success: function(data, textStatus, xhr){
                if(xhr.status == 200){
                    $("#material_id").html(data);
                }else{

                }
            },
            error: function(errorStatus,xhr){

            }
        });
    });
});