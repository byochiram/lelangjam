{{-- admin/users/show.blade.php --}}
<x-app-layout title="Detail Pengguna">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Pengguna #{{ $user->id }}
            </h2>

            {{-- Tombol kembali (atas) --}}
            <a href="{{ route('users.index') }}"
               class="px-3 py-1.5 rounded-full border border-slate-200 text-slate-700 text-xs hover:bg-slate-50">
                ← Kembali
            </a>
        </div>
    </x-slot>

    @php
        $status   = $user->status ?? 'ACTIVE';
        $verified = ! is_null($user->email_verified_at);
        $me       = auth()->user();
        $suspendedUntil = $user->suspended_until;
        $suspendReason  = $user->suspend_reason;

        $isBidder = $user->isBidder();

        // inisial sederhana dari nama / email
        $baseText = trim($user->name ?: $user->email);

        if ($baseText === '') {
            $initials = 'NA';
        } else {
            // pecah berdasarkan spasi
            $parts = preg_split('/\s+/', $baseText);

            if (count($parts) === 1) {
                // satu kata -> ambil 2 huruf pertama
                $initials = mb_substr($parts[0], 0, 2);
            } else {
                // lebih dari satu kata -> huruf pertama kata pertama + kata terakhir
                $first = mb_substr($parts[0], 0, 1);
                $last  = mb_substr($parts[count($parts) - 1], 0, 1);
                $initials = $first.$last;
            }

            $initials = mb_strtoupper($initials);
        }

        $canResetPasswordAdmin =
            $me && $me->isSuperAdmin() &&
            $user->role === 'ADMIN' &&
            $me->id !== $user->id;

        $canResendVerification = ! $verified;

        // profil bidder (kalau ada)
        $profile = $user->bidderProfile;

        // statistik sederhana dari BidderProfile (fallback ke 0 kalau null)
        $bidCount   = $profile->bid_count   ?? 0;
        $winCount   = $profile->win_count   ?? 0;
        $totalSpent = $profile->total_spent ?? 0;

        // label kota & provinsi
        $provinceLabel = $profile->province ?? null;
        $cityLabel     = $profile->city     ?? null;

        // lot yang pernah diikuti (distinct lot_id dari bids)
        $participatedLots = $profile
            ? $profile->bids()->distinct('lot_id')->count()
            : 0;
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">

                @if($isBidder)
                    {{-- TOP HERO --}}
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/80
                                flex flex-wrap items-start justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-full bg-slate-200 flex items-center justify-center text-slate-700 font-semibold text-lg">
                                    {{ $initials }}
                                </div>
                            </div>

                            <div class="space-y-1">
                                <div class="flex items-center flex-wrap gap-2">
                                    <h1 class="text-lg sm:text-xl font-semibold text-slate-900">
                                        {{ $user->name ?: '—' }}
                                    </h1>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-white border border-slate-200 text-[11px] font-medium text-slate-600">
                                        ID #{{ $user->id }}
                                    </span>
                                </div>
                                <div class="text-xs text-slate-400 mt-1">
                                    Akun Bidder Tempus Auctions
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- MAIN CONTENT --}}
                    <div class="p-6 space-y-8">

                        {{-- Informasi Akun + Keamanan --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                            <div class="space-y-3">
                                <h2 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Informasi Akun</h2>
                                <div class="rounded-xl border border-slate-100 bg-slate-50/40 p-4 space-y-2">
                                    <dl class="space-y-2">
                                        <div class="flex justify-between gap-4">
                                            <dt class="text-slate-500">Nama Lengkap</dt>
                                            <dd class="text-slate-800 text-right">{{ $user->name ?: '—' }}</dd>
                                        </div>
                                        <div class="flex justify-between gap-4">
                                            <dt class="text-slate-500">Username</dt>
                                            <dd class="text-slate-800 text-right">{{ '@'.$user->username ?: '—' }}</dd> 
                                        </div>
                                        <div class="flex justify-between gap-4">
                                            <dt class="text-slate-500">Email</dt>
                                            <dd class="text-slate-800 text-right break-all">{{ $user->email }}</dd>
                                        </div>
                                        <div class="flex justify-between gap-4">
                                            <dt class="text-slate-500">No. HP</dt>
                                            <dd class="text-slate-800 text-right break-all">{{ $profile->phone }}</dd>
                                        </div>
                                        <div class="flex justify-between gap-4">
                                            <dt class="text-slate-500">Role</dt>
                                            <dd class="text-slate-800 text-right">{{ $user->role ?? 'BIDDER' }}</dd>
                                        </div>
                                        <div class="flex justify-between gap-4">
                                            <dt class="text-slate-500">Dibuat</dt>
                                            <dd class="text-slate-800 text-right">
                                                {{ $user->created_at?->format('d M Y, H:i') ?? '—' }}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <h2 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                                    Keamanan &amp; Aktivitas
                                </h2>
                                <div class="rounded-xl border border-slate-100 bg-slate-50/40 p-4 space-y-2">
                                    <dl class="space-y-2">
                                        <div class="flex justify-between gap-4">
                                            <dt class="text-slate-500">Status Verifikasi Email</dt>
                                            <dd class="text-slate-800 text-right">
                                                @if($verified)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-medium border border-emerald-100">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Email terverifikasi
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[11px] font-medium border border-amber-100">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        Email belum verifikasi
                                                    </span>
                                                @endif
                                            </dd>
                                        </div>
                                        <div class="flex justify-between gap-4">
                                            <dt class="text-slate-500">Email Diverifikasi Pada</dt>
                                            <dd class="text-slate-800 text-right">
                                                {{ $user->email_verified_at?->format('d M Y, H:i') ?? '—' }}
                                            </dd>
                                        </div>
                                        <div class="flex justify-between gap-4">
                                            <dt class="text-slate-500">Terakhir Diperbarui</dt>
                                            <dd class="text-slate-800 text-right">
                                                {{ $user->updated_at?->format('d M Y, H:i') ?? '—' }}
                                            </dd>
                                        </div>
                                        <div class="flex justify-between gap-4">
                                            <dt class="text-slate-500">Status Akun</dt>
                                            <dd class="text-slate-800 text-right">
                                                @if($status !== 'SUSPENDED')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">    
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 "></span> Aktif
                                                    </span>
                                                @elseif($suspendedUntil)
                                                    Ditangguhkan otomatis sampai {{ $suspendedUntil->format('d M Y, H:i') }}
                                                @else
                                                    Ditangguhkan manual (hingga diaktifkan admin)
                                                @endif
                                                
                                            </dd>
                                        </div>

                                        <div class="flex justify-between gap-4">
                                            <dt class="text-slate-500">Alasan Penangguhan</dt>
                                            <dd class="text-slate-800 text-right">
                                                @if($status !== 'SUSPENDED')
                                                    —
                                                @else
                                                    {{ $suspendReason ?: 'Tidak ada keterangan' }}
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        {{-- Profil + Statistik + Riwayat (hanya kalau ada profile) --}}
                        @if($profile)
                            {{-- Ringkasan Aktivitas Lelang --}}
                            <div class="space-y-3">
                                <h2 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                                    Ringkasan Aktivitas Lelang
                                </h2>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 px-3 py-3">
                                        <div class="text-xs text-slate-500">Total Bid</div>
                                        <div class="mt-1 text-xl font-semibold text-slate-900">
                                            {{ number_format($bidCount, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 px-3 py-3">
                                        <div class="text-xs text-slate-500">Lot yang Pernah Diikuti</div>
                                        <div class="mt-1 text-xl font-semibold text-slate-900">
                                            {{ number_format($participatedLots, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 px-3 py-3">
                                        <div class="text-xs text-slate-500">Lot Dimenangkan</div>
                                        <div class="mt-1 text-xl font-semibold text-slate-900">
                                            {{ number_format($winCount, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 px-3 py-3">
                                        <div class="text-xs text-slate-500">Total Belanja</div>
                                        <div class="mt-1 text-xl font-semibold text-slate-900">
                                            @if($totalSpent > 0)
                                                Rp{{ number_format($totalSpent, 0, ',', '.') }}
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Riwayat Bid Terbaru --}}
                            <div class="space-y-3">
                                <h2 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                                    Riwayat Bid Terbaru
                                </h2>

                                @if(($bidLots ?? collect())->isEmpty())
                                    <div class="text-sm text-slate-500 italic">
                                        Belum ada aktivitas bid.
                                    </div>
                                @else
                                    <div class="rounded-xl border border-slate-200 divide-y">
                                        @foreach($bidLots as $row)
                                            @php
                                                /** @var \App\Models\Bid $latest */
                                                $latest  = $row['latest'];
                                                $lot     = $row['lot'];
                                                $older   = $row['previous_bids'];
                                            @endphp

                                            <div x-data="{ open:false }" class="">
                                                {{-- baris utama: lot + bid terakhir --}}
                                                <div class="p-3 flex items-center justify-between gap-3">
                                                    <div class="min-w-0">
                                                        <div class="flex items-center gap-2">
                                                            @if($lot)
                                                                <a href="{{ route('lots.detail', ['lot' => $lot, 'from' => 'user-'.$user->id]) }}"
                                                                class="font-medium text-[14px] text-blue-600 hover:underline truncate">
                                                                    @if($lot->product)
                                                                        {{ $lot->product->brand }} {{ $lot->product->model }}
                                                                    @else
                                                                        Lot #{{ $lot->id }}
                                                                    @endif
                                                                </a>
                                                                <span class="text-[11px] px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                                                    Lot #{{ $lot->id }}
                                                                </span>
                                                            @else
                                                                <span class="text-sm text-slate-400 italic">
                                                                    Lot tidak tersedia
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="text-xs text-slate-500 mt-0.5">
                                                            Bid terakhir: Rp{{ number_format($latest->amount, 0, ',', '.') }}
                                                            • {{ $latest->created_at?->format('d M Y, H:i') ?? '—' }}
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center gap-2 shrink-0">
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

                                                {{-- panel accordion: bid-bid sebelumnya di lot ini --}}
                                                <div x-show="open" x-cloak class="px-3 pb-3 bg-slate-50 border-t border-slate-100">
                                                    @if($older->isNotEmpty())
                                                        <div class="mt-2 border border-slate-200 rounded-xl bg-white overflow-hidden">
                                                            <div class="divide-y divide-slate-100 text-xs">
                                                                @foreach($older as $old)
                                                                    <div class="flex items-center justify-between gap-3 px-3 py-2">
                                                                        <span class="text-slate-500">
                                                                            {{ $old->created_at?->format('d M Y, H:i') ?? '—' }}
                                                                        </span>
                                                                        <span class="font-semibold text-slate-900">
                                                                            Rp {{ number_format($old->amount, 0, ',', '.') }}
                                                                        </span>
                                                                        <span class="hidden sm:inline text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">
                                                                            Bid sebelumnya
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="mt-2 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-2 text-[11px] text-slate-500">
                                                            Tidak ada bid lain di lot ini.
                                                            Bid di atas adalah satu-satunya penawaran pengguna.
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif {{-- end if profile --}}
                    </div> {{-- end main content --}}

                @else
                    {{-- ADMIN / SUPERADMIN: tampilan minimal --}}
                    <div class="p-6 text-sm text-slate-600">
                        Halaman detail lengkap hanya tersedia untuk akun Bidder. Untuk admin, gunakan daftar pengguna untuk pengelolaan status dan hak akses.
                    </div>
                @endif

                {{-- TINDAKAN CEPAT (bawah) --}}
                <div class="p-6 border-t border-slate-100">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        {{-- kiri: tombol kembali --}}
                        <a href="{{ route('users.index') }}"
                        class="px-3 py-1.5 rounded-full border border-slate-200 text-slate-700 text-xs hover:bg-slate-50">
                            ← Kembali
                        </a>

                        {{-- kanan: tindakan cepat --}}
                        <div class="flex flex-wrap gap-2">
                            @if($canResetPasswordAdmin)
                                <form method="POST"
                                    action="{{ route('users.send-reset', $user) }}"
                                    onsubmit="return confirm('Kirim link reset password ke {{ $user->email }}?')">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-full border border-slate-200 text-slate-700 text-xs hover:bg-slate-50">
                                        Reset Password Admin
                                    </button>
                                </form>
                            @endif

                            @if($canResendVerification)
                                <form method="POST"
                                    action="{{ route('users.resend-verification', $user) }}"
                                    onsubmit="return confirm('Kirim ulang email verifikasi ke {{ $user->email }}?')">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-full border border-amber-200 text-amber-700 text-xs hover:bg-amber-50">
                                        Kirim Ulang Verifikasi
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
