<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Notes\Pages;
use App\Filament\Resources\Notes\Schemas\NoteForm;
use App\Filament\Resources\Notes\Schemas\NoteInfolist;
use App\Filament\Resources\Notes\Tables\NotesTable;
use App\Models\Note;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Infolists;
class NoteResource extends Resource
{

    protected static ?string $model = Note::class;
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Notes';
    protected static ?string $modelLabel = 'Note';
    protected static ?string $pluralModelLabel = 'Notes';
    protected static ?int $navigationSort = 1;
    protected static string|null|\UnitEnum $navigationGroup = 'Content Management';


    public static function form(Schema $schema): Schema
    {
        return NoteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NotesTable::configure($table);
    }
    public static function infolist(Schema $schema): Schema
    {
        return NoteInfolist::configure($schema);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotes::route('/'),
            'create' => Pages\CreateNote::route('/create'),
            'view' => Pages\ViewNote::route('/{record}'),
            'edit' => Pages\EditNote::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }
}
