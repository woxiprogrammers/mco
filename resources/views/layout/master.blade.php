<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta name="csrf_token" content="{{ csrf_token() }}">
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="/assets/layouts/layout3/css/layout.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/layouts/layout3/css/themes/default.min.css" rel="stylesheet" type="text/css" id="style_color" />
    <link href="/assets/global/css/style/app.css" rel="stylesheet" type="text/css" />
    <!-- END THEME LAYOUT STYLES -->
    @yield('css')
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" /> </head>
<!-- END HEAD -->
<body class="page-container-bg-solid page-boxed">
@yield('content')
<!-- BEGIN FOOTER -->
<!-- BEGIN PRE-FOOTER -->

<!-- BEGIN HEADER -->
<div class="page-header">
<!-- BEGIN HEADER TOP -->
    <div class="page-header-top">
        <div class="container">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="javascript:;">
                    <img src="/assets/global/img/logo.jpg" alt="logo" height="100" width="100" class="logo-default">
                </a>
            </div>
            <!-- END LOGO -->
            <!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="menu-toggler"></a>
            <!-- END RESPONSIVE MENU TOGGLER -->
            <!-- BEGIN TOP NAVIGATION MENU -->
            <div class="top-menu">
                <ul class="nav navbar-nav pull-right" id="nav_bar_menu">
                    <li>
                        Account
                    </li>
                </ul>
            </div>

        </div>
    <!-- END MEGA MENU -->
    </div>
</div>
<!-- END HEADER MENU -->
</div>
<!-- END HEADER -->

<!-- END PRE-FOOTER -->
<!-- BEGIN INNER FOOTER -->

    <div class="page-footer">
        <div class="container"> Copyright &copy; Manisha Constructions <?php echo date("Y");?>
        </div>
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
    <!-- END INNER FOOTER -->
    <!-- END FOOTER -->
    <!--[if lt IE 9]>
    <script src="/assets/global/plugins/respond.min.js"></script>
    <script src="/assets/global/plugins/excanvas.min.js"></script>
    <![endif]-->
    <!-- BEGIN CORE PLUGINS -->
    <script src="/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="/assets/global/scripts/app.min.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="/assets/layouts/layout3/scripts/layout.min.js" type="text/javascript"></script>
    <!-- END THEME LAYOUT SCRIPTS -->
    @yield('javascript')
    <script>
        //    $(document).ajaxStop($.unblockUI);
        $("#edit_brand_menu").click(function(){
            var roleType = "{{Session::get('role_type')}}"
            if(roleType=="superadmin"){
                //App.blockUI();
                var themeName = 'sk-fading-circle';
                $.getScript("/assets/custom/superadmin/brand/data/get-all-brands.js")
            }
            //App.unblockUI();
        });

        $(document).ready(function(){

            var rememberToken = $('meta[name="csrf_token"]').attr('content');
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : rememberToken } });
            $("#change_language" ).change(function() {
                var language = $( this ).val();
                $.ajax({
                    url: "/change-language",
                    async:false,
                    data:{'language':language},
                    type: 'POST',
                    success: function(data, textStatus, xhr) {
                        if(xhr.status==200){
                            location.reload();
                        }else{
                            //alert(xhr.responseText);
                        }
                    }
                });
            });

            var roleType = "{{Session::get('role_type')}}";
            if(roleType == 'financeadmin'){
                $.ajax({
                    url:'/finance/order/create-payment-advices',
                    type:'GET',
                    async:true,
                    success: function(data,textStatus,xhr){

                    },
                    error: function(data,textStatus,xhr){

                    }
                });
            }
            if(roleType == 'financeadmin' || roleType == 'shipmentadmin' || roleType == 'shipmentpartner'){
                $.ajax({
                    url:'/get-notifications',
                    type:'GET',
                    async: false,
                    success:function(data, textStatus, xhr){
                        if(data.notificationCount > 0){
                            $("#notification-count").html(data.notificationCount);
                        }else{
                            $("#notification-count").hide();
                        }
                        $("#notification-list").html(data.notificationList);
                        $("#notification-title").html(data.notificationTitle);
                    },
                    error:function(data,textStatus,xhr){

                    }
                });
            }

            $(".icon-bell").on("mouseenter",function(){
                if(!$("notification-count").is('hidden')){
                    setTimeout(function(){
                        $.ajax({
                            url:'/read-notifications',
                            async:false,
                            data: [],
                            type: 'GET',
                            success: function(data, textStatus, xhr){
                                $("#notification-count").hide();
                            },
                            error:function(){

                            }
                        });
                    },4000);
                }
            });

        });

    </script>
</body>
</html>

