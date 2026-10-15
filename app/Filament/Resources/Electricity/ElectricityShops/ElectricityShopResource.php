<?php

namespace App\Filament\Resources\Electricity\ElectricityShops;

use App\Filament\Resources\Electricity\ElectricityShops\Pages\CreateElectricityShop;
use App\Filament\Resources\Electricity\ElectricityShops\Pages\EditElectricityShop;
use App\Filament\Resources\Electricity\ElectricityShops\Pages\ListElectricityShops;
use App\Filament\Resources\Electricity\ElectricityShops\RelationManagers\MetersRelationManager;
use App\Filament\Resources\Electricity\ElectricityShops\RelationManagers\ShopCustomersRelationManager;
use App\Filament\Resources\Electricity\ElectricityShops\Schemas\ElectricityShopForm;
use App\Filament\Resources\Electricity\ElectricityShops\Tables\ElectricityShopsTable;
use App\Models\Electricity\ElectricityShop;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ElectricityShopResource extends Resource
{
    protected static ?string $model = ElectricityShop::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingStorefront;

    protected static ?string $recordTitleAttribute = 'shop_no';

    protected static ?string $modelLabel = 'দোকান';

    protected static ?string $pluralModelLabel = 'দোকানসমূহ';

    protected static ?string $navigationLabel = 'দোকান ব্যবস্থাপনা';

    protected static \UnitEnum|string|null $navigationGroup = 'বিদ্যুৎ বিলিং';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ElectricityShopForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ElectricityShopsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ShopCustomersRelationManager::class,
            MetersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListElectricityShops::route('/'),
            'create' => CreateElectricityShop::route('/create'),
            'edit' => EditElectricityShop::route('/{record}/edit'),
        ];
    }
}
