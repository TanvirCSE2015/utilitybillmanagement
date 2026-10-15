<?php

namespace App\Filament\Resources\Electricity\ElectricityShops\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ElectricityShopForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('দোকানের মৌলিক তথ্যসমূহ')
                    ->icon(Heroicon::HomeModern)
                    ->schema([

                    
                        Select::make('area_id')
                            ->label(__('fields.area_name'))
                            ->relationship('area', 'area_name')
                            ->required(),
                        Select::make('substation_id')
                            ->label(__('fields.substation_name'))
                            ->relationship('substation', 'substation_name')
                            ->default(null),
                        TextInput::make('shop_no')
                            ->label(__('fields.shop_no'))
                            ->required(),
                        TextInput::make('shop_name')
                            ->label(__('fields.shop_name'))
                            ->default(null),
                        TextInput::make('shop_block')
                            ->label(__('fields.shop_block'))
                            ->required(),
                        TextInput::make('shop_area')
                            ->label(__('fields.shop_area'))
                            ->required()
                            ->numeric()
                            ->default(0.0),
                        TextInput::make('central_ac_area')
                            ->label(__('fields.central_ac_area'))
                            ->required()
                            ->numeric()
                            ->default(0.0),
                        TextInput::make('common_ac_area')
                            ->label(__('fields.common_ac_area'))
                            ->required()
                            ->numeric()
                            ->default(0.0),
                        TextInput::make('water_area')
                            ->label(__('fields.water_area'))
                            ->required()
                            ->numeric()
                            ->default(0.0),
                        Toggle::make('is_active')
                            ->label(__('fields.is_active'))
                            ->required(),
                        TextEntry::make('created_by')
                            ->label(__('fields.created_by'))
                            ->formatStateUsing(fn($record)=>$record->creator->name),
                        Textarea::make('description')
                            ->label(__('fields.description'))
                            ->default(null),
                        
                    ])
                    ->columnSpanFull()
                    ->columns(5),
            ]);
    }
}
