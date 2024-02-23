<?php

namespace App\Providers;

use App\Filament\Pages\Profile;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
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
            if (auth()->user() && auth()->user()->is_admin === 1) {
                Filament::registerUserMenuItems([
                    MenuItem::make()
                        ->label('个人设置')
                        ->url(Profile::getUrl())
                        ->icon('heroicon-s-user'),
                ]);
            }
        });

        Table::configureUsing(function (Table $table): void {
            $table
                ->filtersLayout(FiltersLayout::AboveContentCollapsible)
                ->paginationPageOptions([10, 25, 50])->striped();
        });
    }
}
