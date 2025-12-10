<x-form-section submit="updatePassword">
    <x-slot name="title">
        Ubah Kata Sandi
    </x-slot>

    <x-slot name="description">
        Pastikan akun Anda menggunakan kata sandi yang kuat dan sulit ditebak.
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-label for="current_password" value="Kata Sandi Saat Ini" required/>
            <x-input id="current_password" type="password" class="mt-1 block w-full"
                     wire:model="state.current_password" autocomplete="current-password" />
            <x-input-error for="current_password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password" value="Kata Sandi Baru" required/>
            <x-input id="password" type="password" class="mt-1 block w-full"
                    wire:model="state.password" autocomplete="new-password" />
            <x-input-error for="password" class="mt-2" />

            {{-- Kriteria password --}}
            <div class="mt-2 text-xs text-gray-500 space-y-1">
                <p class="font-medium text-gray-600">Kriteria kata sandi:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    <li>Minimal 8 karakter.</li>
                    <li>Mengandung setidaknya satu huruf (a–z atau A–Z).</li>
                    <li>Mengandung setidaknya satu angka (0–9).</li>
                    @if(app()->isProduction())
                        <li>Mengandung huruf besar dan huruf kecil.</li>
                        <li>Mengandung setidaknya satu simbol (misalnya: ! @ # ?).</li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password_confirmation" value="Konfirmasi Kata Sandi Baru" required/>
            <x-input id="password_confirmation" type="password" class="mt-1 block w-full"
                     wire:model="state.password_confirmation" autocomplete="new-password" />
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            Kata sandi berhasil diperbarui.
        </x-action-message>

        <x-button>
            Simpan Kata Sandi
        </x-button>
    </x-slot>
</x-form-section>
