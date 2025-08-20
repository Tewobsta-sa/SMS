<?php

namespace App\Filament\Resources\OrganizationContacts\Pages;

use App\Filament\Resources\OrganizationContacts\OrganizationContactResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrganizationContacts extends ListRecords
{
    protected static string $resource = OrganizationContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
