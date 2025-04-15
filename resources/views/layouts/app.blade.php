<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Projeto UEFS - Netra')</title>
    <meta name="description" content="@yield('description', 'Free open source Tailwind CSS starter Templates and Components.')">
    <link rel="stylesheet" href="https://unpkg.com/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet">
    <style>
        .gradient {
            background: linear-gradient(90deg, #d53369 0%, #daae51 100%);
        }
    </style>
    @livewireStyles
</head>
<body class="leading-normal tracking-normal text-white gradient" style="font-family: 'Source Sans Pro', sans-serif;">
    @include('partials.nav')
    <main>
        @yield('content')
        
    </main>
    @include('partials.footer')

    @livewireScripts
</body>
</html>