<?php

namespace App\Support;

class FootnoteParser
{
    /**
     * Parse footnotes dari teks tafsir/interpretation.
     *
     * Penanda yang didukung:
     * - [[isi footnote]]
     * - ((isi footnote))
     *
     * Mengembalikan struktur:
     * [
     *   'content'   => string HTML dengan <sup> indeks footnote,
     *   'footnotes' => array<int, array{index:int, content:string}>
     * ]
     */
    public function process(?string $text): array
    {
        if ($text === null || trim($text) === '') {
            return [
                'content' => $text ?? '',
                'footnotes' => [],
            ];
        }

        $index = 1;
        $footnotes = [];

        // Tangkap [[...]] atau ((...)) sebagai footnote
        $pattern = '/(\[\[(.*?)\]\]|\(\((.*?)\)\))/s';

        $rendered = preg_replace_callback(
            $pattern,
            function (array $matches) use (&$footnotes, &$index): string {
                // Pilih grup non-kosong antara [[...]] (group 2) atau ((...)) (group 3)
                $note = $matches[2] !== '' ? $matches[2] : ($matches[3] ?? '');
                $note = trim(preg_replace('/\s+/', ' ', $note));

                $footnotes[] = [
                    'index' => $index,
                    'content' => $note,
                ];

                $sup = '<sup class="footnote-ref">' . $index . '</sup>';
                $index++;

                return $sup;
            },
            $text
        );

        // Normalisasi newline agar rapi saat dirender
        $rendered = preg_replace("/\r\n|\r/", "\n", $rendered);
        $rendered = preg_replace("/\n{3,}/", "\n\n", $rendered);
        // Konversi newline ke <br> untuk dukungan teks polos
        $rendered = nl2br($rendered, false);

        return [
            'content' => $rendered,
            'footnotes' => $footnotes,
        ];
    }
}