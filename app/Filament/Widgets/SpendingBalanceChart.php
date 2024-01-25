<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SpendingBalanceChart extends ChartWidget
{
    protected static ?string $heading = 'Transaçoes realizadas por Mês';

    protected static ?string $pollingInterval = '15s';

    protected static string $color = 'info';

    protected function getData(): array
    {
        $query = Transaction::where('user_id', auth()->user()->id);

        if ($this->filter !== null && $this->filter !== '') {
            $query = Transaction::where('type', $this->filter)->where('user_id', auth()->user()->id);
        }

        $data = Trend::query($query)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Transações realizadas por mês',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            '' => 'Todos',
            'expense' => 'Gastos',
            'income' => 'Ganhos',
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public static function canView(): bool
    {
        return auth()->user()->is_admin == 0;
    }
}
