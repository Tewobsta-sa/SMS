<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_picture')->circular()->label('Avatar'),
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('school.name')->label('School')->sortable()->toggleable(),
                TextColumn::make('role')->badge()->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'Admin' => 'Admin',
                        'Teacher' => 'Teacher',
                        'Parent' => 'Parent',
                        'Student' => 'Student',
                    ]),
                SelectFilter::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive']),
                SelectFilter::make('school_id')
                    ->relationship('school', 'name')
                    ->label('School'),
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
