<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Filament\Schemas\Components\Utilities\Get;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('school_id')
                ->label('School')
                ->relationship('school', 'name')
                ->searchable()
                ->required(),

            TextInput::make('username')->maxLength(255),

            TextInput::make('name')->required()->maxLength(255),

            TextInput::make('email')
                ->email()
                ->required()
                ->unique(table: 'users', ignorable: fn ($record) => $record),

            TextInput::make('phone')->maxLength(255),
            Textarea::make('address')->rows(2),

            FileUpload::make('profile_picture_url')
                ->image()
                ->disk('public')
                ->directory('profiles')
                ->imageEditor()
                ->maxSize(5024) // optional: limit to 1MB
                ->label('Upload Profile Picture')
                ->getUploadedFileNameForStorageUsing(fn ($file) => time() . '_' . $file->getClientOriginalName())
                ->helperText('Optional. Will override the Profile Picture URL if uploaded.'),

            Select::make('status')
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ])
                ->required()
                ->default('active'),

            Select::make('role')
                ->label('Role')
                ->options([
                    'Admin' => 'Admin',
                    'Teacher' => 'Teacher',
                    'Parent' => 'Parent',
                    'Student' => 'Student',
                ])
                ->required(),

            TextInput::make('password')
                ->password()
                ->revealable()
                ->required(fn ($record) => $record === null)
                ->dehydrateStateUsing(function ($state, Get $get) {
                    if (blank($state)) return null;
                    $hash = Hash::make($state);
                    request()->merge(['password_hash' => $hash]);
                    return $hash;
                })
                ->dehydrated(fn ($state) => filled($state)),

            TextInput::make('password_hash')
                ->hidden()
                ->dehydrated(fn () => filled(request('password_hash')))
                ->dehydrateStateUsing(fn () => request('password_hash')),

            DateTimePicker::make('last_login_at'),
            TextInput::make('last_login_ip')->label('Last Login IP')->maxLength(255),
            TextInput::make('ip_address')->label('Current IP')->maxLength(255),

            Select::make('is_active')
                ->label('Is Active')
                ->options([1 => 'Yes', 0 => 'No'])
                ->default(1)
                ->required(),
        ]);
    }
}
