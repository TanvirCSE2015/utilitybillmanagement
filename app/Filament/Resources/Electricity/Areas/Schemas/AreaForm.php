<?php

namespace App\Filament\Resources\Electricity\Areas\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AreaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Section::make('আওয়াতাধীন এলাকার তথ্য')
                    ->schema([
                        Grid::make(2)
                        ->schema([
                            TextInput::make('area_name')
                                ->label(__('fields.area_name'))
                                ->required()
                                ->maxLength(150),

                            Toggle::make('is_active')
                                ->label(__('fields.is_active'))
                                ->default(true),
                        ]),
                        

                        Textarea::make('description')
                            ->label(__('fields.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                 
            ]);
    }
}
