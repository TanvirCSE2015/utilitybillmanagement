<?php

namespace App\Models\Electricity;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class ECustomer extends Model
{
    protected $fillable = [
        'customer_no',
        'customer_name',
        'mobile',
        'nid',
        'address',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ECustomer $customer) {

            if (blank($customer->customer_no)) {

                $customer->customer_no = DB::transaction(function () {
                    $lastCustomer = static::query()
                        ->where('customer_no', 'like', 'EC-%')
                        ->orderByRaw(
                            'CAST(SUBSTRING(customer_no, 4) AS UNSIGNED) DESC'
                        )
                        ->lockForUpdate()
                        ->first();

                    $nextNumber = $lastCustomer
                        ? ((int) substr($lastCustomer->customer_no, 3)) + 1
                        : 1;

                    return 'EC-' . str_pad(
                        $nextNumber,
                        6,
                        '0',
                        STR_PAD_LEFT
                    );
                });
            }
            if (blank($customer->created_by) && auth()->check()) {
                $customer->created_by = auth()->id();
            }
        });
    }

    public function shopCustomers(): HasMany
    {
        return $this->hasMany(
            ShopCustomer::class,
            'customer_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | কাস্টমারের বর্তমান দোকানসমূহ
    |--------------------------------------------------------------------------
    */

    public function currentShopCustomers(): HasMany
    {
        return $this->hasMany(
            ShopCustomer::class,
            'customer_id'
        )->where('is_current', true);
    }

    /*
    |--------------------------------------------------------------------------
    | কাস্টমারের তৈরি করা রেকর্ড
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
