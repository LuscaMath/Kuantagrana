<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['environment_id', 'name', 'slug', 'description', 'goal_metric', 'goal_target', 'points_reward', 'start_date', 'end_date', 'is_active'])]
class Challenge extends Model
{
    protected function casts(): array
    {
        return [
            'goal_target' => 'integer',
            'points_reward' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function environment(): BelongsTo
    {
        return $this->belongsTo(Environment::class);
    }

    public function userChallenges(): HasMany
    {
        return $this->hasMany(UserChallenge::class);
    }
}
