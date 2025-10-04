<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Chapter;
use App\Models\Hadith;

class HadithSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hadits untuk Bab 1: Sifat Rupa Rasulullah SAW
        $chapter1 = Chapter::where('chapter_number', 1)->first();
        
        if ($chapter1) {
            Hadith::create([
                'chapter_id' => $chapter1->id,
                'arabic_text' => 'كَانَ رَسُولُ اللَّهِ صَلَّى اللَّهُ عَلَيْهِ وَسَلَّمَ أَحْسَنَ النَّاسِ وَجْهًا وَأَحْسَنَهُمْ خُلُقًا',
                'translation' => 'Rasulullah SAW adalah manusia yang paling baik wajahnya dan paling baik akhlaknya.',
                'footnotes' => 'Hadits ini menjelaskan tentang keindahan wajah Rasulullah SAW yang tidak hanya terpancar dari bentuk fisiknya, tetapi juga dari cahaya keimanan dan ketakwaannya. Akhlak beliau yang mulia juga mencerminkan keindahan batininya.',
                                'hadith_number' => 1,
            ]);
            
            Hadith::create([
                'chapter_id' => $chapter1->id,
                'arabic_text' => 'كَانَ وَجْهُ رَسُولِ اللَّهِ صَلَّى اللَّهُ عَلَيْهِ وَسَلَّمَ كَالْقَمَرِ لَيْلَةَ الْبَدْرِ',
                'translation' => 'Wajah Rasulullah SAW bagaikan bulan purnama di malam yang cerah.',
                'footnotes' => 'Perbandingan wajah Rasulullah SAW dengan bulan purnama menunjukkan sinar yang terpancar darinya yang mampu menerangi kegelapan hati. Keindahan wajah beliau memberikan ketenangan bagi siapa saja yang memandangnya.',
                                'hadith_number' => 2,
            ]);
        }
        
        // Hadits untuk Bab 2: Sifat Rambut Rasulullah SAW
        $chapter2 = Chapter::where('chapter_number', 2)->first();
        
        if ($chapter2) {
            Hadith::create([
                'chapter_id' => $chapter2->id,
                'arabic_text' => 'كَانَ شَعْرُ رَسُولِ اللَّهِ صَلَّى اللَّهُ عَلَيْهِ وَسَلَّمَ بَيْنَ أُذُنَيْهِ وَعَاتِقَيْهِ',
                'translation' => 'Rambut Rasulullah SAW berada di antara kedua telinganya dan kedua bahunya.',
                'footnotes' => 'Panjangnya rambut Rasulullah SAW menunjukkan kesederhanaan beliau dalam penampilan. Rambut beliau yang hitam dan lurus menambah keagungan dan kewibawaan beliau sebagai utusan Allah.',
                                'hadith_number' => 1,
            ]);
        }
        
        // Hadits untuk Bab 3: Sifat Mata Rasulullah SAW
        $chapter3 = Chapter::where('chapter_number', 3)->first();
        
        if ($chapter3) {
            Hadith::create([
                'chapter_id' => $chapter3->id,
                'arabic_text' => 'كَانَتْ عَيْنَا رَسُولِ اللَّهِ صَلَّى اللَّهُ عَلَيْهِ وَسَلَّمَ سَوْدَاوَيْنِ كَأَنَّهُمَا عَيْنَا مِنْظَامِ الْخِنْزِيرِ',
                'translation' => 'Mata Rasulullah SAW hitam pekat, seolah-olah mata kacang hitam.',
                'footnotes' => 'Mata Rasulullah SAW yang hitam dan besar menunjukkan ketajaman penglihatan beliau, baik secara fisik maupun batin. Pandangan beliau mampu menembus hati dan memberikan ketenangan bagi siapa saja yang berada di dekatnya.',
                                'hadith_number' => 1,
            ]);
            
            Hadith::create([
                'chapter_id' => $chapter3->id,
                'arabic_text' => 'كَانَ إِذَا نَظَرَ إِلَى الشَّيْءِ أَدْنَى بَصَرَهُ وَإِذَا أَعْجَبَهُ الشَّيْءُ بَسَطَهُ',
                'translation' => 'Jika Rasulullah SAW memandang sesuatu, beliau menundukkan pandangannya. Dan jika beliau kagum pada sesuatu, beliau melebarkannya.',
                'footnotes' => 'Hadits ini menunjukkan adab Rasulullah SAW dalam memandang sesuatu. Beliau selalu menundukkan pandangan sebagai bentuk kesopanan dan pengendalian diri. Namun ketika melihat keindahan ciptaan Allah, beliau melebarkan pandangan sebagai bentuk kekaguman terhadap kekuasaan-Nya.',
                                'hadith_number' => 2,
            ]);
        }
        
        // Hadits untuk Bab 4: Sifat Dahi Rasulullah SAW
        $chapter4 = Chapter::where('chapter_number', 4)->first();
        
        if ($chapter4) {
            Hadith::create([
                'chapter_id' => $chapter4->id,
                'arabic_text' => 'كَانَ رَسُولُ اللَّهِ صَلَّى اللَّهُ عَلَيْهِ وَسَلَّمَ عَرِيضُ الْجَبْهَةِ',
                'translation' => 'Rasulullah SAW memiliki dahi yang luas.',
                'footnotes' => 'Dahi yang luas merupakan salah satu ciri keindahan Rasulullah SAW. Dahi yang luas juga menunjukkan keluasan ilmu dan kebijaksanaan yang dimiliki beliau.',
                                'hadith_number' => 1,
            ]);
        }
        
        // Hadits untuk Bab 5: Sifat Alis Mata Rasulullah SAW
        $chapter5 = Chapter::where('chapter_number', 5)->first();
        
        if ($chapter5) {
            Hadith::create([
                'chapter_id' => $chapter5->id,
                'arabic_text' => 'كَانَ رَسُولُ اللَّهِ صَلَّى اللَّهُ عَلَيْهِ وَسَلَّمَ مُتَّصِلَ الْحَاجِبَيْنِ',
                'translation' => 'Rasulullah SAW memiliki alis mata yang terhubung.',
                'footnotes' => 'Alis mata yang terhubung merupakan salah satu ciri fisik Rasulullah SAW yang menunjukkan keindahan dan kekhususan beliau dibandingkan dengan manusia pada umumnya.',
                                'hadith_number' => 1,
            ]);
        }
        
        // Hadits untuk Bab 6: Sifat Hidung Rasulullah SAW
        $chapter6 = Chapter::where('chapter_number', 6)->first();
        
        if ($chapter6) {
            Hadith::create([
                'chapter_id' => $chapter6->id,
                'arabic_text' => 'كَانَ أَشْنَبَ الْأَنْفِ',
                'translation' => 'Rasulullah SAW memiliki hidung yang mancung.',
                'footnotes' => 'Hidung yang mancung merupakan salah satu ciri keindahan Rasulullah SAW. Bentuk hidung yang indah menambah keagungan wajah beliau.',
                                'hadith_number' => 1,
            ]);
        }
        
        // Hadits untuk Bab 7: Sifat Pipi Rasulullah SAW
        $chapter7 = Chapter::where('chapter_number', 7)->first();
        
        if ($chapter7) {
            Hadith::create([
                'chapter_id' => $chapter7->id,
                'arabic_text' => 'كَانَ مُمْتَلِئَ الْوَجْهِ',
                'translation' => 'Rasulullah SAW memiliki wajah yang padat.',
                'footnotes' => 'Wajah yang padat menunjukkan keindahan pipi Rasulullah SAW yang proporsional dan seimbang, menambah keagungan penampilan beliau.',
                                'hadith_number' => 1,
            ]);
        }
        
        // Hadits untuk Bab 8: Sifat Mulut Rasulullah SAW
        $chapter8 = Chapter::where('chapter_number', 8)->first();
        
        if ($chapter8) {
            Hadith::create([
                'chapter_id' => $chapter8->id,
                'arabic_text' => 'كَانَ فَمُهُ عَرِيضًا',
                'translation' => 'Rasulullah SAW memiliki mulut yang lebar.',
                'footnotes' => 'Mulut yang lebar merupakan salah satu ciri keindahan Rasulullah SAW. Dengan mulut yang lebar, beliau dapat berbicara dengan jelas dan lancar dalam menyampaikan wahyu.',
                                'hadith_number' => 1,
            ]);
        }
        
        // Hadits untuk Bab 9: Sifat Gigi Rasulullah SAW
        $chapter9 = Chapter::where('chapter_number', 9)->first();
        
        if ($chapter9) {
            Hadith::create([
                'chapter_id' => $chapter9->id,
                'arabic_text' => 'كَانَتْ أَسْنَانُهُ مُتَفَرِّقَةً',
                'translation' => 'Gigi Rasulullah SAW terpisah-pisah.',
                'footnotes' => 'Gigi yang terpisah-pisah merupakan salah satu ciri keindahan Rasulullah SAW. Bentuk gigi yang indah menambah keagungan senyum beliau.',
                                'hadith_number' => 1,
            ]);
        }
        
        // Hadits untuk Bab 10: Sifat Janggut Rasulullah SAW
        $chapter10 = Chapter::where('chapter_number', 10)->first();
        
        if ($chapter10) {
            Hadith::create([
                'chapter_id' => $chapter10->id,
                'arabic_text' => 'كَانَ كَثِيرَ شَعْرِ اللِّحْيَةِ',
                'translation' => 'Rasulullah SAW memiliki janggut yang lebat.',
                'footnotes' => 'Janggut yang lebat merupakan salah satu ciri keindahan dan kewibawaan Rasulullah SAW. Janggut yang terawat menambah keagungan penampilan beliau.',
                                'hadith_number' => 1,
            ]);
        }
    }
}
