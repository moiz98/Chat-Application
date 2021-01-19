<!DOCTYPE html>
<html style="overflow-y: auto;" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ChatApp') }}</title>


    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        #app{
            background-color: #FF416C !important;
            text-align: center;
            height: 100vh !important;
            max-height: 100% !important;
            overflow: hidden;
        }
        .navbar{
            display: inline-flex;
            position: relative;
            width: 98%;
            max-width: 100%;
            background-color: #fff !important;
            padding: 0 20px;
            margin-top: 5px;
            margin-bottom: 5px;
            margin-left: 5px;
            margin-right: 5px;
            border-radius: 40px;
            box-shadow: 0 10px 40px rgba(159, 162, 177, .8) !important;
        }
    </style>
</head>
<body>
    <div id="app">
        <chat-app :user="{{ auth()->user() }}"></chat-app>            
    </div>
</body>
</html>