<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="KD7bvGKswbz-zokTglMXCStQmkwJQdD80XSUG4s0uGc" />
    <meta name="facebook-domain-verification" content="qczaiq0ge041h1fzrnfv995mtvcbbx" />
    
    <!-- Primary Meta Tags -->
    <title>Ente Keralam - Government of Kerala Citizen Engagement Platform</title>
    <meta name="title" content="Ente Keralam - Government of Kerala Citizen Engagement Platform">
    <meta name="description" content="Join Ente Keralam, Kerala's premier citizen engagement platform. Participate in competitions, quizzes, polls, pledges, and discussions. Contribute to Kerala's development and growth.">
    <meta name="keywords" content="Kerala, citizen engagement, competitions, quizzes, polls, pledges, discussions, Government of Kerala, Ente Keralam">
    <meta name="author" content="Government of Kerala - C-DIT">
    <meta name="language" content="English, Malayalam">
    <meta name="revisit-after" content="7 days">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ config('app.url') }}">

    <!-- Open Graph / Facebook Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:title" content="Ente Keralam - Government of Kerala Citizen Engagement Platform">
    <meta property="og:description" content="Join Ente Keralam, Kerala's premier citizen engagement platform. Participate in competitions, quizzes, polls, pledges, and discussions.">
    <meta property="og:image" content="{{ url('ente-keralam.png') }}">
    <meta property="og:image:secure_url" content="{{ url('ente-keralam.png') }}">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Ente Keralam">
    <meta property="og:site_name" content="Ente Keralam">
    <meta property="og:locale" content="en_IN">
    <meta property="og:locale:alternate" content="ml_IN">

    <!-- Twitter / X Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ config('app.url') }}">
    <meta name="twitter:title" content="Ente Keralam - Government of Kerala Citizen Engagement Platform">
    <meta name="twitter:description" content="Join Ente Keralam, Kerala's premier citizen engagement platform. Participate in competitions, quizzes, polls, pledges, and discussions.">
    <meta name="twitter:image" content="{{ url('ente-keralam.png') }}">
    <meta name="twitter:creator" content="@GovernmentofKerala">

    <!-- LinkedIn Meta Tags -->
    <meta property="linkedin:url" content="{{ config('app.url') }}">
    <meta property="linkedin:title" content="Ente Keralam - Citizen Engagement">

    <!-- WhatsApp Meta Tags -->
    <meta property="whatsapp:image" content="{{ url('ente-keralam.png') }}">

    <!-- Theme Color -->
    <meta name="theme-color" content="#ff176b">
    <meta name="msapplication-TileColor" content="#ff176b">

    <!-- Additional SEO Tags -->
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="geo.region" content="IN-KL">
    <meta name="geo.placename" content="Kerala, India">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="Ente Keralam" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}" />

    <!-- Copy all CSS from the design directory -->
    <link rel="stylesheet" href="{{ asset('design/css/bootstrap.min.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('design/css/fontawesome-all.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('design/css/flaticon-5.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('design/css/flaticon-34.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('design/css/animate.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('design/css/video.min.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('design/css/slick.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('design/css/side-demo.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('design/css/buttons.css') }}?v=1.0.1">
    <link rel="stylesheet" href="{{ asset('design/css/style-34.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('design/css/responsive-35.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('design/css/jquery-ui.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('design/css/jquery.mCustomScrollbar.min.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('design/css/sidebar.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('design/owl.carousel.css') }}?v=1.0">

    {{-- React Fast Refresh preamble for Vite (dev only, no-op in prod) --}}
    @viteReactRefresh
    @vite(['resources/sass/app.scss', 'resources/js/app.jsx'])
</head>

<body>
    <div id="app"></div>
</body>

</html>
