<?php

namespace App\Support\Cache;

/**
 * Centralized cache key management for the application.
 */
class CacheKeys
{
    // Product cache keys
    public static function product(int $id): string
    {
        return "product:{$id}";
    }

    public static function productVariants(int $productId): string
    {
        return "product:{$productId}:variants";
    }

    public static function productInventory(int $productId): string
    {
        return "product:{$productId}:inventory";
    }

    // Catalog cache keys
    public static function catalog(int $id): string
    {
        return "catalog:{$id}";
    }

    public static function catalogProducts(int $catalogId): string
    {
        return "catalog:{$catalogId}:products";
    }

    public static function catalogCategories(int $catalogId): string
    {
        return "catalog:{$catalogId}:categories";
    }

    // Category cache keys
    public static function category(int $id): string
    {
        return "category:{$id}";
    }

    public static function categoryTree(int $catalogId): string
    {
        return "catalog:{$catalogId}:category-tree";
    }

    public static function categoryProducts(int $categoryId): string
    {
        return "category:{$categoryId}:products";
    }

    // Settings cache keys
    public static function siteSettings(): string
    {
        return "site:settings";
    }

    public static function themeSettings(): string
    {
        return "site:theme:settings";
    }

    // User cache keys
    public static function userCart(int $userId): string
    {
        return "user:{$userId}:cart";
    }

    public static function userWishlist(int $userId): string
    {
        return "user:{$userId}:wishlist";
    }

    public static function userLoyaltyPoints(int $userId): string
    {
        return "user:{$userId}:loyalty-points";
    }

    // Inventory cache keys
    public static function batchInventory(int $batchId): string
    {
        return "batch:{$batchId}:inventory";
    }

    public static function lowStockProducts(): string
    {
        return "inventory:low-stock-products";
    }

    // Cache TTL constants (in seconds)
    public const TTL_SHORT = 300; // 5 minutes
    public const TTL_MEDIUM = 1800; // 30 minutes
    public const TTL_LONG = 3600; // 1 hour
    public const TTL_DAY = 86400; // 24 hours
}
