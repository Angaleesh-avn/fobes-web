<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobRole;
use App\Models\JobType;
use App\Models\SalaryType;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\Location\Entities\Country;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Job::factory(50)->create();
        $faker = Factory::create();

        $job_list = json_decode(file_get_contents(base_path('resources/backend/dummy-data/jobs.json')), true);

        for ($i = 0; $i < count($job_list); $i++) {
            $job_data[] = [
                'title' => $job_list[$i]['title'],
                'slug' => Str::slug($job_list[$i]['title']).'_'.time().'_'.rand(1111111111, 9999999999),
                'company_id' => Company::inRandomOrder()->value('id'),
                'category_id' => JobCategory::inRandomOrder()->value('id'),
                'role_id' => JobRole::inRandomOrder()->value('id'),
                'experience_id' => Experience::inRandomOrder()->value('id'),
                'education_id' => Education::inRandomOrder()->value('id'),
                'job_type_id' => JobType::inRandomOrder()->value('id'),
                'salary_type_id' => SalaryType::inRandomOrder()->value('id'),
                'vacancies' => $faker->randomElement(['1-2', '2-3', '3-5', '5-10', '10-20']),
                'min_salary' => $job_list[$i]['min_salary'],
                'max_salary' => $job_list[$i]['max_salary'],
                'salary_mode' => Arr::random(['range', 'custom']),
                'custom_salary' => 'Competitive',
                'deadline' => $faker->dateTimeBetween('now', '+07 days'),
                'description' => $this->getDescription($job_list[$i]['title']),
                'is_remote' => $job_list[$i]['is_remote'],
                'status' => 'active',
                'featured' => Arr::random([0, 1, 0, 0, 1]),
                'highlight' => rand(0, 1),
                'apply_on' => Arr::random(['app', 'email', 'custom_url', 'app', 'app', 'app', 'app', 'app']),
                'apply_email' => 'templatecookie@gmail.com',
                'apply_url' => 'https://forms.gle/qhUeH3qte7N3rSJ5A',
                'country' => $faker->country(),
                'lat' => $faker->latitude(-90, 90),
                'long' => $faker->longitude(-90, 90),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $job_chunks = array_chunk($job_data, ceil(count($job_data) / 3));

        foreach ($job_chunks as $product) {
            Job::insert($product);
        }

        Job::factory(50)->create();

        // Every country has jobs => 5
        $this->everyCountryJobs();
    }

    public function everyCountryJobs()
    {
        $job_list = json_decode(file_get_contents(base_path('resources/backend/dummy-data/jobs.json')), true);
        $faker = Factory::create();

        $countries = Country::active()->get();
        foreach ($countries as $key => $country) {

            for ($i = 0; $i < 5; $i++) {
                $product1 = [
                    'title' => $job_list[$i]['title'],
                    'slug' => Str::slug($job_list[$i]['title']).'_'.time().'_'.rand(1111111111, 9999999999),
                    'company_id' => Company::inRandomOrder()->value('id'),
                    'category_id' => JobCategory::inRandomOrder()->value('id'),
                    'role_id' => JobRole::inRandomOrder()->value('id'),
                    'experience_id' => Experience::inRandomOrder()->value('id'),
                    'education_id' => Education::inRandomOrder()->value('id'),
                    'job_type_id' => JobType::inRandomOrder()->value('id'),
                    'salary_type_id' => SalaryType::inRandomOrder()->value('id'),
                    'vacancies' => $faker->randomElement(['1-2', '2-3', '3-5', '5-10', '10-20']),
                    'min_salary' => $job_list[$i]['min_salary'],
                    'max_salary' => $job_list[$i]['max_salary'],
                    'salary_mode' => Arr::random(['custom', 'range']),
                    'custom_salary' => 'Competitive',
                    'deadline' => $faker->dateTimeBetween('now', '+07 days'),
                    'description' => $job_list[$i]['description'],
                    'is_remote' => $job_list[$i]['is_remote'],
                    'status' => 'active',
                    'featured' => rand(0, 1),
                    'highlight' => rand(0, 1),
                    'apply_on' => Arr::random(['app', 'email', 'custom_url', 'app', 'app']),
                    'apply_email' => 'templatecookie@gmail.com',
                    'apply_url' => 'https://forms.gle/qhUeH3qte7N3rSJ5A',
                    'country' => $country->name,
                    'lat' => $country->lat,
                    'long' => $country->long,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                Job::insert($product1);
            }
        }
    }

    public function getDescription($title)
    {
        return "<h2>Job Summary:</h2>
        <p>Are you an exceptional individual with a diverse skill set and the ability to adapt to various roles? If so, we have an
          exciting opportunity for you! We are seeking a {$title} to join our dynamic team and take on a wide
          range of responsibilities across our organization. As a {$title}, you will wear many hats and
          contribute to different areas of our business, ensuring its smooth functioning and continued success.</p>

        <h2>Key Responsibilities:</h2>
        <ol>
          <li>
            <strong>Project Management:</strong> Lead and manage projects from inception to completion, ensuring timelines,
            budgets, and objectives are met. Utilize your organizational skills and attention to detail to deliver successful
            outcomes.
          </li>
          <li>
            <strong>Marketing Specialist:</strong> Develop and implement marketing strategies, create engaging content, and
            oversee social media campaigns to promote our products and services effectively.
          </li>
          <li>
            <strong>Customer Support:</strong> Provide excellent customer service by addressing inquiries, resolving issues, and
            maintaining a positive relationship with our clients.
          </li>
          <li>
            <strong>Data Analysis:</strong> Analyze data to identify trends, make informed business decisions, and provide
            insights to various teams.
          </li>
          <li>
            <strong>Graphic Design:</strong> Utilize your artistic flair to create visually appealing graphics, logos, and
            promotional materials for both digital and print platforms.
          </li>
          <li>
            <strong>Sales Representative:</strong> Drive sales by identifying potential clients, presenting product offerings,
            and negotiating contracts to secure new business opportunities.
          </li>
          <li>
            <strong>Human Resources:</strong> Assist with recruitment efforts, conduct interviews, and support employee onboarding
            and development initiatives.
          </li>
        </ol>";
    }
}
