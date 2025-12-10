{{-- resources/views/admin/transactions/_table.blade.php --}}
@php
    $currentTab    = $tab ?? 'pending';
    $showShipping  = $currentTab === 'paid'; // cuma di Awarded
@endphp

<div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
    <table class="min-w-full text-sm md:text-base">
        <thead class="bg-slate-50 text-xs uppercase tracking-[0.16em] text-slate-500">
            <tr>
                <th class="px-6 py-3 text-left">Invoice</th>
                <th class="px-6 py-3 text-left">Lelang</th>
                <th class="px-6 py-3 text-left">Pembeli</th>
                <th class="px-6 py-3 text-right">Total</th>
                <th class="px-6 py-3 text-center">Tanggal</th>
                @if($showShipping)
                    <th class="px-6 py-3 text-center">Pengiriman</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($payments as $payment)
                @php
                    $status = $payment->status;
                @endphp

                <tr class="hover:bg-slate-50/70">
                    {{-- INVOICE --}}
                    <td class="px-6 py-3 align-top">
                        <div class="font-medium text-slate-900">
                            <a href="{{ route('payments.show', [
                                    'payment' => $payment,
                                    'tab'     => $currentTab,   
                                ]) }}"
                                class="text-sm text-blue-600 hover:underline">
                                {{ $payment->invoice_no }}
                            </a>
                        </div>
                        <div class="mt-0.5 text-xs text-slate-500">
                            ID #{{ $payment->id }}
                        </div>
                    </td>

                    {{-- LOT --}}
                    <td class="px-6 py-3 align-top">
                        @if($payment->lot)
                            <div class="font-medium text-sm text-slate-900 break-words"> 
                                @if($payment->lot->product)
                                    {{ $payment->lot->product->brand }} {{ $payment->lot->product->model }}
                                @else
                                    {{ $payment->lot->title }}
                                @endif
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                ID Lot #{{ $payment->lot->id }}
                            </div>
                        @else
                            <span class="text-xs text-slate-400 italic">Lot tidak tersedia</span>
                        @endif
                    </td>

                    {{-- BUYER --}}
                    <td class="px-6 py-3 align-top">
                        @if($payment->user)
                            <div class="font-medium text-sm text-slate-900"> 
                                {{ $payment->user->name ?: '—' }}
                            </div>
                            <div class="text-xs text-slate-500">
                                {{ $payment->user->username ? '@'.$payment->user->username : '—' }}
                            </div>
                        @else
                            <span class="text-xs text-slate-400 italic">Pengguna tidak tersedia</span>
                        @endif
                    </td>

                    {{-- AMOUNT --}}
                    <td class="px-6 py-3 align-top text-sm text-right whitespace-nowrap">
                        <div class="inline-flex flex-col items-end">

                            {{-- angka total --}}
                            <div class="flex items-center gap-1">
                                <span class="font-medium">
                                    Rp {{ number_format($payment->grand_total, 0, ',', '.') }}
                                </span>

                                {{-- ICON INFO + TOOLTIP --}}
                                <div class="relative inline-block group">
                                    <button type="button"
                                            class="inline-flex items-center justify-center w-4 h-4
                                                rounded-full border border-slate-400 text-[10px]
                                                text-slate-500 leading-none">
                                        i
                                    </button>

                                    {{-- TOOLTIP KE KANAN --}}
                                    <div class="absolute left-full top-1/2 -translate-y-1/2 ml-2
                                                hidden group-hover:block z-20
                                                rounded-md bg-slate-800 text-white text-[12px]
                                                px-3 py-2 shadow-lg whitespace-nowrap">
                                        <div>Lelang: Rp {{ number_format($payment->amount_due,0,',','.') }}</div>
                                        <div>Layanan: Rp 500</div>
                                        <div>Ongkir: Rp {{ number_format($payment->shipping_fee ?? 0,0,',','.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- DATES --}}
                    <td class="px-6 py-3 align-top text-center text-xs text-slate-700 whitespace-nowrap">
                        <div>Terbit: {{ $payment->issued_at?->format('d M Y, H:i') ?? '—' }}</div>
                        @if($payment->paid_at)
                            <div>Dibayar: {{ $payment->paid_at->format('d M Y, H:i') }}</div>
                        @else
                            <div>Jatuh tempo: {{ $payment->expires_at?->format('d M Y, H:i') ?? '—' }}</div>
                        @endif
                    </td>

                    {{-- SHIPPING --}}
                    @if($showShipping)
                        <td class="px-6 py-3 align-top text-center text-xs">
                            @if($status === 'PAID')
                                @if($payment->shipping_tracking_no)
                                    <div class="inline-flex flex-col items-center md:items-center gap-1">

                                        {{-- Kurir --}}
                                        <div class="text-[11px] uppercase tracking-[0.12em] text-slate-500">
                                            {{ $payment->shipping_courier ?? 'KURIR ?' }}
                                        </div>

                                        {{-- No Resi --}}
                                        <div class="font-mono text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-800">
                                            {{ $payment->shipping_tracking_no }}
                                        </div>

                                        {{-- Tombol Ubah --}}
                                        <button type="button"
                                                class="inline-flex items-center px-2 py-1 rounded-full border border-slate-200 text-[11px] text-slate-700 hover:bg-slate-50"
                                                @click="openShip(@js([
                                                    'id'       => $payment->id,
                                                    'courier'  => $payment->shipping_courier,
                                                    'tracking' => $payment->shipping_tracking_no,
                                                ]))">
                                            Ubah
                                        </button>
                                    </div>
                                @else
                                    <button type="button"
                                            class="inline-flex items-center px-3 py-1.5 rounded-full bg-slate-900 text-xs text-white hover:bg-slate-700"
                                            @click="openShip(@js([
                                                'id'       => $payment->id,
                                                'courier'  => $payment->shipping_courier,
                                                'tracking' => $payment->shipping_tracking_no,
                                            ]))">
                                        + No. Resi
                                    </button>
                                @endif
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                @php
                    $colspan = $showShipping ? 6 : 5;
                @endphp

                <tr>
                    <td colspan="{{ $colspan }}" class="px-6 py-6 text-center text-sm text-slate-500">
                        Belum ada transaksi yang sesuai filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


