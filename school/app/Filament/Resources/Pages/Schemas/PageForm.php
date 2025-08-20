<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class PageForm {
    public static function configure( Schema $schema ): Schema {
        return $schema
        ->components( [
          TextInput::make( 'title' )->required()->maxLength( 255 ),
            TextInput::make( 'slug' )->unique( ignoreRecord: true ),
            Textarea::make( 'short_description' ),
            Toggle::make( 'is_active' )->default( true ),
            TextInput::make( 'display_order' )->numeric()->default( 0 ),
        ] );
    }
}
