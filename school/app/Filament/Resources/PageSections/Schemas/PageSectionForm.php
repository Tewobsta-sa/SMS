<?php

namespace App\Filament\Resources\PageSections\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
// use Filament\Forms\Components\Section;
use Filament\Forms\Components\Section;
use App\Models\Page;

class PageSectionForm {
    public static function configure( Schema $schema ): Schema {
        return $schema
        ->components( [
            Select::make( 'page_id' )
            ->label( 'Page' )
            ->options( Page::active()->pluck( 'title', 'id' ) )
            ->required()
            ->searchable()
            ->preload(),

            TextInput::make( 'title' )
            ->required()
            ->maxLength( 255 ),

            Textarea::make( 'subtitle' )
            ->maxLength( 500 )
            ->nullable(),
            Toggle::make( 'is_active' )
            ->label( 'Active' )
            ->default( true )
            ->helperText( 'Only active sections will be visible to visitors' ),

            TextInput::make( 'display_order' )
            ->numeric()
            ->default( 0 )
            ->helperText( 'Order for display (lower numbers appear first)' ),
        ] )
        ->columns( 2 );
    }
}
