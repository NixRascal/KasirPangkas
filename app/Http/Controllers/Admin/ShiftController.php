<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShiftRequest;
use App\Models\Shift;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ShiftController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Shift::class, 'shift');
    }

    public function index(): View
    {
        $shifts = Shift::orderBy('start_time')->paginate(20);

        return view('admin.shifts.index', compact('shifts'));
    }

    public function create(): View
    {
        $shift = new Shift();

        return view('admin.shifts.create', compact('shift'));
    }

    public function store(ShiftRequest $request): RedirectResponse
    {
        Shift::create($request->validated());

        return redirect()->route('admin.shifts.index')->with('status', 'Shift created.');
    }

    public function edit(Shift $shift): View
    {
        return view('admin.shifts.edit', compact('shift'));
    }

    public function update(ShiftRequest $request, Shift $shift): RedirectResponse
    {
        $shift->update($request->validated());

        return redirect()->route('admin.shifts.index')->with('status', 'Shift updated.');
    }

    public function destroy(Shift $shift): RedirectResponse
    {
        $shift->delete();

        return redirect()->route('admin.shifts.index')->with('status', 'Shift deleted.');
    }
}
