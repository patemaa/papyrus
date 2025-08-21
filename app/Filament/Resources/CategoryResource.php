<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Categories\Schemas\CategoryForm;
use App\Filament\Resources\Categories\Tables\CategoriesTable;
use App\Filament\Resources\Categories\Pages;
use App\Models\Category;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationLabel = 'Categories';
    protected static ?string $modelLabel = 'Category';
    protected static ?string $pluralModelLabel = 'Categories';
    protected static ?int $navigationSort = 2;
    protected static string|null|\UnitEnum $navigationGroup = 'Content Management';

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }
    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
