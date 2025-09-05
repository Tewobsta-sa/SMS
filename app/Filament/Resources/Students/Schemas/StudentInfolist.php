<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            ImageEntry::make('user.profile_picture_url')
                ->label('Profile Picture')
                ->disk('public')   // storage/app/public
                ->circular()        // optional: adjust avatar size
                ->default(asset('images/image.png')),

            TextEntry::make('user.name')->label('Student Name'),
            TextEntry::make('registration_no'),
            TextEntry::make('admission_no'),
            TextEntry::make('gender')->badge(),
            TextEntry::make('date_of_birth')->date(),
            TextEntry::make('guardian.user.name')->label('Guardian'),
            TextEntry::make('classModel.name')->label('Class'),
            TextEntry::make('section.name')->label('Section'),
            TextEntry::make('status')->badge(),
            TextEntry::make('registration_status')->badge(),
            TextEntry::make('enrollment_date')->date(),
            TextEntry::make('created_at')->dateTime(),
        ]);
    }
}
