<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans text-gray-900 antialiased bg-[#F3F4F6]">
        <div class="min-h-screen p-4 sm:p-8 flex items-center justify-center">
            <div class="w-full max-w-6xl bg-white shadow-2xl rounded-2xl overflow-hidden grid lg:grid-cols-2">
                <!-- Left: auth form -->
                <div class="p-6 sm:p-10 lg:p-12 flex flex-col">
                    <a href="/" wire:navigate class="inline-flex items-center">
                        <img
                            src="{{ asset('assets/Logo.png') }}"
                            alt="{{ config('app.name') }} logo"
                            class="h-9 w-auto"
                        />
                    </a>

                    <div class="mt-10 max-w-md">
                        {{ $slot }}
                    </div>

                    <div class="mt-auto pt-10 flex items-center justify-between text-xs text-gray-400">
                        <span>Copyright © {{ date('Y') }} {{ config('app.name') }}.</span>
                        <a href="#" class="hover:text-gray-600">Privacy Policy</a>
                    </div>
                </div>

                <!-- Right: marketing panel -->
                <div class="hidden lg:block relative">
                    <div class="absolute inset-0 bg-[#2F39F3]"></div>
                    <div class="absolute inset-0 opacity-30"
                         style="background:
                            radial-gradient(circle at 20% 20%, rgba(255,255,255,.35), transparent 40%),
                            radial-gradient(circle at 80% 30%, rgba(255,255,255,.25), transparent 45%),
                            radial-gradient(circle at 60% 85%, rgba(255,255,255,.20), transparent 50%);">
                    </div>

                    <div class="relative h-full p-12 text-white flex flex-col">
                        <h2 class="text-4xl font-semibold leading-tight">
                            Effortlessly manage your team
                            <span class="block text-white/90">and operations.</span>
                        </h2>
                        <p class="mt-4 text-white/80 max-w-md">
                            Log in to access your dashboard and manage your support tickets.
                        </p>

                        <!-- Mock dashboard -->
                        <div class="mt-10 flex-1 flex items-end">
                            <div class="w-full bg-white rounded-2xl shadow-2xl p-6">
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="rounded-xl bg-indigo-50 p-4">
                                        <div class="text-xs text-indigo-700/70">Tickets resolved</div>
                                        <div class="mt-2 text-2xl font-semibold text-indigo-900">189</div>
                                        <div class="mt-1 text-xs text-indigo-700/60">This month</div>
                                    </div>
                                    <div class="rounded-xl bg-white border border-gray-100 p-4 col-span-2">
                                        <div class="text-xs text-gray-500">Chat performance</div>
                                        <div class="mt-2 text-2xl font-semibold text-gray-900">00:01:30</div>
                                        <div class="mt-1 h-2 rounded bg-gray-100 overflow-hidden">
                                            <div class="h-full w-1/3 bg-indigo-500"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 rounded-xl border border-gray-100 p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="text-xs text-gray-500">Sales overview</div>
                                        <div class="text-xs text-gray-400">Weekly</div>
                                    </div>
                                    <div class="mt-3 grid grid-cols-12 gap-2 items-end">
                                        @foreach ([18,26,14,30,22,34,16,28,20,36,24,32] as $h)
                                            <div class="col-span-1 rounded bg-indigo-200" style="height: {{ $h }}px;"></div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
