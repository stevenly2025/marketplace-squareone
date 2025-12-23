<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SquareOne - Masuk / Daftar</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            squareblue: '#2563eb',
                            squarenavy: '#0f172a',
                        }
                    }
                }
            }
        </script>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-100">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div>
                <a href="/">
                    <img src="{{ asset('images/logo.png') }}" class="w-20 h-20 object-contain mx-auto" alt="SquareOne Logo">
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg border border-gray-200">
                {{ $slot }}
            </div>
            
            <p class="text-center text-xs text-gray-400 mt-4">
                &copy; {{ date('Y') }} SquareOne Marketplace.
            </p>
        </div>
    </body>
</html>