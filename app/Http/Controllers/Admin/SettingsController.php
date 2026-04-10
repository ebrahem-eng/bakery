<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Show the unified Settings page.
     */
    public function index()
    {
        $currencies = Currency::orderBy('is_default', 'desc')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        $bakeryName = Setting::get('bakery_name', '');
        $bakeryPhone = Setting::get('bakery_phone', '');
        $bakeryAddress = Setting::get('bakery_address', '');
        $bakeryEmail = Setting::get('bakery_email', '');
        $defaultLang = Setting::get('default_language', 'ar');
        $defaultPricePerBundle = Setting::get('default_price_per_bundle', '0');

        return view('Admin.Settings.index', compact(
            'currencies', 'categories',
            'bakeryName', 'bakeryPhone', 'bakeryAddress', 'bakeryEmail', 'defaultLang', 'defaultPricePerBundle'
        ));
    }

    // ─── General Bakery Info ──────────────────────────────────────────

    public function updateBakeryInfo(Request $request)
    {
        $request->validate([
            'bakery_name' => 'nullable|string|max:255',
            'bakery_phone' => 'nullable|string|max:50',
            'bakery_address' => 'nullable|string|max:500',
            'bakery_email' => 'nullable|email|max:255',
            'default_language' => 'nullable|in:ar,en',
            'default_price_per_bundle' => 'nullable|numeric|min:0',
        ]);

        Setting::set('bakery_name', $request->bakery_name);
        Setting::set('bakery_phone', $request->bakery_phone);
        Setting::set('bakery_address', $request->bakery_address);
        Setting::set('bakery_email', $request->bakery_email);
        Setting::set('default_language', $request->default_language);
        Setting::set('default_price_per_bundle', $request->default_price_per_bundle);

        return redirect()->route('admin.settings.index')
            ->with('success', __('Settings saved successfully.'));
    }

    // ─── Currencies ──────────────────────────────────────────────────

    public function storeCurrency(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:currencies,name',
            'code' => 'required|string|max:10|unique:currencies,code',
            'exchange_rate' => 'required|numeric|min:0',
        ]);

        Currency::create([
            'name' => $request->name,
            'code' => $request->code,
            'exchange_rate' => $request->exchange_rate,
            'is_default' => false,
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'currencies'])
            ->with('success', __('Currency added successfully.'));
    }

    public function updateCurrency(Request $request, Currency $currency)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:currencies,name,'.$currency->id,
            'code' => 'required|string|max:10|unique:currencies,code,'.$currency->id,
            'exchange_rate' => 'required|numeric|min:0',
            'is_default' => 'nullable|boolean',
        ]);

        if ($request->is_default) {
            Currency::where('is_default', true)->update(['is_default' => false]);
            $currency->is_default = true;
        }

        $currency->update([
            'name' => $request->name,
            'code' => $request->code,
            'exchange_rate' => $request->exchange_rate,
            'is_default' => $currency->is_default,
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'currencies'])
            ->with('success', __('Currency updated successfully.'));
    }

    public function destroyCurrency(Currency $currency)
    {
        if ($currency->is_default) {
            return redirect()->route('admin.settings.index', ['tab' => 'currencies'])
                ->with('error', __('Cannot delete the default currency.'));
        }

        $currency->delete();

        return redirect()->route('admin.settings.index', ['tab' => 'currencies'])
            ->with('success', __('Currency deleted successfully.'));
    }

    // ─── Categories ──────────────────────────────────────────────────

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
        ]);

        Category::create([
            'name' => $request->name,
            'is_active' => true,
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'categories'])
            ->with('success', __('Category added successfully.'));
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,'.$category->id,
            'is_active' => 'nullable|boolean',
        ]);

        $category->update([
            'name' => $request->name,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'categories'])
            ->with('success', __('Category updated successfully.'));
    }

    public function destroyCategory(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.settings.index', ['tab' => 'categories'])
            ->with('success', __('Category deleted successfully.'));
    }
}
