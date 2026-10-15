<?php

namespace App\Filament\Resources\Electricity\ElectricityShops\Pages;

use App\Filament\Resources\Electricity\ElectricityShops\ElectricityShopResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditElectricityShop extends EditRecord
{
    protected static string $resource = ElectricityShopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
