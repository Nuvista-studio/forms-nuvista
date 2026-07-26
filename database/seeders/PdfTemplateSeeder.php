<?php

namespace Database\Seeders;

use App\Models\PdfTemplate;
use Illuminate\Database\Seeder;

class PdfTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Form Pemeriksaan',
                'slug' => 'pemeriksaan',
                'html_content' => file_get_contents(resource_path('views/pdf/pemeriksaan.blade.php')),
                'is_active' => true,
            ],
            [
                'name' => 'Form Perawatan',
                'slug' => 'perawatan',
                'html_content' => file_get_contents(resource_path('views/pdf/perawatan.blade.php')),
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            $model = PdfTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
            $model->syncToFile();
        }
    }
}
