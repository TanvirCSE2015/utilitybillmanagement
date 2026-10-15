<?php

namespace App\Filament\Resources\Electricity\ECustomers;

use App\Filament\Resources\Electricity\ECustomers\Pages\CreateECustomer;
use App\Filament\Resources\Electricity\ECustomers\Pages\EditECustomer;
use App\Filament\Resources\Electricity\ECustomers\Pages\ListECustomers;
use App\Filament\Resources\Electricity\ECustomers\RelationManagers\ShopCustomersRelationManager;
use App\Filament\Resources\Electricity\ECustomers\Schemas\ECustomerForm;
use App\Filament\Resources\Electricity\ECustomers\Tables\ECustomersTable;
use App\Models\Electricity\ECustomer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ECustomerResource extends Resource
{
    protected static ?string $model = ECustomer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    protected static ?string $recordTitleAttribute = 'customer_no';

    protected static ?string $modelLabel = 'গ্রাহক';

    protected static ?string $pluralModelLabel = 'গ্রাহকগণ';

    protected static ?string $navigationLabel = 'গ্রাহক ব্যবস্থাপনা';

    protected static \UnitEnum|string|null $navigationGroup = 'বিদ্যুৎ বিলিং';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ECustomerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ECustomersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ShopCustomersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListECustomers::route('/'),
            'create' => CreateECustomer::route('/create'),
            'edit' => EditECustomer::route('/{record}/edit'),
        ];
    }
}
