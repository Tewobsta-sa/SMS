<?php

namespace App\Filament\Resources\Parents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ParentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('user.name')->label('Parent')->searchable()->sortable(),
                TextColumn::make('user.email')->label('Email')->searchable(),
                TextColumn::make('occupation')->sortable(),
                TextColumn::make('relation')->badge()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
