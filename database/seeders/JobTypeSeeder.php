<?php

namespace Database\Seeders;

use App\Models\JobType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! config('templatecookie.testing_mode')) {
            $types = [
                'Full Time', 'Part Time', 'Contractual', 'Intern', 'Freelance',
            ];
        } else {
            $types = [
                'Full Time', 'Part Time',
            ];
        }

        foreach ($types as $type) {
            JobType::create([
                'name' => $type,
                'slug' => Str::slug($type),
            ]);
        }
    }
}
