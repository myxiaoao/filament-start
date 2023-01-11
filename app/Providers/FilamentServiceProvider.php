<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Foundation\Vite;
use Filament\Navigation\UserMenuItem;
use Illuminate\Support\ServiceProvider;
use App\Filament\Resources\RoleResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\PermissionResource;

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

            if (auth()->user()
                && auth()->user()->is_admin === 1
                && auth()->user()->hasAnyRole(['super-admin', 'admin'])) {
                Filament::registerUserMenuItems([
                    UserMenuItem::make()
                        ->label('用户管理')
                        ->url(UserResource::getUrl())
                        ->icon('heroicon-s-users'),
                    UserMenuItem::make()
                        ->label('角色管理')
                        ->url(RoleResource::getUrl())
                        ->icon('heroicon-s-cog'),
                    UserMenuItem::make()
                        ->label('权限管理')
                        ->url(PermissionResource::getUrl())
                        ->icon('heroicon-s-key'),
                ]);
            }
        });
    }
}
