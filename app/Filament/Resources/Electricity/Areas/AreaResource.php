<?php

namespace App\Filament\Resources\Electricity\Areas;

use App\Filament\Resources\Electricity\Areas\Pages\CreateArea;
use App\Filament\Resources\Electricity\Areas\Pages\EditArea;
use App\Filament\Resources\Electricity\Areas\Pages\ListAreas;
use App\Filament\Resources\Electricity\Areas\Schemas\AreaForm;
use App\Filament\Resources\Electricity\Areas\Tables\AreasTable;
use App\Models\Electricity\Area;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AreaResource extends Resource
{
    protected static ?string $model = Area::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Map;

    protected static ?string $recordTitleAttribute = 'area_name';

    protected static ?string $modelLabel = 'এলাকা';

    protected static ?string $pluralModelLabel = 'এলাকাসমূহ';

    protected static ?string $navigationLabel = 'এলাকা ব্যবস্থাপনা';

    protected static \UnitEnum|string|null $navigationGroup = 'বিদ্যুৎ বিলিং';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return AreaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AreasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAreas::route('/'),
            'create' => CreateArea::route('/create'),
            'edit' => EditArea::route('/{record}/edit'),
        ];
    }
}
