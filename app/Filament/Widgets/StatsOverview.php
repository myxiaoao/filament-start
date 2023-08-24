<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = '2';

    protected function getCards(): array
    {
        return [
            Card::make('流量统计', '192.1k')
                ->description('32k increase')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Card::make('转换比例', '21%')
                ->description('7% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-down'),
            Card::make('平均在线', '3:12')
                ->description('3% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }
}
