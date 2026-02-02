<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Product;
use App\Models\Category;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\DB;

class OrderNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;
    protected $product;
    protected $address;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create User
        $this->user = User::factory()->create();

        // Create Admin with FCM Token
        $this->admin = Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
            'status' => true,
            'fcm_token' => 'dummy_fcm_token_12345'
        ]);

        // Create Category (Foreign Key for Product often needed)
        $category = Category::create([
            'name_en' => 'Test Category',
            'name_ar' => 'Test Category Ar',
            'slug' => 'test-category',
            'is_active' => true,
            'sort_order' => 1
            // 'module_id' might be needed if validation restricts, assuming nullable or optional for now
        ]);

        // Create Product
        $this->product = Product::create([
            'name_en' => 'Test Product',
            'name_ar' => 'Test Product Ar',
            'description_en' => 'Desc En',
            'description_ar' => 'Desc Ar',
            'base_price' => 100.00,
            'is_active' => true,
            'category_id' => $category->id,
            // 'store_id' => 1 // Assuming nullable or we can mock if needed
        ]);

        // Create Address
        $this->address = UserAddress::create([
            'user_id' => $this->user->id,
            'address_type' => 'apartment',
            'building_name' => 'Test Bldg',
            'apartment_number' => '1',
            'floor_number' => '1',
            'street_name' => 'Test St',
            'phone' => '1234567890',
            'latitude' => 30.0,
            'longitude' => 31.0,
        ]);
    }

    public function test_new_order_dispatches_notification_to_admin()
    {
        Notification::fake();

        // 1. Add item to cart
        $this->actingAs($this->user, 'api')
            ->postJson('/api/user/cart/items', [
                'product_id' => $this->product->id,
                'quantity' => 1
            ])->assertStatus(201);

        // 2. Checkout
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/user/checkout', [
                'address_id' => $this->address->id,
                'payment_method' => 'cash',
                'notes' => 'Test Order'
            ]);
            
        $response->assertStatus(201);

        // 3. Assert Notification Sent
        Notification::assertSentTo(
            [$this->admin],
            NewOrderNotification::class,
            function ($notification, $channels) {
                return in_array(\App\Channels\FcmChannel::class, $channels);
            }
        );
    }
}
