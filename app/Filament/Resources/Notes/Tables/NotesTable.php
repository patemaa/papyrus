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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class NotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('featured_image')
                    ->label('Featured Image')
                    ->collection('featured_image')
                    ->conversion('thumb')
                    ->size(60)
                    ->alignCenter()
                    ->circular(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->alignCenter()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Note $record): string => Str::limit($record->excerpt ?? '', 50)),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->alignCenter()
                    ->colors([
                        'yellow' => 'draft',
                        'green' => 'published',
                        'purple' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    }),

                Tables\Columns\BadgeColumn::make('priority')
                    ->label('Priority')
                    ->alignCenter()
                    ->colors([
                        'gray' => 'low',
                        'blue' => 'medium',
                        'orange' => 'high',
                        'red' => 'urgent',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    }),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->alignCenter()
                    ->sortable()
                    ->formatStateUsing(function (Note $record) {
                        $color = $record->category?->color ?? '#6b7280';
                        $record->category->name = Str::limit($record->category?->name ?? '', 10);
                        return "<span style='background-color: {$color}; color: #fff; padding: 4px 8px; border-radius: 4px;'>{$record->category?->name}</span>";
                    })
                    ->html(),

                Tables\Columns\IconColumn::make('is_pinned')
                    ->label('Pinned')
                    ->alignCenter()
                    ->boolean()
                    ->trueIcon('heroicon-s-paper-clip')
                    ->falseIcon('heroicon-o-paper-clip')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('is_favorite')
                    ->label('Favorited')
                    ->alignCenter()
                    ->boolean()
                    ->trueIcon('heroicon-s-heart')
                    ->falseIcon('heroicon-o-heart')
                    ->trueColor('pink')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->alignCenter()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('word_count')
                    ->label('Word Count')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reading_time')
                    ->label('Reading Time')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published At')
                    ->alignCenter()
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->alignCenter()
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_pinned')
                    ->label('Pinned')
                    ->query(fn(Builder $query) => $query->where('is_pinned', true))
                    ->columnSpan(1),
                Tables\Filters\Filter::make('is_favorite')
                    ->label('Favorited')
                    ->query(fn(Builder $query) => $query->where('is_favorite', true))
                    ->columnSpan(1),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->multiple()
                    ->columnSpan(1),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('Priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])
                    ->multiple()
                    ->columnSpan(1),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->multiple()
                    ->preload()
                    ->columnSpan(1),

                TrashedFilter::make()
                    ->label('Deleted Records')
                    ->columnSpan(1),

                Tables\Filters\Filter::make('created_at')
                    ->label('Created At')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Started At')
                            ->placeholder('dd.mm.yyyy'),
                        DatePicker::make('created_until')
                            ->label('Ended At')
                            ->placeholder('dd.mm.yyyy'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn($q, $date) => $q->whereDate('created_at', '<=', $date));
                    })
                    ->columnSpan(2),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('toggle_pin')
                        ->label(fn (Note $record): string => $record->is_pinned ? 'Unpin' : 'Pin')
                        ->icon(fn(Note $record): string => $record->is_pinned ? 'heroicon-o-paper-clip' : 'heroicon-s-paper-clip')
                        ->color(fn (Note $record): string => $record->is_pinned ? 'warning' : 'gray')
                        ->action(fn (Note $record) => $record->update(['is_pinned' => !$record->is_pinned])),
                    Action::make('toggle_favorite')
                        ->label(fn(Note $record): string => $record->is_favorite ? 'Remove From Favorites' : 'Add to Favorites')
                        ->icon(fn (Note $record): string => $record->is_favorite ? 'heroicon-s-heart' : 'heroicon-o-heart')
                        ->color(fn (Note $record): string => $record->is_favorite ? 'pink' : 'gray')
                        ->action(fn (Note $record) => $record->update(['is_favorite' => !$record->is_favorite])),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('change_status')
                        ->label('Change Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Select::make('status')
                                ->label('New Status')
                                ->options([
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                    'archived' => 'Archived',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            $records->each->update(['status' => $data['status']]);
                        }),
                    BulkAction::make('change_category')
                        ->label('Change Category')
                        ->icon('heroicon-o-folder')
                        ->form([
                            Select::make('category_id')
                                ->label('New Category')
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
