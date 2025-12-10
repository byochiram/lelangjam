<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\BidderProfile;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        $isBidder = $user->role === 'BIDDER';

        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9._]+$/',
                Rule::unique('users')->ignore($user->id),
            ],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo'    => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ];

        if ($isBidder) {
            // normalisasi: ambil hanya digit
            $rawPhone = isset($input['phone'])
                ? preg_replace('/\D/', '', $input['phone'])
                : '';

            $input['phone'] = $rawPhone;

            // validasi sama persis dengan register
            $rules['phone'] = [
                'required',
                'string',
                'regex:/^[8][0-9]{8,11}$/', // mulai 8, total 9–12 digit
            ];
        }

        Validator::make($input, $rules, [
            'name.required'       => 'Nama lengkap wajib diisi.',
            'username.required'   => 'Username wajib diisi.',
            'username.unique'     => 'Username sudah digunakan.',
            'username.regex'      => 'Username hanya boleh berisi huruf kecil, angka, garis bawah (_), atau titik (.).',
            'email.required'      => 'Email wajib diisi.',
            'email.email'         => 'Format email tidak valid.',
            'email.unique'        => 'Email sudah terdaftar.',
            'phone.required'      => 'Nomor HP wajib diisi.',
            'phone.regex'         => 'Nomor HP harus diawali angka 8 dan berisi 9–12 digit, contoh: 81234567890.',
        ])->validateWithBag('updateProfileInformation');

        // Foto profil
        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        // Update user (name, username, email)
        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {

            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name'     => $input['name'],
                'username' => $input['username'],
                'email'    => $input['email'],
            ])->save();
        }

        // Kalau bukan BIDDER, selesai
        if (! $isBidder) {
            return;
        }

        // Update / buat bidder_profile (hanya phone)
        /** @var \App\Models\BidderProfile $bidder */
        $bidder = $user->bidderProfile ?: new BidderProfile(['user_id' => $user->id]);

        $normalizedPhone = '+62' . $input['phone'];
        $bidder->phone   = $normalizedPhone;
        $bidder->save();
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name'              => $input['name'],
            'username'          => $input['username'],
            'email'             => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
