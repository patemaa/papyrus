<?php

namespace App\Filament\Resources\Notes\Schemas;

use App\Models\Category;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components;
use Filament\Schemas\Components\Group;
use Illuminate\Support\Str;

class NoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Not Bilgileri')
                            ->schema([
                                Components\TextInput::make('title')
                                    ->label('Başlık')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $context, $state, callable $set) {
                                        if ($context === 'create') {
                                            $set('excerpt', Str::limit($state, 100));
                                        }
                                    }),

                                Components\Textarea::make('excerpt')
                                    ->label('Özet')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->helperText('Notun kısa özeti (maksimum 500 karakter)'),

                                Components\RichEditor::make('content')
                                    ->label('İçerik')
                                    ->required()
                                    ->columnSpanFull()
                                    ->fileAttachmentsDirectory('note-attachments')
                                    ->toolbarButtons([
                                        'attachFiles',
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'table',
                                        'undo',
                                    ]),

                                SpatieTagsInput::make('tags')
                                    ->label('Etiketler')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Section::make('Medya')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('featured_image')
                                    ->label('Öne Çıkan Görsel')
                                    ->collection('featured_image')
                                    ->image()
                                    ->imageEditor()
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('1920')
                                    ->imageResizeTargetHeight('1080'),

                                SpatieMediaLibraryFileUpload::make('attachments')
                                    ->label('Ekler')
                                    ->collection('attachments')
                                    ->multiple()
                                    ->reorderable()
                                    ->downloadable()
                                    ->openable()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Durum')
                            ->schema([
                                Components\Select::make('status')
                                    ->label('Durum')
                                    ->options([
                                        'draft' => 'Taslak',
                                        'published' => 'Yayınlandı',
                                        'archived' => 'Arşivlendi',
                                    ])
                                    ->default('draft')
                                    ->required()
                                    ->native(false),

                                Components\Select::make('priority')
                                    ->label('Öncelik')
                                    ->options([
                                        'low' => 'Düşük',
                                        'medium' => 'Orta',
                                        'high' => 'Yüksek',
                                        'urgent' => 'Acil',
                                    ])
                                    ->default('medium')
                                    ->required()
                                    ->native(false),

                                Components\Toggle::make('is_pinned')
                                    ->label('Sabitlenmiş'),

                                Components\Toggle::make('is_favorite')
                                    ->label('Favori'),

                                Components\DateTimePicker::make('published_at')
                                    ->label('Yayın Tarihi')
                                    ->native(false),
                            ]),

                        Section::make('Organizasyon')
                            ->schema([
                                Components\Select::make('category_id')
                                    ->label('Kategori')
                                    ->options(Category::active()->ordered()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Components\TextInput::make('name')
                                            ->label('Kategori Adı')
                                            ->required()
                                            ->maxLength(255),
                                        Components\Textarea::make('description')
                                            ->label('Açıklama')
                                            ->rows(3),
                                        Components\ColorPicker::make('color')
                                            ->label('Renk')
                                            ->default('#6366f1'),
                                    ]),

                                Components\Select::make('user_id')
                                    ->label('Yazar')
                                    ->relationship('user', 'name')
                                    ->default(auth()->id())
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Section::make('Metadata')
                            ->schema([
                                Components\KeyValue::make('metadata')
                                    ->label('Ek Bilgiler')
                                    ->keyLabel('Anahtar')
                                    ->valueLabel('Değer')
                                    ->reorderable(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
