<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;

class UsersChart extends LineChartWidget
{
    protected static ?string $heading = '用户统计';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = '2';

    protected function getData(): array
    {
        $users = User::select('created_at')->orderBy('created_at')->get()->groupBy(function ($users) {
            return Carbon::parse($users->created_at)->format('Y-m');
        });
        $quantities = [];
        foreach ($users as $user => $value) {
            $quantities[] = $value->count();
        }
        return [
            'datasets' => [
                [
                    'label'   => '加入时间',
                    'data'    => $quantities,
                    'tension' => 0.3
                ],
            ],
            'labels'   => $users->keys(),
        ];
    }
}
