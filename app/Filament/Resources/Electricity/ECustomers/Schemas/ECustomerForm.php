<?php

namespace App\Filament\Resources\Electricity\ECustomers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ECustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('গ্রাহকের মৌলিক তথ্যসমূহ')
                    ->icon(Heroicon::User)
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('customer_no')
                                    ->label(__('fields.customer_no'))
                                    ->readOnly()
                                    ->dehydrated(),
                                TextInput::make('customer_name')
                                    ->label(__('fields.customer_name'))
                                    ->required(),
                                TextInput::make('mobile')
                                    ->label(__('fields.mobile'))
                                    ->default(null),
                            ]),
                        Grid::make(3)
                            ->schema([
                                 TextInput::make('nid')
                                    ->label(__('fields.nid'))
                                    ->default(null),
                                 Toggle::make('is_active')
                                    ->label(__('fields.is_active'))
                                    ->required(),
                                TextEntry::make('created_by')
                                    ->label(__('fields.created_by'))
                                    ->formatStateUsing(fn($record)=>$record->creator->name),
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
