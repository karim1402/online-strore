<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'branches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'name_en',
        'name_ar',
        'address',
        'latitude',
        'longitude',
        'phone',
        'description_en',
        'description_ar',
        'is_main',
        'is_active',
        'working_hours',
        'opening_time',
        'closing_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_main' => 'boolean',
        'is_active' => 'boolean',
        'working_hours' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['name', 'description'];

    /**
     * Get the store that owns the branch.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Check if branch is currently open based on opening and closing times.
     *
     * @return bool
     */
    public function isCurrentlyOpen(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (is_null($this->opening_time) || is_null($this->closing_time)) {
            return true; // If no times set, assume always open
        }

        $currentTime = now()->format('H:i:s');
        return $currentTime >= $this->opening_time && $currentTime <= $this->closing_time;
    }

    /**
     * Check if branch is open at a specific time.
     *
     * @param string $time Time in H:i or H:i:s format
     * @return bool
     */
    public function isOpenAt(string $time): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (is_null($this->opening_time) || is_null($this->closing_time)) {
            return true;
        }

        return $time >= $this->opening_time && $time <= $this->closing_time;
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
     * Scope a query to only include active branches.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include main branches.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    /**
     * Check if branch is the main branch.
     *
     * @return bool
     */
    public function isMain(): bool
    {
        return $this->is_main;
    }

    /**
     * Check if branch is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get distance from given coordinates (in kilometers).
     *
     * @param float $lat
     * @param float $lng
     * @return float
     */
    public function getDistanceFrom($lat, $lng): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Configure activity logging options.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name_en', 'name_ar', 'address', 'phone', 'opening_time', 'closing_time', 'is_main', 'is_active', 'latitude', 'longitude'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Branch {$eventName}")
            ->useLogName('branch');
    }
}
