<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <title>GENETVI - Error 404</title>
    <link rel="stylesheet" href="{{asset('404/style.css')}}">

    <style>
        .bg-purple{
            background: url("{{asset('404/img/392790.jpg')}}");
            background-repeat: repeat-x;
            background-size: cover;
            background-position: left top;
            height: 100%;
            overflow: hidden;  
        }

        .stars{
            background: url("{{asset('404/img/overlay_stars.svg')}}");
            background-repeat: repeat;
            background-size: contain;
            background-position: left top;
        }

    </style>    
</head>

<!-- partial:index.partial.html -->
<!--
VIEW IN FULL SCREEN MODE
FULL SCREEN MODE: http://salehriaz.com/404Page/404.html

DRIBBBLE: https://dribbble.com/shots/4330167-404-Page-Lost-In-Space
-->

<body class="bg-purple">
        
    <div class="stars">
        <div class="custom-navbar">
            <!--<div class="brand-logo">
                <img src="http://salehriaz.com/404Page/img/logo.svg" width="80px">
            </div>
            <div class="navbar-links">
                <ul>
                <li><a href="http://salehriaz.com/404Page/404.html" target="_blank">Home</a></li>
                <li><a href="http://salehriaz.com/404Page/404.html" target="_blank">About</a></li>
                <li><a href="http://salehriaz.com/404Page/404.html" target="_blank">Features</a></li>
                <li><a href="http://salehriaz.com/404Page/404.html" class="btn-request" target="_blank">Request A Demo</a></li>
                </ul>
            </div>-->
        </div>
        <div class="central-body">
            <img class="image-404" src="{{asset('404/img/404.svg')}}" width="300px">
            <a href="{{route('home')}}" class="btn-go-home">Regresar a la página principal</a>
        </div>
        <div class="objects">
            <img class="object_rocket" src="{{asset('404/img/rocket.svg')}}" width="40px">
            <div class="earth-moon">
                <img class="object_earth" src="{{asset('404/img/earth.svg')}}" width="100px">
                <img class="object_moon" src="{{asset('404/img/moon.svg')}}" width="80px">
            </div>
            <div class="box_astronaut">
                <img class="object_astronaut" src="{{asset('404/img/astronaut.svg')}}" width="140px">
            </div>
        </div>
        <div class="glowing_stars">
            <div class="star"></div>
            <div class="star"></div>
            <div class="star"></div>
            <div class="star"></div>
            <div class="star"></div>
        </div>
    </div>
</body>
    

</html>
