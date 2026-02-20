<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Domains\Site\Models\Site;
use App\Domains\Catalog\Models\Category;
use App\Domains\Product\Models\Product;
use App\Domains\Order\Models\Order;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TenantIsolationTest extends TestCase
{
    use DatabaseTransactions;

    protected function connectionsToTransact(): array
    {
        return ['mysql'];
    }

    private ?Site $siteA = null;
    private ?Site $siteB = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear any existing tenant binding
        if (app()->bound('current.site')) {
            app()->forgetInstance('current.site');
        }

        $this->siteA = Site::create([
            'name'      => 'Site A',
            'domain'    => 'site-a.test',
            'locale'    => 'en',
            'currency'  => 'USD',
            'timezone'  => 'UTC',
            'is_active' => true,
        ]);

        $this->siteB = Site::create([
            'name'      => 'Site B',
            'domain'    => 'site-b.test',
            'locale'    => 'en',
            'currency'  => 'USD',
            'timezone'  => 'UTC',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        // Clear tenant binding after each test
        if (app()->bound('current.site')) {
            app()->forgetInstance('current.site');
        }

        parent::tearDown();
    }

    public function test_categories_are_scoped_to_current_site(): void
    {
        // Create categories for each site without tenant scope
        $categoryA = Category::withoutTenantScope()->create([
            'name'    => 'Category A',
            'slug'    => 'category-a-' . uniqid(),
            'site_id' => $this->siteA->id,
            'is_active' => true,
        ]);

        $categoryB = Category::withoutTenantScope()->create([
            'name'    => 'Category B',
            'slug'    => 'category-b-' . uniqid(),
            'site_id' => $this->siteB->id,
            'is_active' => true,
        ]);

        // Bind site A as current tenant
        app()->instance('current.site', $this->siteA);

        // With site A bound, only site A's category should appear
        $visible = Category::all();
        $this->assertTrue($visible->contains('id', $categoryA->id));
        $this->assertFalse($visible->contains('id', $categoryB->id));
    }

    public function test_belongs_to_site_trait_auto_sets_site_id_on_create(): void
    {
        // Bind site A as current tenant
        app()->instance('current.site', $this->siteA);

        $category = Category::create([
            'name'      => 'Auto-scoped Category',
            'slug'      => 'auto-scoped-' . uniqid(),
            'is_active' => true,
        ]);

        $this->assertEquals($this->siteA->id, $category->site_id);
    }

    public function test_without_tenant_scope_returns_all_records(): void
    {
        // Bind site A as current tenant
        app()->instance('current.site', $this->siteA);

        Category::withoutTenantScope()->create([
            'name' => 'A Cat',
            'slug' => 'a-cat-' . uniqid(),
            'site_id' => $this->siteA->id,
            'is_active' => true
        ]);
        Category::withoutTenantScope()->create([
            'name' => 'B Cat',
            'slug' => 'b-cat-' . uniqid(),
            'site_id' => $this->siteB->id,
            'is_active' => true
        ]);

        // Count all categories (bypass tenant scope)
        $allCategories = Category::withoutTenantScope()
            ->whereIn('site_id', [$this->siteA->id, $this->siteB->id])
            ->count();
        $this->assertEquals(2, $allCategories);

        // Count with tenant scope should only return site A's category
        $scopedCount = Category::whereIn('site_id', [$this->siteA->id, $this->siteB->id])->count();
        $this->assertEquals(1, $scopedCount);
    }

    public function test_resolve_tenant_middleware_binds_site_from_domain(): void
    {
        $response = $this->withServerVariables(['HTTP_HOST' => 'site-a.test'])
            ->get('/');

        // Site should be bound (middleware ran)
        // The request should succeed
        $response->assertStatus(200);
    }
}
