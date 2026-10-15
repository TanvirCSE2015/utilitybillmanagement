<?php

namespace App\Filament\Resources\Electricity\Substations\Pages;

use App\Filament\Resources\Electricity\Substations\SubstationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditSubstation extends EditRecord
{
    protected static string $resource = SubstationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
            ->icon(Heroicon::Trash),
        ];
    }
}
