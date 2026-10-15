<?php

namespace App\Models\Electricity;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopCustomer extends Model
{
    protected $fillable = [
        'shop_id',
        'customer_id',
        'start_date',
        'end_date',
        'is_current',
        'remarks',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'is_current' => 'boolean',
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
    | কাস্টমার
    |--------------------------------------------------------------------------
    */

    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            ECustomer::class,
            'customer_id'
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
