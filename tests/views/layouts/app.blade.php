<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel Chatter') }}</title>

        <!-- Styles -->
        @stack('styles')

    </head>
    <body>
        <main>
            @yield('content')
        </main>

        @stack('scripts')
    </body>
</html>
