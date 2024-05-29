<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobRole;
use App\Models\JobType;
use App\Models\SalaryType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class JobFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Job::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->unique()->jobTitle();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'company_id' => Company::inRandomOrder()->value('id'),
            'category_id' => JobCategory::inRandomOrder()->value('id'),
            'role_id' => JobRole::inRandomOrder()->value('id'),
            'experience_id' => Experience::inRandomOrder()->value('id'),
            'education_id' => Education::inRandomOrder()->value('id'),
            'job_type_id' => JobType::inRandomOrder()->value('id'),
            'salary_type_id' => SalaryType::inRandomOrder()->value('id'),
            'vacancies' => $this->faker->randomElement(['1-2', '2-3', '3-5', '5-10', '10-20']),
            'min_salary' => random_int(10, 100),
            'max_salary' => random_int(100, 1000),
            'salary_mode' => Arr::random(['range', 'custom']),
            'custom_salary' => 'Competitive',
            'deadline' => $this->faker->dateTimeBetween('now', '+07 days'),
            'description' => $this->faker->text(),
            'is_remote' => rand(0, 1),
            'status' => $this->faker->randomElement(['pending', 'active', 'expired']),
            'featured' => Arr::random([0, 1, 0, 0, 1]),
            'highlight' => rand(0, 1),
            'apply_on' => Arr::random(['app', 'email', 'custom_url', 'app', 'app']),
            'apply_email' => 'templatecookie@gmail.com',
            'apply_url' => 'https://forms.gle/qhUeH3qte7N3rSJ5A',
            'country' => $this->faker->country(),
            'lat' => $this->faker->latitude(-90, 90),
            'long' => $this->faker->longitude(-90, 90),
        ];
    }
}
