<?php

namespace App\Filament\Resources\CommunitiesResource\Pages;

use App\Filament\Resources\CommunitiesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommunities extends ListRecords
{
    protected static string $resource = CommunitiesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
