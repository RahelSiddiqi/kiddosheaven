<?php

namespace App\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Class aliases for backward compatibility.
     * Maps old App\Models and App\Services namespaces to new App\Domains namespaces.
     */
    protected array $modelAliases = [
        // Catalog Domain
        'App\Models\Catalog' => \App\Domains\Catalog\Models\Catalog::class,
        'App\Models\Category' => \App\Domains\Catalog\Models\Category::class,
        'App\Models\Brand' => \App\Domains\Catalog\Models\Brand::class,
        'App\Models\PricingTemplate' => \App\Domains\Catalog\Models\PricingTemplate::class,

        // Product Domain
        'App\Models\Product' => \App\Domains\Product\Models\Product::class,
        'App\Models\ProductVariant' => \App\Domains\Product\Models\ProductVariant::class,
        'App\Models\ProductAttribute' => \App\Domains\Product\Models\ProductAttribute::class,
        'App\Models\ProductAttributeValue' => \App\Domains\Product\Models\ProductAttributeValue::class,
        'App\Models\VariantAttribute' => \App\Domains\Product\Models\VariantAttribute::class,

        // Inventory Domain
        'App\Models\PurchaseBatch' => \App\Domains\Inventory\Models\PurchaseBatch::class,
        'App\Models\InventoryMovement' => \App\Domains\Inventory\Models\InventoryMovement::class,
        'App\Models\InventoryItem' => \App\Domains\Inventory\Models\InventoryItem::class,

        // Order Domain
        'App\Models\Order' => \App\Domains\Order\Models\Order::class,
        'App\Models\OrderItem' => \App\Domains\Order\Models\OrderItem::class,

        // Customer Domain
        'App\Models\Address' => \App\Domains\Customer\Models\Address::class,
        'App\Models\Wishlist' => \App\Domains\Customer\Models\Wishlist::class,
        'App\Models\Review' => \App\Domains\Customer\Models\Review::class,

        // Marketing Domain
        'App\Models\Coupon' => \App\Domains\Marketing\Models\Coupon::class,
        'App\Models\FlashSale' => \App\Domains\Marketing\Models\FlashSale::class,
        'App\Models\LoyaltyProgram' => \App\Domains\Marketing\Models\LoyaltyProgram::class,
        'App\Models\LoyaltyTransaction' => \App\Domains\Marketing\Models\LoyaltyTransaction::class,

        // Accounting Domain
        'App\Models\Expense' => \App\Domains\Accounting\Models\Expense::class,
        'App\Models\ExpenseCategory' => \App\Domains\Accounting\Models\ExpenseCategory::class,
        'App\Models\Partner' => \App\Domains\Accounting\Models\Partner::class,
        'App\Models\PartnerPayment' => \App\Domains\Accounting\Models\PartnerPayment::class,
        'App\Models\PartnerCalculation' => \App\Domains\Accounting\Models\PartnerCalculation::class,
        'App\Models\Investment' => \App\Domains\Accounting\Models\Investment::class,
        'App\Models\Investor' => \App\Domains\Accounting\Models\Investor::class,
        'App\Models\CapitalAccount' => \App\Domains\Accounting\Models\CapitalAccount::class,
        'App\Models\FinancialTransaction' => \App\Domains\Accounting\Models\FinancialTransaction::class,

        // Content Domain
        'App\Models\CmsPage' => \App\Domains\Content\Models\CmsPage::class,
        'App\Models\Setting' => \App\Domains\Content\Models\Setting::class,

        // Auth Domain
        'App\Models\Role' => \App\Domains\Auth\Models\Role::class,
        'App\Models\Permission' => \App\Domains\Auth\Models\Permission::class,
    ];

    /**
     * Service aliases for backward compatibility.
     */
    protected array $serviceAliases = [
        'App\Services\InventoryService' => \App\Domains\Inventory\Services\InventoryService::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        // Register class aliases for backward compatibility
        $loader = AliasLoader::getInstance();

        foreach ($this->modelAliases as $alias => $class) {
            $loader->alias($alias, $class);
        }

        foreach ($this->serviceAliases as $alias => $class) {
            $loader->alias($alias, $class);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
