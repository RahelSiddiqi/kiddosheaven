<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->default('tag');
            $table->string('color')->default('blue');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default categories
        $categories = [
            ['name' => 'Rent & Utilities', 'icon' => 'home', 'color' => 'blue'],
            ['name' => 'Salaries & Wages', 'icon' => 'users', 'color' => 'green'],
            ['name' => 'Marketing & Advertising', 'icon' => 'megaphone', 'color' => 'purple'],
            ['name' => 'Shipping & Logistics', 'icon' => 'truck', 'color' => 'orange'],
            ['name' => 'Inventory & Supplies', 'icon' => 'box', 'color' => 'yellow'],
            ['name' => 'Equipment & Maintenance', 'icon' => 'wrench', 'color' => 'red'],
            ['name' => 'Professional Services', 'icon' => 'briefcase', 'color' => 'indigo'],
            ['name' => 'Software & Subscriptions', 'icon' => 'laptop', 'color' => 'cyan'],
            ['name' => 'Travel & Entertainment', 'icon' => 'plane', 'color' => 'pink'],
            ['name' => 'Miscellaneous', 'icon' => 'dots', 'color' => 'gray'],
        ];

        foreach ($categories as $category) {
            \App\Models\ExpenseCategory::create($category);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};
