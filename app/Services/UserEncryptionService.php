<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Log;

class UserEncryptionService
{
    /**
     * Enkripsi data sensitif
     *
     * @param string $data
     * @return string
     * @throws EncryptException
     */
    public function encrypt(string $data): string
    {
        if (empty($data)) {
            return $data;
        }

        Log::debug('Encrypting data', [
            'length' => strlen($data),
        ]);

        $cipher = Crypt::encryptString($data);

        Log::debug('Encrypted data generated', [
            'length' => strlen($cipher),
        ]);

        return $cipher;
    }

    /**
     * Dekripsi data sensitif
     *
     * @param string $encryptedData
     * @return string
     * @throws DecryptException
     */
    public function decrypt(string $encryptedData): string
    {
        if (empty($encryptedData)) {
            return $encryptedData;
        }

        Log::debug('Decrypting data', [
            'length' => strlen($encryptedData),
        ]);

        try {
            $plain = Crypt::decryptString($encryptedData);

            Log::debug('Decrypted data generated', [
                'length' => strlen($plain),
                'valid_utf8' => mb_check_encoding($plain, 'UTF-8'),
            ]);

            return $plain;
        } catch (DecryptException $e) {
            Log::error('DecryptException occurred', [
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Enkripsi array data sensitif
     *
     * @param array $data
     * @param array $sensitiveKeys
     * @return array
     */
    public function encryptArray(array $data, array $sensitiveKeys): array
    {
        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key]) && !empty($data[$key])) {
                $data[$key] = $this->encrypt($data[$key]);
            }
        }

        return $data;
    }

    /**
     * Dekripsi array data sensitif
     *
     * @param array $data
     * @param array $sensitiveKeys
     * @return array
     */
    public function decryptArray(array $data, array $sensitiveKeys): array
    {
        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key]) && !empty($data[$key])) {
                $data[$key] = $this->decrypt($data[$key]);
            }
        }

        return $data;
    }

    /**
     * Validasi apakah data terenkripsi
     *
     * @param string $data
     * @return bool
     */
    public function isEncrypted(string $data): bool
    {
        if (empty($data)) {
            return false;
        }

        try {
            $this->decrypt($data);
            return true;
        } catch (DecryptException $e) {
            return false;
        }
    }
}