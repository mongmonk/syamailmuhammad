<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hadith;
use App\Models\AudioFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class AudioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the audio directory exists
        Storage::disk('local')->makeDirectory('audio');
        
        // Get some hadiths to attach audio files
        $hadiths = Hadith::take(5)->get();
        
        // Sample audio files data (in a real scenario, you would have actual audio files)
        $sampleAudios = [
            [
                'filename' => 'hadith_1_1.mp3',
                'duration' => 120, // 2 minutes in seconds
                'file_size' => 1920000, // ~1.84 MB
                'title' => 'Hadits tentang Keindahan Wajah Rasulullah SAW'
            ],
            [
                'filename' => 'hadith_1_2.mp3',
                'duration' => 180, // 3 minutes in seconds
                'file_size' => 2880000, // ~2.75 MB
                'title' => 'Hadits tentang Perbandingan Wajah Rasulullah dengan Bulan Purnama'
            ],
            [
                'filename' => 'hadith_2_1.mp3',
                'duration' => 150, // 2.5 minutes in seconds
                'file_size' => 2400000, // ~2.29 MB
                'title' => 'Hadits tentang Rambut Rasulullah SAW'
            ],
            [
                'filename' => 'hadith_3_1.mp3',
                'duration' => 210, // 3.5 minutes in seconds
                'file_size' => 3360000, // ~3.20 MB
                'title' => 'Hadits tentang Mata Rasulullah SAW'
            ],
            [
                'filename' => 'hadith_3_2.mp3',
                'duration' => 195, // 3.25 minutes in seconds
                'file_size' => 3120000, // ~2.98 MB
                'title' => 'Hadits tentang Adab Pandangan Rasulullah SAW'
            ]
        ];
        
        // Create audio files and associate them with hadiths
        foreach ($hadiths as $index => $hadith) {
            if ($index < count($sampleAudios)) {
                $audioData = $sampleAudios[$index];
                $filePath = 'audio/' . $audioData['filename'];
                
                // Create a dummy audio file (in a real scenario, you would upload actual audio files)
                // This is just for demonstration purposes
                $dummyContent = 'Dummy audio content for ' . $audioData['title'];
                Storage::disk('local')->put($filePath, $dummyContent);
                
                // Create audio file record
                AudioFile::create([
                    'hadith_id' => $hadith->id,
                    'file_path' => $filePath,
                    'duration' => $audioData['duration'],
                    'file_size' => $audioData['file_size'],
                ]);
                
                $this->command->info("Created audio file for hadith #{$hadith->id}: {$audioData['title']}");
            }
        }
        
        $this->command->info('Audio seeder completed successfully!');
    }
}