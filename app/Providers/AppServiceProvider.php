<?php

namespace App\Providers;

use App\Services\Llm\FakeLlmProvider;
use App\Services\Llm\GeminiLlmProvider;
use App\Services\Llm\LlmProviderInterface;
use App\Services\Stt\FakeSttProvider;
use App\Services\Stt\SttProviderInterface;
use App\Services\Stt\WhisperSttProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SttProviderInterface::class, WhisperSttProvider::class);
        $this->app->singleton(LlmProviderInterface::class, GeminiLlmProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
