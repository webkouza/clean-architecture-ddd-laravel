<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Book\Repository\BookRepositoryInterface;
use App\Infrastructure\Book\Repository\EloquentBookRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 依存性注入の設定
        // インターフェースと実装をバインドする
        $this->app->bind(
            BookRepositoryInterface::class,
            EloquentBookRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
