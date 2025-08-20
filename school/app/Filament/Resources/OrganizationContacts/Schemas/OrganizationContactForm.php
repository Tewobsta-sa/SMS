<?php

namespace App\Filament\Resources\OrganizationContacts\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\OrganizationContact;
use App\Enums\StatusEnum;

class OrganizationContactForm {
    public static function configure( Schema $schema ): Schema {
        return $schema
        ->components( [
            Select::make( 'type' )
            ->label( 'Contact Type' )
            ->required()
            ->options( OrganizationContact::getTypeOptions() ),
            TextInput::make( 'value' )
            ->label( 'Contact Value' )
            ->required()
            ->email( fn ( $get ) => $get( 'type' ) === 'email' )
            ->tel( fn ( $get ) => $get( 'type' ) === 'phone' )
            ->maxLength( 255 ),
            Select::make( 'status' )
            ->label( 'Status' )
            ->options( StatusEnum::class )
            ->default( StatusEnum::active ),
        ] )->columns( 2 );
    }
}
