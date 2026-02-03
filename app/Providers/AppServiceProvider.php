<?php

namespace App\Providers;

use App\Services\Llm\FakeLlmProvider;
use App\Services\Llm\LlmProviderInterface;
use App\Services\Stt\FakeSttProvider;
use App\Services\Stt\SttProviderInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SttProviderInterface::class, FakeSttProvider::class, LlmProviderInterface::class, FakeLlmProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
