<?php

namespace App\Filament\Resources\Pages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PagesTable {
    public static function configure( Table $table ): Table {
        return $table
        ->columns( [
            \Filament\Tables\Columns\TextColumn::make( 'title' )->searchable()->sortable(),
            \Filament\Tables\Columns\TextColumn::make( 'slug' )->sortable(),
            \Filament\Tables\Columns\TextColumn::make( 'display_order' )->sortable(),
            \Filament\Tables\Columns\IconColumn::make( 'is_active' )->boolean(),
            \Filament\Tables\Columns\TextColumn::make( 'created_at' )->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            \Filament\Tables\Columns\TextColumn::make( 'updated_at' )->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            \Filament\Tables\Columns\TextColumn::make( 'deleted_at' )
                ->dateTime()
                ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
        ] )
        ->filters( [
            TrashedFilter::make(),
        ] )
        ->recordActions( [
            EditAction::make(),
        ] )
        ->toolbarActions( [
            BulkActionGroup::make( [
                DeleteBulkAction::make(),
                ForceDeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ] ),
        ] );
    }
}
