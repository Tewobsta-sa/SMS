<?php

namespace App\Filament\Resources\Permissions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class PermissionsTable {
    public static function configure( Table $table ): Table {
        return $table
        ->columns( [
            \Filament\Tables\Columns\TextColumn::make( 'id' )->sortable(),
            \Filament\Tables\Columns\TextColumn::make( 'name' )->searchable()->sortable(),
            \Filament\Tables\Columns\TextColumn::make( 'guard_name' )->sortable(),
            \Filament\Tables\Columns\TextColumn::make( 'roles.name' )
            ->label( 'Roles' )
            ->badge()
            ->separator( ', ' )
            ->limit( 3 ),
            \Filament\Tables\Columns\TextColumn::make( 'created_at' )->dateTime()->sortable(),
        ] )
        ->filters( [
            \Filament\Tables\Filters\SelectFilter::make( 'roles' )
            ->label( 'Role' )
            ->relationship( 'roles', 'name' )
            ->multiple(),
        ] )
        ->recordActions( [
            ViewAction::make(),
            EditAction::make(),
        ] )
        ->toolbarActions( [
            BulkActionGroup::make( [
                DeleteBulkAction::make(),
            ] ),
        ] );
    }
}
