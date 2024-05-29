<?php

namespace Modules\Faq\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Faq\Entities\Faq;
use Modules\Language\Entities\Language;

class FaqDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $lang_codes = Language::all();
        // Faq::factory(50)->create();
        foreach ($lang_codes as $lang_code) {
            Faq::factory(10)->create(['code' => $lang_code->code]);
        }
    }
}
