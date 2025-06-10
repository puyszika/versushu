<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Versus.hu</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />

    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
    </style>

</head>

<body class="font-sans text-white relative">

<!-- Háttér videó -->
<video id="video-background" preload muted autoplay loop playsinline
       disablepictureinpicture controlslist="nodownload nofullscreen noremoteplayback">
    <source src="{{ asset('videos/background.mp4') }}" type="video/mp4" />
</video>

<!-- Navigáció -->
<div class="nav bg-gray-800">
    <div class="flex items-center gap-3 px-4 py-3">

        <img src="{{ asset('images/logo.png') }}" alt="Versus logó" class="h-10 w-auto inline-block">


    </div>
    <div class="flex gap-4">
        @if (Route::has('login'))
            <div class="hidden fixed top-0 right-0 px-10 py-4 sm:block">
                <a href="{{ route('home') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Kezdőlap</a>

                @auth
                    {{-- Bejelentkezett user --}}
                @else
                    <a href="{{ route('login') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Bejelentkezés</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Regisztráció</a>
                    @endif
                @endauth
            </div>
        @endif
    </div>
</div>

<!-- Középső tartalom -->
<div class="container">
    <div class="row">
        <div class="col-md-6 loginmiddle">
            <div class="hero-box">
                <h1>Versus<span>CS</span>.hu</h1>
                <p>A Magyar Amatőr <span class="highlight">Counter Strike</span> játékosok otthona – amatőr bajnokságok, <span class="highlight">Online/Offline</span> versenyek.</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
