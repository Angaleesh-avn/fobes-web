<?php

use App\Models\Admin;
use App\Models\User;
use Database\Seeders\ImportTestingTableSeeder;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)
    ->beforeEach(function () {
        $this->seed(ImportTestingTableSeeder::class);

        //share view file global variable for testing
        view()->share('setting', loadSetting());
        view()->share('cms_setting', loadCms());
        view()->share('cookies', loadCookies());
        view()->share('currency_symbol', config('jobpilot.currency_symbol'));
        view()->share('languages', loadLanguage());
        $this->withoutVite();
    })
    ->in('Feature');

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

function createUser(string $role = 'company'): User
{
    return User::factory()->create([
        'role' => $role,
    ]);
}

function createAdmin(): Admin
{
    $admin = Admin::factory()->create();
    $roleSuperAdmin = Role::first();
    $admin->assignRole($roleSuperAdmin);

    return $admin;
}

function actingAs(Authenticatable $user, string $driver = null)
{
    return test()->actingAs($user, $driver);
}
