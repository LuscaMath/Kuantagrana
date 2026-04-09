<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'environment_id', 'name', 'unit', 'quantity', 'minimum_quantity', 'expires_at', 'notes', 'is_active'])]
class HouseholdItem extends Model
{
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'minimum_quantity' => 'integer',
            'expires_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function environment(): BelongsTo
    {
        return $this->belongsTo(Environment::class);
    }
}
