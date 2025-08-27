<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Schemas\Schema;

class PermissionForm {
    public static function configure( Schema $schema ): Schema {
        return $schema
        ->components( [
            \Filament\Forms\Components\TextInput::make( 'name' )
            ->label( 'Permission Name' )
            ->required()
            ->maxLength( 255 ),
            \Filament\Forms\Components\TextInput::make( 'guard_name' )
            ->label( 'Guard Name' )
            ->default( 'web' )
            ->required()
            ->maxLength( 255 ),
            \Filament\Forms\Components\Select::make( 'roles' )
            ->label( 'Assign to Roles' )
            ->multiple()
            ->required()
            ->relationship( 'roles', 'name' ),

        ] );
    }
}
