<?php

return [
    // Autentikasi & Autorisasi
    'UNAUTHENTICATED' => 'Silakan login terlebih dahulu.',
    'ACCESS_DENIED' => 'Akses ditolak.',
    'FORBIDDEN_ADMIN_ONLY' => 'Hanya admin yang dapat mengakses.',

    // Status pengguna
    'USER_STATUS_NOT_ACTIVE' => 'Status pengguna belum aktif.',
    'USER_STATUS_BANNED' => 'Pengguna diblokir.',
    'USER_STATUS_PENDING' => 'Status pengguna masih pending.',
    'USER_STATUS_ACTIVE' => 'Status pengguna aktif.',
    'USER_STATUS_UNKNOWN' => 'Status pengguna tidak dikenal.',

    // Token/JWT
    'TOKEN_MISSING' => 'Token tidak ditemukan.',
    'TOKEN_INVALID' => 'Token tidak valid.',
    'TOKEN_EXPIRED' => 'Token kedaluwarsa.',
    'TOKEN_DECODE_ERROR' => 'Gagal memproses token.',

    // Pengguna & kredensial
    'USER_NOT_FOUND' => 'Pengguna tidak ditemukan.',
    'CREDENTIALS_INVALID' => 'Kredensial tidak valid.',
    'SESSION_NOT_ESTABLISHED' => 'Sesi belum terbentuk.',

    // Validasi & input
    'VALIDATION_FAILED' => 'Data yang dikirim tidak valid.',
    'PHONE_INVALID_FORMAT' => 'Format nomor telepon tidak valid (gunakan format E.164, contoh: +628123456789).',
    'PHONE_ALREADY_TAKEN' => 'Nomor telepon sudah digunakan.',
    'EMAIL_INVALID_FORMAT' => 'Format email tidak valid.',
    'EMAIL_ALREADY_TAKEN' => 'Email sudah digunakan.',

    // Rate limiting
    'RATE_LIMIT_EXCEEDED' => 'Batas percobaan terlampaui, coba lagi nanti.',
];