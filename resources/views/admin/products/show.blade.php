{{-- resources/views/admin/products/show.blade.php --}}
<x-app-layout :title="'Detail Produk #'.$product->id">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Produk #{{ $product->id }}
                </h2>
            </div>

            <a href="{{ route('products.index') }}"
               class="px-3 py-1.5 rounded-full border border-slate-200 text-slate-700 text-xs hover:bg-slate-50">
                ← Kembali
            </a>
        </div>
    </x-slot>

    @php
        $images     = $product->images;
        $primary    = $images->first();

        $lots       = $product->auctionLots->sortByDesc('start_at');
        $totalLots  = $lots->count();
        $activeLots = $lots->filter(fn($lot) => $lot->runtime_status === 'ACTIVE')->count();
        $schedLots  = $lots->filter(fn($lot) => $lot->runtime_status === 'SCHEDULED')->count();
        $endedLots  = $lots->filter(fn($lot) => in_array($lot->runtime_status, ['ENDED','UNSOLD','AWARDED','PENDING']))->count();
    @endphp

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <div class="bg-white shadow-xl sm:rounded-2xl overflow-hidden">

            {{-- HEADER ABU-ABU (nyamain detail lot / payment) --}}
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                <div class="space-y-1">
                    <h1 class="text-2xl font-semibold text-slate-900">
                        {{ $product->brand }} — {{ $product->model }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-white border border-slate-200 text-[11px] font-medium text-slate-700">
                            ID Produk #{{ $product->id }}
                        </span>
                    </div>

                    <div class="text-xs text-slate-400">
                        Dibuat: {{ $product->created_at?->format('d M Y, H:i') ?? '—' }}
                        • Update terakhir: {{ $product->updated_at?->format('d M Y, H:i') ?? '—' }}
                    </div>
                </div>

                <div class="text-xs text-left md:text-right space-y-1">
                    <div class="flex flex-wrap items-center gap-2 md:justify-end">
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-[11px] font-medium text-slate-700">
                            Dipakai di {{ $totalLots }} lot
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500">
                        {{ $activeLots }} aktif · {{ $schedLots }} terjadwal · {{ $endedLots }} berakhir
                    </div>
                </div>
            </div>

            {{-- KONTEN UTAMA --}}
            <div class="px-6 py-6 space-y-6">

                {{-- GRID: GALERI + INFO PRODUK --}}
                <div class="grid lg:grid-cols-2 gap-6 items-start">

                    {{-- KIRI: GALERI INTERAKTIF (semua foto, bisa diklik & zoom) --}}
                    <div
                        x-data="{ active: 0, zoom: false }"
                        class="space-y-3  min-w-0"
                    >
                        {{-- foto besar --}}
                        <div
                            class="aspect-[4/3] rounded-2xl overflow-hidden bg-slate-100 relative cursor-zoom-in"
                            @click="if({{ $images->count() }}) zoom = true"
                        >
                            @forelse($images as $idx => $img)
                                <img
                                    x-show="active === {{ $idx }}"
                                    x-cloak
                                    src="{{ $img->public_url }}"
                                    alt="Gambar {{ $idx+1 }}"
                                    class="w-full h-full object-cover"
                                >
                            @empty
                                <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs">
                                    Tidak ada gambar produk
                                </div>
                            @endforelse

                            @if($images->count() > 0)
                                <div class="absolute right-2 bottom-2 rounded-full bg-black/60 text-white text-[11px] px-2 py-0.5">
                                    Klik untuk perbesar
                                </div>
                            @endif
                        </div>

                        {{-- THUMBNAIL HORIZONTAL (semua foto tampil, mobile bisa scroll) --}}
                        @if($images->count() > 1)
                            <div class="flex gap-2 overflow-x-auto pb-1 lg:pb-3 max-w-full min-w-0">
                                @foreach($images as $idx => $img)
                                    <button
                                        type="button"
                                        @click.stop="active = {{ $idx }}"
                                        class="relative flex-none
                                                basis-1/4 md:basis-1/5 lg:basis-auto   {{-- mobile: 4–5 thumb per layar, sisanya scroll --}}
                                                aspect-square                           {{-- selalu kotak --}}
                                                lg:w-20 lg:h-20                         {{-- di layar besar tetap 80px --}}
                                                rounded-lg overflow-hidden bg-slate-100 border
                                                flex items-center justify-center
                                                transition ring-offset-2"
                                        :class="active === {{ $idx }}
                                            ? 'ring-2 ring-slate-900 border-slate-900'
                                            : 'border-slate-200 hover:border-slate-400'">
                                        <img
                                            src="{{ $img->public_url }}"
                                            alt="Thumb {{ $idx+1 }}"
                                            class="w-full h-full object-cover"
                                        >
                                        @if($loop->first)
                                            <span class="absolute left-1 top-1 bg-black/60 text-[10px] text-white px-1.5 py-0.5 rounded">
                                                UTAMA
                                            </span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        {{-- OVERLAY ZOOM FULLSCREEN --}}
                        @if($images->count() > 0)
                            <div
                                x-show="zoom"
                                x-cloak
                                @keydown.escape.window="zoom = false"
                                class="fixed inset-0 z-[999] bg-black/80 flex flex-col items-center justify-center px-4"
                            >
                                <button
                                    type="button"
                                    class="absolute top-4 right-4 h-8 w-8 rounded-full bg-white/90 text-slate-800 flex items-center justify-center text-sm hover:bg-white"
                                    @click="zoom = false"
                                >
                                    ✕
                                </button>

                                <div class="max-w-4xl w-full max-h-[80vh]">
                                    @foreach($images as $idx => $img)
                                        <img
                                            x-show="active === {{ $idx }}"
                                            x-cloak
                                            src="{{ $img->public_url }}"
                                            class="w-full h-full object-contain rounded-xl bg-black/10"
                                        >
                                    @endforeach
                                </div>

                                @if($images->count() > 1)
                                    <div class="mt-4 flex gap-2 overflow-x-auto max-w-full">
                                        @foreach($images as $idx => $img)
                                            <button
                                                type="button"
                                                @click="active = {{ $idx }}"
                                                class="flex-none h-14 w-14 rounded-lg overflow-hidden border
                                                    {{ $loop->first ? 'border-yellow-400' : 'border-slate-200' }}"
                                            >
                                                <img src="{{ $img->public_url }}" class="w-full h-full object-cover">
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- KANAN: INFO PRODUK & STATISTIK --}}
                    <div class="space-y-4">

                        {{-- INFO UTAMA + KATEGORI --}}
                        <div class="border border-slate-200 rounded-xl p-4 space-y-3">
                            <div>
                                <div class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-1">
                                    Informasi Produk
                                </div>
                                <div class="text-lg font-semibold text-slate-900">
                                    {{ $product->brand }} — {{ $product->model }}
                                </div>
                            </div>

                            <dl class="grid grid-cols-2 gap-y-2 gap-x-4 text-sm">
                                <div>
                                    <dt class="text-slate-500 text-xs">Brand</dt>
                                    <dd class="font-medium text-slate-900">{{ $product->brand ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500 text-xs">Model</dt>
                                    <dd class="font-medium text-slate-900">{{ $product->model ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500 text-xs">Tahun</dt>
                                    <dd class="font-medium text-slate-900">{{ $product->year ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500 text-xs">Kondisi</dt>
                                    <dd class="font-medium text-slate-900">{{ $product->condition ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500 text-xs">Berat (gram)</dt>
                                    <dd class="font-medium text-slate-900">
                                        {{ $product->weight_grams ? number_format($product->weight_grams,0,',','.') : '—' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500 text-xs">Kategori</dt>
                                    <dd class="font-medium text-slate-900">
                                        @if(!empty($categories))
                                            <div class="flex flex-wrap gap-1.5 mt-0.5">
                                                @foreach($categories as $cat)
                                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-700">
                                                        {{ $cat }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            —
                                        @endif
                                    </dd>
                                </div>
                            </dl>

                            <div class="mt-3 rounded-lg bg-yellow-50 border border-yellow-100 px-3 py-2 text-xs text-yellow-900">
                                <strong>Catatan:</strong>
                                Berat produk di atas otomatis dipakai sebagai <em>shipping weight</em> saat sistem membuat invoice pemenang lelang.
                            </div>
                        </div>

                        {{-- STAT LOT PAKAI PRODUK INI --}}
                        <div class="border border-slate-200 rounded-xl p-4">
                            <div class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-2">
                                Ringkasan Penggunaan di Lelang
                            </div>
                            <div class="grid grid-cols-3 gap-3 text-sm">
                                <div class="rounded-lg bg-slate-50 border border-slate-100 px-3 py-2">
                                    <div class="text-[11px] text-slate-500">Total Lot</div>
                                    <div class="mt-1 text-lg font-semibold text-slate-900">
                                        {{ $totalLots }}
                                    </div>
                                </div>
                                <div class="rounded-lg bg-slate-50 border border-slate-100 px-3 py-2">
                                    <div class="text-[11px] text-slate-500">Aktif</div>
                                    <div class="mt-1 text-lg font-semibold text-slate-900">
                                        {{ $activeLots }}
                                    </div>
                                </div>
                                <div class="rounded-lg bg-slate-50 border border-slate-100 px-3 py-2">
                                    <div class="text-[11px] text-slate-500">Selesai</div>
                                    <div class="mt-1 text-lg font-semibold text-slate-900">
                                        {{ $endedLots }}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- DESKRIPSI PRODUK --}}
                <div class="border border-slate-200 rounded-xl p-4">
                    <h3 class="text-sm font-semibold text-slate-800 mb-2">
                        Deskripsi Produk
                    </h3>

                    @if($product->description)
                        <div class="prose prose-sm max-w-none text-slate-700">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    @else
                        <p class="text-sm text-slate-500">
                            Belum ada deskripsi produk. Tambahkan catatan singkat di menu edit produk
                            untuk membantu calon bidder memahami kondisi & cerita jam ini.
                        </p>
                    @endif
                </div>

                {{-- LOT YANG MEMAKAI PRODUK INI --}}
                <div class="border border-slate-200 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-800">
                                Lot Lelang yang menggunakan produk ini
                            </h3>
                            <p class="text-xs text-slate-500">
                                Daftar lot yang terhubung dengan produk #{{ $product->id }}.
                            </p>
                        </div>
                    </div>

                    @if($lots->isEmpty())
                        <div class="rounded-lg border border-dashed border-slate-200 py-6 text-center text-sm text-slate-500">
                            Produk ini belum pernah dipakai di lot lelang mana pun.
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-lg border border-slate-200">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50 text-xs uppercase tracking-[0.16em] text-slate-500">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Lot</th>
                                        <th class="px-4 py-2 text-left">Status</th>
                                        <th class="px-4 py-2 text-left">Jadwal</th>
                                        <th class="px-4 py-2 text-right">Start Price</th>
                                        <th class="px-4 py-2 text-right">Current / Final</th>
                                        <th class="px-4 py-2 text-left">Pemenang</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($lots as $lot)
                                        @php
                                            $status = $lot->runtime_status;
                                            $badgeClass = match($status) {
                                                'ACTIVE'   => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                'SCHEDULED'=> 'bg-blue-50 text-blue-700 border-blue-100',
                                                'PENDING'  => 'bg-amber-50 text-amber-700 border-amber-100',
                                                'AWARDED'  => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                'UNSOLD'   => 'bg-slate-100 text-slate-700 border-slate-200',
                                                'CANCELLED'=> 'bg-red-50 text-red-700 border-red-100',
                                                default    => 'bg-slate-100 text-slate-700 border-slate-200',
                                            };
                                            $winnerUser = $lot->winnerUser;
                                            $payment    = $lot->payment;
                                        @endphp
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="px-4 py-2 align-top">
                                                <div class="font-medium">
                                                    <a href="{{ route('lots.detail', ['lot' => $lot, 'from' => 'product-'.$product->id]) }}"
                                                        class="text-blue-600 hover:underline">
                                                            Lot #{{ $lot->id }}
                                                    </a>
                                                </div>
                                                <div class="text-xs text-slate-500">
                                                    Increment {{ number_format($lot->increment, 0, ',', '.') }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-2 align-top">
                                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-semibold {{ $badgeClass }}">
                                                    {{ $status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 align-top text-xs text-slate-700">
                                                <div>Mulai: {{ $lot->start_at?->format('d M Y, H:i') }}</div>
                                                <div>Selesai: {{ $lot->end_at?->format('d M Y, H:i') }}</div>
                                            </td>
                                            <td class="px-4 py-2 align-top text-right text-slate-700">
                                                {{ number_format($lot->start_price, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-2 align-top text-right text-slate-700">
                                                {{ number_format($lot->current_price, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-2 align-top text-xs text-slate-700">
                                                @if($winnerUser && $payment)
                                                    <a href="{{ route('payments.show', ['payment' => $payment, 'from' => 'product-'.$product->id]) }}"
                                                    class="font-medium text-blue-600 hover:underline">
                                                        {{ $winnerUser->name }}
                                                    </a>
                                                    <div class="text-[11px] text-slate-500">
                                                        {{ $winnerUser->email }}
                                                    </div>
                                                @elseif($winnerUser)
                                                    <div class="font-medium text-slate-900">
                                                        {{ $winnerUser->name }}
                                                    </div>
                                                    <div class="text-[11px] text-slate-500">
                                                        {{ $winnerUser->email }}
                                                    </div>
                                                @else
                                                    <span class="text-slate-400">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="p-6 border-t border-slate-100">
                <a href="{{ route('products.index') }}"
                   class="px-3 py-1.5 rounded-full border border-slate-200 text-slate-700 text-xs hover:bg-slate-50">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
