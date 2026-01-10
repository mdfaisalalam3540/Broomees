<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\ReputationService::class);
        $this->app->singleton(\App\Services\RelationshipService::class);
    }

    public function boot(): void
    {
        Route::aliasMiddleware('api.ratelimit', \App\Http\Middleware\ApiRateLimiter::class);
        Route::aliasMiddleware('auth.api', \App\Http\Middleware\AuthenticateApiToken::class);
        Route::aliasMiddleware('optimistic.lock', \App\Http\Middleware\OptimisticLocking::class);

        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $method = $request->method();

            if (in_array($method, ['GET', 'HEAD'])) {
                return Limit::perMinute(120)
                    ->by($request->bearerToken() ?: $request->ip());
            }

            return Limit::perMinute(30)
                ->by($request->bearerToken() ?: $request->ip());
        });
    }
}
