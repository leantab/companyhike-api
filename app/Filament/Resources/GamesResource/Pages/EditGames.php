<?php

namespace App\Filament\Resources\GamesResource\Pages;

use App\Filament\Resources\GamesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGames extends EditRecord
{
    protected static string $resource = GamesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
