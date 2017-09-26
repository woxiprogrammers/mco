/**
 * Created by Ameya Joshi on 16/9/17.
 */

$(document).ready(function(){
    $("#removeMaterialButton").on('click',function(e){
        e.stopPropagation();
        $(".material-city-row:checkbox:checked").each(function(){
            $(this).closest('tr').remove();
        });
    })
});