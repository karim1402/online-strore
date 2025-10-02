<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class Store extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stores';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name_en',
        'name_ar',
        'description_en',
        'description_ar',
        'address',
        'latitude',
        'longitude',
        'logo',
        'document',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['logo_url', 'document_url', 'name', 'description'];

    /**
     * Get the vendors that belong to the store.
     */
    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }

    /**
     * Get the main categories that the store belongs to.
     */
    public function mainCategories()
    {
        return $this->belongsToMany(MainCategory::class, 'main_category_store');
    }

    /**
     * Get the name attribute based on the current locale.
     *
     * @return string|null
     */
    public function getNameAttribute()
    {
        $locale = App::getLocale();
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }

    /**
     * Get the description attribute based on the current locale.
     *
     * @return string|null
     */
    public function getDescriptionAttribute()
    {
        $locale = App::getLocale();
        return $locale === 'ar' ? $this->description_ar : $this->description_en;
    }

    /**
     * Get the full URL for the logo.
     *
     * @return string|null
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return Storage::disk('public')->url($this->logo);
        }
        return null;
    }

    /**
     * Get the full URL for the document.
     *
     * @return string|null
     */
    public function getDocumentUrlAttribute()
    {
        if ($this->document) {
            return Storage::disk('public')->url($this->document);
        }
        return null;
    }

    /**
     * Scope a query to only include active stores.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include pending stores.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', false);
    }
}
