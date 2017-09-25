/**
 * Created by Ameya Joshi on 22/9/17.
 */

$(document).ready(function(){
    $("#role_id").on('change',function(){
       var roleId = $(this).val();
       var token = $("input[name='_token']").val();
       $.ajax({
          url:'/user/get-route-acls/'+roleId+'?_token='+token,
          type:'GET',
          async:true,
          success: function(data,textMessage,xhr){
              $("#aclTable").html(data);
          },
          error: function(errorData){

          }
       });
    });
});