<?php

namespace App\Filament\Widgets;

use App\Models\Note;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class NotesChart extends ChartWidget
{
    protected ?string $heading = 'Not Oluşturma Trendi';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $points = Trend::model(Note::class)
            ->between(
                start: now()->startOfMonth()->subMonths(11),
                end: now()->endOfMonth(),
            )
            ->perMonth()
            ->count();

        $labels = $points
            ->map(fn (TrendValue $v) => Carbon::parse($v->date)->isoFormat('MMM YYYY'))
            ->all();

        $values = $points
            ->map(fn (TrendValue $v) => (int) $v->aggregate)
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'Oluşturulan Notlar',
                    'data' => $values,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
