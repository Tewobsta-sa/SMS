<?php

namespace App\Filament\Resources\ContentBlocks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\IconColumn;
use App\Enums\ContentTypeEnum;
use App\Models\PageSection;

class ContentBlocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
              TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('section.page.title')
                    ->label('Page')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('section.title')
                    ->label('Section')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->color(fn(ContentTypeEnum $state): string => match ($state) {
                        ContentTypeEnum::Text => 'primary',
                        ContentTypeEnum::Image => 'info',
                        ContentTypeEnum::Video => 'warning',
                        ContentTypeEnum::List => 'success',
                        ContentTypeEnum::Timeline => 'secondary',
                        ContentTypeEnum::Gallery => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('short_description')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                SpatieMediaLibraryImageColumn::make('images')
                    ->collection('images')
                    ->label('Images')
                    ->size(40)
                    ->square(),
                TextColumn::make('video_url')
                    ->label('video_url')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('display_order')
                    ->sortable()
                    ->label('Order'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
               SelectFilter::make('section_id')
                    ->label('Page Section')
                    ->options(PageSection::active()->with('page')->get()->mapWithKeys(function ($section) {
                        return [$section->id => $section->page->title . ' → ' . $section->title];
                    }))
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type')
                    ->options([
                        'text' => 'Text Block',
                        'image' => 'Image Block',
                        'video' => 'Video Block',
                        'list' => 'List Block',
                        'timeline' => 'Timeline Block',
                        'gallery' => 'Gallery Block',
                    ]),

              TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All Blocks')
                    ->trueLabel('Active Blocks')
                    ->falseLabel('Inactive Blocks'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
