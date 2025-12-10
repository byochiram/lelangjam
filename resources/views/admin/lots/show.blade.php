{{-- resources/views/admin/lots/show.blade.php --}}
<x-app-layout :title="'Detail Lelang #'.$lot->id">
    @php
        $product = $lot->product;
        $images  = $product?->images ?? collect();
        $payment = $lot->payment;

        $runtime = $lot->runtime_status;

        $badgeLabel = match($runtime) {
            'SCHEDULED' => 'Segera',
            'ACTIVE'    => 'Live',
            'ENDED'     => 'Selesai',
            'CANCELLED' => 'Dibatalkan',
            'PENDING'   => 'Pending',
            'AWARDED'   => 'Terjual',
            'UNSOLD'    => 'Unsold',
            default     => $runtime,
        };

        $badgeClass = match($runtime) {
            'SCHEDULED' => 'bg-amber-100 text-amber-700',
            'ACTIVE'    => 'bg-emerald-100 text-emerald-700',
            'ENDED'     => 'bg-slate-900 text-white',
            'CANCELLED' => 'bg-red-100 text-red-700',
            'PENDING'   => 'bg-blue-100 text-blue-700',
            'AWARDED'   => 'bg-green-900 text-green-100',
            'UNSOLD'    => 'bg-slate-200 text-slate-700',
            default     => 'bg-slate-200 text-slate-700',
        };

        // TAB KEMBALI
        if (in_array($runtime, ['AWARDED','UNSOLD','PENDING'])) {
            $backTab = 'ended';
        } elseif (in_array($runtime, ['SCHEDULED','ACTIVE','CANCELLED','ENDED'])) {
            $backTab = strtolower($runtime);
        } else {
            $backTab = 'scheduled';
        }

        // LOGIKA BACK DINAMIS
        $fromParam = request('from');
        $backUrl   = null;
        $backText  = '← Kembali ke daftar lelang';

        if ($fromParam && str_starts_with($fromParam, 'product-')) {
            $productId = (int) str_replace('product-', '', $fromParam);
            $backUrl   = route('products.show', $productId);
            $backText  = '← Kembali ke detail produk';
        } elseif ($fromParam && str_starts_with($fromParam, 'user-')) {
            $userId  = (int) str_replace('user-', '', $fromParam);
            $backUrl = route('users.show', $userId);
            $backText = '← Kembali ke detail pengguna';
        } elseif ($fromParam && str_starts_with($fromParam, 'payment-')) {
            $paymentId = (int) str_replace('payment-', '', $fromParam);
            $backUrl   = route('payments.show', ['payment' => $paymentId]);
            $backText  = '← Kembali ke detail transaksi';
        } else {
            $backUrl = route('lots.index', ['tab' => $backTab]);
        }
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Lelang #{{ $lot->id }}
            </h2>

            <a href="{{ $backUrl }}"
                class="px-3 py-1.5 rounded-full border border-slate-200 text-slate-700 text-xs hover:bg-slate-50">
                    {{ $backText }}
            </a>
        </div>
    </x-slot>

    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <div class="bg-white shadow-xl sm:rounded-2xl overflow-hidden">

            {{-- HEADER ABU-ABU --}}
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                <div class="space-y-1">
                    <h1 class="text-2xl font-semibold text-slate-900">
                        {{ $product->brand ?? '—' }}
                        {{ $product->model ?? '' }}
                    </h1>
                    <div class="text-sm text-slate-500">
                        Dibuat: {{ $lot->created_at->format('d M Y H:i') }}
                    </div>
                    @if($lot->cancelled_at)
                        <div class="text-sm text-red-600">
                            Dibatalkan: {{ $lot->cancelled_at->format('d M Y H:i') }}
                        </div>
                    @endif
                </div>

                <div class="text-xs text-left md:text-right">
                    <div class="flex flex-wrap items-center gap-2 md:justify-end">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold {{ $badgeClass }}">
                            {{ $badgeLabel }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="px-6 py-6 space-y-6">
                {{-- ALASAN PEMBATALAN: hanya kalau lot dibatalkan --}}
                @if($lot->runtime_status === 'CANCELLED')
                    @php
                        $hasCancel = $lot->cancelled_at && $lot->cancel_reason;
                    @endphp
                    <div class="border border-red-100 bg-red-50 rounded-xl px-4 py-3 text-sm">
                        <div class="font-semibold text-red-800 mb-1">Alasan pembatalan</div>
                        <p class="{{ $hasCancel ? 'text-red-800' : 'text-red-400 italic' }}">
                            {{ $hasCancel ? $lot->cancel_reason : '—' }}
                        </p>
                    </div>
                @endif

                {{-- LAYOUT 2 KOLOM: KIRI GALERI, KANAN INFO --}}
                <div class="grid lg:grid-cols-2 gap-6 items-start">

                    {{-- KOLOM KIRI: GALERI GAMBAR --}}
                    <div x-data="{ active: 0, zoom: false }"
                        class="space-y-3 max-w-full min-w-0">
                        <div
                            class="aspect-[4/3] w-full max-w-full rounded-2xl overflow-hidden bg-slate-100 relative cursor-zoom-in"
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

                        @if($images->count() > 1)
                            {{-- thumbnail: SCROLL HORIZONTAL --}}
                            <div class="flex gap-2 overflow-x-auto pb-1 max-w-full min-w-0">
                                @foreach($images as $idx => $img)
                                    <button
                                        type="button"
                                        @click="active = {{ $idx }}"
                                        class="relative flex-none h-16 w-16 md:h-20 md:w-20
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

                    {{-- KOLOM KANAN: INFO PRODUK & INFO LELANG --}}
                    <div class="space-y-4">
                        {{-- KARTU: INFORMASI PRODUK --}}
                        <div class="border border-slate-200 rounded-xl p-4 space-y-3">
                            <div>
                                <div class="text-xs uppercase tracking-wide text-slate-500 mb-1">Nama Produk</div>
                                <div class="text-lg font-semibold text-slate-900">
                                    {{ $product->brand ?? '—' }} {{ $product->model ?? '' }}
                                </div>
                                <div class="text-xs text-slate-500 mt-1">
                                    ID Produk: #{{ $lot->product_id }}
                                </div>
                            </div>

                            @if($product)
                                <dl class="grid grid-cols-2 gap-y-2 gap-x-4 text-sm">
                                    <div>
                                        <dt class="text-slate-500 text-xs">Brand</dt>
                                        <dd class="font-medium text-slate-900">{{ $product->brand ?? '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-slate-500 text-xs">Model</dt>
                                        <dd class="font-medium text-slate-900">{{ $product->model ?? '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-slate-500 text-xs">Tahun</dt>
                                        <dd class="font-medium text-slate-900">{{ $product->year ?? '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-slate-500 text-xs">Kondisi</dt>
                                        <dd class="font-medium text-slate-900">{{ $product->condition ?? '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-slate-500 text-xs">Berat (gram)</dt>
                                        <dd class="font-medium text-slate-900">{{ $product->weight_grams ?? '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-slate-500 text-xs">Kategori</dt>
                                        <dd class="font-medium text-slate-900">{{ $product->category ?? '-' }}</dd>
                                    </div>
                                </dl>
                                
                                <div class="pt-3 border-t border-slate-100 text-sm">
                                    <div class="text-slate-500 text-xs mb-2">Deskripsi</div>
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
                            @endif
                        </div>

                        {{-- KARTU: INFORMASI LELANG (HARGA + JADWAL) --}}
                        <div class="border border-slate-200 rounded-xl p-4 space-y-3">
                            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">
                                Informasi Lelang
                            </h3>

                            <dl class="text-sm space-y-1.5">
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Harga Awal</dt>
                                    <dd class="font-semibold">
                                        Rp {{ number_format($lot->start_price,0,',','.') }}
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Kelipatan Bid</dt>
                                    <dd class="font-semibold">
                                        Rp {{ number_format($lot->increment,0,',','.') }}
                                    </dd>
                                </div>
                                @if($highestBid)
                                    <div class="flex justify-between pt-2 border-t border-dashed">
                                        <dt class="text-slate-500">Bid Tertinggi</dt>
                                        <dd class="font-semibold text-emerald-700">
                                            Rp {{ number_format($highestBid->amount,0,',','.') }}
                                        </dd>
                                    </div>
                                @endif
                                <div class="flex justify-between pt-2 border-t border-dashed">
                                    <dt class="text-slate-500">Total Bid Masuk</dt>
                                    <dd class="font-semibold">
                                        {{ $bids->count() }} bid
                                    </dd>
                                </div>
                            </dl>

                            <div class="pt-3 border-t border-slate-100">
                                <dl class="text-sm space-y-1.5">
                                    <div class="flex justify-between">
                                        <dt class="text-slate-500">Mulai</dt>
                                        <dd class="font-semibold">
                                            {{ $lot->start_at->format('d M Y H:i') }}
                                        </dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-slate-500">Selesai</dt>
                                        <dd class="font-semibold">
                                            {{ $lot->end_at->format('d M Y H:i') }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                                                {{-- KARTU: INVOICE (UNTUK AWARDED / UNSOLD / PENDING) --}}
                        @if($payment && in_array($lot->runtime_status, ['AWARDED','UNSOLD','PENDING']))
                            <div class="border border-slate-200 rounded-xl p-4 space-y-2">
                                <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">
                                    Invoice
                                </h3>

                                <div class="flex items-center justify-between text-sm">
                                    <div>
                                        <div class="text-slate-500 text-xs">Nomor Invoice</div>
                                        <div class="font-semibold text-slate-900">
                                            <a href="{{ route('payments.show', ['payment' => $payment, 'from' => 'lot-'.$lot->id]) }}"
                                                class="text-blue-600 hover:underline">
                                                    {{ $payment->invoice_no }}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="text-right text-xs text-slate-500">
                                        Status: {{ $payment->status }}
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- RIWAYAT BID (DI BAWAH 2 KOLOM) --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-sm text-slate-700">
                            Riwayat Bid
                        </h3>
                        @if($bids->count() > 0)
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-[12px] font-medium text-slate-700">
                                {{ $bids->count() }} bid tercatat
                            </span>
                        @endif
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="p-2 text-left text-sm font-semibold text-slate-600">Waktu</th>
                                    <th class="p-2 text-left text-sm font-semibold text-slate-600">Bidder</th>
                                    <th class="p-2 text-right text-sm font-semibold text-slate-600">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($bids as $bid)
                                    @php
                                        $bidUser = optional($bid->bidderProfile)->user;
                                    @endphp
                                    <tr class="bg-white hover:bg-slate-50">
                                        <td class="p-2 whitespace-nowrap text-sm text-slate-700">
                                            {{ $bid->created_at->format('d M Y H:i:s') }}
                                        </td>
                                        <td class="p-2 text-sm">
                                            @if($bidUser)
                                                <a href="{{ route('users.show', ['user' => $bidUser, 'from' => 'lot-'.$lot->id]) }}"
                                                    class="font-semibold text-blue-600 hover:underline">
                                                        {{ $bidUser->username ?? $bidUser->name ?? '—' }}
                                                </a>
                                            @else
                                                <span class="text-sm text-slate-400">Tidak diketahui</span>
                                            @endif
                                        </td>
                                        <td class="p-2 text-right whitespace-nowrap text-sm">
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 font-semibold text-slate-900">
                                                Rp {{ number_format($bid->amount,0,',','.') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-4 text-center text-xs text-slate-500">
                                            Belum ada bid untuk lelang ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-slate-100">
                <a href="{{ $backUrl }}"
                    class="px-3 py-1.5 rounded-full border border-slate-200 text-slate-700 text-xs hover:bg-slate-50">
                        {{ $backText }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
