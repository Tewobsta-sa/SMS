<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('school_id')
                ->label('School')
                ->relationship('user.school', 'name')
                ->searchable()
                ->required(),

            Select::make('user_id')
                ->label('User (Student)')
                ->relationship('user', 'name', fn ($query) => $query->where('role', 'Student'))
                ->searchable()
                ->required(),

            TextInput::make('registration_no')->maxLength(255),
            TextInput::make('admission_no')->maxLength(255),

            DatePicker::make('date_of_birth')->required(),
            Select::make('gender')->options(['Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other'])->required(),

            RichEditor::make('health_info')->columnSpanFull()->toolbarButtons(['bold','italic','bulletList','orderedList','link']),
            RichEditor::make('family_info')->columnSpanFull()->toolbarButtons(['bold','italic','bulletList','orderedList','link']),
            RichEditor::make('transfer_history')->columnSpanFull()->toolbarButtons(['bold','italic','bulletList','orderedList','link']),
            RichEditor::make('immunization')->columnSpanFull()->toolbarButtons(['bold','italic','bulletList','orderedList','link']),
            RichEditor::make('immunization_record')->columnSpanFull()->toolbarButtons(['bold','italic','bulletList','orderedList','link']),

            Select::make('guardian_id')
                    ->label('Guardian (Parent)')
                    ->relationship('guardian', 'id') // keep 'id' because that's what actually exists
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name)
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        return \App\Models\ParentModel::whereHas('user', function ($query) use ($search) {
                                $query->where('name', 'like', "%{$search}%");
                            })
                            ->with(['user:id,name']) // only load id and name
                            ->limit(50)
                            ->get()
                            ->pluck('user.name', 'id'); // pluck directly
                    }),



            // Class & Section assignment
            Select::make('class_id')
                ->label('Class')
                ->relationship('classModel', 'name')
                ->searchable(),

            Select::make('section_id')
                ->label('Section')
                ->relationship('section', 'name')
                ->searchable(),

            Select::make('status')
                ->options([
                    'active' => 'Active',
                    'pending' => 'Pending',
                    'transfer' => 'Transfer',
                ])
                ->required()
                ->default('active'),

            Select::make('registration_status')
                ->label('Registration Status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->default('pending')
                ->required(),

            DatePicker::make('enrollment_date')->required(),
        ]);
    }
}
