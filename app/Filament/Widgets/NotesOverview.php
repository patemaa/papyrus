<?php

namespace App\Filament\Widgets;

use App\Models\Note;
use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NotesOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 2;
    protected function getStats(): array
    {
        $totalNotes = Note::count();
        $publishedNotes = Note::where('status', 'published')->count();
        $draftNotes = Note::where('status', 'draft')->count();
        $pinnedNotes = Note::where('is_pinned', true)->count();
        $categories = Category::where('is_active', true)->count();

        return [
            Stat::make('All Notes', $totalNotes)
                ->description('Note Count')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Published', $publishedNotes)
                ->description('Published Notes')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([3, 3, 1, 2, 2, 4, 3, 1]),

            Stat::make('Draft', $draftNotes)
                ->description('Draft Notes')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('warning')
                ->chart([1, 2, 3, 1, 2, 1, 2, 3]),

            Stat::make('Pinned', $pinnedNotes)
                ->description('Pinned Notes')
                ->descriptionIcon('heroicon-m-paper-clip')
                ->color('info'),

            Stat::make('Categories', $categories)
                ->description('Active Categories')
                ->descriptionIcon('heroicon-m-folder')
                ->color('gray'),
        ];
    }
}
