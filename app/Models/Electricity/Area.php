<?php

namespace App\Models\Electricity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{

    protected $fillable = [
        'area_name',
        'description',
        'is_active',
        'created_by',
    ];
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function substations(): HasMany
    {
        return $this->hasMany(Substation::class);
    }
}
