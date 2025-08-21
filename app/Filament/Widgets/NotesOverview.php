<?php
// app/Filament/Widgets/NotesOverview.php

namespace App\Filament\Widgets;

use App\Models\Note;
use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NotesOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalNotes = Note::count();
        $publishedNotes = Note::where('status', 'published')->count();
        $draftNotes = Note::where('status', 'draft')->count();
        $pinnedNotes = Note::where('is_pinned', true)->count();
        $categories = Category::where('is_active', true)->count();

        return [
            Stat::make('Toplam Notlar', $totalNotes)
                ->description('Tüm notların toplam sayısı')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Yayınlanan', $publishedNotes)
                ->description('Yayınlanmış notlar')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([3, 3, 1, 2, 2, 4, 3, 1]),

            Stat::make('Taslaklar', $draftNotes)
                ->description('Taslak durumundaki notlar')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('warning')
                ->chart([1, 2, 3, 1, 2, 1, 2, 3]),

            Stat::make('Sabitlenmiş', $pinnedNotes)
                ->description('Sabitlenmiş notlar')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),

            Stat::make('Kategoriler', $categories)
                ->description('Aktif kategoriler')
                ->descriptionIcon('heroicon-m-folder')
                ->color('gray'),
        ];
    }
}
