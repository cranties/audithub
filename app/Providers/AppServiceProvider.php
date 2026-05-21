<?php

namespace App\Providers;

use App\Models\Survey;
use App\Policies\SurveyPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Survey::class, SurveyPolicy::class);
    }
}

