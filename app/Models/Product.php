<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'store_id',
        'category_id',
        'subcategory_id',
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
        'is_best_seller',
        'best_seller_image',
        'offer_price',
        'quantity_en',
        'quantity_ar',
        'stock',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_best_seller' => 'boolean',
        'view_count' => 'integer',
        'sales_count' => 'integer',
        'metadata' => 'array',
        'sort_order' => 'integer',
        'stock' => 'integer',
    ];

    protected $appends = ['name', 'description', 'image_url', 'best_seller_image_url', 'effective_price', 'quantity', 'module_id'];

    /**
     * Get the effective price (offer price if set, otherwise base price)
     */
    public function getEffectivePriceAttribute()
    {
        return $this->offer_price !== null ? $this->offer_price : $this->base_price;
    }

    /**
     * Get the product image url
     */
    public function getImageUrlAttribute()
    {
        $image = $this->primaryImage ?? $this->images->first();
        return $image ? $image->image_url : null;
    }

    /**
     * Get the best seller image url
     */
    public function getBestSellerImageUrlAttribute()
    {
        if ($this->best_seller_image) {
             return \Illuminate\Support\Facades\Storage::disk('public')->url($this->best_seller_image);
        }
        return null;
    }

    /**
     * Get the module id
     */
    public function getModuleIdAttribute()
    {
        return $this->category->module_id ?? null;
    }

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
     * Get the subcategory that owns the product
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
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
     * Get localized quantity attribute
     */
    public function getQuantityAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->quantity_ar : $this->quantity_en;
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

    /**
     * Configure activity logging options.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name_en', 'name_ar', 'description_en', 'description_ar', 'base_price', 'is_active', 'category_id', 'subcategory_id', 'quantity_en', 'quantity_ar'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Product {$eventName}")
            ->useLogName('product');
    }
}
