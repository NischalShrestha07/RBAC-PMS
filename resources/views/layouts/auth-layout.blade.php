<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{asset('admincss/vendor/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('admincss/vendor/font-awesome/css/font-awesome.min.css')}}">

    <link rel="stylesheet" href="{{asset('admincss/css/style.default.css')}}" id="theme-stylesheet">
    <link rel="stylesheet" href="{{asset('admincss/css/custom.css')}}">
    <link rel="icon" href="{{asset('admincss/img/pms.ico')}}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>
    .custom-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15);
        border-radius: 5px;
        padding: 20px;
        max-width: 300px;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.5s ease-out;
    }

    .custom-alert.fade-in {
        opacity: 1;
        transform: translateX(0);
    }

    .custom-alert.fade-out {
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.5s ease-in;
    }

    .alert strong {
        font-weight: bold;
        font-size: 1.2rem;
    }

    .close {
        font-size: 1.2rem;
    }

    html,
    body {
        height: 100%;
        margin: 0;
        font-family: Arial, sans-serif;
    }

    .main-content {
        margin-left: 250px;
        padding: 20px;
        min-height: 100vh;

    }
</style>

<body>
    @yield('content')

</body>
<!-- JavaScript files-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    <script src={{asset('admincss/vendor/jquery/jquery.min.js')}}>
</script>
<script src={{asset('admincss/vendor/popper.js/umd/popper.min.js')}}></script>
<script src={{asset('admincss/vendor/bootstrap/js/bootstrap.min.js')}}></script>
<script src={{asset('admincss/vendor/jquery-validation/jquery.validate.min.js')}}></script>
<script src={{asset('admincss/js/front.js')}}></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fade in the alert on page load
        let alertBox = document.querySelectorAll('.custom-alert');

        // Automatically fade out after 5 seconds
        setTimeout(function() {
            alertBox.forEach(function(alert) {
                alert.classList.add('fade-out');
            });
        }, 5000); // 5 seconds

        setTimeout(function() {
            alertBox.forEach(function(alert) {
                alert.remove();
            });
        }, 5500); // 5 seconds + 0.5s for fade-out effect
    });
</script>

</html>
