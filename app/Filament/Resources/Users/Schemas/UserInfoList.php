<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            ImageEntry::make('profile_picture_url')
                ->label('Profile Picture')
                ->disk('public') // look in storage/app/public
                ->circular()
                ->default(asset('images/image.png')),

            TextEntry::make('name'),
            TextEntry::make('email'),
            TextEntry::make('username'),
            TextEntry::make('role')->badge(),
            TextEntry::make('school.name')->label('School'),
            TextEntry::make('phone'),
            TextEntry::make('address'),
            TextEntry::make('last_login_at')->dateTime(),
            TextEntry::make('last_login_ip'),
            TextEntry::make('created_at')->dateTime(),
        ]);
    }
}
