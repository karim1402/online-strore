<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OptionGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name_en',
        'name_ar',
        'type',
        'is_active',
        'image',
        'makook_sandwitch',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'makook_sandwitch' => 'boolean',
    ];

    protected $appends = ['name'];

    /**
     * Get the option values for the group
     */
    public function values(): HasMany
    {
        return $this->hasMany(OptionValue::class);
    }

    /**
     * Get the product options that use this group
     */
    public function productOptions(): HasMany
    {
        return $this->hasMany(ProductOption::class);
    }

    /**
     * Scope to get only active option groups
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get localized name attribute
     */
    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }
}
