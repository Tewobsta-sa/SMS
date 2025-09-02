<?php

namespace App\Filament\Resources\Parents\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ParentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('user.name')->label('Parent'),
            TextEntry::make('user.email')->label('Email'),
            TextEntry::make('occupation'),
            TextEntry::make('relation')->badge(),
            TextEntry::make('created_at')->dateTime(),
        ]);
    }
}
