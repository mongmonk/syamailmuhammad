<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChapterRequest extends FormRequest
{
    /**
     * Otorisasi request.
     * Middleware role.admin sudah melindungi rute; di sini cukup return true.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk pembuatan Chapter.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'chapter_number' => ['required', 'integer', 'min:1', 'unique:chapters,chapter_number'],
        ];
    }

    /**
     * Label atribut untuk pesan validasi lokal (id).
     */
    public function attributes(): array
    {
        return [
            'title' => 'Judul Bab',
            'description' => 'Deskripsi',
            'chapter_number' => 'Nomor Bab',
        ];
    }

    /**
     * Normalisasi input sebelum validasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => is_string($this->title) ? trim($this->title) : $this->title,
            'description' => is_string($this->description ?? null) ? trim((string) $this->description) : $this->description,
            'chapter_number' => is_numeric($this->chapter_number) ? (int) $this->chapter_number : $this->chapter_number,
        ]);
    }
}