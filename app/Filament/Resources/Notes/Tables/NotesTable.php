<?php

namespace App\Filament\Resources\Notes\Tables;

use App\Models\Category;
use App\Models\Note;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;

class NotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('featured_image')
                    ->label('Görsel')
                    ->collection('featured_image')
                    ->conversion('thumb')
                    ->size(60)
                    ->circular(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Note $record): string => \Str::limit($record->excerpt ?? '', 50)),

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

                Tables\Columns\BadgeColumn::make('priority')
                    ->label('Öncelik')
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                        'primary' => 'urgent',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Düşük',
                        'medium' => 'Orta',
                        'high' => 'Yüksek',
                        'urgent' => 'Acil',
                    }),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->badge()
                    ->color(fn (Note $record): string => $record->category?->color ?? 'gray'),

                Tables\Columns\IconColumn::make('is_pinned')
                    ->label('Sabitlenmiş')
                    ->boolean()
                    ->trueIcon('heroicon-o-thumb-tack')
                    ->falseIcon('heroicon-o-thumb-tack')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('is_favorite')
                    ->label('Favori')
                    ->boolean()
                    ->trueIcon('heroicon-o-heart')
                    ->falseIcon('heroicon-o-heart')
                    ->trueColor('danger')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Yazar')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('word_count')
                    ->label('Kelime Sayısı')
                    ->alignEnd()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reading_time')
                    ->label('Okuma Süresi')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Yayın Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'draft' => 'Taslak',
                        'published' => 'Yayınlandı',
                        'archived' => 'Arşivlendi',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('Öncelik')
                    ->options([
                        'low' => 'Düşük',
                        'medium' => 'Orta',
                        'high' => 'Yüksek',
                        'urgent' => 'Acil',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->multiple()
                    ->preload(),

                Tables\Filters\Filter::make('is_pinned')
                    ->label('Sabitlenmiş')
                    ->query(fn (Builder $query): Builder => $query->where('is_pinned', true)),

                Tables\Filters\Filter::make('is_favorite')
                    ->label('Favori')
                    ->query(fn (Builder $query): Builder => $query->where('is_favorite', true)),

                Tables\Filters\Filter::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Başlangıç')
                            ->placeholder('dd.mm.yyyy'),
                        DatePicker::make('created_until')
                            ->label('Bitiş')
                            ->placeholder('dd.mm.yyyy'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                TrashedFilter::make()
                    ->label('Silinmiş Kayıtlar'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('toggle_pin')
                        ->label(fn (Note $record): string => $record->is_pinned ? 'Sabitlemeyi Kaldır' : 'Sabitle')
                        ->icon(fn (Note $record): string => $record->is_pinned ? 'heroicon-o-thumb-tack' : 'heroicon-s-thumb-tack')
                        ->color(fn (Note $record): string => $record->is_pinned ? 'warning' : 'gray')
                        ->action(fn (Note $record) => $record->update(['is_pinned' => !$record->is_pinned])),
                    Action::make('toggle_favorite')
                        ->label(fn (Note $record): string => $record->is_favorite ? 'Favorilerden Çıkar' : 'Favorilere Ekle')
                        ->icon(fn (Note $record): string => $record->is_favorite ? 'heroicon-s-heart' : 'heroicon-o-heart')
                        ->color(fn (Note $record): string => $record->is_favorite ? 'danger' : 'gray')
                        ->action(fn (Note $record) => $record->update(['is_favorite' => !$record->is_favorite])),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('change_status')
                        ->label('Durum Değiştir')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Select::make('status')
                                ->label('Yeni Durum')
                                ->options([
                                    'draft' => 'Taslak',
                                    'published' => 'Yayınlandı',
                                    'archived' => 'Arşivlendi',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            $records->each->update(['status' => $data['status']]);
                        }),
                    BulkAction::make('change_category')
                        ->label('Kategori Değiştir')
                        ->icon('heroicon-o-folder')
                        ->form([
                            Select::make('category_id')
                                ->label('Yeni Kategori')
                                ->options(Category::active()->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            $records->each->update(['category_id' => $data['category_id']]);
                        }),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession();
    }
}
