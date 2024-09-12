<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\AlertRepositoryInterface;
use App\Repositories\Implementations\AlertRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AlertRepositoryInterface::class, AlertRepository::class);
    }
}
