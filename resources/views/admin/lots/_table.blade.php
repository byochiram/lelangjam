{{-- admin/lots/_table.blade.php --}}
@php
    $hasStatus       = $showStatusColumn ?? false;
    $hasActions      = ($showActions ?? true) === true;
    $showCancelledAt = $showCancelledAt ?? false;
@endphp

<div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
    <table class="min-w-full text-sm md:text-base">
        <thead class="bg-slate-50 text-xs uppercase tracking-[0.16em] text-slate-500">
            <tr>
                <th class="px-4 sm:px-6 py-3 text-left">ID</th>
                <th class="px-4 sm:px-6 py-3 text-left">Produk</th>
                <th class="px-4 sm:px-6 py-3 text-center">Harga Awal</th>
                <th class="px-4 sm:px-6 py-3 text-center">Step</th>
                <th class="px-4 sm:px-6 py-3 text-center">Jadwal</th>
                @if($showCancelledAt)
                    <th class="px-4 sm:px-6 py-3 text-center">Dibatalkan</th>
                @endif
                @if($hasStatus)
                    <th class="px-4 sm:px-6 py-3 text-center">Status</th>
                @endif
                <th class="px-4 sm:px-6 py-3 text-center">Dibuat</th>
                @if($hasActions)
                    <th class="px-4 sm:px-6 py-3 text-center">Aksi</th>
                @endif
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
            @forelse($lots as $lot)
                @php
                    $product = $lot->product;
                    $thumb   = optional($product?->images->first())->filename;
                    $st      = $lot->runtime_status;
                @endphp

                <tr class="bg-white align-top hover:bg-slate-50/70">
                    {{-- ID --}}
                    <td class="px-4 sm:px-6 py-3 align-middle whitespace-nowrap">
                        <div class="text-center text-sm text-slate-900">
                            {{ $lot->id }}
                        </div>
                    </td>

                    {{-- PRODUK --}}
                    <td class="px-4 sm:px-6 py-3 align-top">
                        <div class="flex items-start gap-3">
                            <div class="h-12 w-12 rounded-lg bg-slate-100 overflow-hidden flex-shrink-0">
                                @if($thumb)
                                    <img src="{{ asset('storage/products/'.$thumb) }}"
                                         class="h-full w-full object-cover" alt="">
                                @endif
                            </div>

                            <div class="space-y-0.5 min-w-0">
                                <div class="text-[11px] uppercase tracking-[0.12em] text-slate-400">
                                    @if($product)
                                        ID Produk #{{ $product->id }}
                                    @else
                                        Produk tidak tersedia
                                    @endif
                                </div>

                                @if($product)
                                    <div class="font-medium leading-snug">
                                        <a href="{{ route('lots.detail', $lot) }}"
                                           class="block text-sm text-blue-600 hover:underline underline-offset-2 max-w-[11rem] truncate sm:max-w-none sm:whitespace-normal">
                                            {{ $product->brand }}
                                            @if($product->model)
                                                {{ $product->model }}
                                            @endif
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- HARGA AWAL --}}
                    <td class="px-4 sm:px-6 py-3 text-sm text-right align-middle whitespace-nowrap">
                        Rp {{ number_format($lot->start_price, 0, ',', '.') }}
                    </td>

                    {{-- STEP --}}
                    <td class="px-4 sm:px-6 py-3 text-sm text-right align-middle whitespace-nowrap">
                        Rp {{ number_format($lot->increment, 0, ',', '.') }}
                    </td>

                    {{-- JADWAL --}}
                    <td class="px-4 sm:px-6 py-3 text-center align-middle text-sm text-slate-700 whitespace-nowrap">
                        <div>{{ $lot->start_at->format('d M Y, H:i') }}</div>
                        <div class="text-sm text-slate-700">s.d. {{ $lot->end_at->format('d M Y, H:i') }}</div>
                    </td>

                    {{-- DIBATALKAN (khusus tab dibatalkan) --}}
                    @if($showCancelledAt)
                        <td class="px-4 sm:px-6 py-3 text-center align-middle whitespace-nowrap text-sm text-slate-700">
                            {{ optional($lot->cancelled_at)->format('d M Y, H:i') ?? '-' }}
                        </td>
                    @endif

                    {{-- STATUS (tab ended) --}}
                    @if($hasStatus)
                        <td class="px-4 sm:px-6 py-3 text-center align-middle">
                            <span @class([
                                'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold',
                                'bg-amber-100 text-amber-700'     => $st === 'SCHEDULED',
                                'bg-emerald-100 text-emerald-700' => $st === 'ACTIVE',
                                'bg-slate-900 text-white'         => $st === 'ENDED',
                                'bg-red-100 text-red-700'         => $st === 'CANCELLED',
                                'bg-blue-100 text-blue-700'       => $st === 'PENDING',
                                'bg-green-900 text-green-100'     => $st === 'AWARDED',
                                'bg-slate-200 text-slate-700'     => $st === 'UNSOLD',
                            ])>
                                {{ $st }}
                            </span>
                        </td>
                    @endif

                    {{-- DIBUAT --}}
                    <td class="px-4 sm:px-6 py-3 text-center align-middle whitespace-nowrap text-sm text-slate-700">
                        {{ $lot->created_at->format('d M Y, H:i') }}
                    </td>

                    {{-- AKSI --}}
                    @if($hasActions)
                        <td class="px-4 sm:px-6 py-3 text-center align-middle">
                            @if($st === 'CANCELLED')
                                <span class="text-slate-400">-</span>
                            @else
                                <div class="inline-flex items-center justify-end gap-1">
                                    @if(in_array($st, ['SCHEDULED','ACTIVE'], true))
                                        <button type="button"
                                                class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-slate-300 hover:bg-slate-100"
                                                title="Edit"
                                                @click="openEdit(@js([
                                                    'id'             => $lot->id,
                                                    'product_id'     => $lot->product_id,
                                                    'start_price'    => $lot->start_price,
                                                    'increment'      => $lot->increment,
                                                    'start_at'       => $lot->start_at->format('Y-m-d\TH:i'),
                                                    'end_at'         => $lot->end_at->format('Y-m-d\TH:i'),
                                                    'runtime_status' => $st,
                                                ]))">
                                            <svg viewBox="0 0 20 20" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M4 13.5V16h2.5L14 8.5l-2.5-2.5L4 13.5z"/>
                                                <path d="M11.5 6l2-2 2 2-2 2z"/>
                                            </svg>
                                        </button>
                                    @endif

                                    @if($st === 'SCHEDULED')
                                        <form method="POST" action="{{ route('lots.destroy',$lot) }}"
                                              @submit.prevent="confirmAndSubmit($el,'Hapus Lot','Hapus Lot #{{ $lot->id }}?','Hapus')">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="tab" :value="tab">
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-red-200 text-red-600 hover:bg-red-50"
                                                    title="Hapus">
                                                <svg viewBox="0 0 20 20" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <path d="M6 7h8l-.5 9h-7z"/>
                                                    <path d="M8 7V5h4v2M5 5h10M9 9v5M11 9v5"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    @if($st === 'ACTIVE')
                                        <button type="button"
                                                class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-red-200 text-red-600 hover:bg-red-50"
                                                title="Cancel"
                                                @click="openCancel('{{ route('lots.cancel',$lot) }}')">
                                            <svg viewBox="0 0 20 20" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M6 6l8 8M14 6l-8 8"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                @php
                    $colspan = 6
                        + ($showCancelledAt ? 1 : 0)
                        + ($hasStatus ? 1 : 0)
                        + ($hasActions ? 1 : 0);
                @endphp
                <tr>
                    <td colspan="{{ $colspan }}" class="px-6 py-6 text-center text-sm text-slate-500">
                        Tidak ada data.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($lots instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-3 text-sm text-slate-600">
        {{ $lots->onEachSide(1)->links() }}
    </div>
@endif
