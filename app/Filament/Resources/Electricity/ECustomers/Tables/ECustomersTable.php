<?php

namespace App\Filament\Resources\Electricity\ECustomers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ECustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_no')
                    ->label(__('fields.customer_no'))
                    ->searchable(),
                TextColumn::make('customer_name')
                    ->label(__('fields.customer_name'))
                    ->searchable(),
                TextColumn::make('mobile')
                    ->label(__('fields.mobile'))
                    ->searchable(),
                TextColumn::make('nid')
                    ->label(__('fields.nid'))
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
                    ->label(__('fields.updated_at'))
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
