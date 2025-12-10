<x-action-section>
    <x-slot name="title">
        Hapus Akun
    </x-slot>

    <x-slot name="description">
        Hapus akun Anda secara permanen.
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            Setelah akun dihapus, semua data dan riwayat yang terkait tidak dapat dikembalikan.
            Pastikan Anda sudah menyimpan informasi penting sebelum melanjutkan.
        </div>

        <div class="mt-5">
            <x-danger-button wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                Hapus Akun
            </x-danger-button>
        </div>

        <!-- Delete User Confirmation Modal -->
        <x-dialog-modal wire:model.live="confirmingUserDeletion">
            <x-slot name="title">
                Konfirmasi Penghapusan Akun
            </x-slot>

            <x-slot name="content">
                Apakah Anda yakin ingin menghapus akun ini secara permanen?
                Masukkan kata sandi untuk konfirmasi.

                <div class="mt-4"
                     x-data="{}"
                     x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                    <x-input type="password" class="mt-1 block w-3/4"
                             autocomplete="current-password"
                             placeholder="Kata sandi"
                             x-ref="password"
                             wire:model="password"
                             wire:keydown.enter="deleteUser" />

                    <x-input-error for="password" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                    Batal
                </x-secondary-button>

                <x-danger-button class="ms-3" wire:click="deleteUser" wire:loading.attr="disabled">
                    Hapus
                </x-danger-button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>
