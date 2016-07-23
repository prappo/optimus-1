<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>@yield('title')</title>
    @yield('headjs')

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{--Bootstrap and Sass--}}
    <link rel="stylesheet" href="{{ elixir('css/app.css') }}">

    {{--AdminLTE and Less--}}
    <link rel="stylesheet" href="{{ elixir('css/admin.css') }}">


    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.1/css/buttons.bootstrap.min.css">
    {{--Plugins CSS--}}

    <link rel="stylesheet" href="{{ elixir('css/plugins.css') }}">
    {{--custom css--}}
    <link rel="stylesheet" href="/opt/css/custom.css">
    <link rel="stylesheet" href="/opt/intro/introjs.css">



    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        .emotionbdge{
            position: absolute;
            top: -3px;
            right: -10px;
            font-size: 10px;
            font-weight: 400;
        }
    </style>
    @yield('css')
</head>
<body class="hold-transition sidebar-mini skin-red-light">

@yield('content')

<script src="{{ elixir('js/app.js') }}"></script>

<script src="https://cdn.datatables.net/buttons/1.2.1/js/dataTables.buttons.min.js"></script>
<script src="http://cdn.datatables.net/buttons/1.2.1/js/buttons.flash.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="/opt/pdfmake.min.js"></script>
<script src="/opt/vfs_fonts.js"></script>
<script src="http://cdn.datatables.net/buttons/1.2.1/js/buttons.html5.min.js"></script>
<script src="http://cdn.datatables.net/buttons/1.2.1/js/buttons.print.min.js"></script>
<script src="/opt/intro/intro.js"></script>



<script src="https://js.pusher.com/3.1/pusher.min.js"></script>

<script>
    // notification start


    document.addEventListener('DOMContentLoaded', function () {
        if (Notification.permission !== "granted")
            Notification.requestPermission();
    });


    // notification end

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('{{\App\Http\Controllers\Data::get('notifyAppKey')}}', {
        encrypted: true
    });
            @if(\App\Http\Controllers\Data::get('notifyAppSecret') != "")
    var channel = pusher.subscribe('optimus');
    channel.bind('my_event', function (data) {
        if (data.type == "message") {
            notify(messengericon(), data.title, data.message, data.url);
            $.ajax({
                type: 'POST',
                url: '/notify',
                data: {
                    'img': messengericon(),
                    'title': data.title,
                    'body': data.message,
                    'url': data.url,
                    'type': data.type,
                    'time': data.time
                },
                success: function (data) {
                    console.log(data);
                }
            });
        }
        else if (data.type == "schedule") {
            notify(optscheduleicon(), data.title, data.message, data.url);
            $.ajax({
                type: 'POST',
                url: '/notify',
                data: {
                    'img': optscheduleicon(),
                    'title': data.title,
                    'body': data.message,
                    'url': data.url,
                    'type': data.type,
                    'time': data.time
                },
                success: function (data) {
                    console.log(data);
                }
            });

        }
        else if (data.type == "fbnotify") {
            notify(optfbicon(), data.title, data.message, data.url);
            $.ajax({
                type: 'POST',
                url: '/notify',
                data: {
                    'img': optfbicon(),
                    'title': data.title,
                    'body': data.message,
                    'url': data.url,
                    'type': data.type,
                    'time': data.time
                },
                success: function (data) {
                    console.log(data);
                }
            });

        }

    });
    @endif
</script>
@yield('js')
<script>
    $(document).ready(function () {


        var table = $('#mytable').DataTable({

            dom: '<""flB>tip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<button class="btn btn-success btn-xs fak"><i class="fa fa-file-excel-o"></i> Export all to excel</button>'
                },
                {
                    extend: 'csv',
                    text: '<button class="btn btn-warning btn-xs fak"><i class="fa fa-file-o"></i> Export all to csv</button>'
                },
                {
                    extend: 'pdf',
                    text: '<button class="btn btn-danger btn-xs fak"><i class="fa fa-file-pdf-o"></i> Print all in pdf</button>'
                },
                {
                    extend: 'print',
                    text: '<button class="btn btn-default btn-xs fak"><i class="fa fa-print"></i> Print all</button>'
                },
            ]
        });


    });
</script>
</body>
</html>