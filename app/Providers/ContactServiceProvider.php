<?php

namespace App\Providers;

use App\Services\ContactService;
use App\Services\Interface\ContactServiceInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ContactServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        ContactServiceInterface::class => ContactService::class
    ];

    public function provides()
    {
        return [ContactService::class];
    }
    
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
