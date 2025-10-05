<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChapterRequest extends FormRequest
{
    /**
     * Otorisasi request.
     * Rute dilindungi middleware role.admin; di sini cukup return true.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk pembaruan Chapter.
     */
    public function rules(): array
    {
        // Ambil ID chapter dari route model binding atau parameter
        $routeChapter = $this->route('chapter');
        $chapterId = is_object($routeChapter) ? ($routeChapter->id ?? null) : $routeChapter;
        if (!$chapterId) {
            $chapterId = $this->route('id') ?? $this->input('id');
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'chapter_number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('chapters', 'chapter_number')->ignore($chapterId),
            ],
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
        $chapterNumber = $this->chapter_number;
        if (is_string($chapterNumber) && is_numeric($chapterNumber)) {
            $chapterNumber = (int) $chapterNumber;
        }

        $this->merge([
            'title' => is_string($this->title) ? trim($this->title) : $this->title,
            'description' => is_string($this->description ?? null) ? trim((string) $this->description) : $this->description,
            'chapter_number' => $chapterNumber,
        ]);
    }
}