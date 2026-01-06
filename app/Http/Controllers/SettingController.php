<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Handle checkbox logic: if unchecked, it's not present in request.
        // We iterate through all known boolean settings to check their state.
        $features = [
            'feature_produksi',
            'feature_distribusi', 
            'feature_notifikasi'
        ];

        foreach ($features as $key) {
            $value = $request->has($key) ? '1' : '0';
            Setting::where('key', $key)->update(['value' => $value]);
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
