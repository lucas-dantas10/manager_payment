<?php

namespace App\Filament\Widgets;

use App\Models\Balance;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{

    protected static ?int $sort = -2;


    protected function getStats(): array
    {
        return [
            Stat::make('Usuários Total', User::count())
                ->icon('heroicon-o-users')
                ->description('Total de usuários'),

            Stat::make('Meu Saldo', Balance::where('user_id', auth()->user()->id)->first()->total_amount)
                ->icon('heroicon-o-credit-card')
                ->description('Total valor do saldo'),

            Stat::make('Total Transações', Transaction::count())
                ->icon('heroicon-o-building-library')
                ->description('Total de transações realizadas'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->is_admin;
    }
}
