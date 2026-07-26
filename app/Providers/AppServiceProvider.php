<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('access-admin', function ($user) {
            return $user->hasRole('admin');
        });

        $templatePath = storage_path('app/templates');
        if (!is_dir($templatePath)) {
            mkdir($templatePath, 0755, true);
        }
        $this->app['view']->addNamespace('pdf-templates', $templatePath);
    }
}
