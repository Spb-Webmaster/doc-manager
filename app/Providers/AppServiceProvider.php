<?php

namespace App\Providers;

use App\Models\Act;
use App\Models\Invoice;
use App\Observers\ActObserver;
use App\Observers\InvoiceObserver;
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
        // Наблюдатели для автоматической генерации и удаления PDF
        Invoice::observe(InvoiceObserver::class);
        Act::observe(ActObserver::class);

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
