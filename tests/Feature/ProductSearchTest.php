<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use App\Models\Module;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    protected $store;
    protected $category;
    protected $module;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create necessary data for tests manually
        $this->module = Module::create([
            'name_en' => 'Food',
            'name_ar' => 'طعام',
            'description_en' => 'Food module',
            'description_ar' => 'وحدة الطعام',
            'image' => 'modules/default.png',
            'is_active' => true
        ]);

        $this->store = Store::create([
            'name_en' => 'Test Store',
            'name_ar' => 'متجر تجريبي',
            'description_en' => 'Test Store Description',
            'description_ar' => 'وصف متجر تجريبي',
            'logo' => 'stores/logo.png',
            'document' => 'stores/doc.pdf',
            'status' => 'approved'
        ]);

        $this->category = Category::create([
            'module_id' => $this->module->id,
            'name_en' => 'Test Category',
            'name_ar' => 'فئة تجريبية',
            'description_en' => 'Test Category Description',
            'description_ar' => 'وصف فئة تجريبية',
            'image' => 'categories/default.png',
            'is_active' => true
        ]);
    }

    public function test_search_endpoint_returns_results_by_name()
    {
        // Create a product matching the search
        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'name_en' => 'Test Product',
            'name_ar' => 'منتج تجريبي',
            'base_price' => 100,
            'is_active' => true
        ]);

        // Create a product NOT matching the search
        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'name_en' => 'Other Item',
            'name_ar' => 'عنصر آخر',
            'base_price' => 200,
            'is_active' => true
        ]);

        $response = $this->getJson('/api/user/products/search?q=Test');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.name', 'Test Product');
    }

    public function test_search_endpoint_returns_results_by_keyword()
    {
        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'name_en' => 'Special Item',
            'name_ar' => 'عنصر مميز',
            'search_keywords' => 'hidden_gem',
            'base_price' => 150,
            'is_active' => true
        ]);

        $response = $this->getJson('/api/user/products/search?q=hidden_gem');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.name', 'Special Item');
    }

    public function test_search_endpoint_filters_by_price()
    {
        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'name_en' => 'Cheap Item',
            'name_ar' => 'عنصر رخيص',
            'base_price' => 50,
            'is_active' => true
        ]);

        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'name_en' => 'Expensive Item',
            'name_ar' => 'عنصر غالي',
            'base_price' => 500,
            'is_active' => true
        ]);

        // Filter for cheap items
        $response = $this->getJson('/api/user/products/search?max_price=100');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.name', 'Cheap Item');

        // Filter for expensive items
        $response = $this->getJson('/api/user/products/search?min_price=200');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.name', 'Expensive Item');
    }

    public function test_search_endpoint_filters_by_module()
    {
        // Create another module
        $otherModule = Module::create([
            'name_en' => 'Electronics',
            'name_ar' => 'إلكترونيات',
            'description_en' => 'Electronics module',
            'description_ar' => 'وحدة الإلكترونيات',
            'image' => 'modules/electronics.png',
            'is_active' => true
        ]);

        // Create category for other module
        $otherCategory = Category::create([
            'module_id' => $otherModule->id,
            'name_en' => 'Phones',
            'name_ar' => 'هواتف',
            'description_en' => 'Phones category',
            'description_ar' => 'فئة الهواتف',
            'image' => 'categories/phones.png',
            'is_active' => true
        ]);

        // Product in target module (Food)
        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'name_en' => 'Food Item',
            'name_ar' => 'عنصر طعام',
            'base_price' => 50,
            'is_active' => true
        ]);

        // Product in other module (Electronics)
        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $otherCategory->id,
            'name_en' => 'Phone Item',
            'name_ar' => 'عنصر هاتف',
            'base_price' => 500,
            'is_active' => true
        ]);

        // Filter for Food module
        $response = $this->getJson('/api/user/products/search?module_id=' . $this->module->id);
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.name', 'Food Item');

        // Filter for Electronics module
        $response = $this->getJson('/api/user/products/search?module_id=' . $otherModule->id);
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.name', 'Phone Item');
    }
}
