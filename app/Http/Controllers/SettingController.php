<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $profile = Setting::get('profile', []);
        $target  = Setting::get('target', ['amount' => 0]);
        $tab     = request('tab', 'profile');

        return view('settings.index', compact('profile', 'target', 'tab'));
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'company_name'    => 'nullable|string|max:200',
            'company_address' => 'nullable|string',
            'company_phone'   => 'nullable|string|max:50',
            'company_email'   => 'nullable|email|max:100',
            'company_npwp'    => 'nullable|string|max:30',
            'bank1_name'      => 'nullable|string|max:50',
            'bank1_account'   => 'nullable|string|max:30',
            'bank1_holder'    => 'nullable|string|max:100',
            'bank2_name'      => 'nullable|string|max:50',
            'bank2_account'   => 'nullable|string|max:30',
            'bank2_holder'    => 'nullable|string|max:100',
            'bank3_name'      => 'nullable|string|max:50',
            'bank3_account'   => 'nullable|string|max:30',
            'bank3_holder'    => 'nullable|string|max:100',
        ]);

        $existing = Setting::get('profile', []);
        Setting::set('profile', array_merge($existing, $data));

        return redirect()->route('settings.index', ['tab' => 'profile'])->with('success', 'Profil perusahaan berhasil disimpan.');
    }

    public function updateBank(Request $request)
    {
        return $this->updateProfile($request);
    }

    public function updateRevenue(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'period' => 'nullable|string|max:7',
        ]);

        Setting::set('target', $data);

        return redirect()->route('settings.index', ['tab' => 'revenue'])->with('success', 'Target revenue berhasil disimpan.');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate(['logo' => 'required|image|max:2048']);

        $file = $request->file('logo');
        $file->move(public_path(), 'logo.png');

        $existing = Setting::get('profile', []);
        Setting::set('profile', array_merge($existing, ['logo_updated' => now()->toDateTimeString()]));

        return redirect()->route('settings.index', ['tab' => 'profile'])->with('success', 'Logo berhasil diupload.');
    }
}
