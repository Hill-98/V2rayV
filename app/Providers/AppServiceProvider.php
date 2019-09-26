<?php

namespace App\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        if (!$this->app->environment('production')) {
            $this->app->register(IdeHelperServiceProvider::class);
        }
        $this->app->bind(\App\Models\DataFilter::class, static function (Container $app) {
            $model = $app->make(\App\Helper\Router::class)->getFilter($app->make(\Illuminate\Http\Request::class));
            return $model;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Response::macro('result', function (bool $success, $code = 0, string $message = '', $data = null) {
            /** @var \Illuminate\Routing\ResponseFactory $this */
            return $this->json([
                'success' => $success,
                'code' => $code,
                'message' => $message,
                'data' => $data
            ]);
        });
    }
}
