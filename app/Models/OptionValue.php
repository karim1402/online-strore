<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OptionValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'option_group_id',
        'value_en',
        'value_ar',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = ['value'];

    /**
     * Get the option group that owns the value
     */
    public function optionGroup(): BelongsTo
    {
        return $this->belongsTo(OptionGroup::class);
    }

    /**
     * Get the product option values that use this value
     */
    public function productOptionValues(): HasMany
    {
        return $this->hasMany(ProductOptionValue::class);
    }

    /**
     * Scope to get only active option values
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get values for a specific group
     */
    public function scopeForGroup($query, $groupId)
    {
        return $query->where('option_group_id', $groupId);
    }

    /**
     * Get localized value attribute
     */
    public function getValueAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->value_ar : $this->value_en;
    }
}
