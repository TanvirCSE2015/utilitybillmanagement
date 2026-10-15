<?php

namespace App\Filament\Resources\Electricity\Substations;

use App\Filament\Resources\Electricity\Substations\Pages\CreateSubstation;
use App\Filament\Resources\Electricity\Substations\Pages\EditSubstation;
use App\Filament\Resources\Electricity\Substations\Pages\ListSubstations;
use App\Filament\Resources\Electricity\Substations\Schemas\SubstationForm;
use App\Filament\Resources\Electricity\Substations\Tables\SubstationsTable;
use App\Models\Electricity\Substation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubstationResource extends Resource
{
    protected static ?string $model = Substation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Bolt;

    protected static ?string $recordTitleAttribute = 'substation_name';

    protected static ?string $modelLabel = 'সাবস্টেশন';

    protected static ?string $pluralModelLabel = 'সাবস্টেশনসমূহ';

    protected static ?string $navigationLabel = 'সাবস্টেশন ব্যবস্থাপনা';

    protected static \UnitEnum|string|null $navigationGroup = 'বিদ্যুৎ বিলিং';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return SubstationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubstationsTable::configure($table);
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
            'index' => ListSubstations::route('/'),
            'create' => CreateSubstation::route('/create'),
            'edit' => EditSubstation::route('/{record}/edit'),
        ];
    }
}
