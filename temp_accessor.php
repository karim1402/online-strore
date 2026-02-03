
    /**
     * Get localized quantity attribute
     */
    public function getQuantityAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->quantity_ar : $this->quantity_en;
    }
