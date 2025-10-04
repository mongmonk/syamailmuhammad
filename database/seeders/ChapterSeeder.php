<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Chapter;

class ChapterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chapters = [
            [
                'title' => 'Bab Pertama: Sifat Rupa Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang ciri-ciri fisik dan wajah Rasulullah SAW yang mulia.',
                'chapter_number' => 1,
            ],
            [
                'title' => 'Bab Kedua: Sifat Rambut Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang keindahan dan bentuk rambut Rasulullah SAW.',
                'chapter_number' => 2,
            ],
            [
                'title' => 'Bab Ketiga: Sifat Mata Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang keindahan dan cahaya yang terpancar dari mata Rasulullah SAW.',
                'chapter_number' => 3,
            ],
            [
                'title' => 'Bab Keempat: Sifat Dahi Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang keindahan dahi Rasulullah SAW yang luas dan cerah.',
                'chapter_number' => 4,
            ],
            [
                'title' => 'Bab Kelima: Sifat Alis Mata Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang bentuk alis mata Rasulullah SAW yang melengkung dan tipis.',
                'chapter_number' => 5,
            ],
            [
                'title' => 'Bab Keenam: Sifat Hidung Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang bentuk hidung Rasulullah SAW yang mancung dan indah.',
                'chapter_number' => 6,
            ],
            [
                'title' => 'Bab Ketujuh: Sifat Pipi Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang pipi Rasulullah SAW yang padat dan bercahaya.',
                'chapter_number' => 7,
            ],
            [
                'title' => 'Bab Kedelapan: Sifat Mulut Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang mulut Rasulullah SAW yang sedang dan indah.',
                'chapter_number' => 8,
            ],
            [
                'title' => 'Bab Kesembilan: Sifat Gigi Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang keindahan gigi Rasulullah SAW yang rapat dan bersih.',
                'chapter_number' => 9,
            ],
            [
                'title' => 'Bab Kesepuluh: Sifat Janggut Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang janggut Rasulullah SAW yang lebat dan terawat.',
                'chapter_number' => 10,
            ],
            [
                'title' => 'Bab Kesebelas: Sifat Leher Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang leher Rasulullah SAW yang indah dan putih.',
                'chapter_number' => 11,
            ],
            [
                'title' => 'Bab Keduabelas: Sifat Bahu Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang bahu Rasulullah SAW yang lebar dan kokoh.',
                'chapter_number' => 12,
            ],
            [
                'title' => 'Bab Ketigabelas: Sifat Dada Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang dada Rasulullah SAW yang luas seperti lembaran kertas.',
                'chapter_number' => 13,
            ],
            [
                'title' => 'Bab Keempatbelas: Sifat Perut Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang perut Rasulullah SAW yang rata dan tidak menonjol.',
                'chapter_number' => 14,
            ],
            [
                'title' => 'Bab Kelimabelas: Sifat Punggung Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang punggung Rasulullah SAW yang lurus seperti cincin.',
                'chapter_number' => 15,
            ],
            [
                'title' => 'Bab Keenambelas: Sifat Dada Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang dada Rasulullah SAW yang berbulu halus.',
                'chapter_number' => 16,
            ],
            [
                'title' => 'Bab Ketujuhbelas: Sifat Lengan Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang lengan Rasulullah SAW yang panjang dan berisi.',
                'chapter_number' => 17,
            ],
            [
                'title' => 'Bab Kedelapanbelas: Sifat Tangan Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang tangan Rasulullah SAW yang lembut dan beraroma misk.',
                'chapter_number' => 18,
            ],
            [
                'title' => 'Bab Kesembilanbelas: Sifat Jari Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang jari Rasulullah SAW yang panjang dan ramping.',
                'chapter_number' => 19,
            ],
            [
                'title' => 'Bab Keduapuluh: Sifat Telapak Tangan Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang telapak tangan Rasulullah SAW yang lebar dan lembut.',
                'chapter_number' => 20,
            ],
            [
                'title' => 'Bab Keduapuluh satu: Sifat Paha Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang paha Rasulullah SAW yang berisi dan kuat.',
                'chapter_number' => 21,
            ],
            [
                'title' => 'Bab Keduapuluh dua: Sifat Betis Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang betis Rasulullah SAW yang indah dan tidak berbulu.',
                'chapter_number' => 22,
            ],
            [
                'title' => 'Bab Keduapuluh tiga: Sifat Telapak Kaki Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang telapak kaki Rasulullah SAW yang licin dan tidak berlekuk.',
                'chapter_number' => 23,
            ],
            [
                'title' => 'Bab Keduapuluh empat: Sifat Jari Kaki Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang jari kaki Rasulullah SAW yang panjang dan sejajar.',
                'chapter_number' => 24,
            ],
            [
                'title' => 'Bab Keduapuluh lima: Sifat Langkah Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang langkah Rasulullah SAW yang tegap dan penuh wibawa.',
                'chapter_number' => 25,
            ],
            [
                'title' => 'Bab Keduapuluh enam: Sifat Tubuh Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang bentuk tubuh Rasulullah SAW yang proporsional dan seimbang.',
                'chapter_number' => 26,
            ],
            [
                'title' => 'Bab Keduapuluh tujuh: Sifat Warna Kulit Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang warna kulit Rasulullah SAW yang putih kemerahan.',
                'chapter_number' => 27,
            ],
            [
                'title' => 'Bab Keduapuluh delapan: Sifat Keringat Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang keringat Rasulullah SAW yang beraroma misk.',
                'chapter_number' => 28,
            ],
            [
                'title' => 'Bab Keduapuluh sembilan: Sifat Pakaian Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang pakaian yang biasa dikenakan Rasulullah SAW.',
                'chapter_number' => 29,
            ],
            [
                'title' => 'Bab Ketigapuluh: Sifat Sandal Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang sandal yang biasa dikenakan Rasulullah SAW.',
                'chapter_number' => 30,
            ],
            [
                'title' => 'Bab Ketigapuluh satu: Sifat Cincin Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang cincin yang biasa dikenakan Rasulullah SAW.',
                'chapter_number' => 31,
            ],
            [
                'title' => 'Bab Ketigapuluh dua: Sifat Pedang Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang pedang yang dimiliki Rasulullah SAW.',
                'chapter_number' => 32,
            ],
            [
                'title' => 'Bab Ketigapuluh tiga: Sifat Perisai Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang perisai yang dimiliki Rasulullah SAW.',
                'chapter_number' => 33,
            ],
            [
                'title' => 'Bab Ketigapuluh empat: Sifat Helm Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang helm yang dimiliki Rasulullah SAW.',
                'chapter_number' => 34,
            ],
            [
                'title' => 'Bab Ketigapuluh lima: Sifat Panji Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang panji yang dimiliki Rasulullah SAW.',
                'chapter_number' => 35,
            ],
            [
                'title' => 'Bab Ketigapuluh enam: Sifat Busur Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang busur yang dimiliki Rasulullah SAW.',
                'chapter_number' => 36,
            ],
            [
                'title' => 'Bab Ketigapuluh tujuh: Sifat Anak Panah Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang anak panah yang dimiliki Rasulullah SAW.',
                'chapter_number' => 37,
            ],
            [
                'title' => 'Bab Ketigapuluh delapan: Sifat Kendaraan Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang kendaraan yang biasa digunakan Rasulullah SAW.',
                'chapter_number' => 38,
            ],
            [
                'title' => 'Bab Ketigapuluh sembilan: Sifat Unta Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang unta yang dimiliki Rasulullah SAW.',
                'chapter_number' => 39,
            ],
            [
                'title' => 'Bab Keempatpuluh: Sifat Kuda Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang kuda yang dimiliki Rasulullah SAW.',
                'chapter_number' => 40,
            ],
            [
                'title' => 'Bab Keempatpuluh satu: Sifat Keledai Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang keledai yang dimiliki Rasulullah SAW.',
                'chapter_number' => 41,
            ],
            [
                'title' => 'Bab Keempatpuluh dua: Sifat Domba Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang domba yang dimiliki Rasulullah SAW.',
                'chapter_number' => 42,
            ],
            [
                'title' => 'Bab Keempatpuluh tiga: Sifat Makanan Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang makanan yang biasa dikonsumsi Rasulullah SAW.',
                'chapter_number' => 43,
            ],
            [
                'title' => 'Bab Keempatpuluh empat: Sifat Minuman Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang minuman yang biasa dikonsumsi Rasulullah SAW.',
                'chapter_number' => 44,
            ],
            [
                'title' => 'Bab Keempatpuluh lima: Sifat Buah Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang buah yang disukai Rasulullah SAW.',
                'chapter_number' => 45,
            ],
            [
                'title' => 'Bab Keempatpuluh enam: Sifat Daging Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang daging yang disukai Rasulullah SAW.',
                'chapter_number' => 46,
            ],
            [
                'title' => 'Bab Keempatpuluh tujuh: Sifat Roti Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang roti yang biasa dimakan Rasulullah SAW.',
                'chapter_number' => 47,
            ],
            [
                'title' => 'Bab Keempatpuluh delapan: Sifat Kurma Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang kurma yang disukai Rasulullah SAW.',
                'chapter_number' => 48,
            ],
            [
                'title' => 'Bab Keempatpuluh sembilan: Sifat Susu Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang susu yang biasa diminum Rasulullah SAW.',
                'chapter_number' => 49,
            ],
            [
                'title' => 'Bab Kelimapuluh: Sifat Madu Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang madu yang disukai Rasulullah SAW.',
                'chapter_number' => 50,
            ],
            [
                'title' => 'Bab Kelimapuluh satu: Sifat Zaitun Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang zaitun yang disukai Rasulullah SAW.',
                'chapter_number' => 51,
            ],
            [
                'title' => 'Bab Kelimapuluh dua: Sifat Tharid Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang tharid (roti berkuah) yang disukai Rasulullah SAW.',
                'chapter_number' => 52,
            ],
            [
                'title' => 'Bab Kelimapuluh tiga: Sifat Tidur Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang kebiasaan tidur Rasulullah SAW.',
                'chapter_number' => 53,
            ],
            [
                'title' => 'Bab Kelimapuluh empat: Sifat Bangun Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang kebiasaan bangun Rasulullah SAW.',
                'chapter_number' => 54,
            ],
            [
                'title' => 'Bab Kelimapuluh lima: Sifat Duduk Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang cara duduk Rasulullah SAW.',
                'chapter_number' => 55,
            ],
            [
                'title' => 'Bab Kelimapuluh enam: Sifat Tawa Rasulullah SAW',
                'description' => 'Bab ini menjelaskan tentang tawa Rasulullah SAW yang indah dan bersahaja.',
                'chapter_number' => 56,
            ],
        ];

        foreach ($chapters as $chapter) {
            Chapter::create($chapter);
        }
    }
}
