<?php

namespace App\Filament\Pages;

use App\Models\Note;
use App\Models\Category;
use Filament\Pages\Page;

class Analytics extends Page
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-chart-bar';
    protected string $view = 'filament.pages.analytics';
    protected static ?string $navigationLabel = 'Analitik';
    protected static ?int $navigationSort = 10;
    protected static string|null|\UnitEnum $navigationGroup = 'Raporlar';

    public function getViewData(): array
    {
        return [
            'totalNotes' => Note::count(),
            'publishedNotes' => Note::where('status', 'published')->count(),
            'draftNotes' => Note::where('status', 'draft')->count(),
            'categoriesCount' => Category::count(),
            'averageWordsPerNote' => Note::get()
                    ->map(fn($note) => str_word_count(strip_tags($note->content)))
                    ->avg() ?? 0,            'topCategories' => Category::withCount('notes')
                ->orderByDesc('notes_count')
                ->limit(5)
                ->get(),
            'recentActivity' => Note::with(['user', 'category'])
                ->latest()
                ->limit(10)
                ->get(),
        ];
    }
}
