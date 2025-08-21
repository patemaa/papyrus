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
                        Section::make('Note Information')
                            ->schema([
                                Components\TextInput::make('title')
                                    ->label('Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $context, $state, callable $set) {
                                        if ($context === 'create') {
                                            $set('excerpt', Str::limit($state, 100));
                                        }
                                    }),

                                Components\Textarea::make('excerpt')
                                    ->label('Summary')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->helperText('Note Summary (max 500 characters)'),

                                Components\RichEditor::make('content')
                                    ->label('Content')
                                    ->required()
                                    ->columnSpanFull()
                                    ->fileAttachmentsDirectory('note-attachments')
                                    ->toolbarButtons([
                                        [
                                            'h1',
                                            'h2',
                                            'h3',
                                            'bold',
                                            'italic',
                                            'underline',
                                            'strike',
                                            'highlight',
                                            'superscript',
                                            'subscript',
                                            'code',
                                            'bulletList',
                                            'orderedList',
                                            'table',
                                            'link',
                                            'attachFiles',
                                            'redo',
                                            'undo',
                                            'clearFormatting',
                                        ]
                                    ]),

                                SpatieTagsInput::make('tags')
                                    ->label('Tags')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Section::make('Medya')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('featured_image')
                                    ->label('Featured Image')
                                    ->collection('featured_image')
                                    ->image()
                                    ->imageEditor()
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('1920')
                                    ->imageResizeTargetHeight('1080'),

                                SpatieMediaLibraryFileUpload::make('attachments')
                                    ->label('Attachments')
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
                        Section::make('Status')
                            ->schema([
                                Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                        'archived' => 'Arvhived',
                                    ])
                                    ->default('draft')
                                    ->required()
                                    ->native(false),

                                Components\Select::make('priority')
                                    ->label('Priority')
                                    ->options([
                                        'low' => 'Low',
                                        'medium' => 'Medium',
                                        'high' => 'High',
                                        'urgent' => 'Urgent',
                                    ])
                                    ->default('medium')
                                    ->required()
                                    ->native(false),

                                Components\Toggle::make('is_pinned')
                                    ->label('Pinned'),

                                Components\Toggle::make('is_favorite')
                                    ->label('Favorite'),

                                Components\DateTimePicker::make('published_at')
                                    ->label('Published At')
                                    ->native(false),
                            ]),

                        Section::make('Organisation')
                            ->schema([
                                Components\Select::make('category_id')
                                    ->label('Category')
                                    ->options(Category::active()->ordered()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Components\TextInput::make('name')
                                            ->label('Category Name')
                                            ->required()
                                            ->maxLength(255),
                                        Components\Textarea::make('description')
                                            ->label('Description')
                                            ->rows(3),
                                        Components\ColorPicker::make('color')
                                            ->label('Color')
                                            ->default('#6366f1'),
                                    ]),

                                Components\Select::make('user_id')
                                    ->label('Author')
                                    ->relationship('user', 'name')
                                    ->default(auth()->id())
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Section::make('Metadata')
                            ->schema([
                                Components\KeyValue::make('metadata')
                                    ->label('Additional Information')
                                    ->keyLabel('Key')
                                    ->valueLabel('Value')
                                    ->reorderable(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
