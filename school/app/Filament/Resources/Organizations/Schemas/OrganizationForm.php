<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TimePicker;
use App\Enums\StatusEnum;
use App\Models\Organization;

class OrganizationForm {
    public static function configure( Schema $schema ): Schema {
        return $schema
        ->components( [
            TextInput::make( 'title' )
            ->label( 'Title' )
            ->required()
            ->maxLength( 255 ),
            TextInput::make( 'po_box' )
            ->label( 'PO Box' )
            ->maxLength( 100 ),
            TextInput::make( 'address' )
            ->label( 'Address' )
            ->maxLength( 255 ),
            TextInput::make( 'map_url' )
            ->label( 'Map URL' )
            ->nullable()
            ->maxLength( 4024 ),
            Repeater::make( 'opening_hours' )
            ->label( 'Opening Hours' )
            ->schema( [
                Select::make( 'days' )
                ->label( 'Days' )
                ->multiple()
                ->options( Organization::getDayOptions() ),
                TimePicker::make( 'from' )
                ->label( 'From' ),
                TimePicker::make( 'to' )
                ->label( 'To' )
            ] )
            ->columns( 3 )
            ->minItems( 1 )
            ->addActionLabel( 'Add Opening Hours' ),

            Select::make( 'status' )
            ->options( StatusEnum::class )
            ->default( StatusEnum::active )
            ->required(),
            TextInput::make( 'created_at' )
            ->label( 'Created At' )
            ->disabled(),
            TextInput::make( 'updated_at' )
            ->label( 'Updated At' )
            ->disabled()
        ] );
    }
}
