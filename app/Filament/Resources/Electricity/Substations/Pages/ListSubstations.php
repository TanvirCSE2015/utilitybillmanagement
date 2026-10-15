<?php

namespace App\Filament\Resources\Electricity\Substations\Pages;

use App\Filament\Resources\Electricity\Substations\SubstationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListSubstations extends ListRecords
{
    protected static string $resource = SubstationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::Plus),
        ];
    }
}
