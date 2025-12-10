<?php

return [
    'required'   => 'Kolom :attribute wajib diisi.',
    'unique'     => 'Kolom :attribute sudah digunakan.',
    'integer'    => 'Kolom :attribute harus berupa bilangan bulat.',
    'confirmed'  => 'Konfirmasi :attribute tidak sama.',
    'email'      => 'Format :attribute tidak valid.',
    'min' => [
        'numeric' => 'Kolom :attribute minimal :min.',
        'string'  => 'Kolom :attribute minimal :min karakter.',
    ],

    'max' => [
        'numeric' => 'Kolom :attribute maksimal :max.',
        'string'  => 'Kolom :attribute maksimal :max karakter.',
    ],

    'password' => [
        'letters'       => 'Kolom :attribute harus mengandung setidaknya satu huruf.',
        'mixed'         => 'Kolom :attribute harus mengandung huruf besar dan huruf kecil.',
        'numbers'       => 'Kolom :attribute harus mengandung setidaknya satu angka.',
        'symbols'       => 'Kolom :attribute harus mengandung setidaknya satu simbol.',
        'uncompromised' => ':Attribute yang Anda gunakan ditemukan di kebocoran data. Silakan pilih :attribute lain.',
    ],

    'attributes' => [
        'brand'     => 'brand',
        'model'     => 'model',
        'year'      => 'tahun',
        'condition' => 'kondisi',
        'email'                => 'email',
        'current_password'     => 'kata sandi saat ini',
        'password'             => 'kata sandi baru',
        'password_confirmation'=> 'konfirmasi kata sandi',
        'name'                 => 'nama',
        'username'             => 'username',
    ],
];
