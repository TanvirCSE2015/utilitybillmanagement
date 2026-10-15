<?php

namespace App\Filament\Resources\Electricity\ECustomers\Pages;

use App\Filament\Resources\Electricity\ECustomers\ECustomerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListECustomers extends ListRecords
{
    protected static string $resource = ECustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::Plus),
        ];
    }
}
