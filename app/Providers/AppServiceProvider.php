<?php

namespace App\Providers;

use App\Contracts\TimeRecordRepositoryInterface;
use App\Repositories\TimeRecordRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the TimeRecordRepositoryInterface to the TimeRecordRepository
        $this->app->bind(TimeRecordRepositoryInterface::class, TimeRecordRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
