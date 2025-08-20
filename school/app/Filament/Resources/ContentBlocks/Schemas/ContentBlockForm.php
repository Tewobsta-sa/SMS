<?php

namespace App\Filament\Resources\ContentBlocks\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;

use App\Models\PageSection;
use App\Enums\ContentTypeEnum;
use App\Models\ContentBlock;
use Illuminate\Support\Str;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\KeyValue;

class ContentBlockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Select::make('section_id')
                ->label('Page Section')
                ->options(
                    PageSection::active()
                        ->with('page')
                        ->get()
                        ->mapWithKeys(fn($section) => [
                            $section->id => $section->page->title . ' → ' . $section->title,
                        ])
                )
                ->required()
                ->searchable()
                ->preload(),
                   Select::make('type')
                ->label('Type')
                ->options(
                    collect(ContentTypeEnum::cases())
                        ->mapWithKeys(fn($case) => [$case->value => ucfirst($case->name)])
                )
                ->default(ContentTypeEnum::Text->value)
                ->required()
                ->reactive(),
                 TextInput::make('title')
                ->label('Title')
                ->maxLength(255)
                ->nullable()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('slug', Str::slug($state));
                }),
                   TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(120)
                ->unique(ContentBlock::class, 'slug', ignoreRecord: true)
                ->readOnly(),
                   TextInput::make('icon')
                ->label('Icon')
                ->maxLength(100),
                  TextInput::make('subtitle')
                ->label('Subtitle')
                ->maxLength(255)
                ->nullable(),
                 Textarea::make('short_description')
                ->label('Short Description')
                ->maxLength(500)
                ->nullable(),
                // Dynamic list items
                Repeater::make('list_items')
                    ->label('List Items')
                    ->visible(fn(callable $get) => $get('type') === 'list')
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('icon')
                            ->label('Icon (SVG or class)')
                            ->maxLength(100)
                            ->helperText('Provide an icon class or SVG.'),

                        Textarea::make('description')
                            ->label('Description')
                            ->maxLength(500)
                            ->rows(2),
                    ])
                    ->minItems(1)
                    ->columnSpanFull(),

                // Content input (fallback to Textarea)
                Textarea::make('content')
                    ->label('Content')
                    ->rows(8)
                    ->nullable()
                    ->columnSpanFull(),

                // Images upload (for image/gallery)
                SpatieMediaLibraryFileUpload::make('images')
                    ->collection('images')
                    ->multiple()
                    ->image()
                    ->imagePreviewHeight(150)
                    ->visible(fn(callable $get) => in_array($get('type'), ['image', 'gallery']))
                    ->columnSpanFull(),

                // Video input
                TextInput::make('video_url')
                    ->label('Video URL')
                    ->placeholder('Enter video URL')
                    ->visible(fn(callable $get) => $get('type') === 'video')
                    ->columnSpanFull(),

                // Metadata key/value pairs
                KeyValue::make('metadata')
                    ->label('Additional Data')
                    ->keyLabel('Field')
                    ->valueLabel('Value')
                    ->nullable()
                    ->helperText('Store additional structured data for complex blocks')
                    ->columnSpanFull(),
            ]);
    }
}