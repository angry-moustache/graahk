<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ app('site')->getTitle() }}</title>
        <link rel="icon" href="{{ asset('images/icon.jpg') }}" type="image/x-icon" />
        @vite(['resources/css/app.scss'])
        <script src="https://unpkg.com/@nextapps-be/livewire-sortablejs@0.4.0/dist/livewire-sortable.js"></script>

    </head>

    <body class="bg-background text-text">
        <livewire:modal-controller />
        <x-toasts />
        <x-tooltipper />

        <x-main>
            {{ $slot }}
        </x-main>

        @vite(['resources/js/app.js'])
    </body>
</html>
