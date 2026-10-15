<?php

namespace App\Models\Electricity;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meter extends Model
{
    protected $fillable = [
        'shop_id',
        'meter_no',
        'installation_date',
        'removal_date',
        'initial_reading',
        'meter_status',
        'is_current',
        'remarks',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'installation_date' => 'date',
            'removal_date'      => 'date',
            'initial_reading'   => 'decimal:2',
            'is_current'        => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | দোকান
    |--------------------------------------------------------------------------
    */

    public function shop(): BelongsTo
    {
        return $this->belongsTo(
            ElectricityShop::class,
            'shop_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | মিটারের রিডিংসমূহ
    |--------------------------------------------------------------------------
    */

    public function readings(): HasMany
    {
        return $this->hasMany(
            MeterReading::class,
            'meter_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | রেকর্ড তৈরি করেছেন
    |--------------------------------------------------------------------------
    */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}
