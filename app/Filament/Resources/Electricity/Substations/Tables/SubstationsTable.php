<?php

namespace App\Filament\Resources\Electricity\Substations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubstationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('area.area_name')
                    ->label(__('fields.area_name'))
                    ->searchable(),
                TextColumn::make('substation_code')
                    ->label(__('fields.substation_code'))
                    ->searchable(),
                TextColumn::make('substation_name')
                    ->label(__('fields.substation_name'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean(),
                TextColumn::make('created_by')
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
