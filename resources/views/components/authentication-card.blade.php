@props(['logo'])

<div class="flex justify-center w-full">
    <div
        class="
            w-full
            max-w-[480px]      {{-- <== di sini yang bikin lebih lebar --}}
            bg-white
            border border-slate-200
            rounded-2xl
            shadow-sm
            p-8 md:p-10
        "
    >
        @if (isset($logo))
            <div class="flex justify-center mb-6">
                {{ $logo }}
            </div>
        @endif

        {{ $slot }}
    </div>
</div>
