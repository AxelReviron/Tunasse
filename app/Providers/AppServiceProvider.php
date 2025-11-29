<?php

namespace App\Providers;

use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\ServiceProvider;

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
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales([
                    'fr',
                    'en',
                ])
                ->flags([
                    'fr' => asset('images/flags/fr.svg'),
                    'en' => asset('images/flags/en.svg'),
                ]);
        });
    }
}
