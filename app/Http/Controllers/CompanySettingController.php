<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class CompanySettingController extends Controller
{
    public function edit()
    {
        $setting = CompanySetting::firstOrNew(['user_id' => auth()->id()]);

        return view('company-settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:150',
            'company_address' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,gif|max:2048',
        ]);

        $setting = CompanySetting::firstOrNew(['user_id' => auth()->id()]);
        $oldLogoPath = $setting->logo_path;

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('company-logos', 'public');
            if ($logoPath === false) {
                throw new RuntimeException('Logo perusahaan gagal disimpan.');
            }

            $validated['logo_path'] = $logoPath;
        }

        $setting->fill([
            'company_name' => $validated['company_name'],
            'company_address' => $validated['company_address'] ?? null,
            'logo_path' => $validated['logo_path'] ?? $setting->logo_path,
        ]);
        $setting->save();

        if (isset($validated['logo_path']) && $oldLogoPath && !Storage::disk('public')->delete($oldLogoPath)) {
            throw new RuntimeException('Logo perusahaan lama gagal dihapus.');
        }

        return redirect()->route('company-settings.edit')
            ->with('success', 'Identitas perusahaan berhasil diperbarui.');
    }
}
