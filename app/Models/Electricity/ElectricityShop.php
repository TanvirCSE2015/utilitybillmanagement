<?php

namespace App\Models\Electricity;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ElectricityShop extends Model
{
     protected $fillable = [
        'area_id',
        'substation_id',
        'shop_no',
        'shop_name',
        'shop_area',
        'shop_block',
        'central_ac_area',
        'common_ac_area',
        'water_area',
        'description',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'shop_area' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Area
    |--------------------------------------------------------------------------
    */

    public function area(): BelongsTo
    {
        return $this->belongsTo(
            Area::class,
            'area_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Substation
    |--------------------------------------------------------------------------
    */

    public function substation(): BelongsTo
    {
        return $this->belongsTo(
            Substation::class,
            'substation_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | কাস্টমার বরাদ্দের সম্পূর্ণ ইতিহাস
    |--------------------------------------------------------------------------
    */

    public function shopCustomers(): HasMany
    {
        return $this->hasMany(
            ShopCustomer::class,
            'shop_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | বর্তমান কাস্টমার বরাদ্দ
    |--------------------------------------------------------------------------
    */

    public function currentShopCustomer(): HasOne
    {
        return $this->hasOne(
            ShopCustomer::class,
            'shop_id'
        )->where('is_current', true);
    }

    /*
    |--------------------------------------------------------------------------
    | দোকানের সকল মিটার
    |--------------------------------------------------------------------------
    */

    public function meters(): HasMany
    {
        return $this->hasMany(
            Meter::class,
            'shop_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | বর্তমান মিটার
    |--------------------------------------------------------------------------
    */

    public function currentMeter(): HasOne
    {
        return $this->hasOne(
            Meter::class,
            'shop_id'
        )->where('is_current', true);
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
