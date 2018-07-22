<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Contracts\IEmployeeService',
            'App\Services\EmployeeService'
        );
        $this->app->bind(
            'App\Contracts\IUserService',
            'App\Services\UserService'
        );
        $this->app->bind(
            'App\Contracts\IUserRoleService',
            'App\Services\UserRoleService'
        );
        $this->app->bind(
            'App\Contracts\IDepartmentService',
            'App\Services\DepartmentService'
        );
        $this->app->bind(
            'App\Contracts\ICategoryService',
            'App\Services\CategoryService'
        );
    }
}
