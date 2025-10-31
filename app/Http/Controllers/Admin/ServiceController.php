<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Service::class, 'service');
    }

    public function index(): View
    {
        $services = Service::with('category')
            ->when(request('category'), fn ($query, $category) => $query->where('service_category_id', $category))
            ->paginate(15);
        $categories = ServiceCategory::orderBy('name')->get();

        return view('admin.services.index', compact('services', 'categories'));
    }

    public function create(): View
    {
        $categories = ServiceCategory::orderBy('name')->get();
        $service = new Service();

        return view('admin.services.create', compact('categories', 'service'));
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        Service::create($request->validated());

        return redirect()->route('admin.services.index')->with('status', 'Service created.');
    }

    public function edit(Service $service): View
    {
        $categories = ServiceCategory::orderBy('name')->get();

        return view('admin.services.edit', compact('service', 'categories'));
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($request->validated());

        return redirect()->route('admin.services.index')->with('status', 'Service updated.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('admin.services.index')->with('status', 'Service deleted.');
    }
}
