<?php
return [
    'accepted' => ':attribute harus diterima.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'required' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',
    'unique' => ':attribute sudah digunakan.',
    'max' => [
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
    ],
    'min' => [
        'string' => ':attribute minimal :min karakter.',
    ],
    'current_password' => 'Kata sandi saat ini tidak sesuai.',
    'password' => [
        'letters' => ':attribute harus mengandung setidaknya satu huruf.',
        'mixed' => ':attribute harus mengandung huruf besar dan huruf kecil.',
        'numbers' => ':attribute harus mengandung setidaknya satu angka.',
        'symbols' => ':attribute harus mengandung setidaknya satu simbol.',
        'min' => ':attribute minimal :min karakter.',
        'uncompromised' => ':attribute ditemukan dalam kebocoran data. Silakan pilih :attribute lain.',
    ],
    'attributes' => [
        'name' => 'nama',
        'email' => 'email',
        'phone' => 'nomor telepon',
        'password' => 'kata sandi',
        'password_confirmation' => 'konfirmasi kata sandi',
    ],
];