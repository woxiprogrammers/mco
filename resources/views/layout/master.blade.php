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
    <meta content="Preview page of Metronic Admin Theme #3 for " name="description" />
    <meta content="" name="author" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="/assets/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="/assets/global/css/plugins-md.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="/assets/layouts/layout3/css/layout.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/layouts/layout3/css/themes/default.min.css" rel="stylesheet" type="text/css" id="style_color" />
    <link href="/assets/global/css/style/app.css" rel="stylesheet" type="text/css" />

    <link rel="apple-touch-icon" sizes="57x57" href="/assets/favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/assets/favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/assets/favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/assets/favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/assets/favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/assets/favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/assets/favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/assets/favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/assets/favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/assets/favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/favicons/favicon-16x16.png">
    <link rel="manifest" href="/assets/favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/assets/favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- END THEME LAYOUT STYLES -->
    @yield('css')
    {{--<link rel="shortcut icon" href="favicon.ico" /> --}}

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <!--<script async src="https://www.googletagmanager.com/gtag/js?id=UA-146605175-1"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-146605175-1');
    </script>-->


</head>
<!-- END HEAD -->
<body class="page-container-bg-solid page-md">
        <input type="hidden" id="appUrl" value="{{env('APP_URL')}}">
        @yield('navBar')
        @yield('content')
        <!-- <div class="page-wrapper-row">
            <div class="page-wrapper-bottom">
                <div class="page-footer">
                    <div class="container">
                        Copyright &copy; Manisha Constructions <?php echo date("Y");?>
                    </div>
                </div>
                <div class="scroll-to-top">
                    <i class="icon-arrow-up"></i>
                </div>
                </div>
            </div> -->
                <!-- END INNER FOOTER -->
                <!-- END FOOTER -->
    </div>

<!--[if lt IE 9]>
<script src="/assets/global/plugins/respond.min.js"></script>
<script src="/assets/global/plugins/excanvas.min.js"></script>
<script src="/assets/global/plugins/ie8.fix.min.js"></script>
<![endif]-->
<!-- BEGIN CORE PLUGINS -->
<script src="/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="/assets/global/scripts/app.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/jquery-loading-overlay/src/loadingoverlay.min.js"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="/assets/layouts/layout3/scripts/layout.min.js" type="text/javascript"></script>
<script src="/assets/layouts/layout3/scripts/demo.min.js" type="text/javascript"></script>
<script src="/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
<script src="/assets/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
        <!-- END THEME LAYOUT SCRIPTS -->
<script src="https://www.gstatic.com/firebasejs/4.8.1/firebase.js"></script>
<script>
    // Initialize Firebase
    var config = {
        apiKey: "AIzaSyDtYXt1BQzsnLutfzZnlsDEXpM0N7pEp10",
        authDomain: "mcon-android.firebaseapp.com",
        databaseURL: "https://mcon-android.firebaseio.com",
        projectId: "mcon-android",
        storageBucket: "mcon-android.appspot.com",
        messagingSenderId: "425183955461"
    };
    const firebaseApp = firebase.initializeApp(config);
</script>
<script src="https://www.gstatic.com/firebasejs/4.6.2/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.6.2/firebase-messaging.js"></script>
<script>
    const messaging = firebaseApp.messaging();
    const app_url = $("#appUrl").val();
    messaging.requestPermission()
        .then(function(){
            navigator.serviceWorker.register(app_url + '/firebase-messaging-sw.js')
                .then((registration) => {
                messaging.useServiceWorker(registration);
            return messaging.getToken();
        })
        .then((token) => {
                sendfcmToken(token)
            });
        })
        .catch(function(err){

        });
    messaging.onTokenRefresh(function() {
        messaging.getToken()
            .then(function(refreshedToken) {
                sendfcmToken(refreshedToken);
            })
            .catch(function(err) {
            });
    });
    function sendfcmToken(token){
        $.ajax({
            url: '/notification/store-fcm-token',
            type: 'POST',
            data:{
                _token : $("input[name='_token']").val(),
                fcm_token: token
            },
            success: function(data, textStatus, xhr){
            },
            error: function(errorData){
            }
        });
    }
</script>
<script>
    $(document).ready(function()
    {
        $('#clickmewow').click(function()
        {
            $('#radio1003').attr('checked', 'checked');
        });

        $("#globalProjectSite").on('changed.bs.select', function(){
            var newProjectSite = $(this).val();
            $.ajax({
                url: '/change-project-site',
                type: 'POST',
                data: {
                    project_site_id: newProjectSite
                },
                success: function(data,textStatus,xhr){
                    location.reload();
                },
                error: function(errorData){

                }
            });
        });
    });
    $(document).ajaxStart(function(){
        $.LoadingOverlay("show",{
            color:"rgba(255, 255, 255, 0.6)",
        });
    });
    $(document).ajaxStop(function(){
        //setTimeout(function(){
            $.LoadingOverlay("hide");
        //}, 200);

    });

    function customRound(number){
        var floorNumber = parseInt(number);
        if((Math.abs(number%1)) >= 0.5){
            if(number >= 0){
                floorNumber = floorNumber + 0.5;
            }else{
                floorNumber = floorNumber - 0.5;
            }
        }
        return floorNumber;
    }
</script>
@yield('javascript')
</body>
</html>
