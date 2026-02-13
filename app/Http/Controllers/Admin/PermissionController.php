<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Permission::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        $permissions = $query->orderBy('group')->orderBy('name')->get();
        $groups = Permission::distinct()->pluck('group')->filter()->sort()->values();
        $roles = Role::all();

        return view('admin.permissions.index', compact('permissions', 'groups', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'group' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        Permission::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'group' => $validated['group'] ?? 'general',
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Permission created successfully.');
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'group' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $permission->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'group' => $validated['group'] ?? 'general',
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return back()->with('success', 'Permission deleted successfully.');
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $role->permissions()->sync($validated['permissions'] ?? []);

        return back()->with('success', 'Permissions assigned to role successfully.');
    }

    public function generate()
    {
        $controllers = [
            'DashboardController',
            'CategoryController',
            'Product\ProductController',
            'Attribute\AttributeController',
            'Attribute\AttributeValueController',
            'BrandController',
            'OrderController',
            'InventoryController',
            'InventoryMovementController',
            'PurchaseBatchController',
            'CustomerController',
            'CouponController',
            'FlashSaleController',
            'ReviewController',
            'CmsPageController',
            'PartnerController',
            'PartnerCalculationController',
            'CapitalAccountController',
            'FinancialTransactionController',
            'InvestmentController',
            'InvestorController',
            'ExpenseController',
            'LoyaltyController',
            'ReportController',
            'SettingController',
            'RoleController',
            'PermissionController',
            'Customer\AccountController',
            'Customer\OrderController',
            'Shop\OrderTrackingController',
            'Shop\SearchController',
        ];

        $methods = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];

        $count = 0;

        foreach ($controllers as $controller) {
            $className = "App\\Http\\Controllers\\{$controller}";
            if (!class_exists($className)) {
                continue;
            }

            $reflection = new \ReflectionClass($className);
            $controllerGroup = str_replace('Controller', '', $controller);
            $controllerGroup = str_replace('\\', '/', $controllerGroup);

            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if (in_array($method->getName(), $methods)) {
                    $name = "{$controllerGroup}.{$method->getName()}";
                    $slug = str_replace('/', '.', $name);

                    if (!Permission::where('slug', $slug)->exists()) {
                        $comment = $method->getDocComment();
                        $description = null;

                        if ($comment) {
                            $description = trim(preg_replace('/(\/\*\*|\*\/|\s\*)/', '', $comment));
                        }

                        Permission::create([
                            'name' => ucfirst($method->getName()) . ' ' . $controllerGroup,
                            'slug' => $slug,
                            'group' => $controllerGroup,
                            'description' => $description,
                        ]);

                        $count++;
                    }
                }
            }
        }

        return back()->with('success', "Generated {$count} new permissions.");
    }
}
