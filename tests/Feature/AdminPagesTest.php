<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'is_admin' => true,
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    /** @test */
    public function admin_dashboard_loads()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    /** @test */
    public function inventory_pages_load()
    {
        $routes = [
            'admin.inventory.index',
            'admin.inventory.alerts',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->admin)->get(route($route));
            $response->assertStatus(200);
        }
    }

    /** @test */
    public function report_pages_load()
    {
        $routes = [
            'admin.reports.index',
            'admin.reports.sales',
            'admin.reports.products',
            'admin.reports.inventory',
            'admin.reports.product-profit',
            'admin.reports.category-profit',
            'admin.reports.expenses',
            'admin.reports.partners',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->admin)->get(route($route));
            $response->assertStatus(200);
            echo "✓ {$route} loads successfully\n";
        }
    }

    /** @test */
    public function product_pages_load()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.products.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('admin.products.create'));
        $response->assertStatus(200);
    }

    /** @test */
    public function order_pages_load()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.orders.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function purchase_batch_pages_load()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.purchase-batches.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function all_stat_card_links_are_valid()
    {
        // Test dashboard stat card links
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $content = $response->getContent();

        // Check for broken route references
        $this->assertStringNotContainsString('admin.reports.revenue', $content, 'Dashboard still references admin.reports.revenue');
        $this->assertStringNotContainsString('admin.inventory.low-stock', $content, 'Dashboard still references admin.inventory.low-stock');
    }
}
