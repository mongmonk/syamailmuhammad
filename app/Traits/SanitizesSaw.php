<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait SanitizesSaw
{
    /**
     * Replace occurrences of "Nabi|Muhammad|Rasulullah SAW" with the ﷺ symbol.
     */
    protected function sanitizeSawText(?string $text): ?string
    {
        if ($text === null) {
            return null;
        }

        // Regex: matches word before SAW among specified terms (case-insensitive), allowing any whitespace between.
        return preg_replace('/\b(Nabi|Muhammad|Rasulullah)\s+SAW\b(?=[\s\p{P}\)]|$)/iu', '$1 ﷺ', $text);
    }

    /**
     * Automatically sanitize configured attributes on model saving.
     *
     * To enable, define protected $sawSanitize = ['attribute1', ...] on the model
     */
    public static function bootSanitizesSaw(): void
    {
        static::saving(function (Model $model): void {
            if (property_exists($model, 'sawSanitize') && is_array($model->sawSanitize)) {
                foreach ($model->sawSanitize as $attribute) {
                    $value = $model->getAttribute($attribute);

                    if ($value === null || is_string($value)) {
                        $model->setAttribute($attribute, $model->sanitizeSawText($value));
                    }
                }
            }
        });
    }
}