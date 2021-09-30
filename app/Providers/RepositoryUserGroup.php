<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryUserGroup extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
        'App\Repositories\Admin\UserGroup\UserGroupInterface',
        'App\Repositories\Admin\UserGroup\UserGroupAbstract');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
