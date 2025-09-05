<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Schema;

class TeacherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            ImageEntry::make('user.profile_picture_url')
                ->label('Profile Picture')
                ->disk('public')   // resolve path from storage/app/public
                ->circular()    
                ->default(asset('images/image.png')),

            TextEntry::make('user.name')->label('Teacher'),
            TextEntry::make('user.email')->label('Email'),
            TextEntry::make('employee_no')->label('Employee No'),
            TextEntry::make('department'),
            TextEntry::make('specialization'),
            TextEntry::make('qualification'),
            TextEntry::make('workload'),
            TextEntry::make('experience_years'),
            TextEntry::make('hire_date')->date(),
            TextEntry::make('created_at')->dateTime(),
        ]);
    }
}
