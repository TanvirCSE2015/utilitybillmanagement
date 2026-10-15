<?php

namespace App\Filament\Resources\Electricity\Substations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubstationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('সাবস্টেশনের তথ্য')
                    ->schema([
                        Grid::make(4)
                        ->schema([
                            Select::make('area_id')
                                ->label(__('fields.area_name'))
                                ->relationship('area', 'area_name')
                                ->required(),
                            TextInput::make('substation_code')
                                ->label(__('fields.substation_code'))
                                ->required(),
                            TextInput::make('substation_name')
                                ->label(__('fields.substation_name'))
                                ->required(),
                            Toggle::make('is_active')
                                ->label(__('fields.is_active'))
                                ->required()
                                ->default(true),
                            
                            // TextInput::make('created_by')
                            //     ->numeric()
                            //     ->default(),
                        ]),
                        
                            Textarea::make('address')
                                ->label(__('fields.address'))
                                ->default(null)
                                ->columnSpanFull(),
                         
                    ])
                    ->columnSpanFull(),
               
            ]);
    }
}
