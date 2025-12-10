{{-- resources/views/bidder/auctions/_bid-row.blade.php --}}
@php
    /** @var array $row */
    /** @var \App\Models\Bid $bid */
    $bid          = $row['latest'];
    $lot          = $row['lot'];
    $product      = $lot?->product;
    $img          = optional($product?->images->first())->public_url ?? asset('tempus/placeholder.jpg');

    $status       = $bid->status ?? 'LOST';
    $previousBids = $row['previous_bids'] ?? collect();

    $statusClasses = match ($status) {
        'LEADING'   => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        'OUTBID'    => 'bg-amber-50 text-amber-700 ring-amber-100',
        'LOST'      => 'bg-red-50 text-red-700 ring-red-100',
        'WON'       => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
        'CANCELLED' => 'bg-slate-200 text-slate-700 ring-slate-300',
        default     => 'bg-slate-100 text-slate-700 ring-slate-200',
    };

    $startLabel = $lot?->start_at?->format('d M Y, H:i');
    $endLabel   = $lot?->end_at?->format('d M Y, H:i');
    $payment    = $lot?->payment;
@endphp

{{-- wrapper 1 baris + accordion --}}
<div class="border-b last:border-b-0 border-slate-100 hover:bg-slate-50/60 transition-colors"
    x-data="{ open: false }"
    x-cloak
    data-bid-row
    data-bid-id="{{ $bid->id }}"
    data-lot-label="Lot #{{ $lot->id ?? '—' }}">

    {{-- ===== MOBILE CARD ===== --}}
    <div class="md:hidden px-4 py-3 space-y-3">
        <div class="flex items-start gap-3">
            <div class="w-14 h-14 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0 ring-1 ring-slate-200/70">
                <img src="{{ $img }}" alt="{{ $lot->title ?? 'Lot' }}" class="w-full h-full object-cover">
            </div>
            <div class="space-y-0.5 min-w-0">
                {{-- NAMA LOT = LINK BIRU KE DETAIL --}}
                <a href="{{ route('lots.show', ['lot' => $lot, 'from' => 'my-auctions']) }}"
                    class="text-sm font-semibold text-blue-600 hover:underline underline-offset-2 truncate">
                        {{ $lot->title ?? (($product->brand ?? '-') . ' ' . ($product->model ?? '')) }}
                </a>
                <p class="text-sm font-semibold text-slate-500">
                    Lot #{{ $lot->id ?? '—' }}
                </p>

                @if($startLabel || $endLabel)
                    <p class="text-[12px] font-medium text-slate-500">
                        @if($startLabel)
                            Mulai: {{ $startLabel }}
                        @endif
                        @if($endLabel)
                            <br>Berakhir: {{ $endLabel }}
                        @endif
                    </p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 text-sm mt-2">
            {{-- Bid Anda --}}
            <div>
                <div class="text-[11px] uppercase tracking-[0.14em] text-slate-400 mb-0.5">
                    Bid Anda
                </div>
                <span class="inline-flex items-center rounded-full bg-slate-900/5 px-3 py-1 text-xs font-semibold text-slate-900">
                    {{ $bid->amount_formatted ?? 'Rp 0' }}
                </span>
            </div>

            {{-- Waktu Bid --}}
            <div>
                <div class="text-[11px] uppercase tracking-[0.14em] text-slate-400 mb-0.5">
                    Waktu Bid Terakhir
                </div>
                <span class="text-slate-700">
                    {{ $bid->created_at?->format('d M Y, H:i') ?? '-' }}
                </span>
            </div>

            {{-- Status --}}
            <div>
                <div class="text-[11px] uppercase tracking-[0.14em] text-slate-400 mb-0.5">
                    Status
                </div>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 {{ $statusClasses }}"
                    data-bid-status-label>
                    {{ ucfirst(strtolower($status)) }}
                </span>
                @if($status === 'WON' && $payment)
                    <a href="{{ route('transactions.show', $payment) }}"
                    class="mt-2 block text-[12px] text-blue-600 hover:underline">
                        Lihat Invoice
                    </a>
                @endif
            </div>

            {{-- Posisi + toggle accordion --}}
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] uppercase tracking-[0.14em] text-slate-400 mb-0.5">
                        Posisi
                    </span>

                    <button type="button"
                            @click="open = !open"
                            class="inline-flex items-center text-[11px] text-slate-500">
                        <span x-text="open ? 'Tutup riwayat' : 'Lihat riwayat'"></span>
                        <svg class="w-3 h-3 ml-1 transition-transform"
                            :class="open ? 'rotate-180' : ''"
                            viewBox="0 0 16 16" fill="none">
                            <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
                <span class="inline-flex items-center justify-center rounded-full bg-slate-900 text-white text-xs font-semibold w-7 h-7"
                    data-bid-rank-label>
                    {{ $bid->rank ?? '—' }}
                </span>
            </div>
        </div>
    </div>

    {{-- ===== DESKTOP TABLE ROW ===== --}}
    <div class="hidden md:grid md:grid-cols-12 gap-3 items-center px-5 py-3">
        {{-- Lot / Lelang (5 kolom) --}}
        <div class="col-span-5 flex items-start gap-3">
            <div class="w-14 h-14 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0 ring-1 ring-slate-200/70">
                <img src="{{ $img }}" alt="{{ $lot->title ?? 'Lot' }}" class="w-full h-full object-cover">
            </div>
            <div class="space-y-0.5 min-w-0">
                <a href="{{ route('lots.show', ['lot' => $lot, 'from' => 'my-auctions']) }}"
                class="text-sm font-semibold text-blue-600 hover:underline underline-offset-2 truncate">
                    {{ $lot->title ?? (($product->brand ?? '-') . ' ' . ($product->model ?? '')) }}
                </a>
                <p class="text-[12px] font-semibold text-slate-500">
                    Lot #{{ $lot->id ?? '—' }}
                </p>
                @if($startLabel || $endLabel)
                    <p class="text-[12px] font-medium text-slate-500">
                        @if($startLabel)
                            Mulai: {{ $startLabel }}
                        @endif
                        @if($endLabel)
                            <span class="hidden sm:inline"> • </span>
                            <br class="sm:hidden">
                            Berakhir: {{ $endLabel }}
                        @endif
                    </p>
                @endif
            </div>
        </div>

        {{-- Bid Anda (2 kolom) --}}
        <div class="col-span-2 text-sm text-center">
            <span class="inline-flex items-center rounded-full bg-slate-900/5 px-3 py-1 text-xs font-semibold text-slate-900">
                {{ $bid->amount_formatted ?? 'Rp 0' }}
            </span>
        </div>

        {{-- Waktu Bid (2 kolom) --}}
        <div class="col-span-2 text-sm text-center">
            <span class="text-slate-700">
                {{ $bid->created_at?->format('d M Y, H:i') ?? '-' }}
            </span>
        </div>

        {{-- Status (2 kolom) --}}
        <div class="col-span-2 text-sm text-center">
            <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 {{ $statusClasses }}"
                data-bid-status-label>
                {{ ucfirst(strtolower($status)) }}
            </span>

            @if($status === 'WON' && $payment)
                <a href="{{ route('transactions.show', $payment) }}"
                class="mt-2 block text-[12px] text-blue-600 hover:underline">
                    Lihat Invoice
                </a>
            @endif
        </div>

        {{-- Posisi + toggle accordion (1 kolom) --}}
        <div class="col-span-1 text-sm text-center flex items-center justify-center gap-2">
            <span class="inline-flex items-center justify-center rounded-full bg-slate-900 text-white text-xs font-semibold w-7 h-7"
                data-bid-rank-label>
                {{ $bid->rank ?? '—' }}
            </span>

            <button type="button"
                    @click="open = !open"
                    class="inline-flex items-center justify-center rounded-full border border-slate-300 w-7 h-7 text-slate-500 hover:bg-slate-100">
                <svg class="w-3 h-3 transition-transform"
                    :class="open ? 'rotate-180' : ''"
                    viewBox="0 0 16 16" fill="none">
                    <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ===== PANEL ACCORDION: riwayat bid lainnya di lot ini ===== --}}
    <div x-show="open" x-cloak
        class="px-4 md:px-5 pb-4 bg-slate-50 border-t border-slate-100">
        <p class="text-[12px] font-semibold text-slate-600 mt-3 mb-3">
            Riwayat bid Anda di lot ini:
        </p>

        @if($previousBids->isNotEmpty())
            <div class="border border-slate-200 rounded-xl bg-white overflow-hidden">
                <div class="divide-y divide-slate-100">
                    @foreach($previousBids as $old)
                        <div class="grid grid-cols-[auto,1fr,auto] items-center gap-3 px-3 py-2 text-[12px] sm:text-xs">
                            {{-- Tanggal di kiri --}}
                            <span class="text-[13px] text-slate-500 whitespace-nowrap">
                                {{ $old->created_at?->format('d M Y, H:i') ?? '-' }}
                            </span>

                            {{-- Nominal di tengah --}}
                            <div class="text-end sm:text-center ">
                                <span class="text-[12px] font-semibold text-slate-900">
                                    Rp {{ number_format($old->amount, 0, ',', '.') }}
                                </span>
                            </div>

                            {{-- Badge di kanan --}}
                            <span class="inline-flex hidden sm:inline items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-medium text-slate-500 whitespace-nowrap">
                                Bid sebelumnya
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-2 text-[11px] sm:text-xs text-slate-500">
                Belum ada riwayat bid lain untuk lot ini.
                <br class="hidden sm:inline">
                Bid di atas adalah satu-satunya penawaran Anda.
            </div>
        @endif
    </div>
</div>
