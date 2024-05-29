<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            'php',
            'laravel',
            'mysql',
            'job',
            'frontend',
            'backend',
            'bootstrap',
            'team',
            'testing',
            'database',
            'jobs',
            'remote',
            'others',
            'seeker',
            'candidate',
            'company',
            'technology',
            'work',
        ];

        $languages = loadLanguage();

        foreach ($tags as $data) {
            $translation = new Tag();
            $translation->show_popular_list = rand(0, 1);
            $translation->save();

            foreach ($languages as $language) {
                $translation->translateOrNew($language->code)->name = $data;
            }

            $translation->save();
        }
    }
}
