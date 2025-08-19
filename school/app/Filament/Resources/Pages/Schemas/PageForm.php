<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Schemas\Schema;

class PageForm {
    public static function configure( Schema $schema ): Schema {
        return $schema
        ->components( [
            \Filament\Forms\Components\TextInput::make( 'title' )->required()->maxLength( 255 ),
            \Filament\Forms\Components\TextInput::make( 'slug' )->unique( ignoreRecord: true ),
            \Filament\Forms\Components\Textarea::make( 'short_description' ),
            \Filament\Forms\Components\Toggle::make( 'is_active' )->default( true ),
            \Filament\Forms\Components\TextInput::make( 'display_order' )->numeric()->default( 0 ),
        ] );
    }
}
