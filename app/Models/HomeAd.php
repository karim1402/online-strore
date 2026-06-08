<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HomeAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'link_type',
        'module_id',
        'image',
        'image_ar',
        'image_v2',
        'image_ar_v2',
        'sort_order',
        'is_active',
    ];

    /**
     * Get the module associated with the ad.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the products associated with the ad.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'home_ad_products');
    }

    public function scopeModule($query)
    {
        return $query->where('link_type', 'module');
    }

    public function scopeProduct($query)
    {
        return $query->where('link_type', 'product');
    }

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['image_url', 'image_ar_url'];

    /**
     * Get the image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            if (Storage::disk('public')->exists($this->image)) {
                return Storage::disk('public')->url($this->image);
            }
            return Storage::disk('r2')->url($this->image);
        }
        return null;
    }

    public function getImageArUrlAttribute(): ?string
    {
        if ($this->image_ar) {
            if (filter_var($this->image_ar, FILTER_VALIDATE_URL)) {
                return $this->image_ar;
            }
            if (Storage::disk('public')->exists($this->image_ar)) {
                return Storage::disk('public')->url($this->image_ar);
            }
            return Storage::disk('r2')->url($this->image_ar);
        }
        return null;
    }

    public function getImageArV2UrlAttribute(): ?string
    {
        if ($this->image_ar_v2) {
            if (filter_var($this->image_ar_v2, FILTER_VALIDATE_URL)) {
                return $this->image_ar_v2;
            }
            if (Storage::disk('public')->exists($this->image_ar_v2)) {
                return Storage::disk('public')->url($this->image_ar_v2);
            }
            return Storage::disk('r2')->url($this->image_ar_v2);
        }
        return null;
    }

    public function getImageV2UrlAttribute(): ?string
    {
        if ($this->image_v2) {
            if (filter_var($this->image_v2, FILTER_VALIDATE_URL)) {
                return $this->image_v2;
            }
            if (Storage::disk('public')->exists($this->image_v2)) {
                return Storage::disk('public')->url($this->image_v2);
            }
            return Storage::disk('r2')->url($this->image_v2);
        }
        return null;
    }

    /**
     * Scope a query to only include active ads.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to include only banners.
     */
    public function scopeBanner($query)
    {
        return $query->where('type', 'banner');
    }

    /**
     * Scope a query to include only offers.
     */
    public function scopeOffer($query)
    {
        return $query->where('type', 'offer');
    }

    /**
     * Scope to order by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')
                     ->orderBy('created_at', 'desc');
    }
}
