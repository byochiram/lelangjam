{{-- admin/users/_table.blade.php --}}
@php
    $me = auth()->user();

    // role list yang sedang dilihat di tabel (ADMIN / BIDDER)
    $roleList = $currentRole ?? request('role') ?? 'BIDDER';

    $showActions =
        $me && (
            // TAB BIDDER: admin & superadmin boleh lihat kolom Aksi
            ($roleList === 'BIDDER' && ($me->isAdmin() || $me->isSuperAdmin()))
            // TAB ADMIN: hanya superadmin yang lihat kolom Aksi
            || ($roleList === 'ADMIN' && $me->isSuperAdmin())
        );
@endphp

<div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
    <table class="min-w-full text-sm md:text-base">
        <thead class="bg-slate-50 text-xs uppercase tracking-[0.16em] text-slate-500">
            <tr>
                <th class="px-6 py-3 text-left">Pengguna</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-center">Status</th>
                <th class="px-6 py-3 text-center">Verifikasi</th>
                <th class="px-6 py-3 text-center">Terdaftar</th>
                @if($showActions)
                    <th class="px-6 py-3 text-right">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($users as $user)
                @php
                    $isVerified = !is_null($user->email_verified_at);
                    $status     = $user->status ?? 'ACTIVE';
                    $suspendedUntil  = $user->suspended_until;

                    [$statusLabel, $statusBadgeClasses, $dotClass] = match ($status) {
                        'ACTIVE'    => ['Aktif',        'bg-emerald-50 text-emerald-700', 'bg-emerald-500'],
                        'SUSPENDED' => ['Ditangguhkan', 'bg-red-50 text-red-700',         'bg-red-500'],
                        default     => [$status,        'bg-slate-100 text-slate-700',    'bg-slate-400'],
                    };
                @endphp

                <tr class="hover:bg-slate-50/70">
                    {{-- NAMA + USERNAME --}}
                    <td class="px-6 py-3 align-top">
                        <div class="font-medium text-slate-900">
                            {{ $user->name ?: '—' }}
                        </div>
                        <div class="mt-0.5 text-xs text-slate-500 flex items-center gap-1">
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-700">
                                #{{ $user->id }}
                            </span>
                            @if($user->username)
                                <span>{{ '@'.$user->username }}</span>
                            @endif
                        </div>
                    </td>

                    {{-- EMAIL --}}
                    <td class="px-6 py-3 align-top whitespace-nowrap">
                        @if($user->isBidder())
                            <a href="{{ route('users.show', $user) }}"
                            class="inline-block max-w-[220px] sm:max-w-[220px] lg:max-w-[260px] truncate text-blue-600 hover:underline align-top">
                                {{ $user->email }}
                            </a>
                        @else
                            <span class="inline-block max-w-[220px] sm:max-w-[220px] lg:max-w-[260px] truncate text-slate-700 align-top">
                                {{ $user->email }}
                            </span>
                        @endif
                    </td>

                    {{-- STATUS AKUN --}}
                    <td class="px-6 py-3 align-top text-center">
    <div class="flex flex-col items-center gap-0.5">
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-sm font-medium {{ $statusBadgeClasses }}">
            <span class="w-2 h-2 rounded-full {{ $dotClass }}"></span>
            {{ $statusLabel }}
        </span>

        @if($status === 'SUSPENDED' && $suspendedUntil)
            <span class="text-[11px] text-slate-400">
                s.d. {{ $suspendedUntil->format('d M Y, H:i') }}
            </span>
        @elseif($status === 'SUSPENDED' && ! $suspendedUntil)
            <span class="text-[11px] text-slate-400">
                Suspend manual (hingga diaktifkan admin)
            </span>
        @endif
    </div>
</td>


                    {{-- VERIFIKASI --}}
                    <td class="px-6 py-3 align-top text-center">
                        @if($isVerified)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                        bg-emerald-50 text-emerald-700 text-sm font-medium whitespace-nowrap">
                                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-500"></span>
                                Verified
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                        bg-amber-50 text-amber-700 text-sm font-medium whitespace-nowrap">
                                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-amber-500"></span>
                                Belum Verifikasi
                            </span>
                        @endif
                    </td>

                    {{-- CREATED --}}
                    <td class="px-6 py-3 align-top text-sm text-slate-700 text-center whitespace-nowrap">
                        {{ $user->created_at?->format('d M Y, H:i') ?? '—' }}
                    </td>

                    {{-- AKSI --}}
                    @if($showActions)
                        <td class="px-6 py-3 align-top">
                            <div class="flex justify-end flex-wrap gap-1">

                                {{-- TOGGLE STATUS (ikon), tidak untuk diri sendiri --}}
                                @php $me = auth()->user(); @endphp
                                @if($me && $me->id !== $user->id)
                                    @php
                                        $bolehUbah =
                                            // superadmin boleh ubah semua kecuali superadmin lain
                                            ($me->isSuperAdmin() && ! $user->isSuperAdmin())
                                            // admin biasa hanya boleh ubah BIDDER
                                            || ($me->isAdmin() && $user->role === 'BIDDER');
                                    @endphp

                                    @if($bolehUbah)
                                        @if($user->status === 'ACTIVE')
                                            {{-- STATUS: ACTIVE → buka modal suspend (ada textarea alasan) --}}
                                            <button type="button"
                                                @click="openSuspendModal({
                                                    id: {{ $user->id }},
                                                    name: @js($user->name),
                                                    email: @js($user->email)
                                                })"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full border
                                                    border-red-200 text-red-600 hover:bg-red-50"
                                                title="Tangguhkan akun ini">
                                                {{-- Tangguhkan: person-fill-slash --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                    fill="currentColor" class="bi bi-person-fill-slash" viewBox="0 0 16 16">
                                                    <path d="M13.879 10.414a2.501 2.501 0 0 0-3.465 3.465zm.707.707-3.465 3.465a2.501 2.501 0 0 0 3.465-3.465m-4.56-1.096a3.5 3.5 0 1 1 4.949 4.95 3.5 3.5 0 0 1-4.95-4.95ZM11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/>
                                                </svg>
                                            </button>
                                        @else
                                            {{-- STATUS: SUSPENDED → tetap pakai toggle ke ACTIVE seperti biasa --}}
                                            <form method="POST"
                                                action="{{ route('users.toggle-status', $user) }}"
                                                @submit.prevent="confirmAndSubmit(
                                                    $el,
                                                    'Konfirmasi',
                                                    'Aktifkan kembali pengguna ini?',
                                                    'Aktifkan'
                                                )">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full border
                                                        border-emerald-200 text-emerald-700 hover:bg-emerald-50"
                                                    title="Aktifkan akun ini">
                                                    {{-- Aktifkan: person-fill-check --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                        fill="currentColor" class="bi bi-person-fill-check" viewBox="0 0 16 16">
                                                        <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m1.679-4.493-1.335 2.226a.75.75 0 0 1-1.174.144l-.774-.773a.5.5 0 0 1 .708-.708l.547.548 1.17-1.951a.5.5 0 1 1 .858.514M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                                                        <path d="M2 13c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                @endif
                                {{-- RESET PASSWORD: hanya untuk ADMIN, hanya kalau yang login SUPERADMIN, dan bukan dirinya sendiri --}}
                                @if($user->role === 'ADMIN' && auth()->user()->isSuperAdmin() && auth()->id() !== $user->id)
                                    <button type="button"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-slate-200 text-slate-700 hover:bg-slate-50"
                                            title="Reset password admin ini"
                                            @click="openResetModal({ id: {{ $user->id }}, name: @js($user->name), email: @js($user->email) })">
                                        {{-- Icon reset (arrow-clockwise) --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                                d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                                        <path
                                                d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                                        </svg>
                                    </button>
                                @endif

                                {{-- KIRIM ULANG VERIFIKASI: BIDDER belum verifikasi --}}
                                @if(!$isVerified && $user->role === 'BIDDER')
                                    <form method="POST"
                                        action="{{ route('users.resend-verification', $user) }}"
                                        @submit.prevent="
                                            confirmAndSubmit(
                                                $el,
                                                'Kirim Ulang Verifikasi',
                                                'Kirim ulang email verifikasi ke {{ $user->email }}?',
                                                'Kirim'
                                            )
                                        ">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-amber-200 text-amber-600 hover:bg-amber-50"
                                                title="Kirim ulang email verifikasi">
                                            {{-- Icon mail --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                    d="M4 6h16v12H4z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                    d="M4 7l8 5 8-5" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $showActions ? 6 : 5 }}" class="px-6 py-6 text-center text-sm text-slate-500">
                        Belum ada pengguna yang sesuai filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>