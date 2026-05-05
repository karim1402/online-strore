<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class FeaturedSection extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'type',
        'item_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['name_en', 'name_ar', 'image_url'];

    private ?Model $cachedItem = null;
    private bool $itemResolved = false;

    public function getItem()
    {
        if (!$this->itemResolved) {
            $this->cachedItem = match ($this->type) {
                'module' => Module::find($this->item_id),
                'category', 'subcategory' => Category::find($this->item_id),
                default => null,
            };
            $this->itemResolved = true;
        }
        return $this->cachedItem;
    }

    public function getNameEnAttribute()
    {
        return $this->getItem()?->name_en;
    }

    public function getNameArAttribute()
    {
        return $this->getItem()?->name_ar;
    }

    public function getNameAttribute()
    {
        $locale = App::getLocale();
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getImageUrlAttribute()
    {
        $item = $this->getItem();
        if ($item?->image) {
            return Storage::disk('public')->url($item->image);
        }
        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type', 'item_id', 'sort_order', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Featured Section {$eventName}")
            ->useLogName('featured_section');
    }
}
