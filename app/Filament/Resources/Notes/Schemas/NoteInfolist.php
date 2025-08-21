<?php

namespace App\Filament\Resources\Notes\Schemas;

use App\Models\Note;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components;
use Filament\Schemas\Components\Section;

class NoteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Group::make([
                    Section::make('Note Details')
                        ->schema([
                            Components\TextEntry::make('title')
                                ->label('Title')
                                ->weight(FontWeight::Bold),

                            Components\TextEntry::make('excerpt')
                                ->label('Summary')
                                ->prose(),

                            Components\TextEntry::make('content')
                                ->label('Content')
                                ->html()
                                ->prose()
                                ->columnSpanFull(),
                        ])
                        ->columns(2),

                    Section::make('Statistics')
                        ->schema([
                            Components\TextEntry::make('word_count')
                                ->label('Word Count'),

                            Components\TextEntry::make('reading_time')
                                ->label('Reading Time'),
                        ])
                        ->columns(2),
                ])
                    ->columnSpan(2),

                Group::make([
                    Section::make('Status and Characteristics')
                        ->schema([
                            Components\TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'draft' => 'secondary',
                                    'published' => 'success',
                                    'archived' => 'warning',
                                })
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                    'archived' => 'Archived',
                                }),

                            Components\TextEntry::make('priority')
                                ->label('Priority')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'low' => 'success',
                                    'medium' => 'warning',
                                    'high' => 'danger',
                                    'urgent' => 'primary',
                                })
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'low' => 'Low',
                                    'medium' => 'Medium',
                                    'high' => 'High',
                                    'urgent' => 'Urgent',
                                }),

                            Components\IconEntry::make('is_pinned')
                                ->label('Pin')
                                ->boolean()
                                ->trueIcon('heroicon-o-academic-cap')
                                ->trueColor('warning'),

                            Components\IconEntry::make('is_favorite')
                                ->label('Favorite')
                                ->boolean()
                                ->trueIcon('heroicon-o-heart')
                                ->trueColor('danger'),
                        ]),

                    Section::make('Relations')
                        ->schema([
                            Components\TextEntry::make('user.name')
                                ->label('Author'),

                            Components\TextEntry::make('category.name')
                                ->label('Category')
                                ->badge()
                                ->color(fn (Note $record): string => $record->category?->color ?? 'gray'),
                        ]),

                    Section::make('Dates')
                        ->schema([
                            Components\TextEntry::make('published_at')
                                ->label('Published At')
                                ->dateTime('d.m.Y H:i'),

                            Components\TextEntry::make('created_at')
                                ->label('Created At')
                                ->dateTime('d.m.Y H:i'),

                            Components\TextEntry::make('updated_at')
                                ->label('Updated At')
                                ->dateTime('d.m.Y H:i'),
                        ]),
                ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }
}
