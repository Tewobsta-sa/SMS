<?php

namespace App\Filament\Resources\Schoolclasses\Pages;

use App\Filament\Resources\Schoolclasses\SchoolclassResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSchoolclasses extends ListRecords
{
    protected static string $resource = SchoolclassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
