<?php

namespace App\Filament\Resources\Electricity\ElectricityShops\RelationManagers;

use App\Models\Electricity\Meter;
use App\Models\Electricity\MeterReading;
use Carbon\Carbon;
use Filament\Actions\Action;
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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
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

class MetersRelationManager extends RelationManager
{
    protected static string $relationship = 'meters';

    protected static bool $isLazy = false;

    protected static ?string $title = 'মিটার ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'মিটার';

    protected static ?string $pluralModelLabel = 'মিটারসমূহ';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('মিটারের তথ্য')
                ->schema([
                    TextInput::make('meter_no')
                        ->label('মিটার নম্বর')
                        ->required()
                        ->maxLength(50)
                        ->unique(
                            table: 'meters',
                            column: 'meter_no',
                            ignoreRecord: true
                        ),

                    DatePicker::make('installation_date')
                        ->label('স্থাপনের তারিখ')
                        ->required(),

                    DatePicker::make('removal_date')
                        ->label('অপসারণ / রিপ্লেসের তারিখ')
                        ->nullable(),

                    TextInput::make('initial_reading')
                        ->label('স্থাপনের সময় প্রাথমিক রিডিং')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->default(0),

                    Select::make('meter_status')
                        ->label('মিটারের অবস্থা')
                        ->options([
                            'active' => 'সচল',
                            'faulty' => 'নষ্ট',
                            'replaced' => 'রিপ্লেস',
                            'removed' => 'অপসারিত',
                        ])
                        ->required()
                        ->default('active'),

                    Textarea::make('remarks')
                        ->label('মন্তব্য')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query->with('creator')
            )
            ->columns([
                TextColumn::make('meter_no')
                    ->label('মিটার নম্বর')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('installation_date')
                    ->label('স্থাপনের তারিখ')
                    ->date('d-m-Y')
                    ->sortable(),

                TextColumn::make('removal_date')
                    ->label('অপসারণের তারিখ')
                    ->date('d-m-Y')
                    ->placeholder('—'),

                TextColumn::make('initial_reading')
                    ->label('প্রাথমিক রিডিং')
                    ->numeric(decimalPlaces: 2),

                TextColumn::make('meter_status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'active' => 'সচল',
                        'faulty' => 'নষ্ট',
                        'replaced' => 'রিপ্লেস',
                        'removed' => 'অপসারিত',
                        default => $state ?? 'অজানা',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'active' => 'success',
                        'faulty' => 'danger',
                        'replaced' => 'warning',
                        'removed' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('is_current')
                    ->label('বর্তমান মিটার')
                    ->formatStateUsing(fn (bool $state): string =>
                        $state ? 'হ্যাঁ' : 'না'
                    )
                    ->badge()
                    ->color(fn (bool $state): string =>
                        $state ? 'success' : 'gray'
                    ),

                TextColumn::make('creator.name')
                    ->label('রেকর্ড করেছেন')
                    ->placeholder('অজানা'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::Plus)
                    ->label('নতুন মিটার স্থাপন')
                    ->schema([
                        Section::make('মিটারের তথ্য')
                            ->icon(Heroicon::Calculator)
                            ->schema([
                                TextInput::make('meter_no')
                                    ->label('মিটার নম্বর')
                                    ->required()
                                    ->maxLength(50)
                                    ->unique(table: 'meters', column: 'meter_no'),

                                DatePicker::make('installation_date')
                                    ->label('স্থাপনের তারিখ')
                                    ->required()
                                    ->default(now()),

                                TextInput::make('initial_reading')
                                    ->label('প্রাথমিক রিডিং')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->default(0),

                                Textarea::make('remarks')
                                    ->label('মন্তব্য')
                                    ->rows(3),
                            ])
                            ->columns(4),
                    ])
                    ->using(function (array $data): Meter {
                        return DB::transaction(function () use ($data) {
                            $shop = $this->getOwnerRecord()
                                ->newQuery()
                                ->lockForUpdate()
                                ->findOrFail($this->getOwnerRecord()->getKey());

                            $currentMeter = $shop->meters()
                                ->where('is_current', true)
                                ->lockForUpdate()
                                ->first();

                            if ($currentMeter) {
                                throw ValidationException::withMessages([
                                    'meter_no' =>
                                        'এই দোকানে ইতিমধ্যে একটি সচল মিটার রয়েছে। '
                                        . 'আগে পুরোনো মিটার বন্ধ বা রিপ্লেস করুন।',
                                ]);
                            }

                            $installationDate = Carbon::parse(
                                $data['installation_date']
                            );

                            $initialReading = $data['initial_reading'];

                            $meter = $shop->meters()->create([
                                'meter_no' => $data['meter_no'],
                                'installation_date' => $installationDate->toDateString(),
                                'removal_date' => null,
                                'initial_reading' => $initialReading,
                                'meter_status' => 'active',
                                'is_current' => true,
                                'remarks' => $data['remarks'] ?? null,
                                'created_by' => auth()->id(),
                            ]);

                            MeterReading::create([
                                'meter_id' => $meter->id,
                                'reading_month' => $installationDate->month,
                                'reading_year' => $installationDate->year,
                                'reading_date' => $installationDate->toDateString(),
                                'reading_value' => $initialReading,
                                'reading_status' => 'available',
                                'reading_type' => 'initial',
                                'remarks' => 'মিটার স্থাপনের সময় প্রাথমিক রিডিং',
                                'recorded_by' => auth()->id(),
                            ]);

                            return $meter;
                        });
                    }),
            ])
            ->recordActions([
                // ViewAction::make()
                //     ->label('দেখুন'),

                EditAction::make()
                    ->label('সম্পাদনা')
                    ->visible(fn (Meter $record): bool => ! $record->is_current),

                Action::make('markFaulty')
                    ->label('মিটার নষ্ট করুন')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('মিটার নষ্ট হিসেবে চিহ্নিত করুন')
                    ->modalDescription(
                        'এই মিটারটি আর বর্তমান মিটার থাকবে না। '
                        . 'পুরোনো রিডিংয়ের ইতিহাস সংরক্ষিত থাকবে।'
                    )
                    ->schema([
                        DatePicker::make('removal_date')
                            ->label('মিটার বন্ধের তারিখ')
                            ->required()
                            ->default(now()),

                        Textarea::make('remarks')
                            ->label('নষ্ট হওয়ার কারণ')
                            ->required(),
                    ])
                    ->visible(fn (Meter $record): bool =>
                        $record->is_current
                        && $record->meter_status === 'active'
                    )
                    ->action(function (
                        Meter $record,
                        array $data
                    ): void {
                        DB::transaction(function () use ($record, $data) {
                            $meter = Meter::query()
                                ->lockForUpdate()
                                ->findOrFail($record->getKey());

                            if (! $meter->is_current) {
                                throw ValidationException::withMessages([
                                    'meter' => 'এই মিটারটি ইতিমধ্যে বন্ধ করা হয়েছে।',
                                ]);
                            }

                            $meter->update([
                                'meter_status' => 'faulty',
                                'is_current' => false,
                                'removal_date' => $data['removal_date'],
                                'remarks' => trim(
                                    ($meter->remarks ? $meter->remarks . "\n" : '')
                                    . $data['remarks']
                                ),
                            ]);
                        });
                    }),

                Action::make('replaceMeter')
                    ->label('মিটার রিপ্লেস করুন')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('নতুন মিটার স্থাপন')
                    ->schema([
                        DatePicker::make('removal_date')
                            ->label('পুরোনো মিটার অপসারণের তারিখ')
                            ->required()
                            ->default(now()),

                        TextInput::make('new_meter_no')
                            ->label('নতুন মিটার নম্বর')
                            ->required()
                            ->maxLength(50)
                            ->unique(table: 'meters', column: 'meter_no'),

                        DatePicker::make('installation_date')
                            ->label('নতুন মিটার স্থাপনের তারিখ')
                            ->required()
                            ->default(now()),

                        TextInput::make('initial_reading')
                            ->label('নতুন মিটারের প্রাথমিক রিডিং')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->default(0),

                        Textarea::make('remarks')
                            ->label('রিপ্লেস সংক্রান্ত মন্তব্য')
                            ->required(),
                    ])
                    ->visible(fn (Meter $record): bool =>
                        $record->is_current
                    )
                    ->action(function (
                        Meter $record,
                        array $data
                    ): void {
                        DB::transaction(function () use ($record, $data) {
                            $oldMeter = Meter::query()
                                ->lockForUpdate()
                                ->findOrFail($record->getKey());

                            if (! $oldMeter->is_current) {
                                throw ValidationException::withMessages([
                                    'meter' => 'এই মিটারটি আর বর্তমান মিটার নয়।',
                                ]);
                            }

                            $shop = $oldMeter->shop()
                                ->lockForUpdate()
                                ->firstOrFail();

                            $oldMeter->update([
                                'meter_status' => 'replaced',
                                'is_current' => false,
                                'removal_date' => $data['removal_date'],
                                'remarks' => trim(
                                    ($oldMeter->remarks ? $oldMeter->remarks . "\n" : '')
                                    . 'রিপ্লেস করা হয়েছে: '
                                    . $data['remarks']
                                ),
                            ]);

                            $installationDate = Carbon::parse(
                                $data['installation_date']
                            );

                            $newMeter = $shop->meters()->create([
                                'meter_no' => $data['new_meter_no'],
                                'installation_date' => $installationDate->toDateString(),
                                'removal_date' => null,
                                'initial_reading' => $data['initial_reading'],
                                'meter_status' => 'active',
                                'is_current' => true,
                                'remarks' => $data['remarks'],
                                'created_by' => auth()->id(),
                            ]);

                            MeterReading::create([
                                'meter_id' => $newMeter->id,
                                'reading_month' => $installationDate->month,
                                'reading_year' => $installationDate->year,
                                'reading_date' => $installationDate->toDateString(),
                                'reading_value' => $data['initial_reading'],
                                'reading_status' => 'available',
                                'reading_type' => 'initial',
                                'remarks' => 'মিটার রিপ্লেসের সময় নতুন মিটারের প্রাথমিক রিডিং',
                                'recorded_by' => auth()->id(),
                            ]);
                        });
                    }),
            ]);
    }

    public static function getTabComponent(Model $ownerRecord, string $pageClass): Tab
    {
        return Tab::make('মিটার ব্যবস্থাপনা')
            ->icon(Heroicon::Calculator);
    }
}
