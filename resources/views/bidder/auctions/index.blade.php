{{-- resources/views/bidder/auctions/index.blade.php --}}
<x-guest-layout>
    <div class="max-w-screen-xl mx-auto px-4 space-y-8"
        x-data="{ 
                tab: '{{ request('tab', 'bids') }}',                 // ?tab=bids / watchlist
                bidsTab: '{{ request('bids_group', 'running') }}'    // ?bids_group=running / ended
        }">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mt-2">
            <div class="space-y-1">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">
                    Dashboard Bidder
                </p>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Lelang Saya
                </h1>
                <p class="text-sm text-slate-500 max-w-xl">
                    Pantau lot yang Anda simpan di watchlist dan lihat semua riwayat bid Anda di satu tempat.
                </p>
            </div>

            {{-- Ringkas statistik kecil --}}
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-xl bg-slate-900 text-white px-4 py-3 shadow-sm">
                    <div class="text-[11px] uppercase tracking-[0.16em] text-slate-300">Riwayat Bid</div>
                    <div class="mt-1 text-xl font-semibold">
                        {{ $totalBids ?? 0 }}
                    </div>
                </div>
                <div class="rounded-xl bg-slate-50 px-4 py-3 border border-slate-200">
                    <div class="text-[11px] uppercase tracking-[0.16em] text-slate-500">Watchlist</div>
                    <div class="mt-1 text-xl font-semibold text-slate-900">
                        {{ $watchlistTotal ?? 0 }}
                    </div>
                </div>
            </div>
        </div>

        {{-- TABS --}}
        <div class="inline-flex rounded-full bg-slate-100 p-1 text-sm mt-2">
            <button
                type="button"
                @click="tab = 'bids'"
                class="px-4 py-2 rounded-full font-medium transition-all duration-150"
                :class="tab === 'bids'
                    ? 'bg-white shadow-sm text-slate-900'
                    : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'">
                Riwayat Bid
            </button>
            <button
                type="button"
                @click="tab = 'watchlist'"
                class="px-4 py-2 rounded-full font-medium transition-all duration-150"
                :class="tab === 'watchlist'
                    ? 'bg-white shadow-sm text-slate-900'
                    : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'">
                Watchlist
            </button>
        </div>

        {{-- ================= WATCHLIST TAB ================= --}}
        <section x-show="tab === 'watchlist'" x-cloak class="mt-4 space-y-3" data-watchlist-page>
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">
                        Watchlist
                    </h2>
                    <p class="text-xs text-slate-500">
                        Lot yang Anda tandai untuk dipantau sebelum dan selama lelang.
                    </p>
                </div>
            </div>

            <div class="mt-4">
                @if(($watchlistLots ?? collect())->isNotEmpty())
                    {{-- GRID CARD --}}
                    <div id="watchlistGrid"
                        class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
                        @include('public.partials.lot_cards', [
                            'lots'            => $watchlistLots,
                            'watchlistLotIds' => $watchlistLotIds ?? [],
                        ])
                    </div>

                    {{-- LOAD MORE --}}
                    <div id="watchlistLoadMoreWrapper">
                        @if($watchlistLots instanceof \Illuminate\Pagination\LengthAwarePaginator && $watchlistLots->hasMorePages())
                            <div class="mt-6 flex justify-center">
                                <button id="watchlistLoadMoreBtn"
                                        data-next-page="{{ $watchlistLots->currentPage() + 1 }}"
                                        class="inline-flex items-center rounded-lg bg-slate-900 text-white px-5 py-2.5 text-sm font-semibold hover:bg-slate-800">
                                    Tampilkan Lainnya
                                </button>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-white border border-dashed border-slate-300 rounded-2xl px-6 py-10 text-center">
                        <div
                            class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M3.172 5.172A4 4 0 0110 3.515a4 4 0 016.828 1.657A4.5 4.5 0 0117.5 13H12a2 2 0 00-1.995 1.85L10 15v1a1 1 0 11-2 0v-1a4 4 0 013.8-3.995L12 11h5.5a2.5 2.5 0 000-5 2.5 2.5 0 00-2.45 2H14a4 4 0 00-7.874-.829A3 3 0 004 10H3.5a2.5 2.5 0 01-.328-4.828z" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-800">
                            Belum ada lot di watchlist Anda.
                        </p>
                        <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">
                            Tambahkan lot ke watchlist dari halaman detail lelang untuk memantau pergerakan harga dan
                            penutupan lelang dengan lebih mudah.
                        </p>
                    </div>
                @endif
            </div>
        </section>

        {{-- ================= RIWAYAT BID TAB ================= --}}
        <section x-show="tab === 'bids'" x-cloak class="mt-4 space-y-3" x-bind:data-bids-tab-active="tab === 'bids' ? '1' : '0'">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">
                        Riwayat Bid
                    </h2>
                    <p class="text-xs text-slate-500">
                        Semua penawaran yang pernah Anda lakukan di berbagai lot.
                    </p>
                </div>
            </div>

            {{-- SUB TAB: SEDANG BERJALAN / SUDAH BERAKHIR --}}
            <div class="mt-3 flex justify-center">
                <div class="inline-flex rounded-full bg-slate-100 p-1 text-xs sm:text-sm">
                    <button
                        type="button"
                        @click="bidsTab = 'running'"
                        class="px-3 sm:px-4 py-1.5 rounded-full font-medium transition-all duration-150"
                        :class="bidsTab === 'running'
                            ? 'bg-white shadow-sm text-slate-900'
                            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'">
                        Sedang berjalan
                    </button>
                    <button
                        type="button"
                        @click="bidsTab = 'ended'"
                        class="px-3 sm:px-4 py-1.5 rounded-full font-medium transition-all duration-150"
                        :class="bidsTab === 'ended'
                            ? 'bg-white shadow-sm text-slate-900'
                            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'">
                        Sudah berakhir
                    </button>
                </div>
            </div>

            {{-- KETERANGAN STATUS (DINAMIS PER SUB TAB) --}}
            <div class="mt-3 bg-slate-50 border border-slate-100 rounded-2xl px-4 sm:px-5 py-3 text-[11px] sm:text-xs text-slate-600">
                <p class="font-semibold text-slate-700 mb-2">
                    Keterangan status:
                </p>

                {{-- Untuk tab "Sedang berjalan" --}}
                <div x-show="bidsTab === 'running'" x-cloak>
                    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-1.5">
                        <div>
                            <dt class="font-semibold text-emerald-700">LEADING</dt>
                            <dd>Bid Anda saat ini <span class="font-semibold">yang tertinggi</span> untuk lot tersebut.</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-amber-700">OUTBID</dt>
                            <dd>Sudah ada peserta lain yang menawar <span class="font-semibold">lebih tinggi</span>.</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-700">CANCELLED</dt>
                            <dd>Lelang dibatalkan oleh sistem/admin, sehingga bid tidak lagi berlaku.</dd>
                        </div>
                    </dl>
                </div>

                {{-- Untuk tab "Sudah berakhir" --}}
                <div x-show="bidsTab === 'ended'" x-cloak>
                    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-1.5">
                        <div>
                            <dt class="font-semibold text-emerald-800">WON</dt>
                            <dd>Lelang sudah berakhir dan Anda <span class="font-semibold">pemenang</span> dengan bid tertinggi.</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-red-700">LOST</dt>
                            <dd>Lelang sudah berakhir dan bid Anda <span class="font-semibold">bukan yang tertinggi</span>.</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-700">CANCELLED</dt>
                            <dd>Lelang dibatalkan oleh sistem/admin, sehingga bid tidak lagi berlaku.</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-4 bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
                {{-- header desktop --}}
                <div class="hidden md:grid grid-cols-12 gap-3 px-5 py-3 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500 bg-slate-50 border-b border-slate-100">
                    <div class="col-span-5">Lot / Lelang</div>
                    <div class="col-span-2 text-center">Bid Anda</div>
                    <div class="col-span-2 text-center">Waktu Bid</div>
                    <div class="col-span-2 text-center">Status</div>
                    <div class="col-span-1 text-center">Posisi</div>
                </div>

                {{-- ===== SEDANG BERJALAN ===== --}}
                <div x-show="bidsTab === 'running'" x-cloak>
                    @forelse(($bidHistoryRunning ?? collect()) as $row)
                        @include('bidder.auctions._bid-row', ['row' => $row])
                    @empty
                        <div class="px-4 sm:px-6 py-10 text-center text-sm text-slate-500">
                            Belum ada lelang yang sedang berjalan yang Anda ikuti.
                            <br>
                            <span class="text-xs text-slate-400">
                                Ikuti lelang aktif dan ajukan bid untuk melihatnya di sini.
                            </span>
                        </div>
                    @endforelse

                    {{-- PAGINATION RUNNING --}}
                    @if(($bidHistoryRunning ?? null) instanceof \Illuminate\Pagination\LengthAwarePaginator && $bidHistoryRunning->hasPages())
                        <div class="px-2 sm:px-0 mt-4 ml-4 mr-4 mb-4">
                            {{ $bidHistoryRunning->onEachSide(1)->appends([
                                'tab'               => 'bids',
                                'bids_group'        => 'running',
                                'bids_ended_page'   => request('bids_ended_page'),
                            ])->links() }}
                        </div>
                    @endif
                </div>

                {{-- ===== SUDAH BERAKHIR ===== --}}
                <div x-show="bidsTab === 'ended'" x-cloak>
                    @forelse(($bidHistoryEnded ?? collect()) as $row)
                        @include('bidder.auctions._bid-row', ['row' => $row])
                    @empty
                        <div class="px-4 sm:px-6 py-10 text-center text-sm text-slate-500">
                            Belum ada lelang yang sudah berakhir yang Anda ikuti.
                            <br>
                            <span class="text-xs text-slate-400">
                                Setelah lelang selesai, hasilnya akan muncul di tab ini.
                            </span>
                        </div>
                    @endforelse

                    {{-- PAGINATION ENDED --}}
                    @if(($bidHistoryEnded ?? null) instanceof \Illuminate\Pagination\LengthAwarePaginator && $bidHistoryEnded->hasPages())
                        <div class="px-2 sm:px-0 mt-4 ml-4 mr-4 mb-4">
                            {{ $bidHistoryEnded->onEachSide(1)->appends([
                                'tab'                => 'bids',
                                'bids_group'         => 'ended',
                                'bids_running_page'  => request('bids_running_page'),
                            ])->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

            document.body.addEventListener('click', async (e) => {
                const btn = e.target.closest('[data-watchlist-button]');
                if (!btn) return;

                e.preventDefault();

                const url  = btn.getAttribute('data-url');
                const isWatchlisted = btn.getAttribute('data-watchlisted') === '1';

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });

                    const data = await res.json();
                    if (data.status !== 'ok') return;

                    const nowWatchlisted = data.action === 'added';
                    btn.setAttribute('data-watchlisted', nowWatchlisted ? '1' : '0');

                    // toggle icon & style
                    const outline = btn.querySelector('.icon-heart-outline');
                    const fill    = btn.querySelector('.icon-heart-fill');

                    if (nowWatchlisted) {
                        outline?.classList.add('hidden');
                        fill?.classList.remove('hidden');
                        btn.classList.remove('border-slate-200', 'bg-white', 'text-slate-500', 'hover:bg-slate-50');
                        btn.classList.add('border-slate-900', 'bg-slate-900', 'text-white');
                        btn.title = 'Hapus dari Watchlist';
                    } else {
                        outline?.classList.remove('hidden');
                        fill?.classList.add('hidden');
                        btn.classList.remove('border-slate-900', 'bg-slate-900', 'text-white');
                        btn.classList.add('border-slate-200', 'bg-white', 'text-slate-500', 'hover:bg-slate-50');
                        btn.title = 'Tambahkan ke Watchlist';
                    }

                    // kalau sedang di halaman watchlist, dan action = removed → hapus kartu
                    const watchlistPage = btn.closest('[data-watchlist-page]');
                    if (watchlistPage && data.action === 'removed') {
                        const card = btn.closest('[data-lot-card]');
                        if (card) card.remove();
                    }

                } catch (err) {
                    console.error('Watchlist toggle failed', err);
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const grid    = document.getElementById('watchlistGrid');
            const wrapper = document.getElementById('watchlistLoadMoreWrapper');
            const baseUrl = '{{ route('my.auctions') }}';

            if (!grid || !wrapper) return;

            function attachWatchlistLoadMore() {
                const btn = document.getElementById('watchlistLoadMoreBtn');
                if (!btn) return;

                btn.onclick = async () => {
                    const nextPage = btn.dataset.nextPage;
                    if (!nextPage) return;

                    btn.disabled    = true;
                    btn.textContent = 'Memuat...';

                    try {
                        const params = new URLSearchParams();
                        params.set('watchlist', '1'); // penanda request AJAX watchlist
                        params.set('page', nextPage);

                        const res = await fetch(`${baseUrl}?${params.toString()}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                        });

                        if (!res.ok) throw new Error('Gagal memuat data watchlist');

                        const data = await res.json();

                        if (data.html) {
                            grid.insertAdjacentHTML('beforeend', data.html);
                        }

                        if (data.next_page) {
                            btn.dataset.nextPage = data.next_page;
                            btn.disabled = false;
                            btn.textContent = 'Tampilkan Lainnya';
                        } else {
                            wrapper.innerHTML = '';
                        }
                    } catch (err) {
                        console.error(err);
                        btn.disabled    = false;
                        btn.textContent = 'Coba lagi';
                    }
                };
            }

            attachWatchlistLoadMore();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const pollUrl = '{{ route('bids.poll') }}';

            const STATUS_CLASSES = {
                LEADING:   'bg-emerald-50 text-emerald-700 ring-emerald-100',
                OUTBID:    'bg-amber-50 text-amber-700 ring-amber-100',
                LOST:      'bg-red-50 text-red-700 ring-red-100',
                WON:       'bg-emerald-100 text-emerald-800 ring-emerald-200',
                CANCELLED: 'bg-slate-200 text-slate-700 ring-slate-300',
                PENDING:   'bg-slate-100 text-slate-700 ring-slate-200',
            };

            const allStatusClasses = Object.values(STATUS_CLASSES)
                .flatMap(c => c.split(' '))
                .filter(Boolean);

            // inisialisasi dataset awal dari DOM
            document.querySelectorAll('[data-bid-row]').forEach(row => {
                const statusEl = row.querySelector('[data-bid-status-label]');
                const rankEl   = row.querySelector('[data-bid-rank-label]');
                if (statusEl) row.dataset.currentStatus = statusEl.textContent.trim().toUpperCase();
                if (rankEl)   row.dataset.currentRank   = rankEl.textContent.trim();
            });

            async function poll() {
                try {
                    // kalau tab "Riwayat Bid" tidak aktif, skip
                    const bidsSection = document.querySelector('[data-bids-tab-active]');
                    if (bidsSection && bidsSection.getAttribute('data-bids-tab-active') !== '1') {
                        return;
                    }

                    const res = await fetch(pollUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!res.ok) return;

                    const data = await res.json();
                    if (!data || !Array.isArray(data.updates)) return;

                    data.updates.forEach(update => {
                        const row = document.querySelector(
                            `[data-bid-row][data-bid-id="${update.bid_id}"]`
                        );
                        if (!row) return;

                        const newStatus = (update.status || '').toUpperCase();
                        const newRank   = update.rank ?? '—';

                        const prevStatus = row.dataset.currentStatus || '';
                        row.dataset.currentStatus = newStatus;
                        row.dataset.currentRank   = newRank;

                        // update semua label status di baris ini (mobile + desktop)
                        row.querySelectorAll('[data-bid-status-label]').forEach(label => {
                            allStatusClasses.forEach(cls => label.classList.remove(cls));
                            const extra = STATUS_CLASSES[newStatus] || STATUS_CLASSES.PENDING;
                            extra.split(' ').forEach(cls => label.classList.add(cls));
                            label.textContent = newStatus.charAt(0) + newStatus.slice(1).toLowerCase();
                        });

                        // update rank label
                        row.querySelectorAll('[data-bid-rank-label]').forEach(label => {
                            label.textContent = newRank;
                        });

                        // kalau dari LEADING jadi OUTBID → kasih toast
                        if (prevStatus === 'LEADING' && newStatus === 'OUTBID') {
                            if (window.Alpine && Alpine.store && Alpine.store('toast')) {
                                const lotLabel = row.getAttribute('data-lot-label') || 'salah satu lot Anda';
                                Alpine.store('toast').push({
                                    type: 'warn',
                                    text: `Bid Anda sudah ter-outbid di ${lotLabel}.`,
                                    timeout: 6000,
                                });
                            }
                        }
                    });
                } catch (e) {
                    console.error('Bid status polling error', e);
                }
            }

            poll();
            setInterval(poll, 3000);
        }); 
    </script>

</x-guest-layout>
