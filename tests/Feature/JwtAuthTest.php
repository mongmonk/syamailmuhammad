<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\JwtService;

class JwtAuthTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $phone, string $password, string $status, string $role = User::ROLE_USER): User
    {
        return User::factory()->create([
            'name' => 'Tester ' . substr($phone, -4),
            'email' => null,
            'phone' => $phone,
            'password' => Hash::make($password),
            'status' => $status,
            'role' => $role,
        ]);
    }

    public function test_api_login_returns_token_for_active_user()
    {
        $user = $this->createUser('+6281111111101', 'password123', User::STATUS_ACTIVE);

        $resp = $this->postJson('/api/auth/login', [
            'phone' => $user->phone,
            'password' => 'password123',
        ]);

        $resp->assertStatus(200)
            ->assertJsonStructure(['token', 'token_type', 'expires_in', 'user']);

        $token = $resp->json('token');
        $this->assertNotEmpty($token);

        // Use token to access protected /api/me (jwt + not.banned)
        $me = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/me');

        $me->assertStatus(200)
           ->assertJsonStructure(['user' => ['id', 'name', 'email', 'phone', 'status', 'role']]);
        $this->assertSame(User::STATUS_ACTIVE, $me->json('user.status'));
    }

    public function test_api_login_returns_token_for_pending_user_and_access_me()
    {
        $user = $this->createUser('+6281111111102', 'password123', User::STATUS_PENDING);

        $resp = $this->postJson('/api/auth/login', [
            'phone' => $user->phone,
            'password' => 'password123',
        ]);

        $resp->assertStatus(200)
            ->assertJsonStructure(['token', 'token_type', 'expires_in', 'user']);
        $token = $resp->json('token');

        // Pending user boleh akses /api/me (blocked hanya pada endpoint yang mensyaratkan aktif)
        $me = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/me');

        $me->assertStatus(200)
           ->assertJsonStructure(['user' => ['id', 'name', 'email', 'phone', 'status', 'role']]);
        $this->assertSame(User::STATUS_PENDING, $me->json('user.status'));
    }

    public function test_api_login_rejects_banned_user_with_403()
    {
        $user = $this->createUser('+6281111111103', 'password123', User::STATUS_BANNED);

        $resp = $this->postJson('/api/auth/login', [
            'phone' => $user->phone,
            'password' => 'password123',
        ]);

        $resp->assertStatus(403)
            ->assertJson([
                'code' => 'USER_STATUS_BANNED',
            ]);
    }

    public function test_api_login_invalid_credentials_returns_401()
    {
        $user = $this->createUser('+6281111111104', 'password123', User::STATUS_ACTIVE);

        $resp = $this->postJson('/api/auth/login', [
            'phone' => $user->phone,
            'password' => 'wrongpassword',
        ]);

        $resp->assertStatus(401)
            ->assertJson([
                'code' => 'UNAUTHENTICATED',
            ]);
    }

    public function test_jwt_protected_endpoint_requires_token_missing_returns_401()
    {
        $resp = $this->getJson('/api/me');

        $resp->assertStatus(401)
            ->assertJson([
                'code' => 'UNAUTHENTICATED',
            ]);
    }

    public function test_jwt_protected_endpoint_invalid_token_returns_401()
    {
        $resp = $this->withHeaders([
            'Authorization' => 'Bearer invalid.token.value',
        ])->getJson('/api/me');

        $resp->assertStatus(401)
            ->assertJson([
                'code' => 'UNAUTHENTICATED',
            ]);
    }

    public function test_banned_user_token_is_blocked_by_middleware_on_protected_endpoint()
    {
        // Simulasikan token yang "bocor" untuk user banned (walau loginApi tidak akan mengeluarkan token)
        $banned = $this->createUser('+6281111111105', 'password123', User::STATUS_BANNED);

        /** @var JwtService $jwt */
        $jwt = app(JwtService::class);
        $token = $jwt->issueToken($banned, 3600);

        $resp = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/me');

        $resp->assertStatus(403)
            ->assertJson([
                'code' => 'USER_STATUS_BANNED',
            ]);
    }

    public function test_non_admin_cannot_access_admin_users_endpoints()
    {
        $activeUser = $this->createUser('+6281111111106', 'password123', User::STATUS_ACTIVE, User::ROLE_USER);

        $login = $this->postJson('/api/auth/login', [
            'phone' => $activeUser->phone,
            'password' => 'password123',
        ]);
        $token = $login->json('token');

        $resp = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/users');

        $resp->assertStatus(403)
            ->assertJsonFragment(['code' => 'FORBIDDEN_ADMIN_ONLY']);
    }

    public function test_admin_can_manage_users_and_posts_via_api()
    {
        $admin = $this->createUser('+6281111111199', 'password123', User::STATUS_ACTIVE, User::ROLE_ADMIN);

        $login = $this->postJson('/api/auth/login', [
            'phone' => $admin->phone,
            'password' => 'password123',
        ]);
        $login->assertStatus(200);
        $token = $login->json('token');

        // GET /api/users
        $index = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/users');
        $index->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);

        // POST /api/users
        $createUser = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/users', [
            'name' => 'User Baru',
            'phone' => '+6281111111200',
            'password' => 'password123',
            // status/role default jika tidak diisi: pending & user
        ]);
        $createUser->assertStatus(201)
            ->assertJsonStructure(['message', 'user' => ['id', 'name', 'email', 'phone', 'status', 'role']]);
        $newUserId = $createUser->json('user.id');

        // PATCH /api/users/{user} - ubah status ke active, role ke admin
        $updateUser = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson('/api/users/' . $newUserId, [
            'status' => User::STATUS_ACTIVE,
            'role' => User::ROLE_ADMIN,
        ]);
        $updateUser->assertStatus(200)
            ->assertJsonStructure(['message', 'changed', 'user']);
        $this->assertEquals(User::STATUS_ACTIVE, $updateUser->json('user.status'));
        $this->assertEquals(User::ROLE_ADMIN, $updateUser->json('user.role'));

        // DELETE /api/users/{user}
        $deleteUser = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/users/' . $newUserId);
        $deleteUser->assertStatus(200)
            ->assertJson(['message' => 'Pengguna dihapus']);

        // POSTS management
        // POST /api/posts
        $createPost = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/posts', [
            'title' => 'Judul Post Admin',
            'body' => 'Konten post admin',
            // slug opsional; akan digenerate unik dari title bila kosong
            'is_published' => true,
        ]);
        $createPost->assertStatus(201)
            ->assertJsonStructure(['message', 'post' => ['id', 'title', 'slug', 'is_published', 'created_by', 'created_at']]);
        $postId = $createPost->json('post.id');

        // PATCH /api/posts/{post}
        $updatePost = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson('/api/posts/' . $postId, [
            'title' => 'Judul Post Diubah',
            'is_published' => false,
        ]);
        $updatePost->assertStatus(200)
            ->assertJsonStructure(['message', 'changed', 'post']);
        $this->assertEquals('Judul Post Diubah', $updatePost->json('post.title'));
        $this->assertFalse($updatePost->json('post.is_published'));

        // DELETE /api/posts/{post}
        $deletePost = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/posts/' . $postId);
        $deletePost->assertStatus(200)
            ->assertJson(['message' => 'Post dihapus']);
    }

    public function test_non_admin_cannot_manage_posts()
    {
        $user = $this->createUser('+6281111111210', 'password123', User::STATUS_ACTIVE, User::ROLE_USER);

        $login = $this->postJson('/api/auth/login', [
            'phone' => $user->phone,
            'password' => 'password123',
        ]);
        $token = $login->json('token');

        $createPost = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/posts', [
            'title' => 'Judul Post User',
            'body' => 'Konten post user',
            'is_published' => true,
        ]);
        $createPost->assertStatus(403)
            ->assertJsonFragment(['code' => 'FORBIDDEN_ADMIN_ONLY']);
    }
}