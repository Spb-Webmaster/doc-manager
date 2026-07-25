<?php

namespace App\Providers;

use App\Models\Act;
use App\Models\Invoice;
use App\Observers\DocumentPdfObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // Наблюдатель для автоматической генерации и удаления PDF документов
        Invoice::observe(DocumentPdfObserver::class);
        Act::observe(DocumentPdfObserver::class);

        Password::defaults(function () {
            return Password::min(5)
                /*      ->letters()
                      ->numbers()
                      ->symbols()
                      ->mixedCase()
                      ->uncompromised()*/;
        });
    }
}
