<?php
namespace App\Traits;

trait Translatable
{
    public function translations()
    {
        return $this->morphMany(\App\Models\Translation::class, 'translatable');
    }

    public function translate($field, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        $translation = $this->translations()
            ->where('field', $field)
            ->where('locale', $locale)
            ->first();

        return $translation ? $translation->value : null;
    }

    /**
     * Get all translated attributes for the current locale
     */
    public function getTranslatedAttributes()
    {
        $translations = $this->translations()
            ->where('locale', app()->getLocale())
            ->get();

        $translatedAttributes = [];
        foreach ($translations as $translation) {
            $translatedAttributes[$translation->field] = $translation->value;
        }

        return $translatedAttributes;
    }

    public function getAttribute($key)
    {
        // If the attribute is in the translatable array and no specific locale is requested
        if (in_array($key, $this->translatable ?? [])) {
            // Check if the key ends with _locale (like name_en, description_fr)
            if (!str_ends_with($key, '_locale')) {
                return $this->translate($key) ?? parent::getAttribute($key);
            }
        }

        // Allow for locale-specific requests (e.g., name_en, description_fr)
        foreach ($this->translatable ?? [] as $translatable) {
            if (str_starts_with($key, "{$translatable}_")) {
                $locale = substr($key, strlen($translatable) + 1);
                return $this->translate($translatable, $locale);
            }
        }

        return parent::getAttribute($key);
    }

    /**
     * Convert the model instance to an array with translations
     */
    public function toArray()
    {
        $attributes = parent::toArray();

        // Merge translated attributes for current locale
        foreach ($this->translatable ?? [] as $field) {
            $attributes[$field] = $this->translate($field) ?? $attributes[$field] ?? null;
        }

        return $attributes;
    }
}