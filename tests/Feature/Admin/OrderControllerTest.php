<?php

use App\Models\Admin;
use App\Models\Company;
use Database\Seeders\CompanySeeder;
use Database\Seeders\EarningSeeder;
use Database\Seeders\ExperienceSeeder;
use Database\Seeders\IndustryTypeSeeder;
use Database\Seeders\JobRoleSeeder;
use Database\Seeders\ManualPaymentSeeder;
use Database\Seeders\OrganizationTypeSeeder;
use Database\Seeders\ProfessionSeeder;

beforeEach(function () {
    $this->admin = createAdmin();
    $this->seed([
        ManualPaymentSeeder::class,
        IndustryTypeSeeder::class,
        OrganizationTypeSeeder::class,
        JobRoleSeeder::class,
        ProfessionSeeder::class,
        ExperienceSeeder::class,
        CompanySeeder::class,
        EarningSeeder::class,
    ]);
    actingAs($this->admin, 'admin');
});

it('admin visit order list page', function () {

    $response = $this->get(route('order.index'));
    $response->assertStatus(200);

    // Assert that the view being returned is 'admin.order.index'
    $response->assertViewIs('backend.order.index');

    // Assert that the view has specific data variables: 'orders', 'companies', 'plans'
    $response->assertViewHas(['orders', 'companies', 'plans']);
});

it('admin visit order details page', function () {

    $response = $this->get(route('order.show', 1));
    $response->assertStatus(200);

    // Assert that the view being returned is 'admin.company.show'
    $response->assertViewIs('backend.order.show');

    // Assert that the view has specific data variables: 'order'
    $response->assertViewHas('order');
});
