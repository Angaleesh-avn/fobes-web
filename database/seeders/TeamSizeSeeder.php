<?php

namespace Database\Seeders;

use App\Models\TeamSize;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! config('templatecookie.testing_mode')) {
            $team_sizes = [
                'Only Me', '10 Members', '10-20 Members', '20-50 Members', '50-100 Members', '100-200 Members', '200-500 Members', '500+ Members',
            ];
        } else {
            $team_sizes = [
                'Only Me', '10 - 20 Members',
            ];
        }

        foreach ($team_sizes as $size) {
            TeamSize::create([
                'name' => $size,
                'slug' => Str::slug($size),
            ]);
        }
    }
}
