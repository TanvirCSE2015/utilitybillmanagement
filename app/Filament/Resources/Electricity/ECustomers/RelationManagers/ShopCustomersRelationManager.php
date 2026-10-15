<?php

namespace App\Filament\Resources\Electricity\ECustomers\RelationManagers;

use App\Models\Electricity\Area;
use App\Models\Electricity\ECustomer;
use App\Models\Electricity\ElectricityShop;
use App\Models\Electricity\Meter;
use App\Models\Electricity\MeterReading;
use App\Models\Electricity\ShopCustomer;
use App\Models\Electricity\Substation;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShopCustomersRelationManager extends RelationManager
{
    protected static string $relationship = 'ShopCustomers';

    protected static bool $isLazy = false;

    protected static ?string $title = 'দোকান ও মিটার ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'দোকান';

    protected static ?string $pluralModelLabel = 'দোকানসমূহ';
    
    protected static string|\BackedEnum|null $icon = 'heroicon-o-building-storefront';
    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | দোকান নির্বাচন
            |--------------------------------------------------------------------------
            */

            Section::make('দোকানের ধরন')
                ->icon(Heroicon::Sparkles)
                ->schema([

                    Radio::make('shop_mode')
                        ->label('দোকান নির্বাচন')
                        ->options([
                            'new'      => 'নতুন দোকান তৈরি করুন',
                            'existing' => 'আগের দোকান নির্বাচন করুন',
                        ])
                        ->default('existing')
                        ->live()
                        ->required(),

                ]),


            /*
            |--------------------------------------------------------------------------
            | আগে থেকে তৈরি দোকান
            |--------------------------------------------------------------------------
            */

            Section::make('আগের দোকানের তথ্য')
                ->icon(Heroicon::ArchiveBox)
                ->visible(
                    fn (Get $get): bool =>
                        $get('shop_mode') === 'existing'
                )
                ->schema([

                    Select::make('shop_id')
                        ->label('দোকান')
                        ->options(function (): array {

                            return ElectricityShop::query()
                                ->where('is_active', true)
                                ->whereDoesntHave(
                                    'currentShopCustomer'
                                )
                                ->get()
                                ->mapWithKeys(function ($shop) {

                                    $areaName =
                                        $shop->area?->area_name
                                        ?? $shop->area?->name
                                        ?? 'Area';

                                    return [
                                        $shop->id =>
                                            $areaName
                                            . ' | '
                                            . $shop->shop_no
                                            . (
                                                $shop->shop_name
                                                    ? ' | ' . $shop->shop_name
                                                    : ''
                                            ),
                                    ];
                                })
                                ->all();
                        })
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required(),

                ]),


            /*
            |--------------------------------------------------------------------------
            | নতুন দোকানের তথ্য
            |--------------------------------------------------------------------------
            */

            Section::make('নতুন দোকানের তথ্য')
                ->icon(Heroicon::HomeModern)
                ->visible(
                    fn (Get $get): bool =>
                        $get('shop_mode') === 'new'
                )
                ->schema([
                    Grid::make(4)
                        ->schema([
                            Select::make('area_id')
                                ->label('এরিয়া')
                                ->options(fn (): array =>
                                    Area::query()
                                        ->get()
                                        ->mapWithKeys(fn ($area) => [
                                            $area->id =>
                                                $area->area_name
                                                ?? $area->name
                                                ?? "Area #{$area->id}",
                                        ])
                                        ->all()
                                )
                                ->searchable()
                                ->required()
                                ->placeholder('এরিয়া নির্বাচন'),

                            Select::make('substation_id')
                                ->label('সাবস্টেশন')
                                ->options(fn (): array =>
                                    Substation::query()
                                        ->get()
                                        ->mapWithKeys(fn ($substation) => [
                                            $substation->id =>
                                                $substation->substation_name
                                                ?? $substation->name
                                                ?? "Substation #{$substation->id}",
                                        ])
                                        ->all()
                                )
                                ->searchable()
                                ->required()
                                ->placeholder('সাবস্টেশন নির্বাচন'),

                            TextInput::make('shop_no')
                                ->label('দোকান নম্বর')
                                ->required()
                                ->maxLength(100),

                            TextInput::make('shop_name')
                                ->label('দোকানের নাম')
                                ->maxLength(255),
                    ]),
                    Grid::make(5)
                        ->schema([

                            TextInput::make('shop_block')
                                ->label(__('fields.shop_block'))
                                ->required()
                                ->maxLength(255),

                            TextInput::make('shop_area')
                                ->label('দোকানের আয়তন')
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->required(),
                            TextInput::make('central_ac_area')
                                ->label(__('fields.central_ac_area'))
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->required(),
                            TextInput::make('common_ac_area')
                                ->label(__('fields.common_ac_area'))
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->required(),
                            TextInput::make('water_area')
                                ->label(__('fields.water_area'))
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->required(),
                        ]),

                    Textarea::make('shop_description')
                        ->label('দোকানের বিবরণ')
                        ->rows(2),

                ])
                ->columnSpanFull(),


            /*
            |--------------------------------------------------------------------------
            | দোকান বরাদ্দ
            |--------------------------------------------------------------------------
            */

            Section::make('কাস্টমারের নামে দোকান বরাদ্দ')
                ->icon(Heroicon::CalendarDays)
                ->schema([

                    DatePicker::make('start_date')
                        ->label('বরাদ্দের শুরুর তারিখ')
                        ->default(now()->toDateString())
                        ->required(),

                    Textarea::make('remarks')
                        ->label('মন্তব্য')
                        ->rows(2),

                ])
                ->columns(2),


            /*
            |--------------------------------------------------------------------------
            | মিটারের তথ্য
            |--------------------------------------------------------------------------
            */

            Section::make('মিটারের তথ্য')
                ->icon(Heroicon::Calculator)
                ->description(
                    'নতুন দোকানের জন্য মিটার এন্ট্রি আবশ্যক। '
                    . 'আগের দোকানে বর্তমান মিটার থাকলে নতুন মিটার তৈরি করবেন না।'
                )
                ->schema([

                    TextInput::make('meter_no')
                        ->label('মিটার নম্বর')
                        ->maxLength(100)
                        ->rules([
                            'nullable',
                            'unique:meters,meter_no',
                        ])
                        ->required(
                            fn (Get $get): bool =>
                                $get('shop_mode') === 'new'
                                || $this->selectedShopNeedsMeter($get)
                        ),

                    DatePicker::make('installation_date')
                        ->label('মিটার স্থাপনের তারিখ')
                        ->required(
                            fn (Get $get): bool =>
                                $get('shop_mode') === 'new'
                                || $this->selectedShopNeedsMeter($get)
                        ),

                    TextInput::make('initial_reading')
                        ->label('প্রাথমিক রিডিং')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->required(
                            fn (Get $get): bool =>
                                $get('shop_mode') === 'new'
                                || $this->selectedShopNeedsMeter($get)
                        ),

                    Textarea::make('meter_remarks')
                        ->label('মিটার সম্পর্কিত মন্তব্য')
                        ->rows(2),

                ])
                ->columns(2),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Selected Shop Needs a Meter?
    |--------------------------------------------------------------------------
    */

    protected function selectedShopNeedsMeter(Get $get): bool
    {
        $shopId = $get('shop_id');

        if (! $shopId) {
            return false;
        }

        $shop = ElectricityShop::query()->find($shopId);

        if (! $shop) {
            return false;
        }

        return ! $shop->currentMeter()->exists();
    }


    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public function table(Table $table): Table
    {
        return $table

            ->defaultSort('created_at', 'desc')

            ->columns([

                TextColumn::make('shop.shop_no')
                    ->label('দোকান নম্বর')
                    ->searchable(),

                TextColumn::make('shop.shop_name')
                    ->label('দোকানের নাম')
                    ->placeholder('নাম নেই'),

                TextColumn::make('shop.area')
                    ->label('এরিয়া')
                    ->state(fn (ShopCustomer $record): string =>
                        $record->shop?->area?->area_name
                        ?? $record->shop?->area?->name
                        ?? '-'
                    ),

                TextColumn::make('shop.substation')
                    ->label('সাবস্টেশন')
                    ->state(fn (ShopCustomer $record): string =>
                        $record->shop?->substation?->substation_name
                        ?? $record->shop?->substation?->name
                        ?? '-'
                    ),

                TextColumn::make('shop.currentMeter.meter_no')
                    ->label('বর্তমান মিটার')
                    ->placeholder('মিটার নেই'),

                TextColumn::make('shop.currentMeter.meter_status')
                    ->label('মিটারের অবস্থা')
                    ->badge(),

                TextColumn::make('start_date')
                    ->label('বরাদ্দের তারিখ')
                    ->date('d-m-Y'),

                TextColumn::make('end_date')
                    ->label('শেষ তারিখ')
                    ->date('d-m-Y')
                    ->placeholder('চলমান'),

                TextColumn::make('is_current')
                    ->label('বরাদ্দের অবস্থা')
                    ->badge()
                    ->formatStateUsing(
                        fn (bool $state): string =>
                            $state ? 'বর্তমান' : 'সাবেক'
                    )
                    ->color(
                        fn (bool $state): string =>
                            $state ? 'success' : 'gray'
                    ),

            ])

            ->headerActions([

                CreateAction::make()
                    ->label('দোকান ও মিটার এন্ট্রি')
                    ->icon('heroicon-o-plus')
                    ->modalWidth('5xl')

                    ->using(function (
                        array $data,
                        RelationManager $livewire
                    ): Model {

                        return DB::transaction(function () use (
                            $data,
                            $livewire
                        ): Model {

                            $customer = $livewire->getOwnerRecord();

                            /*
                            |--------------------------------------------------------------------------
                            | নতুন দোকান তৈরি অথবা আগের দোকান নির্বাচন
                            |--------------------------------------------------------------------------
                            */

                            if ($data['shop_mode'] === 'new') {

                                $duplicateShop = ElectricityShop::query()
                                    ->where('area_id', $data['area_id'])
                                    ->where('shop_no', $data['shop_no'])
                                    ->exists();

                                if ($duplicateShop) {
                                    throw ValidationException::withMessages([
                                        'shop_no' =>
                                            'এই এরিয়ায় দোকান নম্বরটি আগে থেকেই রয়েছে।',
                                    ]);
                                }

                                $shop = ElectricityShop::create([

                                    'area_id' => $data['area_id'],

                                    'substation_id' =>
                                        $data['substation_id'],

                                    'shop_no' => $data['shop_no'],

                                    'shop_name' => $data['shop_name'] ?? null,

                                    'shop_block' => $data['shop_block'] ?? null,

                                    'shop_area' => $data['shop_area'],

                                    'central_ac_area' => $data['central_ac_area'],

                                    'common_ac_area' => $data['common_ac_area'],

                                    'water_area' => $data['water_area'],

                                    'description' =>
                                        $data['shop_description'] ?? null,

                                    'is_active' => true,

                                    'created_by' => Auth::id(),

                                ]);

                            } else {

                                $shop = ElectricityShop::query()
                                    ->whereKey($data['shop_id'])
                                    ->lockForUpdate()
                                    ->firstOrFail();

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | একই দোকানে অন্য বর্তমান কাস্টমার আছে কি না
                            |--------------------------------------------------------------------------
                            */

                            $hasCurrentCustomer = ShopCustomer::query()
                                ->where('shop_id', $shop->id)
                                ->where('is_current', true)
                                ->exists();

                            if ($hasCurrentCustomer) {

                                throw ValidationException::withMessages([
                                    'shop_id' =>
                                        'এই দোকানে ইতিমধ্যে একজন বর্তমান কাস্টমার রয়েছেন।',
                                ]);

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | দোকান বরাদ্দের রেকর্ড
                            |--------------------------------------------------------------------------
                            */

                            $shopCustomer = $livewire
                                ->getRelationship()
                                ->create([

                                    'shop_id' => $shop->id,

                                    'start_date' => $data['start_date'],

                                    'end_date' => null,

                                    'is_current' => true,

                                    'remarks' =>
                                        $data['remarks'] ?? null,

                                    'created_by' => Auth::id(),

                                ]);


                            /*
                            |--------------------------------------------------------------------------
                            | বর্তমান মিটার যাচাই
                            |--------------------------------------------------------------------------
                            */

                            $currentMeter = Meter::query()
                                ->where('shop_id', $shop->id)
                                ->where('is_current', true)
                                ->lockForUpdate()
                                ->first();


                            /*
                            |--------------------------------------------------------------------------
                            | বর্তমান মিটার না থাকলে নতুন মিটার তৈরি
                            |--------------------------------------------------------------------------
                            */

                            if (! $currentMeter) {

                                if (blank($data['meter_no'] ?? null)) {

                                    throw ValidationException::withMessages([
                                        'meter_no' =>
                                            'এই দোকানের জন্য মিটার নম্বর দিতে হবে।',
                                    ]);

                                }

                                $installationDate =
                                    $data['installation_date'];

                                $initialReading =
                                    $data['initial_reading'];

                                $meter = Meter::create([

                                    'shop_id' => $shop->id,

                                    'meter_no' => $data['meter_no'],

                                    'installation_date' =>
                                        $installationDate,

                                    'removal_date' => null,

                                    'initial_reading' =>
                                        $initialReading,

                                    'meter_status' => 'active',

                                    'is_current' => true,

                                    'remarks' =>
                                        $data['meter_remarks'] ?? null,

                                    'created_by' => Auth::id(),

                                ]);


                                /*
                                |--------------------------------------------------------------------------
                                | প্রাথমিক রিডিং সংরক্ষণ
                                |--------------------------------------------------------------------------
                                */

                                $readingDate = Carbon::parse(
                                    $installationDate
                                );

                                MeterReading::create([

                                    'meter_id' => $meter->id,

                                    'reading_month' =>
                                        $readingDate->month,

                                    'reading_year' =>
                                        $readingDate->year,

                                    'reading_date' =>
                                        $readingDate->toDateString(),

                                    'reading_value' =>
                                        $initialReading,

                                    'reading_status' => 'available',

                                    'reading_type' => 'initial',

                                    'remarks' =>
                                        'মিটার স্থাপনের সময় প্রাথমিক রিডিং।',

                                    'recorded_by' => Auth::id(),

                                ]);

                            } elseif (
                                filled($data['meter_no'] ?? null)
                            ) {

                                throw ValidationException::withMessages([
                                    'meter_no' =>
                                        'এই দোকানে ইতিমধ্যে বর্তমান মিটার রয়েছে। '
                                        . 'নতুন মিটার বসাতে হলে আগে মিটার পরিবর্তনের প্রক্রিয়া অনুসরণ করুন।',
                                ]);

                            }

                            return $shopCustomer;

                        });

                    }),

            ])

            ->recordActions([

                EditAction::make()
                    ->label('')
                    ->tooltip('দোকান সম্পাদনা')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->modalHeading('দোকানের তথ্য সম্পাদনা')
                    ->modalWidth('5xl')

                    ->schema([

                        Section::make('দোকানের তথ্য')
                            ->icon(Heroicon::HomeModern)
                            ->schema([

                                Select::make('area_id')
                                    ->label('এরিয়া')
                                    ->options(
                                        fn () => Area::query()
                                            ->pluck('area_name', 'id')
                                            ->all()
                                    )
                                    ->required(),

                                Select::make('substation_id')
                                    ->label('সাবস্টেশন')
                                    ->options(
                                        fn () => Substation::query()
                                            ->pluck('substation_name', 'id')
                                            ->all()
                                    )
                                    ->required(),

                                TextInput::make('shop_no')
                                    ->label('দোকান নম্বর')
                                    ->required(),

                                TextInput::make('shop_name')
                                    ->label('দোকানের নাম'),

                                TextInput::make('shop_block')
                                    ->label('দোকানের ব্লক'),

                                TextInput::make('shop_area')
                                    ->label('দোকানের আয়তন')
                                    ->numeric()
                                    ->required(),

                                TextInput::make('central_ac_area')
                                    ->label('সেন্ট্রাল এসি এরিয়া')
                                    ->numeric(),

                                TextInput::make('common_ac_area')
                                    ->label('কমন এসি এরিয়া')
                                    ->numeric(),

                                TextInput::make('water_area')
                                    ->label('পানির এরিয়া')
                                    ->numeric(),

                                Textarea::make('description')
                                    ->label('দোকানের বিবরণ'),

                            ])
                            ->columns(4),

                        Section::make('দোকান বরাদ্দের তথ্য')
                            ->icon(Heroicon::CalendarDays)
                            ->schema([

                                DatePicker::make('start_date')
                                    ->label('বরাদ্দের শুরুর তারিখ')
                                    ->required(),

                                Textarea::make('remarks')
                                    ->label('মন্তব্য'),

                            ])
                            ->columns(2),
                        
                        Section::make('বর্তমান সক্রিয় মিটারের তথ্য')
                            ->icon(Heroicon::Calculator)
                            ->description('এই দোকানের বর্তমানে ব্যবহৃত মিটারের তথ্য।')
                            ->schema([

                                TextInput::make('active_meter_no')
                                    ->label('মিটার নম্বর')
                                    ->readOnly()
                                    ->dehydrated(false),

                                DatePicker::make('active_meter_installation_date')
                                    ->label('মিটার স্থাপনের তারিখ')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('active_meter_initial_reading')
                                    ->label('প্রাথমিক রিডিং')
                                    ->readOnly()
                                    ->dehydrated(false),

                                TextInput::make('active_meter_status')
                                    ->label('মিটারের অবস্থা')
                                    ->readOnly()
                                    ->dehydrated(false),
                                TextInput::make('active_meter_latest_reading')
                                    ->label('সর্বশেষ মিটার রিডিং')
                                    ->readOnly()
                                    ->dehydrated(false),

                                Textarea::make('active_meter_remarks')
                                    ->label('মিটার সম্পর্কিত মন্তব্য')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->columnSpanFull(),

                            ])
                            ->columns(5),

                    ])

                    /*
                    |--------------------------------------------------------------------------
                    | পুরোনো তথ্য Form-এ দেখানো
                    |--------------------------------------------------------------------------
                    */

                    ->fillForm(function (ShopCustomer $record): array {

                        $shop = $record->shop;

                         $activeMeter = $shop?->currentMeter()
                            ->where('is_current', true)
                            ->where('meter_status', 'active')
                            ->first();
                        $latestReading = $activeMeter
                            ? MeterReading::query()
                                ->where('meter_id', $activeMeter->id)
                                ->where('reading_status', 'available')
                                ->orderByDesc('reading_year')
                                ->orderByDesc('reading_month')
                                ->orderByDesc('reading_date')
                                ->orderByDesc('id')
                                ->first()
                            : null;

                        return [

                            'area_id' => $shop?->area_id,

                            'substation_id' => $shop?->substation_id,

                            'shop_no' => $shop?->shop_no,

                            'shop_name' => $shop?->shop_name,

                            'shop_block' => $shop?->shop_block,

                            'shop_area' => $shop?->shop_area,

                            'central_ac_area' => $shop?->central_ac_area,

                            'common_ac_area' => $shop?->common_ac_area,

                            'water_area' => $shop?->water_area,

                            'description' => $shop?->description,

                            'start_date' => $record->start_date,

                            'remarks' => $record->remarks,

                             /*
                            |--------------------------------------------------------------------------
                            | বর্তমান Active মিটারের তথ্য
                            |--------------------------------------------------------------------------
                            */

                            'active_meter_no' =>
                                $activeMeter?->meter_no ?? 'মিটার নেই',

                            'active_meter_installation_date' =>
                                $activeMeter?->installation_date,

                            'active_meter_initial_reading' =>
                                $activeMeter?->initial_reading,

                            'active_meter_status' =>
                                $activeMeter?->meter_status === 'active'
                                    ? 'সক্রিয়'
                                    : 'মিটার নেই',

                            'active_meter_remarks' =>
                                $activeMeter?->remarks,
                            
                            'active_meter_latest_reading' => $latestReading?->reading_value,

                        ];

                    })

                    /*
                    |--------------------------------------------------------------------------
                    | তথ্য আপডেট
                    |--------------------------------------------------------------------------
                    */

                    ->using(function (
                        ShopCustomer $record,
                        array $data
                    ): ShopCustomer {

                        return DB::transaction(function () use ($record, $data) {

                            /*
                            |--------------------------------------------------------------------------
                            | দোকানের তথ্য আপডেট
                            |--------------------------------------------------------------------------
                            */

                            $record->shop->update([

                                'area_id' => $data['area_id'],

                                'substation_id' => $data['substation_id'],

                                'shop_no' => $data['shop_no'],

                                'shop_name' => $data['shop_name'] ?? null,

                                'shop_block' => $data['shop_block'] ?? null,

                                'shop_area' => $data['shop_area'],

                                'central_ac_area' =>
                                    $data['central_ac_area'] ?? 0,

                                'common_ac_area' =>
                                    $data['common_ac_area'] ?? 0,

                                'water_area' =>
                                    $data['water_area'] ?? 0,

                                'description' =>
                                    $data['description'] ?? null,

                            ]);

                            /*
                            |--------------------------------------------------------------------------
                            | দোকান বরাদ্দের তথ্য আপডেট
                            |--------------------------------------------------------------------------
                            */

                            $record->update([

                                'start_date' => $data['start_date'],

                                'remarks' => $data['remarks'] ?? null,

                            ]);

                            return $record;

                        });

                    }),

                    Action::make('vacateShop')
                        ->label('')
                        ->tooltip('দোকান খালি করুন')
                        ->icon('heroicon-o-arrow-right-on-rectangle')
                        ->color('danger')

                        ->visible(
                            fn (ShopCustomer $record): bool =>
                                (bool) $record->is_current
                        )

                        ->modalHeading('দোকান খালি করুন')

                        ->modalDescription(
                            'দোকান খালি করলে বর্তমান কাস্টমারের বরাদ্দ শেষ হবে। '
                            . 'দোকান, মিটার এবং আগের বিলের তথ্য সংরক্ষিত থাকবে।'
                        )

                        ->modalWidth('lg')

                        ->schema([

                            DatePicker::make('end_date')
                                ->label('দোকান ছাড়ার তারিখ')
                                ->default(now()->toDateString())
                                ->required()
                                ->native(false),

                            Textarea::make('remarks')
                                ->label('দোকান ছাড়ার কারণ')
                                ->placeholder('দোকান ছাড়ার কারণ লিখুন...')
                                ->rows(3),

                        ])

                        ->requiresConfirmation()

                        ->modalSubmitActionLabel('দোকান খালি নিশ্চিত করুন')

                        ->action(function (
                            ShopCustomer $record,
                            array $data
                        ): void {

                            DB::transaction(function () use ($record, $data) {

                                /*
                                |--------------------------------------------------------------------------
                                | বর্তমান বরাদ্দ Lock
                                |--------------------------------------------------------------------------
                                */

                                $shopCustomer = ShopCustomer::query()
                                    ->whereKey($record->id)
                                    ->lockForUpdate()
                                    ->firstOrFail();

                                /*
                                |--------------------------------------------------------------------------
                                | বরাদ্দ ইতিমধ্যে শেষ হয়েছে কি না
                                |--------------------------------------------------------------------------
                                */

                                if (! $shopCustomer->is_current) {

                                    throw ValidationException::withMessages([

                                        'end_date' =>
                                            'এই দোকানটি ইতিমধ্যে খালি করা হয়েছে।',

                                    ]);

                                }

                                /*
                                |--------------------------------------------------------------------------
                                | দোকান ছাড়ার তারিখ যাচাই
                                |--------------------------------------------------------------------------
                                */

                                $endDate = Carbon::parse(
                                    $data['end_date']
                                )->startOfDay();

                                $startDate = Carbon::parse(
                                    $shopCustomer->start_date
                                )->startOfDay();

                                if ($endDate->lt($startDate)) {

                                    throw ValidationException::withMessages([

                                        'end_date' =>
                                            'দোকান ছাড়ার তারিখ বরাদ্দের শুরুর তারিখের আগে হতে পারবে না।',

                                    ]);

                                }

                                /*
                                |--------------------------------------------------------------------------
                                | দোকান খালি করা
                                |--------------------------------------------------------------------------
                                */

                                $shopCustomer->update([

                                    'end_date' =>
                                        $endDate->toDateString(),

                                    'is_current' => false,

                                    'remarks' => $data['remarks'] ?? null,

                                ]);

                            });

                            /*
                            |--------------------------------------------------------------------------
                            | Success Notification
                            |--------------------------------------------------------------------------
                            */

                            Notification::make()
                                ->title('দোকান সফলভাবে খালি করা হয়েছে।')
                                ->body(
                                    'বর্তমান কাস্টমারের বরাদ্দ শেষ হয়েছে। '
                                    . 'দোকানটি এখন নতুন কাস্টমারের নামে বরাদ্দ দেওয়া যাবে।'
                                )
                                ->success()
                                ->send();

                        }),

                    Action::make('transferShop')
                        ->label('')
                        ->tooltip('দোকান ট্রান্সফার')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')

                        /*
                        |--------------------------------------------------------------------------
                        | শুধু বর্তমান বরাদ্দে বাটন দেখাবে
                        |--------------------------------------------------------------------------
                        */

                        ->visible(
                            fn (ShopCustomer $record): bool =>
                                (bool) $record->is_current
                        )

                        ->modalHeading('দোকান ট্রান্সফার করুন')

                        ->modalDescription(
                            'বর্তমান কাস্টমারের বরাদ্দ শেষ করে '
                            . 'নতুন কাস্টমারের নামে দোকান বরাদ্দ দেওয়া হবে। '
                            . 'দোকান, মিটার এবং পুরোনো বিলের ইতিহাস সংরক্ষিত থাকবে।'
                        )

                        ->modalWidth('2xl')

                        /*
                        |--------------------------------------------------------------------------
                        | ট্রান্সফার ফর্ম
                        |--------------------------------------------------------------------------
                        */

                        ->schema([

                            Section::make('নতুন কাস্টমারের তথ্য')
                                ->schema([

                                    Select::make('new_customer_id')
                                        ->label('নতুন কাস্টমার')
                                        ->options(
                                            fn (): array =>
                                                ECustomer::query()
                                                    ->get()
                                                    ->mapWithKeys(
                                                        fn ($customer) => [
                                                            $customer->id =>
                                                                $customer->customer_name,
                                                        ]
                                                    )
                                                    ->all()
                                        )
                                        ->searchable()
                                        ->required()
                                        ->createOptionForm([
                                            Section::make('গ্রাহকের মৌলিক তথ্যসমূহ')
                                                ->schema([
                                                    Grid::make(3)
                                                        ->schema([
                                                            TextInput::make('customer_name')
                                                                ->label(__('fields.customer_name'))
                                                                ->required(),
                                                            TextInput::make('mobile')
                                                                ->label(__('fields.mobile'))
                                                                ->default(null),
                                                        
                                                            TextInput::make('nid')
                                                                ->label(__('fields.nid'))
                                                                ->default(null),
                                                            Toggle::make('is_active')
                                                                ->label(__('fields.is_active'))
                                                                ->required(),
                                                    ]),
                                                    Textarea::make('address')
                                                        ->label(__('fields.address'))
                                                        ->default(null)
                                                        ->columnSpanFull(),
                                                ])
                                                ->columnSpanFull()
                                        ]),

                                    DatePicker::make('transfer_date')
                                        ->label('ট্রান্সফারের কার্যকর তারিখ')
                                        ->default(now()->toDateString())
                                        ->required()
                                        ->native(false),

                                    Textarea::make('transfer_remarks')
                                        ->label('ট্রান্সফারের কারণ')
                                        ->rows(3),

                                ]),

                        ])

                        ->requiresConfirmation()
                        ->modalSubmitActionLabel('ট্রান্সফার নিশ্চিত করুন')

                        /*
                        |--------------------------------------------------------------------------
                        | ট্রান্সফার কার্যক্রম
                        |--------------------------------------------------------------------------
                        */

                        ->action(function (
                            ShopCustomer $record,
                            array $data
                        ): void {

                            DB::transaction(function () use ($record, $data) {

                                /*
                                |--------------------------------------------------------------------------
                                | দোকানের রেকর্ড Lock
                                |--------------------------------------------------------------------------
                                */

                                $shop = ElectricityShop::query()
                                    ->whereKey($record->shop_id)
                                    ->lockForUpdate()
                                    ->firstOrFail();

                                /*
                                |--------------------------------------------------------------------------
                                | বর্তমান বরাদ্দ Lock
                                |--------------------------------------------------------------------------
                                */

                                $oldAssignment = ShopCustomer::query()
                                    ->where('shop_id', $shop->id)
                                    ->where('is_current', true)
                                    ->lockForUpdate()
                                    ->first();

                                if (
                                    ! $oldAssignment ||
                                    $oldAssignment->id !== $record->id
                                ) {

                                    throw ValidationException::withMessages([

                                        'new_customer_id' =>
                                            'এই দোকানের বর্তমান বরাদ্দ পরিবর্তিত হয়েছে। '
                                            . 'অনুগ্রহ করে পুনরায় চেষ্টা করুন।',

                                    ]);

                                }

                                /*
                                |--------------------------------------------------------------------------
                                | একই কাস্টমারের নামে ট্রান্সফার বন্ধ
                                |--------------------------------------------------------------------------
                                */

                                if (
                                    (int) $oldAssignment->e_customer_id ===
                                    (int) $data['new_customer_id']
                                ) {

                                    throw ValidationException::withMessages([

                                        'new_customer_id' =>
                                            'বর্তমান কাস্টমারের নামেই পুনরায় '
                                            . 'দোকান ট্রান্সফার করা যাবে না।',

                                    ]);

                                }

                                /*
                                |--------------------------------------------------------------------------
                                | ট্রান্সফারের তারিখ যাচাই
                                |--------------------------------------------------------------------------
                                */

                                $transferDate = Carbon::parse(
                                    $data['transfer_date']
                                )->startOfDay();

                                $startDate = Carbon::parse(
                                    $oldAssignment->start_date
                                )->startOfDay();

                                if ($transferDate->lte($startDate)) {

                                    throw ValidationException::withMessages([

                                        'transfer_date' =>
                                            'ট্রান্সফারের তারিখ বর্তমান বরাদ্দের '
                                            . 'শুরুর তারিখের পরে হতে হবে।',

                                    ]);

                                }

                                /*
                                |--------------------------------------------------------------------------
                                | আগের কাস্টমারের বরাদ্দ বন্ধ
                                |--------------------------------------------------------------------------
                                */

                                $oldAssignment->update([

                                    'end_date' =>
                                        $transferDate
                                            ->copy()
                                            ->subDay()
                                            ->toDateString(),

                                    'is_current' => false,

                                ]);

                                /*
                                |--------------------------------------------------------------------------
                                | নতুন কাস্টমারের নামে বরাদ্দ
                                |--------------------------------------------------------------------------
                                */

                                ShopCustomer::create([

                                    'shop_id' => $shop->id,

                                    'e_customer_id' =>
                                        $data['new_customer_id'],

                                    'start_date' =>
                                        $transferDate->toDateString(),

                                    'end_date' => null,

                                    'is_current' => true,

                                    'remarks' =>
                                        $data['transfer_remarks'] ?? null,

                                    'created_by' => Auth::id(),

                                ]);

                            });

                            /*
                            |--------------------------------------------------------------------------
                            | সফলতার Notification
                            |--------------------------------------------------------------------------
                            */

                            Notification::make()
                                ->title('দোকান সফলভাবে ট্রান্সফার হয়েছে')
                                ->body(
                                    'আগের কাস্টমারের বরাদ্দ শেষ করে '
                                    . 'নতুন কাস্টমারের নামে দোকান বরাদ্দ দেওয়া হয়েছে।'
                                )
                                ->success()
                                ->send();

                        }),

            ])

            ->toolbarActions([]);
    }
}
