<?php

namespace Tests\Unit;

use App\Services\UserEncryptionService;
use Tests\TestCase;

class UserEncryptionServiceTest extends TestCase
{
    protected $encryptionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->encryptionService = new UserEncryptionService();
    }

    /** @test */
    public function it_can_encrypt_and_decrypt_data()
    {
        $originalData = 'test@example.com';
        
        // Encrypt data
        $encryptedData = $this->encryptionService->encrypt($originalData);
        
        // Verify encrypted data is different from original
        $this->assertNotEquals($originalData, $encryptedData);
        
        // Decrypt data
        $decryptedData = $this->encryptionService->decrypt($encryptedData);
        
        // Verify decrypted data matches original
        $this->assertEquals($originalData, $decryptedData);
    }

    /** @test */
    public function it_can_encrypt_and_decrypt_phone_number()
    {
        $originalPhone = '+6281234567890';
        
        // Encrypt phone number
        $encryptedPhone = $this->encryptionService->encrypt($originalPhone);
        
        // Verify encrypted data is different from original
        $this->assertNotEquals($originalPhone, $encryptedPhone);
        
        // Decrypt phone number
        $decryptedPhone = $this->encryptionService->decrypt($encryptedPhone);
        
        // Verify decrypted data matches original
        $this->assertEquals($originalPhone, $decryptedPhone);
    }

    /** @test */
    public function it_can_handle_empty_data()
    {
        $emptyData = '';
        
        // Encrypt empty data
        $encryptedData = $this->encryptionService->encrypt($emptyData);
        
        // Should return empty string
        $this->assertEquals('', $encryptedData);
        
        // Decrypt empty data
        $decryptedData = $this->encryptionService->decrypt($encryptedData);
        
        // Should return empty string
        $this->assertEquals('', $decryptedData);
    }

    /** @test */
    public function it_can_encrypt_and_decrypt_array()
    {
        $originalData = [
            'email' => 'user@example.com',
            'phone' => '+6281234567890',
            'name' => 'John Doe'
        ];
        
        $sensitiveKeys = ['email', 'phone'];
        
        // Encrypt array
        $encryptedArray = $this->encryptionService->encryptArray($originalData, $sensitiveKeys);
        
        // Verify sensitive data is encrypted
        $this->assertNotEquals($originalData['email'], $encryptedArray['email']);
        $this->assertNotEquals($originalData['phone'], $encryptedArray['phone']);
        
        // Verify non-sensitive data is unchanged
        $this->assertEquals($originalData['name'], $encryptedArray['name']);
        
        // Decrypt array
        $decryptedArray = $this->encryptionService->decryptArray($encryptedArray, $sensitiveKeys);
        
        // Verify all data matches original
        $this->assertEquals($originalData['email'], $decryptedArray['email']);
        $this->assertEquals($originalData['phone'], $decryptedArray['phone']);
        $this->assertEquals($originalData['name'], $decryptedArray['name']);
    }

    /** @test */
    public function it_can_detect_encrypted_data()
    {
        $originalData = 'test@example.com';
        
        // Test with plain text
        $this->assertFalse($this->encryptionService->isEncrypted($originalData));
        
        // Encrypt data
        $encryptedData = $this->encryptionService->encrypt($originalData);
        
        // Test with encrypted data
        $this->assertTrue($this->encryptionService->isEncrypted($encryptedData));
    }
}