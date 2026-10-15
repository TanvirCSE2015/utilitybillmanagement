<?php

namespace App\Models\Electricity;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeterReading extends Model
{
    protected $fillable = [
        'meter_id',
        'reading_month',
        'reading_year',
        'reading_date',
        'reading_value',
        'reading_status',
        'reading_type',
        'remarks',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'reading_month' => 'integer',
            'reading_year'  => 'integer',
            'reading_date'  => 'date',
            'reading_value' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Meter
    |--------------------------------------------------------------------------
    */

    public function meter(): BelongsTo
    {
        return $this->belongsTo(
            Meter::class,
            'meter_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | রিডিং এন্ট্রি করেছেন
    |--------------------------------------------------------------------------
    */

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'recorded_by'
        );
    }
}
