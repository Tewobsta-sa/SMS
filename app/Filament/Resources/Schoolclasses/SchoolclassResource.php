<?php

namespace App\Filament\Resources\Schoolclasses;

use App\Filament\Resources\Schoolclasses\Pages\CreateSchoolclass;
use App\Filament\Resources\Schoolclasses\Pages\EditSchoolclass;
use App\Filament\Resources\Schoolclasses\Pages\ListSchoolclasses;
use App\Filament\Resources\Schoolclasses\Schemas\SchoolclassForm;
use App\Filament\Resources\Schoolclasses\Tables\SchoolclassesTable;
use App\Models\Schoolclass;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SchoolclassResource extends Resource
{
    protected static ?string $model = Schoolclass::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'SchoolClass';

    public static function form(Schema $schema): Schema
    {
        return SchoolclassForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchoolclassesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchoolclasses::route('/'),
            'create' => CreateSchoolclass::route('/create'),
            'edit' => EditSchoolclass::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
