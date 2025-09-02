<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('user.name')->label('Student')->searchable()->sortable(),
                TextColumn::make('first_name')->searchable(),
                TextColumn::make('last_name')->searchable(),
                TextColumn::make('registration_no')->label('Reg No')->toggleable(),
                TextColumn::make('classModel.name')->label('Class')->sortable(),
                TextColumn::make('section.name')->label('Section')->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('registration_status')->badge()->sortable(),
                TextColumn::make('enrollment_date')->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'active' => 'Active',
                    'pending' => 'Pending',
                    'transfer' => 'Transfer',
                ]),
                SelectFilter::make('registration_status')->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ]),
                SelectFilter::make('class_id')->relationship('classModel', 'name')->label('Class'),
                SelectFilter::make('section_id')->relationship('section', 'name')->label('Section'),
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
