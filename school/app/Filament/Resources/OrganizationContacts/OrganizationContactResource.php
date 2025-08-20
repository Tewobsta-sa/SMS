<?php

namespace App\Filament\Resources\OrganizationContacts;

use App\Filament\Resources\OrganizationContacts\Pages\CreateOrganizationContact;
use App\Filament\Resources\OrganizationContacts\Pages\EditOrganizationContact;
use App\Filament\Resources\OrganizationContacts\Pages\ListOrganizationContacts;
use App\Filament\Resources\OrganizationContacts\Schemas\OrganizationContactForm;
use App\Filament\Resources\OrganizationContacts\Tables\OrganizationContactsTable;
use App\Models\OrganizationContact;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrganizationContactResource extends Resource
{
    protected static ?string $model = OrganizationContact::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'OrganizationContact';

    public static function form(Schema $schema): Schema
    {
        return OrganizationContactForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrganizationContactsTable::configure($table);
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
            'index' => ListOrganizationContacts::route('/'),
            'create' => CreateOrganizationContact::route('/create'),
            'edit' => EditOrganizationContact::route('/{record}/edit'),
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
