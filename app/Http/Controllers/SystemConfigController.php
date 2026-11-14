<?php

namespace App\Http\Controllers;

use App\Models\SystemConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemConfigController extends Controller
{
    public function index()
    {
        // Ambil konfigurasi nama aplikasi. Jika belum ada, buat defaultnya.
        $appName = SystemConfig::firstOrCreate(
            ['config_key' => 'app_name'],
            ['config_value' => 'ARC Management', 'description' => 'Nama Aplikasi']
        );

        return view('settings.index', compact('appName'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:100',
        ]);

        $config = SystemConfig::where('config_key', 'app_name')->first();

        $config->update([
            'config_value' => $request->app_name,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('settings.index')->with('success', 'Konfigurasi berhasil disimpan!');
    }
}
