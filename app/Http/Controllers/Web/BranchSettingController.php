<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Services\SettingService;
use Illuminate\Http\Request;

class BranchSettingController extends Controller
{
    public function __construct(
        protected SettingService $settings
    ) {}

    public function index(Branch $branch)
    {
        $groups = ['general', 'tax', 'loyalty', 'gateway', 'accounting', 'order', 'notification', 'inventory'];
        $settingValues = [];
        $branches = Branch::all();

        foreach ($groups as $group) {
            $settingValues[$group] = $this->settings->getGroup($group, $branch->id);
        }

        return view('settings.branch-settings', compact('branch', 'settingValues', 'branches'));
    }

    public function update(Branch $branch, Request $request)
    {
        $group = $request->input('group', 'general');

        $validated = $request->validate([
            'group' => 'required|string',
        ]);

        $data = $request->except(['_token', 'group']);
        $this->settings->updateGroup($group, $data, $branch->id);

        return redirect()->route('admin.branch-settings.index', $branch)
            ->with('success', "Pengaturan cabang \"{$branch->name}\" berhasil diperbarui.");
    }
}
