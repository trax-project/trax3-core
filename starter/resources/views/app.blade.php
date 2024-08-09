<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="/fonts/inter/inter.css">

        <!-- Brand -->
        <link rel="apple-touch-icon" sizes="76x76" href="/img/apple-icon.png">
        <link rel="icon" type="image/png" href="/img/favicon.png">

        <!-- Scripts -->
        @routes

        {{
            Vite::useBuildDirectory('starter')
                ->withEntryPoints(['trax/core/starter/resources/js/app.js', "trax/core/starter/resources/js/Pages/{$page['component']}.vue"])
        }}
        
        @inertiaHead
    </head>
    <body class="font-sans antialiased h-full bg-gray-200 dark:bg-gray-900">
        @inertia
    </body>
</html>
