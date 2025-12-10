<x-form-section submit="updateProfileInformation">
    @php
        $isAdmin = $this->user->isAdmin();
        $bidder  = $this->user->bidderProfile ?? null;
    @endphp

    <x-slot name="title">
        Informasi Profil
    </x-slot>

    <x-slot name="description">
        {{ $isAdmin
            ? 'Kelola data dasar akun administrator.'
            : 'Perbarui informasi profil dan kontak Anda.' }}
    </x-slot>

    <x-slot name="form">
        {{-- FOTO PROFIL (SAMA UNTUK ADMIN & BIDDER) --}}
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <input type="file" id="photo" class="hidden"
                       wire:model.live="photo"
                       x-ref="photo"
                       x-on:change="
                            photoName = $refs.photo.files[0].name;
                            const reader = new FileReader();
                            reader.onload = (e) => { photoPreview = e.target.result; };
                            reader.readAsDataURL($refs.photo.files[0]);
                       " />

                <x-label for="photo" value="Foto Profil (Opsional)" />

                {{-- foto lama --}}
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}"
                         alt="{{ $this->user->name }}"
                         class="rounded-full size-20 object-cover ring-2 ring-slate-200" />
                </div>

                {{-- preview foto baru --}}
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center ring-2 ring-slate-200"
                          x-bind:style="'background-image: url(' + photoPreview + ');'">
                    </span>
                </div>

                <div class="flex flex-wrap gap-2 mt-3">
                    <x-secondary-button type="button" x-on:click.prevent="$refs.photo.click()">
                        Pilih Foto Baru
                    </x-secondary-button>

                    @if ($this->user->profile_photo_path)
                        <x-secondary-button type="button" wire:click="deleteProfilePhoto">
                            Hapus Foto
                        </x-secondary-button>
                    @endif
                </div>

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        {{-- NAMA --}}
        <div class="col-span-6 sm:col-span-3">
            <x-label for="name" value="Nama Lengkap" required/>
            <x-input id="name" type="text" class="mt-1 block w-full"
                     wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="text-xs mt-2" />
        </div>

        {{-- USERNAME --}}
        <div class="col-span-6 sm:col-span-3">
            <div class="flex items-baseline justify-between">
                <x-label for="username" value="Username" required/>

                {{-- teks kecil di kanan: hanya untuk status UNIK / TIDAK --}}
                <span class="text-[13px]
                    @if($usernameAvailable === true) text-emerald-600
                    @elseif($usernameAvailable === false) text-red-500
                    @else text-slate-500
                    @endif">
                    @if ($usernameAvailable === true)
                        Username tersedia.
                    @elseif ($usernameAvailable === false)
                        Username sudah digunakan.
                    @else
                        {{-- kosong saja untuk menjaga layout --}}
                        &nbsp;
                    @endif
                </span>
            </div>

            <x-input id="username" type="text"
                    class="mt-1 block w-full"
                    wire:model.defer="state.username"
                    wire:keyup.debounce.400ms="checkUsernameAvailability"
                    required autocomplete="username" />

            {{-- VALIDASI FORMAT DI BAWAH FIELD --}}
            @if ($usernameFormatError)
                <p class="mt-1 text-xs text-red-500">
                    {{ $usernameFormatError }}
                </p>
            @endif
        </div>

        {{-- EMAIL --}}
        <div class="col-span-6 sm:col-span-3">
            <x-label for="email" value="Email" required/>
            <x-input id="email" type="email" class="mt-1 block w-full bg-gray-100 text-gray-500"
                     wire:model="state.email" autocomplete="username" disabled />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-xs mt-2 text-slate-600">
                    Alamat email Anda belum terverifikasi.
                    <button type="button"
                            class="underline text-xs text-indigo-600 hover:text-indigo-800"
                            wire:click.prevent="sendEmailVerification">
                        Klik di sini untuk kirim ulang email verifikasi.
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-xs text-emerald-600">
                        Link verifikasi baru sudah dikirim ke email Anda.
                    </p>
                @endif
            @endif
        </div>

        {{-- FIELD KHUSUS BIDDER --}}
        @unless($isAdmin)
            {{-- NOMOR HP --}}
                <div class="col-span-6 sm:col-span-3"
                    x-data="{
                        value: '{{ $state['phone'] ?? '' }}',
                        minLength: 9,
                        invalidStart: false,
                        invalidLength: false,
                        onInput(e) {
                            // hanya digit
                            let v = e.target.value.replace(/[^0-9]/g, '');

                            // max 12 digit
                            if (v.length > 12) v = v.slice(0, 12);

                            this.value = v;

                            // cek aturan
                            this.invalidStart  = (this.value.length > 0 && this.value[0] !== '8');
                            this.invalidLength = (!this.invalidStart && this.value.length > 0 && this.value.length < this.minLength);

                            // sync ke Livewire state
                            $wire.set('state.phone', this.value);
                        }
                    }"
                >
                    <x-label for="phone" value="Nomor HP" required/>

                    <div class="mt-1 h-10 flex bg-white
                                border border-slate-300
                                rounded-md shadow-sm
                                focus-within:border-indigo-500
                                focus-within:ring-1 focus-within:ring-indigo-500
                                overflow-hidden">

                        {{-- Prefix +62 --}}
                        <span class="inline-flex items-center px-3 h-full text-sm
                                    bg-slate-50 text-slate-500
                                    border-r border-slate-200">
                            +62
                        </span>

                        {{-- Input tanpa prefix --}}
                        <input  id="phone"
                                type="tel"
                                x-model="value"
                                x-on:input="onInput($event)"
                                class="flex-1 border-0 bg-transparent
                                    px-3 py-2 h-full text-sm
                                    placeholder:text-slate-400
                                    text-slate-900
                                    focus:outline-none focus:ring-0"
                                placeholder="81234567890"
                                autocomplete="tel" />
                    </div>

                    {{-- ERROR realtime --}}
                    <p x-show="invalidStart"
                    x-cloak
                    class="mt-1 text-xs text-red-600">
                        Nomor harus dimulai dengan angka 8.
                    </p>

                    <p x-show="!invalidStart && invalidLength"
                    x-cloak
                    class="mt-1 text-xs text-red-600">
                        Nomor HP minimal 9 digit.
                    </p>

                    {{-- ERROR dari backend --}}
                    <x-input-error for="phone" class="text-xs mt-2" />
                </div>

            {{-- INFO TERAKHIR DIPERBARUI --}}
            <div class="col-span-6">
                @php
                    $lastUpdated = $bidder?->updated_at ?? $this->user->updated_at;
                @endphp

                @if ($lastUpdated)
                    <p class="text-xs text-slate-500">
                        Terakhir diperbarui:
                        {{ $lastUpdated->format('d M Y H:i') }} WIB
                    </p>
                @endif
            </div>
        @endunless
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Tersimpan.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Simpan') }}
        </x-button>
    </x-slot>

</x-form-section>