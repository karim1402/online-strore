<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

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
        'logo',
        'document',
        'status',
        'rejection_note',
        'approved_at',
        'approved_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'approved_at' => 'datetime',
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
     * Get the branches that belong to the store.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Get the main branch of the store.
     */
    public function mainBranch()
    {
        return $this->hasOne(Branch::class)->where('is_main', true);
    }

    /**
     * Get the active branches of the store.
     */
    public function activeBranches()
    {
        return $this->hasMany(Branch::class)->where('is_active', true);
    }

    /**
     * Get the main categories that the store belongs to.
     */
    public function mainCategories()
    {
        return $this->belongsToMany(MainCategory::class, 'main_category_store');
    }

    /**
     * Get the product categories that belong to the store.
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function vendorInvoices()
    {
        return $this->hasMany(VendorInvoice::class);
    }

    /**
     * Get the admin who approved the store.
     */
    public function approvedBy()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
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
     * Scope a query to only include approved stores.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending stores.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include rejected stores.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if store is approved.
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if store is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if store is rejected.
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Scope a query to only include suspended stores.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    /**
     * Check if store is suspended.
     *
     * @return bool
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if store can process orders (approved and not suspended).
     *
     * @return bool
     */
    public function canProcessOrders(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Configure activity logging options.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name_en', 'name_ar', 'description_en', 'description_ar', 'status', 'rejection_note', 'approved_at', 'approved_by'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Store {$eventName}")
            ->useLogName('store');
    }
}
