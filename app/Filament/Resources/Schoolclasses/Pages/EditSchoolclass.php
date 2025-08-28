<?php

namespace App\Filament\Resources\Schoolclasses\Pages;

use App\Filament\Resources\Schoolclasses\SchoolclassResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditSchoolclass extends EditRecord
{
    protected static string $resource = SchoolclassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
