<?php

namespace App\Filament\Resources\Parents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ParentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('school_id')
                ->label('School')
                ->relationship('user.school', 'name')
                ->searchable()
                ->required(),

            Select::make('user_id')
                ->label('User (Parent)')
                ->relationship('user', 'name', fn ($query) => $query->where('role', 'Parent'))
                ->searchable()
                ->required(),

            TextInput::make('occupation')->maxLength(255),

            Select::make('relation')
                ->label('Relation')
                ->options([
                    'Father' => 'Father',
                    'Mother' => 'Mother',
                    'Guardian' => 'Guardian',
                ])
                ->required(),

        ]);
    }
}
