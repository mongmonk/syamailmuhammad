<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHadithRequest extends FormRequest
{
    /**
     * Otorisasi request.
     * Middleware role.admin melindungi rute; di sini cukup return true.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk pembuatan Hadits.
     */
    public function rules(): array
    {
        $chapterId = $this->input('chapter_id');

        return [
            'chapter_id' => ['required', 'integer', 'exists:chapters,id'],
            'arabic_text' => ['required', 'string'],
            'translation' => ['required', 'string'],
            'footnotes' => ['nullable', 'string'],
            'audio_file' => ['nullable', 'file', 'mimes:mp3,ogg,wav,m4a', 'max:15360'], // max 15MB
            'hadith_number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('hadiths', 'hadith_number')->where(function ($query) use ($chapterId) {
                    return $query->where('chapter_id', $chapterId);
                }),
            ],
        ];
    }

    /**
     * Label atribut untuk pesan validasi lokal (id).
     */
    public function attributes(): array
    {
        return [
            'chapter_id' => 'Bab',
            'arabic_text' => 'Teks Arab',
            'translation' => 'Terjemahan',
            'footnotes' => 'Catatan Kaki',
            'audio_file' => 'File Audio',
            'hadith_number' => 'Nomor Hadits',
        ];
    }

    /**
     * Normalisasi input sebelum validasi.
     */
    protected function prepareForValidation(): void
    {
        $chapterId = $this->chapter_id;
        if (is_string($chapterId) && is_numeric($chapterId)) {
            $chapterId = (int) $chapterId;
        }

        $hadithNumber = $this->hadith_number;
        if (is_string($hadithNumber) && is_numeric($hadithNumber)) {
            $hadithNumber = (int) $hadithNumber;
        }

        $this->merge([
            'chapter_id' => $chapterId,
            'arabic_text' => is_string($this->arabic_text) ? trim($this->arabic_text) : $this->arabic_text,
            'translation' => is_string($this->translation) ? trim($this->translation) : $this->translation,
            'footnotes' => is_string($this->footnotes ?? null) ? trim((string) $this->footnotes) : $this->footnotes,
            'hadith_number' => $hadithNumber,
        ]);
    }
}