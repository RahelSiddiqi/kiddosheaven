# E-Commerce Admin Panel - shadcn/ui Implementation Plan

## Design System Overview

shadcn/ui (ui.shadcn.com) is a copy-paste based UI library with these key characteristics:

1. **Copy-Paste Components**: No npm package dependencies for components
2. **CSS Variables**: Uses `hsl()` format with CSS custom properties
3. **Tailwind Integration**: Fully integrated with Tailwind CSS
4. **Dark Mode**: Built-in CSS variable-based theming
5. **Accessibility**: WCAG compliant components
6. **Consistent Design Tokens**: Unified color scales and spacing

---

## 1. CSS Variables Architecture (shadcn/ui Pattern)

### 1.1 Primary CSS Variables

```css
/* Base colors */
--primary: 222.2 47.4% 11.2%;
--primary-foreground: 210 40% 98%;

/* Accent colors */
--accent: 210 40% 96.1%;
--accent-foreground: 222.2 47.4% 11.2%;

/* Destructive/Error */
--destructive: 0 84.2% 60.2%;
--destructive-foreground: 210 40% 98%;

/* Muted/Gray */
--muted: 210 40% 96.1%;
--muted-foreground: 215.4 16.3% 46.9%;

/* Borders & Inputs */
--border: 214.3 31.8% 91.4%;
--input: 214.3 31.8% 91.4%;
--ring: 222.2 47.4% 11.2%;

/* Radius */
--radius: 0.5rem;
```

### 1.2 Dashboard-Specific Colors

```css
/* Sidebar */
--sidebar-background: #f9fafb;
--sidebar-foreground: #1f2937;
--sidebar-border: #e5e7eb;

/* Header */
--header-background: #ffffff;
--header-foreground: #1f2937;

/* Cards */
--card-background: #ffffff;
--card-foreground: #1f2937;
--card-border: #e5e7eb;

/* Charts */
--chart-1: 221.2 83.2% 53.3%;
--chart-2: 160 60% 45%;
--chart-3: 30 80% 55%;
--chart-4: 280 65% 60%;
--chart-5: 340 75% 55%;
```

---

## 2. shadcn/ui Components to Implement

### 2.1 Core Components

```
resources/views/components/ui/
├── button.blade.php
│   └── Variants: default, destructive, outline, secondary, ghost, link
│   └── Sizes: default, sm, lg, icon
│
├── input.blade.php
│   └── With label wrapper, error handling
│
├── select.blade.php
│   └── Native select styling
│
├── textarea.blade.php
│   └── Auto-resize support
│
├── checkbox.blade.php
│   └── With label support
│
├── radio-group.blade.php
│   └── Radio button groups
│
├── switch.blade.php
│   └── Toggle switches
│
├── badge.blade.php
│   └── Variants: default, secondary, destructive, outline
│
├── card.blade.php
│   └── Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter
│
├── table.blade.php
│   └── Table, TableHeader, TableBody, TableFooter, TableRow, TableHead, TableCell
│
├── pagination.blade.php
│   └── Full pagination component
│
├── dropdown-menu.blade.php
│   └── Dropdown with submenus
│
├── dialog.blade.php
│   └── Modal dialogs
│
├── sheet.blade.php
│   └── Slide-over panels (mobile sidebar)
│
├── tabs.blade.php
│   └── Tab navigation
│
├── avatar.blade.php
│   └── User avatars
│
├── progress.blade.php
│   └── Progress bars
│
├── tooltip.blade.php
│   └── Hover tooltips
│
├── separator.blade.php
│   └── Horizontal/vertical dividers
│
├── alert.blade.php
│   └── Alert messages
│
├── label.blade.php
│   └── Form labels
│
└── form.blade.php
    └── Form field wrapper with validation
```

### 2.2 Layout Components

```
resources/views/components/layout/
├── sidebar.blade.php
│   └── Responsive sidebar with collapsible sections
│
├── header.blade.php
│   └── Page header with breadcrumbs
│
├── page-header.blade.php
│   └── Title + description + actions
│
├── section.blade.php
│   └── Content sections
│
└── container.blade.php
    └── Max-width container
```

### 2.3 Chart Components (using Recharts pattern)

```
resources/views/components/charts/
├── chart-container.blade.php
│   └── Base chart wrapper
│
├── area-chart.blade.php
│   └── Area charts for trends
│
├── bar-chart.blade.php
│   └── Bar charts for comparisons
│
├── line-chart.blade.php
│   └── Line charts for time series
│
├── pie-chart.blade.php
│   └── Pie/doughnut charts
│
└── radar-chart.blade.php
    └── Radar charts
```

---

## 3. Dashboard Layout Structure (shadcn/ui Style)

### 3.1 Main Layout

```blade
{{-- resources/views/admin/layout.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin — Kiddo\'s Heaven')</title>
    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])

    <script>
        // shadcn/ui theme initialization
        (function() {
            const theme = localStorage.getItem('admin-theme') ?? 'light';
            document.documentElement.classList.add(theme);
        })();
    </script>
</head>
<body class="{{ request()->routeIs('admin.*') ? 'admin-layout' : '' }}">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <x-ui.sidebar class="w-64 hidden md:flex" />

        <div class="flex-1 flex flex-col">
            {{-- Header --}}
            <x-ui.header />

            {{-- Main Content --}}
            <main class="flex-1 p-6 md:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Mobile Sidebar Sheet --}}
    <x-ui.sheet />
</body>
</html>
```

### 3.2 Dashboard Page Structure

```blade
{{-- resources/views/admin/dashboard.blade.php --}}
<x-layout.admin>
    <x-layout.page-header>
        <x-slot:title>Dashboard</x-slot:title>
        <x-slot:description>Welcome back! Here's what's happening with your store.</x-slot:description>
        <x-slot:actions>
            <x-ui.button variant="outline">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export
            </x-ui.button>
            <x-ui.button>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Product
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Stats Grid --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <x-ui.card>
            <x-ui.card-header class="flex flex-row items-center justify-between space-y-0 pb-2">
                <x-ui.card-title class="text-sm font-medium">Total Revenue</x-ui.card-title>
                <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="text-2xl font-bold">$45,231.89</div>
                <p class="text-xs text-muted-foreground">+20.1% from last month</p>
            </x-ui.card-content>
        </x-ui.card>

        {{-- More stats cards... --}}
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7 mt-6">
        {{-- Main Chart --}}
        <x-ui.card class="col-span-4">
            <x-ui.card-header>
                <x-ui.card-title>Overview</x-ui.card-title>
            </x-ui.card-header>
            <x-ui.card-content class="pl-2">
                <x-charts.area-chart :data="$chartData" />
            </x-ui.card-content>
        </x-ui.card>

        {{-- Recent Sales --}}
        <x-ui.card class="col-span-3">
            <x-ui.card-header>
                <x-ui.card-title>Recent Sales</x-ui.card-title>
                <x-ui.card-description>You made 265 sales this month.</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                {{-- Recent sales list... --}}
            </x-ui.card-content>
        </x-ui.card>
    </div>
</x-layout.admin>
```

---

## 4. Color Palette Implementation

### 4.1 Primary Colors (Tailwind-based)

```css
/* Primary - Navy Blue */
.primary {
    @apply bg-primary text-primary-foreground;
}
.primary:hover {
    @apply bg-primary/90;
}

.primary-50 {
    @apply bg-primary/5;
}
.primary-100 {
    @apply bg-primary/10;
}
.primary-200 {
    @apply bg-primary/20;
}
.primary-300 {
    @apply bg-primary/30;
}
.primary-400 {
    @apply bg-primary/40;
}
.primary-500 {
    @apply bg-primary/50;
}
.primary-600 {
    @apply bg-primary/60;
}
.primary-700 {
    @apply bg-primary/70;
}
.primary-800 {
    @apply bg-primary/80;
}
.primary-900 {
    @apply bg-primary/90;
}
```

### 4.2 Chart Colors

```javascript
// Chart.js color configuration
const chartColors = {
    primary: "hsl(221.2, 83.2%, 53.3%)",
    secondary: "hsl(160, 60%, 45%)",
    tertiary: "hsl(30, 80%, 55%)",
    quaternary: "hsl(280, 65%, 60%)",
    quinary: "hsl(340, 75%, 55%)",
};
```

---

## 5. Dark Mode Implementation (shadcn/ui Pattern)

### 5.1 CSS Variables for Dark Mode

```css
.dark {
    /* Base colors inverted for dark mode */
    --background: 222.2 84% 4.9%;
    --foreground: 210 40% 98%;

    --card: 222.2 84% 4.9%;
    --card-foreground: 210 40% 98%;

    --popover: 222.2 84% 4.9%;
    --popover-foreground: 210 40% 98%;

    --primary: 210 40% 98%;
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
    --ring: 212.7 26.8% 83.9%;

    /* Sidebar dark */
    --sidebar-background: #0f172a;
    --sidebar-foreground: #f1f5f9;
    --sidebar-border: #1e293b;

    /* Card dark */
    --card-background: #1e293b;
    --card-foreground: #f1f5f9;
    --card-border: #334155;
}
```

### 5.2 Theme Toggle Component

```blade
{{-- resources/views/components/ui/theme-toggle.blade.php --}}
<button
    id="theme-toggle"
    type="button"
    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10"
>
    <svg class="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>
    <svg class="absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
    </svg>
    <span class="sr-only">Toggle theme</span>
</button>

<script>
document.getElementById('theme-toggle').addEventListener('click', function() {
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');
    localStorage.setItem('admin-theme', isDark ? 'dark' : 'light');
});
</script>
```

---

## 6. Component Implementation Order

### Phase 1: Core UI Components

1. button.blade.php
2. input.blade.php
3. select.blade.php
4. card.blade.php
5. table.blade.php
6. badge.blade.php
7. alert.blade.php
8. label.blade.php

### Phase 2: Layout Components

1. sidebar.blade.php
2. header.blade.php
3. page-header.blade.php
4. theme-toggle.blade.php

### Phase 3: Dashboard

1. Enhanced dashboard.blade.php
2. Stats cards (8 metrics)
3. Charts (area, bar, pie)
4. Recent orders table
5. Top products list
6. Quick actions

### Phase 4: Feature Pages

1. Products management
2. Orders management
3. Customers management
4. Marketing pages

---

## 7. File Structure

```
resources/
├── css/
│   ├── admin.css              # Main admin styles with shadcn/ui variables
│   └── components.css          # Component-specific styles
│
├── views/
│   ├── admin/
│   │   ├── layout.blade.php   # Main admin layout
│   │   ├── dashboard.blade.php
│   │   ├── products/
│   │   ├── orders/
│   │   ├── customers/
│   │   └── marketing/
│   │
│   └── components/
│       ├── ui/                # shadcn/ui style components
│       │   ├── button.blade.php
│       │   ├── input.blade.php
│       │   ├── card.blade.php
│       │   ├── table.blade.php
│       │   ├── badge.blade.php
│       │   └── ...
│       │
│       ├── layout/            # Layout components
│       │   ├── sidebar.blade.php
│       │   ├── header.blade.php
│       │   └── page-header.blade.php
│       │
│       └── charts/            # Chart components
│           ├── chart-container.blade.php
│           ├── area-chart.blade.php
│           └── bar-chart.blade.php
```

---

## 8. Next Steps

1. **Implement Core UI Components** - button, input, card, table, badge
2. **Create Layout Components** - sidebar, header, theme toggle
3. **Build Dashboard** - stats cards, charts, tables
4. **Extend to Feature Pages** - products, orders, customers

This plan aligns with shadcn/ui's copy-paste philosophy while maintaining proper Laravel Blade component integration.
