<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceCategoryRequest;
use App\Models\ServiceCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ServiceCategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ServiceCategory::class, 'service_category');
    }

    public function index(): View
    {
        $categories = ServiceCategory::orderBy('order')->paginate(20);

        return view('admin.service_categories.index', compact('categories'));
    }

    public function create(): View
    {
        $category = new ServiceCategory();

        return view('admin.service_categories.create', compact('category'));
    }

    public function store(ServiceCategoryRequest $request): RedirectResponse
    {
        ServiceCategory::create($request->validated());

        return redirect()->route('admin.service_categories.index')->with('status', 'Category created.');
    }

    public function edit(ServiceCategory $service_category): View
    {
        return view('admin.service_categories.edit', ['category' => $service_category]);
    }

    public function update(ServiceCategoryRequest $request, ServiceCategory $service_category): RedirectResponse
    {
        $service_category->update($request->validated());

        return redirect()->route('admin.service_categories.index')->with('status', 'Category updated.');
    }

    public function destroy(ServiceCategory $service_category): RedirectResponse
    {
        $service_category->delete();

        return redirect()->route('admin.service_categories.index')->with('status', 'Category deleted.');
    }
}
