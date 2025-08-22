<?php

namespace App\Filament\Widgets;

use App\Models\Note;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentNotes extends BaseWidget
{
    protected static ?string $heading = 'Recent Notes';
    protected int | string | array $columnSpan = 2;
    protected static ?int $sort = 3;


    public function table(Table $table): Table
    {
        return $table
            ->query(
                Note::query()
                    ->with(['user', 'category'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'published',
                        'warning' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->since(),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Note $record): string => route('filament.admin.resources.notes.view', $record)),
            ]);
    }
}
