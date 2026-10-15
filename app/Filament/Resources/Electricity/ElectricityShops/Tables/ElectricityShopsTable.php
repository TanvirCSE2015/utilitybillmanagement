<?php

namespace App\Filament\Resources\Electricity\ElectricityShops\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ElectricityShopsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('area.area_name')
                    ->label(__('fields.area_name'))
                    ->searchable(),
                // TextColumn::make('substation.id')
                //     ->searchable(),
                TextColumn::make('shop_no')
                    ->label(__('fields.shop_no'))
                    ->searchable(),
                TextColumn::make('shop_name')
                    ->label(__('fields.shop_name'))
                    ->searchable(),
                TextColumn::make('shop_block')
                    ->label(__('fields.shop_block'))
                    ->searchable(),
                // TextColumn::make('shop_area')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('central_ac_area')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('common_ac_area')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('water_area')
                //     ->numeric()
                //     ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean(),
                TextColumn::make('creator.name')
                    ->label(__('fields.created_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
