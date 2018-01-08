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
    <!-- END THEME LAYOUT STYLES -->
    @yield('css')
    <link rel="shortcut icon" href="favicon.ico" /> </head>
<!-- END HEAD -->
<body class="page-container-bg-solid page-md">
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
    messaging.requestPermission()
        .then(function(){
            navigator.serviceWorker.register('https://test.mconstruction.co.in/firebase-messaging-sw.js')
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
            console.log('token refreshed');
            console.log(refreshedToken);
            sendfcmToken(refreshedToken);
        })
        .catch(function(err) {
            console.log('in token refresh get token catch');
        });
    });
    function sendfcmToken(token){
        $.ajax({
            url: '',
            type: 'POST',
            data:{
                _token : $("input[name='_token']").val(),
                fcm_token: token
            },
            success: function(data, textStatus, xhr){
                console.log('token stored successfully');
            },
            error: function(errorData){
                console.log('fcm token ajax error');
                console.log(errorData);
            }
        });
    }
</script>
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
        setTimeout(function(){
            $.LoadingOverlay("hide");
        }, 1000);

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
