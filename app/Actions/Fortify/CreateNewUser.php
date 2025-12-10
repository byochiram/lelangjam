<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */

    public function create(array $input): User
    {
        // Normalisasi: ambil hanya digit
        $rawPhone = isset($input['phone'])
            ? preg_replace('/\D/', '', $input['phone'])
            : '';

        $input['phone'] = $rawPhone;

        Validator::make(
            $input,
            [
                'name' => ['required', 'string', 'max:255'],
                'username' => [
                    'required',
                    'string',
                    'max:50',
                    'regex:/^[a-z0-9._]+$/',
                    'unique:users,username',
                ],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],

                // ✅ harus mulai 8, total 9–12 digit
                'phone' => [
                    'required',
                    'string',
                    'regex:/^[8][0-9]{8,11}$/',
                ],

                'password' => $this->passwordRules(),
                'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature()
                    ? ['accepted', 'required']
                    : '',
            ],
            [
                'name.required'      => 'Nama wajib diisi.',
                'username.required'  => 'Username wajib diisi.',
                'username.unique'    => 'Username sudah digunakan.',
                'username.regex'     => 'Username hanya boleh berisi huruf kecil, angka, tanpa spasi, garis bawah (_), atau titik (.).',
                'email.required'     => 'Email wajib diisi.',
                'email.email'        => 'Format email tidak valid.',
                'email.unique'       => 'Email sudah terdaftar.',

                'phone.required'     => 'Nomor HP wajib diisi.',
                // ✅ pesan disesuaikan dengan rule regex
                'phone.regex'        => 'Nomor HP harus diawali angka 8 dan berisi 9–12 digit, contoh: 81234567890.',

                'password.required'  => 'Password wajib diisi.',
                'password.confirmed' => 'Konfirmasi password tidak sama.',
                'terms.accepted'     => 'Anda harus menyetujui Syarat dan Kebijakan Privasi.',
            ]
        )->validate();

        $normalizedPhone = '+62' . $input['phone'];

        $user = User::create([
            'name'     => $input['name'],
            'username' => $input['username'],
            'email'    => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $user->bidderProfile()->create([
            'phone'   => $normalizedPhone,
            'address' => null,
        ]);

        return $user;
    }
}
