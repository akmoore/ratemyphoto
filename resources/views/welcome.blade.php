<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">   
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Rate My Photo</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        
        <!-- Styles -->
        <link rel="stylesheet" href="{{asset('css/app.css?v=1.2.2')}}">
    </head>
    <body>
        <div id="app">
            <app></app>
        </div>
        <script src="{{asset('js/app.js?v=1.2.2')}}"></script>
    </body>
</html>
