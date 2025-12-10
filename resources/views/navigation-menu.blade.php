{{-- resources/views/navigation-menu.blade.php --}}
<nav x-data="{ open: false }"
     class="bg-slate-900/90 backdrop-blur border-b border-slate-700 relative z-40">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8 relative">
        <div class="flex justify-between items-center h-16 md:h-20">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('tempus/logo2.png') }}"
                             class="h-20 sm:h-20 md:h-20 lg:h-24 w-auto object-contain"
                             alt="Tempus Auctions Logo" />
                    </a>
                </div>

                <!-- Navigation Links (DESKTOP) -->
                @php
                    $current = request()->route()->getName();

                    function adminNavActive($cond){
                        return $cond
                            ? 'text-yellow-500'
                            : 'text-slate-200 hover:text-yellow-500 transition';
                    }
                @endphp

                <div class="hidden lg:-my-px lg:ms-6 lg:flex lg:gap-1">
                    <a href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center px-3 py-2 rounded-md text-[15px] font-medium
                                {{ adminNavActive(request()->routeIs('admin.dashboard') || request()->routeIs('dashboard')) }}">
                            Dashboard
                    </a>

                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center px-3 py-2 rounded-md text-[15px] font-medium {{ adminNavActive(request()->routeIs('products.*')) }}">
                        Produk
                    </a>

                    <a href="{{ route('lots.index') }}"
                       class="inline-flex items-center px-3 py-2 rounded-md text-[15px] font-medium {{ adminNavActive(request()->routeIs('lots.*')) }}">
                        Lelang
                    </a>

                    <a href="{{ route('users.index') }}"
                        class="inline-flex items-center px-3 py-2 rounded-md text-[15px] font-medium {{ adminNavActive(request()->routeIs('users.*')) }}">
                            Pengguna
                    </a>

                    <a href="{{ route('payments.index') }}"
                        class="inline-flex items-center px-3 py-2 rounded-md text-[15px] font-medium {{ adminNavActive(request()->routeIs('payments.*')) }}">
                            Transaksi
                    </a>
                </div>
            </div>

            {{-- KANAN DESKTOP: Beranda + Avatar --}}
            <div class="hidden lg:flex lg:items-center lg:ms-6 gap-3">
                {{-- TOMBOL KE WEBSITE --}}
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 rounded-full
                          border border-white/10 bg-slate-900
                          px-3 py-1.5 text-[13px] font-semibold text-slate-200
                          shadow-[0_0_0_1px_rgba(15,23,42,0.8)]
                          hover:bg-yellow-500 hover:text-slate-900
                          hover:shadow-[0_0_20px_rgba(250,204,21,0.55)]
                          transition">
                    <span>Beranda</span>
                </a>

                {{-- Settings Dropdown (Avatar) --}}
                <div class="relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-yellow-500/60 transition">
                                    <img class="size-8 rounded-full object-cover"
                                         src="{{ Auth::user()->profile_photo_url }}"
                                         alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-200 bg-slate-800 hover:bg-slate-700 hover:text-white transition">
                                        {{ Auth::user()->name }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link
                                href="{{ route('profile.show') }}"
                                :active="request()->routeIs('profile.show')"
                            >
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link
                                    href="{{ route('api-tokens.index') }}"
                                    :active="request()->routeIs('api-tokens.index')"
                                >
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200"></div>

                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}"
                                                 @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger (MOBILE) -->
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = ! open"
                        type="button"
                        class="inline-flex items-center justify-center p-2 text-sm text-gray-300 rounded-lg
                               hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-600">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                        <path x-show="!open"
                              fill-rule="evenodd"
                              d="M3 5h14a1 1 0 010 2H3a1 1 0 010-2zm0 4h14a1 1 0 010 2H3a1 1 0 010-2zm0 4h14a1 1 0 010 2H3a1 1 0 010-2z"
                              clip-rule="evenodd"></path>
                        <path x-show="open"
                              fill-rule="evenodd"
                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                              clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- MOBILE MENU ala GUEST --}}
        <div x-show="open"
            x-transition
            class="lg:hidden absolute left-0 right-0 top-full bg-slate-900/95 border-t border-slate-700 shadow-2xl z-50">
            <nav class="px-4 py-4 space-y-3">
                {{-- HEADER AKUN --}}
                <div class="flex items-center gap-3 rounded-xl bg-slate-800/80 px-3 py-3 mb-2">
                    <img src="{{ Auth::user()->profile_photo_url }}"
                         class="w-9 h-9 rounded-full ring-2 ring-yellow-500/60"
                         alt="Avatar">
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-white">
                            {{ Str::limit(Auth::user()->name, 22) }}
                        </span>
                        <span class="text-[11px] uppercase tracking-[0.15em] text-slate-400">
                            Administrator
                        </span>
                    </div>
                </div>

                <ul class="space-y-1 text-base font-medium">
                    {{-- NAV UTAMA --}}
                    <li class="text-[11px] uppercase tracking-[0.2em] text-slate-500 px-1 mb-1">
                        Navigasi
                    </li>

                    <li>
                        <a href="{{ url('/') }}"
                           class="flex items-center justify-between rounded-lg px-3 py-3
                                  text-gray-100 hover:bg-slate-800 hover:text-yellow-500 transition">
                            <span>Beranda</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center justify-between rounded-lg px-3 py-3
                            {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard')
                                    ? 'bg-slate-700/80 text-yellow-500'
                                    : 'text-gray-100 hover:bg-slate-800 hover:text-yellow-500' }} transition">
                                <span>Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('products.index') }}"
                           class="flex items-center justify-between rounded-lg px-3 py-3
                           {{ request()->routeIs('products.*')
                                ? 'bg-slate-700/80 text-yellow-500'
                                : 'text-gray-100 hover:bg-slate-800 hover:text-yellow-500' }} transition">
                            <span>Produk</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('lots.index') }}"
                           class="flex items-center justify-between rounded-lg px-3 py-3
                           {{ request()->routeIs('lots.*')
                                ? 'bg-slate-700/80 text-yellow-500'
                                : 'text-gray-100 hover:bg-slate-800 hover:text-yellow-500' }} transition">
                            <span>Lelang</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('users.index') }}"
                            class="flex items-center justify-between rounded-lg px-3 py-3
                            {{ request()->routeIs('users.*')
                                    ? 'bg-slate-700/80 text-yellow-500'
                                    : 'text-gray-100 hover:bg-slate-800 hover:text-yellow-500' }} transition">
                                <span>Pengguna</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('payments.index') }}"
                            class="flex items-center justify-between rounded-lg px-3 py-3
                            {{ request()->routeIs('payments.*')
                                    ? 'bg-slate-700/80 text-yellow-500'
                                    : 'text-gray-100 hover:bg-slate-800 hover:text-yellow-500' }} transition">
                                <span>Transaksi</span>
                        </a>
                    </li>

                    {{-- AKUN --}}
                    <li class="pt-4 text-[11px] uppercase tracking-[0.2em] text-slate-500 px-1">
                        Akun
                    </li>

                    <li>
                        <a href="{{ route('profile.show') }}"
                           class="flex items-center justify-between rounded-lg px-3 py-3
                           {{ request()->routeIs('profile.show')
                                ? 'bg-slate-700/80 text-yellow-500'
                                : 'text-gray-100 hover:bg-slate-800 hover:text-yellow-500' }} transition">
                            <span>Profil Akun</span>
                        </a>
                    </li>

                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                class="w-full text-left rounded-lg px-3 py-3 text-gray-100 hover:bg-red-500/10 hover:text-red-400 transition">
                                Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</nav>
