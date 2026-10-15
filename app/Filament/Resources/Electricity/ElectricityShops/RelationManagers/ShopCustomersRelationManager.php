<?php

namespace App\Filament\Resources\Electricity\ElectricityShops\RelationManagers;

use App\Models\Electricity\ShopCustomer;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShopCustomersRelationManager extends RelationManager
{
    protected static string $relationship = 'shopCustomers';

    protected static bool $isLazy = false;

    protected static ?string $title = 'মালিকানা ইতিহাস';

    protected static ?string $modelLabel = 'মালিকানা';

    protected static ?string $pluralModelLabel = 'মালিকগণ';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('মালিকানা তথ্য')
                ->icon(Heroicon::User)
                ->schema([
                    Select::make('customer_id')
                        ->label('গ্রাহক')
                        ->relationship(
                            name: 'customer',
                            titleAttribute: 'customer_name'
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    DatePicker::make('start_date')
                        ->label('মালিকানা শুরুর তারিখ')
                        ->required(),

                    DatePicker::make('end_date')
                        ->label('মালিকানা শেষের তারিখ')
                        ->nullable(),

                    Textarea::make('remarks')
                        ->label('মন্তব্য')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull()
                ->columns(3),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query->with([
                    'customer',
                    'creator',
                ])
            )
            ->columns([
                TextColumn::make('customer.customer_no')
                    ->label('গ্রাহক নম্বর')
                    ->searchable(),

                TextColumn::make('customer.customer_name')
                    ->label('গ্রাহকের নাম')
                    ->searchable(),

                TextColumn::make('start_date')
                    ->label('শুরুর তারিখ')
                    ->date('d-m-Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('শেষের তারিখ')
                    ->date('d-m-Y')
                    ->placeholder('বর্তমান')
                    ->sortable(),

                IconColumn::make('is_current')
                    ->label('বর্তমান মালিক')
                    ->boolean(),

                TextColumn::make('creator.name')
                    ->label('রেকর্ড করেছেন')
                    ->placeholder('অজানা'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::Plus)
                    ->label('নতুন মালিক')
                    ->using(function (array $data): ShopCustomer {
                        return DB::transaction(function () use ($data) {
                            $shop = $this->getOwnerRecord()
                                ->newQuery()
                                ->lockForUpdate()
                                ->findOrFail($this->getOwnerRecord()->getKey());

                            $hasCurrent = $shop->shopCustomers()
                                ->where('is_current', true)
                                ->exists();

                            if ($hasCurrent) {
                                throw ValidationException::withMessages([
                                    'customer_id' =>
                                        'এই দোকানে ইতিমধ্যে বর্তমান মালিক রয়েছেন। '
                                        . 'আগে বর্তমান মালিকানা শেষ করুন।',
                                ]);
                            }

                            return $shop->shopCustomers()->create([
                                'customer_id' => $data['customer_id'],
                                'start_date' => $data['start_date'],
                                'end_date' => null,
                                'is_current' => true,
                                'remarks' => $data['remarks'] ?? null,
                                'created_by' => auth()->id(),
                            ]);
                        });
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('দেখুন'),

                EditAction::make()
                    ->label('সম্পাদনা')
                    ->using(function (
                        ShopCustomer $record,
                        array $data
                    ): ShopCustomer {
                        return DB::transaction(function () use ($record, $data) {
                            $record = ShopCustomer::query()
                                ->lockForUpdate()
                                ->findOrFail($record->getKey());

                            if ($record->is_current && ! empty($data['end_date'])) {
                                $record->end_date = $data['end_date'];
                                $record->is_current = false;
                                $record->remarks = $data['remarks'] ?? null;
                                $record->save();

                                return $record;
                            }

                            if ($record->is_current) {
                                $record->customer_id = $data['customer_id'];
                                $record->start_date = $data['start_date'];
                                $record->remarks = $data['remarks'] ?? null;
                                $record->save();

                                return $record;
                            }

                            $record->remarks = $data['remarks'] ?? null;
                            $record->save();

                            return $record;
                        });
                    }),
            ]);
    }

    public static function getTabComponent(Model $ownerRecord, string $pageClass): Tab
    {
        return Tab::make('মালিকানা ইতিহাস')
            ->icon('heroicon-o-users');
    }
}
