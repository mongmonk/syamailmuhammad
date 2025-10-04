<?php

namespace App\Casts;

use App\Services\UserEncryptionService;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class EncryptedString implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     * @return string|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        $encryptionService = App::make(UserEncryptionService::class);
        
        if (is_null($value)) {
            return $value;
        }

        return $encryptionService->decrypt($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     * @return string|null
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        $encryptionService = App::make(UserEncryptionService::class);
        
        if (is_null($value)) {
            return $value;
        }

        return $encryptionService->encrypt($value);
    }
}