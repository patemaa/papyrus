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
                    Section::make('Not Bilgileri')
                        ->schema([
                            Components\TextEntry::make('title')
                                ->label('Başlık')
                                ->weight(FontWeight::Bold),

                            Components\TextEntry::make('excerpt')
                                ->label('Özet')
                                ->prose(),

                            Components\TextEntry::make('content')
                                ->label('İçerik')
                                ->html()
                                ->prose()
                                ->columnSpanFull(),
                        ])
                        ->columns(2),

                    Section::make('İstatistikler')
                        ->schema([
                            Components\TextEntry::make('word_count')
                                ->label('Kelime Sayısı'),

                            Components\TextEntry::make('reading_time')
                                ->label('Okuma Süresi'),
                        ])
                        ->columns(2),
                ])
                    ->columnSpan(2),

                Group::make([
                    Section::make('Durum ve Özellikler')
                        ->schema([
                            Components\TextEntry::make('status')
                                ->label('Durum')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'draft' => 'secondary',
                                    'published' => 'success',
                                    'archived' => 'warning',
                                })
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'draft' => 'Taslak',
                                    'published' => 'Yayınlandı',
                                    'archived' => 'Arşivlendi',
                                }),

                            Components\TextEntry::make('priority')
                                ->label('Öncelik')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'low' => 'success',
                                    'medium' => 'warning',
                                    'high' => 'danger',
                                    'urgent' => 'primary',
                                })
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'low' => 'Düşük',
                                    'medium' => 'Orta',
                                    'high' => 'Yüksek',
                                    'urgent' => 'Acil',
                                }),

                            Components\IconEntry::make('is_pinned')
                                ->label('Sabitlenmiş')
                                ->boolean()
                                ->trueIcon('heroicon-o-thumb-tack')
                                ->trueColor('warning'),

                            Components\IconEntry::make('is_favorite')
                                ->label('Favori')
                                ->boolean()
                                ->trueIcon('heroicon-o-heart')
                                ->trueColor('danger'),
                        ]),

                    Section::make('İlişkiler')
                        ->schema([
                            Components\TextEntry::make('user.name')
                                ->label('Yazar'),

                            Components\TextEntry::make('category.name')
                                ->label('Kategori')
                                ->badge()
                                ->color(fn (Note $record): string => $record->category?->color ?? 'gray'),
                        ]),

                    Section::make('Tarihler')
                        ->schema([
                            Components\TextEntry::make('published_at')
                                ->label('Yayın Tarihi')
                                ->dateTime('d.m.Y H:i'),

                            Components\TextEntry::make('created_at')
                                ->label('Oluşturulma')
                                ->dateTime('d.m.Y H:i'),

                            Components\TextEntry::make('updated_at')
                                ->label('Güncellenme')
                                ->dateTime('d.m.Y H:i'),
                        ]),
                ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }
}
