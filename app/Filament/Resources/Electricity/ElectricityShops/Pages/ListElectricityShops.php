<?php

namespace App\Filament\Resources\Electricity\ElectricityShops\Pages;

use App\Filament\Resources\Electricity\ElectricityShops\ElectricityShopResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListElectricityShops extends ListRecords
{
    protected static string $resource = ElectricityShopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
