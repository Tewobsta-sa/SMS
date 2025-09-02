<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('school_id')
                ->relationship('user.school', 'name') // or direct relationship if Teacher has school()
                ->label('School')
                ->searchable()
                ->required(),

            Select::make('user_id')
                ->label('User (Teacher)')
                ->relationship('user', 'name', fn ($query) => $query->where('role', 'Teacher'))
                ->searchable()
                ->required(),

            TextInput::make('employee_no')->label('Employee No')->maxLength(255),
            TextInput::make('employee_number')->label('Employee Number')->maxLength(255),

            DatePicker::make('hire_date'),

            TextInput::make('department')->maxLength(255)->required(),
            TextInput::make('specialization')->maxLength(255),
            TextInput::make('qualification')->maxLength(255),
            TextInput::make('qualifications')->label('Qualifications (extra)')->maxLength(255),

            TextInput::make('workload')->numeric()->minValue(0),
            TextInput::make('workload_hours')->numeric()->minValue(0),
            TextInput::make('experience_years')->numeric()->minValue(0),
        ]);
    }
}
