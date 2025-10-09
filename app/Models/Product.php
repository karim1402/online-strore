<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_id',
        'category_id',
        'name_en',
        'name_ar',
        'description_en',
        'description_ar',
        'search_keywords',
        'base_price',
        'is_active',
        'view_count',
        'sales_count',
        'metadata',
        'sort_order',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
        'view_count' => 'integer',
        'sales_count' => 'integer',
        'metadata' => 'array',
        'sort_order' => 'integer',
    ];

    protected $appends = ['name', 'description'];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($product) {
            DB::table('elasticsearch_sync_queue')->insert([
                'entity_type' => 'product',
                'entity_id' => $product->id,
                'action' => 'create',
            ]);
        });
        
        static::updated(function ($product) {
            DB::table('elasticsearch_sync_queue')->insert([
                'entity_type' => 'product',
                'entity_id' => $product->id,
                'action' => 'update',
            ]);
        });
        
        static::deleted(function ($product) {
            DB::table('elasticsearch_sync_queue')->insert([
                'entity_type' => 'product',
                'entity_id' => $product->id,
                'action' => 'delete',
            ]);
        });
    }

    /**
     * Get the store that owns the product
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the category that owns the product
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the images for the product
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the primary image for the product
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get the product options for the product
     */
    public function productOptions(): HasMany
    {
        return $this->hasMany(ProductOption::class);
    }

    /**
     * Get the addons for the product
     */
    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(Addon::class, 'product_addons')
            ->withPivot('is_available', 'sort_order')
            ->withTimestamps();
    }

    /**
     * Scope to get only active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get products for a specific store
     */
    public function scopeForStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    /**
     * Scope to get products for a specific category
     */
    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Get localized name attribute
     */
    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }

    /**
     * Get localized description attribute
     */
    public function getDescriptionAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->description_ar : $this->description_en;
    }

    /**
     * Increment view count
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    /**
     * Increment sales count
     */
    public function incrementSalesCount($quantity = 1)
    {
        $this->increment('sales_count', $quantity);
    }
}
