<?php

namespace App\Filament\Resources\Teachers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeachersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('user.name')->label('Teacher')->searchable()->sortable(),
                TextColumn::make('user.email')->label('Email')->searchable(),
                TextColumn::make('department')->sortable()->toggleable(),
                TextColumn::make('qualification')->toggleable(),
                TextColumn::make('workload')->label('Workload')->sortable(),
                TextColumn::make('hire_date')->date()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->label('Department')
                    ->options(fn () => \App\Models\Teacher::query()->distinct()->pluck('department', 'department')->filter()),
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
