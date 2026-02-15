# 🎨 UI DESIGN SYSTEM - shadcn/ui Style Backend

## Overview

For the backend admin panel, we'll implement a **shadcn/ui-inspired design** using Filament 3 with custom theming OR Livewire with shadcn/ui components.

---

## Option A: Filament 3 with shadcn/ui Theme (RECOMMENDED)

Filament 3 already has a modern design similar to shadcn/ui. We'll customize it to match shadcn/ui exactly.

### Filament 3 Features (Already shadcn/ui-like):
- ✅ Modern, clean interface
- ✅ Tailwind CSS-based
- ✅ Dark mode built-in
- ✅ Glassmorphism effects
- ✅ Beautiful form components
- ✅ Advanced tables with filters
- ✅ Modal dialogs
- ✅ Slide-over panels

### Custom shadcn/ui Theming for Filament

```php
// config/filament.php
return [
    'theme' => [
        'colors' => [
            'primary' => [
                50 => '#f0f9ff',
                100 => '#e0f2fe',
                200 => '#bae6fd',
                300 => '#7dd3fc',
                400 => '#38bdf8',
                500 => '#0ea5e9', // shadcn/ui blue
                600 => '#0284c7',
                700 => '#0369a1',
                800 => '#075985',
                900 => '#0c4a6e',
            ],
        ],
        'font' => [
            'family' => 'Inter, system-ui, sans-serif',
            'size' => '14px',
        ],
    ],
];
```

### Custom CSS (resources/css/filament/admin.css)

```css
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

:root {
    --background: 0 0% 100%;
    --foreground: 222.2 84% 4.9%;
    --card: 0 0% 100%;
    --card-foreground: 222.2 84% 4.9%;
    --popover: 0 0% 100%;
    --popover-foreground: 222.2 84% 4.9%;
    --primary: 221.2 83.2% 53.3%;
    --primary-foreground: 210 40% 98%;
    --secondary: 210 40% 96.1%;
    --secondary-foreground: 222.2 47.4% 11.2%;
    --muted: 210 40% 96.1%;
    --muted-foreground: 215.4 16.3% 46.9%;
    --accent: 210 40% 96.1%;
    --accent-foreground: 222.2 47.4% 11.2%;
    --destructive: 0 84.2% 60.2%;
    --destructive-foreground: 210 40% 98%;
    --border: 214.3 31.8% 91.4%;
    --input: 214.3 31.8% 91.4%;
    --ring: 221.2 83.2% 53.3%;
    --radius: 0.5rem;
}

.dark {
    --background: 222.2 84% 4.9%;
    --foreground: 210 40% 98%;
    --card: 222.2 84% 4.9%;
    --card-foreground: 210 40% 98%;
    --popover: 222.2 84% 4.9%;
    --popover-foreground: 210 40% 98%;
    --primary: 217.2 91.2% 59.8%;
    --primary-foreground: 222.2 47.4% 11.2%;
    --secondary: 217.2 32.6% 17.5%;
    --secondary-foreground: 210 40% 98%;
    --muted: 217.2 32.6% 17.5%;
    --muted-foreground: 215 20.2% 65.1%;
    --accent: 217.2 32.6% 17.5%;
    --accent-foreground: 210 40% 98%;
    --destructive: 0 62.8% 30.6%;
    --destructive-foreground: 210 40% 98%;
    --border: 217.2 32.6% 17.5%;
    --input: 217.2 32.6% 17.5%;
    --ring: 224.3 76.3% 48%;
}

/* shadcn/ui-style components */
.fi-btn {
    @apply inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50;
}

.fi-btn-primary {
    @apply bg-primary text-primary-foreground hover:bg-primary/90;
}

.fi-input {
    @apply flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50;
}

.fi-card {
    @apply rounded-lg border bg-card text-card-foreground shadow-sm;
}
```

---

## Option B: Livewire with shadcn-ui Components (Alternative)

If you want PURE shadcn/ui, use **WireUI** or **Tallstack UI** packages that provide shadcn/ui-style components for Livewire.

### Install Tallstack UI

```bash
composer require tallstackui/tallstackui
php artisan tallstackui:install
```

### shadcn/ui Components for Livewire

```php
// Button Component
<x-button variant="default">
    Click me
</x-button>

<x-button variant="destructive">
    Delete
</x-button>

<x-button variant="outline">
    Cancel
</x-button>

// Card Component
<x-card>
    <x-slot name="header">
        <h3 class="text-lg font-semibold">Card Title</h3>
    </x-slot>

    <p>Card content goes here</p>

    <x-slot name="footer">
        <x-button>Action</x-button>
    </x-slot>
</x-card>

// Table Component
<x-table>
    <x-slot name="header">
        <x-table.heading>Name</x-table.heading>
        <x-table.heading>Email</x-table.heading>
        <x-table.heading>Actions</x-table.heading>
    </x-slot>

    @foreach($users as $user)
        <x-table.row>
            <x-table.cell>{{ $user->name }}</x-table.cell>
            <x-table.cell>{{ $user->email }}</x-table.cell>
            <x-table.cell>
                <x-button size="sm">Edit</x-button>
            </x-table.cell>
        </x-table.row>
    @endforeach
</x-table>

// Dialog Component
<x-dialog wire:model="showDialog">
    <x-slot name="title">Delete User</x-slot>
    <x-slot name="description">
        Are you sure you want to delete this user? This action cannot be undone.
    </x-slot>

    <x-slot name="footer">
        <x-button variant="ghost" wire:click="$set('showDialog', false)">
            Cancel
        </x-button>
        <x-button variant="destructive" wire:click="deleteUser">
            Delete
        </x-button>
    </x-slot>
</x-dialog>
```

---

## Recommended Approach: **Filament 3 + shadcn/ui Theme**

### Why?
1. ✅ **Faster development** (Filament auto-generates CRUD)
2. ✅ **Built-in features** (filters, bulk actions, exports)
3. ✅ **shadcn/ui aesthetic** (with custom CSS)
4. ✅ **Better for admin panels** (designed for data management)
5. ✅ **Active community** (Laravel ecosystem)

### Implementation Plan

#### 1. Install Filament with Custom Theme

```bash
composer require filament/filament:^3.0
php artisan filament:install --panels
php artisan make:filament-theme
```

#### 2. Create shadcn/ui Color Palette

```php
// app/Filament/Themes/ShadcnTheme.php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->colors([
            'primary' => '#0ea5e9', // shadcn/ui blue
            'gray' => [
                50 => '#f9fafb',
                100 => '#f3f4f6',
                200 => '#e5e7eb',
                300 => '#d1d5db',
                400 => '#9ca3af',
                500 => '#6b7280',
                600 => '#4b5563',
                700 => '#374151',
                800 => '#1f2937',
                900 => '#111827',
            ],
        ])
        ->font('Inter')
        ->darkMode(true)
        ->brandName('Kiddo\'s Heaven')
        ->brandLogo(asset('logo.svg'))
        ->favicon(asset('favicon.ico'))
        ->topNavigation() // shadcn/ui-style top navigation
        ->sidebarCollapsibleOnDesktop()
        ->viteTheme('resources/css/filament/admin/theme.css');
}
```

#### 3. Create Custom Blade Components (shadcn/ui-style)

```php
// resources/views/filament/components/stat-card.blade.php
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
        <h3 class="tracking-tight text-sm font-medium">{{ $title }}</h3>
        <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            {!! $icon !!}
        </svg>
    </div>
    <div class="p-6 pt-0">
        <div class="text-2xl font-bold">{{ $value }}</div>
        <p class="text-xs text-muted-foreground">
            {{ $description }}
        </p>
    </div>
</div>

// Usage in Filament Widget
public function getStats(): array
{
    return [
        Stat::make('Total Revenue', '৳45,231.89')
            ->description('+20.1% from last month')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),
    ];
}
```

#### 4. Dashboard Layout (shadcn/ui-inspired)

```blade
{{-- resources/views/filament/pages/dashboard.blade.php --}}
<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <x-filament::stat-card
            title="Total Revenue"
            value="৳45,231.89"
            description="+20.1% from last month"
            icon='<path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'
            trend="up"
        />

        <x-filament::stat-card
            title="Orders"
            value="+2350"
            description="+180.1% from last month"
            icon='<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>'
            trend="up"
        />

        <x-filament::stat-card
            title="Products"
            value="+12,234"
            description="+19% from last month"
            icon='<rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/>'
            trend="up"
        />

        <x-filament::stat-card
            title="Active Tenants"
            value="+573"
            description="+201 since last hour"
            icon='<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>'
            trend="up"
        />
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7 mt-4">
        <div class="col-span-4">
            <x-filament::card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold">Overview</h3>
                </x-slot>

                {{-- Chart Component --}}
                <div class="h-80">
                    <livewire:revenue-chart />
                </div>
            </x-filament::card>
        </div>

        <div class="col-span-3">
            <x-filament::card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold">Recent Sales</h3>
                    <p class="text-sm text-muted-foreground">
                        You made 265 sales this month.
                    </p>
                </x-slot>

                <div class="space-y-8">
                    @foreach($recentOrders as $order)
                        <div class="flex items-center">
                            <div class="h-9 w-9 rounded-full bg-gray-100 flex items-center justify-center">
                                <span class="text-sm font-medium">{{ substr($order->customer->name, 0, 2) }}</span>
                            </div>
                            <div class="ml-4 space-y-1">
                                <p class="text-sm font-medium leading-none">{{ $order->customer->name }}</p>
                                <p class="text-sm text-muted-foreground">{{ $order->customer->email }}</p>
                            </div>
                            <div class="ml-auto font-medium">
                                +৳{{ number_format($order->total, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::card>
        </div>
    </div>
</x-filament-panels::page>
```

---

## Component Library

### shadcn/ui-style Components for Filament

```php
// 1. Button Variants
<x-filament::button variant="default">Default</x-filament::button>
<x-filament::button variant="destructive">Destructive</x-filament::button>
<x-filament::button variant="outline">Outline</x-filament::button>
<x-filament::button variant="secondary">Secondary</x-filament::button>
<x-filament::button variant="ghost">Ghost</x-filament::button>
<x-filament::button variant="link">Link</x-filament::button>

// 2. Badge
<x-filament::badge>Badge</x-filament::badge>
<x-filament::badge variant="secondary">Secondary</x-filament::badge>
<x-filament::badge variant="destructive">Destructive</x-filament::badge>
<x-filament::badge variant="outline">Outline</x-filament::badge>

// 3. Alert
<x-filament::alert type="default">
    <x-slot name="title">Heads up!</x-slot>
    You can add components to your app using the cli.
</x-filament::alert>

<x-filament::alert type="destructive">
    <x-slot name="title">Error</x-slot>
    Your session has expired. Please login again.
</x-filament::alert>

// 4. Separator
<div>
    <div class="space-y-1">
        <h4 class="text-sm font-medium leading-none">Radix Primitives</h4>
        <p class="text-sm text-muted-foreground">An open-source UI component library.</p>
    </div>
    <x-filament::separator class="my-4" />
    <div class="flex h-5 items-center space-x-4 text-sm">
        <div>Blog</div>
        <x-filament::separator orientation="vertical" />
        <div>Docs</div>
        <x-filament::separator orientation="vertical" />
        <div>Source</div>
    </div>
</div>
```

---

## Design Tokens

```css
/* resources/css/filament/admin/theme.css */

@import 'tailwindcss/base';
@import 'tailwindcss/components';
@import 'tailwindcss/utilities';

@layer base {
    * {
        @apply border-border;
    }

    body {
        @apply bg-background text-foreground;
        font-feature-settings: "rlig" 1, "calt" 1;
    }
}

@layer components {
    /* shadcn/ui Button Styles */
    .btn-default {
        @apply bg-primary text-primary-foreground shadow hover:bg-primary/90;
    }

    .btn-destructive {
        @apply bg-destructive text-destructive-foreground shadow-sm hover:bg-destructive/90;
    }

    .btn-outline {
        @apply border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground;
    }

    .btn-secondary {
        @apply bg-secondary text-secondary-foreground shadow-sm hover:bg-secondary/80;
    }

    .btn-ghost {
        @apply hover:bg-accent hover:text-accent-foreground;
    }

    .btn-link {
        @apply text-primary underline-offset-4 hover:underline;
    }

    /* shadcn/ui Card Styles */
    .card {
        @apply rounded-lg border bg-card text-card-foreground shadow-sm;
    }

    .card-header {
        @apply flex flex-col space-y-1.5 p-6;
    }

    .card-title {
        @apply text-2xl font-semibold leading-none tracking-tight;
    }

    .card-description {
        @apply text-sm text-muted-foreground;
    }

    .card-content {
        @apply p-6 pt-0;
    }

    .card-footer {
        @apply flex items-center p-6 pt-0;
    }

    /* shadcn/ui Input Styles */
    .input {
        @apply flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50;
    }
}
```

---

## Final Recommendation

**Use Filament 3 with shadcn/ui theming** for:
- ✅ Faster development (auto-generated CRUD)
- ✅ shadcn/ui aesthetic (with custom CSS)
- ✅ Built-in multi-tenancy support
- ✅ Excellent for admin panels

**Total effort:** +8 hours to customize Filament theme to match shadcn/ui exactly.

---

## Implementation Steps

1. **Week 6:** Install Filament 3
2. **Week 6:** Create custom shadcn/ui theme (CSS)
3. **Week 6-8:** Build all Filament resources with shadcn/ui components
4. **Week 8:** Test dark mode + responsiveness

**Result:** Beautiful shadcn/ui-style admin panel with Filament's power! 🎨
