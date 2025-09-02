<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TeacherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('user.name')->label('Teacher'),
            TextEntry::make('user.email')->label('Email'),
            TextEntry::make('department'),
            TextEntry::make('specialization'),
            TextEntry::make('qualification'),
            TextEntry::make('workload'),
            TextEntry::make('workload_hours'),
            TextEntry::make('experience_years'),
            TextEntry::make('hire_date')->date(),
            TextEntry::make('created_at')->dateTime(),
        ]);
    }
}
