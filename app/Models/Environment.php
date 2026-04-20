<?php

namespace App\Models;

use App\Support\EnvironmentCatalog;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'icon', 'theme_color', 'description', 'display_order', 'is_active'])]
class Environment extends Model
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSupporting(Builder $query, string $capability): Builder
    {
        return $query->whereIn('slug', EnvironmentCatalog::slugsFor($capability));
    }

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function householdItems(): HasMany
    {
        return $this->hasMany(HouseholdItem::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }

    public function challenges(): HasMany
    {
        return $this->hasMany(Challenge::class);
    }

    public function tips(): HasMany
    {
        return $this->hasMany(Tip::class);
    }

    public function supportsFeature(string $capability): bool
    {
        return EnvironmentCatalog::supports($this->slug, $capability);
    }

    /**
     * @return array<string, mixed>
     */
    public function getHighlights(): array
    {
        return EnvironmentCatalog::definition($this->slug)['highlights'];
    }

    /**
     * @return array<string, mixed>
     */
    public function getTheme(): array
    {
        return EnvironmentCatalog::definition($this->slug)['theme'];
    }
}
