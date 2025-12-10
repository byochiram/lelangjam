<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm as JetstreamUpdateProfileInformationForm;

class UpdateProfileInformationForm extends JetstreamUpdateProfileInformationForm
{
    /** null = belum dicek, true = tersedia, false = sudah dipakai */
    public ?bool $usernameAvailable = null;

    /** null = tidak ada error format, string = pesan error format */
    public ?string $usernameFormatError = null;

    public function mount()
    {
        parent::mount();

        // Prefill phone untuk BIDDER
        if ($this->user->role === 'BIDDER') {
            $bidder = $this->user->bidderProfile;

            // ambil hanya digit lokal (buang +62 atau 0 di depan)
            $rawPhone = $bidder?->phone
                ? preg_replace('/^(\+62|0)/', '', $bidder->phone)
                : '';

            $this->state['phone'] = $rawPhone;
        }
    }

    public function checkUsernameAvailability(): void
    {
        $value = trim($this->state['username'] ?? '');

        $this->usernameAvailable   = null;
        $this->usernameFormatError = null;

        if ($value === '' || $value === $this->user->username) {
            return;
        }

        if (! preg_match('/^[a-z0-9._]+$/', $value)) {
            $this->usernameFormatError =
                'Username hanya boleh berisi huruf kecil, angka, tanpa spasi, garis bawah (_), atau titik (.).';
            return;
        }

        $exists = User::where('username', $value)
            ->where('id', '!=', $this->user->id)
            ->exists();

        $this->usernameAvailable = ! $exists;
    }

    public function updateProfileInformation(UpdatesUserProfileInformation $updater)
    {
        parent::updateProfileInformation($updater);

        // Refresh data user & bidder (buat "Terakhir diperbarui")
        $this->user->refresh()->load('bidderProfile');
    }
}
