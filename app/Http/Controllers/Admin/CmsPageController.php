<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsPageController extends Controller
{
    public function index(Request $request)
    {
        $query = CmsPage::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $pages = $query->latest()->paginate(10);

        return view('admin.cms.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.cms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
            'show_in_footer' => 'boolean',
            'show_in_header' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['content'] = $validated['content'] ?? '';

        CmsPage::create($validated);

        return redirect()->route('admin.cms.index')
            ->with('success', 'Page created successfully.');
    }

    public function show(CmsPage $page)
    {
        return view('admin.cms.show', compact('page'));
    }

    public function edit(CmsPage $page)
    {
        return view('admin.cms.edit', compact('page'));
    }

    public function update(Request $request, CmsPage $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug,' . $page->id,
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
            'show_in_footer' => 'boolean',
            'show_in_header' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['content'] = $validated['content'] ?? '';

        $page->update($validated);

        return redirect()->route('admin.cms.index')
            ->with('success', 'Page updated successfully.');
    }

    public function destroy(CmsPage $page)
    {
        $page->delete();

        return redirect()->route('admin.cms.index')
            ->with('success', 'Page deleted successfully.');
    }

    public function toggleStatus(CmsPage $page)
    {
        $page->update(['is_active' => !$page->is_active]);

        return back()->with('success', 'Page status updated.');
    }

    public function footerPages()
    {
        $pages = CmsPage::where('show_in_footer', true)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('admin.cms.footer', compact('pages'));
    }

    public function updateFooterOrder(Request $request)
    {
        $validated = $request->validate([
            'pages' => 'required|array',
            'pages.*.id' => 'required|exists:cms_pages,id',
            'pages.*.sort_order' => 'required|integer',
        ]);

        foreach ($validated['pages'] as $page) {
            CmsPage::where('id', $page['id'])->update(['sort_order' => $page['sort_order']]);
        }

        return back()->with('success', 'Footer pages order updated.');
    }
}
