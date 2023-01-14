<?php

namespace App\Providers;

use App\Filament\Pages\Profile;
use App\Filament\Resources\PermissionResource;
use App\Filament\Resources\RoleResource;
use App\Filament\Resources\UserResource;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Illuminate\Foundation\Vite;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Filament::serving(function () {
            Filament::registerTheme(
                app(Vite::class)('resources/css/filament.css'),
            );

            if (auth()->user() && auth()->user()->is_admin === 1) {
                Filament::registerUserMenuItems([
                    UserMenuItem::make()
                        ->label('个人设置')
                        ->url(Profile::getUrl())
                        ->icon('heroicon-s-user'),
                ]);
            }
        });
    }
}
