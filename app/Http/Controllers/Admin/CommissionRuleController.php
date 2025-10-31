<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CommissionRuleRequest;
use App\Models\CommissionRule;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CommissionRuleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(CommissionRule::class, 'commission_rule');
    }

    public function index(): View
    {
        $rules = CommissionRule::with('service')->orderByDesc('created_at')->paginate(20);
        $services = Service::orderBy('name')->get();

        return view('admin.commission_rules.index', compact('rules', 'services'));
    }

    public function create(): View
    {
        $services = Service::orderBy('name')->get();
        $rule = new CommissionRule();

        return view('admin.commission_rules.create', compact('services', 'rule'));
    }

    public function store(CommissionRuleRequest $request): RedirectResponse
    {
        CommissionRule::create($request->validated());

        return redirect()->route('admin.commission_rules.index')->with('status', 'Commission rule created.');
    }

    public function edit(CommissionRule $commission_rule): View
    {
        $services = Service::orderBy('name')->get();

        return view('admin.commission_rules.edit', ['rule' => $commission_rule, 'services' => $services]);
    }

    public function update(CommissionRuleRequest $request, CommissionRule $commission_rule): RedirectResponse
    {
        $commission_rule->update($request->validated());

        return redirect()->route('admin.commission_rules.index')->with('status', 'Commission rule updated.');
    }

    public function destroy(CommissionRule $commission_rule): RedirectResponse
    {
        $commission_rule->delete();

        return redirect()->route('admin.commission_rules.index')->with('status', 'Commission rule deleted.');
    }
}
