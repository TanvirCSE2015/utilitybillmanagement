<?php

namespace App\Filament\Resources\Electricity\ECustomers\Pages;

use App\Filament\Resources\Electricity\ECustomers\ECustomerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditECustomer extends EditRecord
{
    protected static string $resource = ECustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                 ->icon(Heroicon::Trash),
        ];
    }
}
