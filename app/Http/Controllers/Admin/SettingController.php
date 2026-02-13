<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'site_logo' => 'nullable|string',
            'site_favicon' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string',
            'currency' => 'nullable|string|size:3',
            'timezone' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'shipping_fee' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'order_prefix' => 'nullable|string|max:10',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'enable_reviews' => 'boolean',
            'enable_wishlist' => 'boolean',
            'enable_coupon' => 'boolean',
            'maintenance_mode' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Cache::flush();

        return back()->with('success', 'Settings updated successfully.');
    }

    public function email()
    {
        $settings = Setting::where('key', 'like', 'email_%')->pluck('value', 'key')->toArray();

        return view('admin.settings.email', compact('settings'));
    }

    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'email_driver' => 'nullable|string',
            'email_host' => 'nullable|string',
            'email_port' => 'nullable|integer',
            'email_username' => 'nullable|string',
            'email_password' => 'nullable|string',
            'email_encryption' => 'nullable|string',
            'email_from_address' => 'nullable|email',
            'email_from_name' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Cache::flush();

        return back()->with('success', 'Email settings updated successfully.');
    }

    public function payment()
    {
        $settings = Setting::where('key', 'like', 'payment_%')->pluck('value', 'key')->toArray();

        return view('admin.settings.payment', compact('settings'));
    }

    public function updatePayment(Request $request)
    {
        $validated = $request->validate([
            'payment_cod_enabled' => 'boolean',
            'payment_online_enabled' => 'boolean',
            'payment_stripe_enabled' => 'boolean',
            'stripe_key' => 'nullable|string',
            'stripe_secret' => 'nullable|string',
            'payment_paypal_enabled' => 'boolean',
            'paypal_client_id' => 'nullable|string',
            'paypal_secret' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Cache::flush();

        return back()->with('success', 'Payment settings updated successfully.');
    }

    public function seo()
    {
        $settings = Setting::where('key', 'like', 'seo_%')->pluck('value', 'key')->toArray();

        return view('admin.settings.seo', compact('settings'));
    }

    public function updateSeo(Request $request)
    {
        $validated = $request->validate([
            'seo_home_title' => 'nullable|string|max:255',
            'seo_home_description' => 'nullable|string',
            'seo_home_keywords' => 'nullable|string',
            'seo_og_image' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Cache::flush();

        return back()->with('success', 'SEO settings updated successfully.');
    }

    public function clearCache()
    {
        Cache::flush();

        return back()->with('success', 'Cache cleared successfully.');
    }
}
