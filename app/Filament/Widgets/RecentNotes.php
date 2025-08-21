<?php

namespace App\Filament\Widgets;

use App\Models\Note;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentNotes extends BaseWidget
{
    protected static ?string $heading = 'Son Notlar';
    protected int | string | array $columnSpan = 'full';
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
                    ->label('Başlık')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Durum')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'published',
                        'warning' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Taslak',
                        'published' => 'Yayınlandı',
                        'archived' => 'Arşivlendi',
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Yazar'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->since(),
            ])
            ->actions([
                Action::make('view')
                    ->label('Görüntüle')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Note $record): string => route('filament.admin.resources.notes.view', $record)),
            ]);
    }
}
