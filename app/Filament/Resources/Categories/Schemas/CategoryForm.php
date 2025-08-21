<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Category Information')
                    ->schema([
                        Components\TextInput::make('name')
                            ->label('Category Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(string $context, $state, callable $set) => $context === 'create' ? $set('slug', Str::slug($state)) : null
                            ),

                        Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Category::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash']),

                        Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Components\ColorPicker::make('color')
                            ->label('Color')
                            ->default('#6366f1'),

                        Components\TextInput::make('icon')
                            ->label('Icon')
                            ->helperText('Heroicon icon adı (örn: document-text)'),

                        Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(2),
            ]);
    }
}

