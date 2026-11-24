<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\SystemConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemConfigController extends Controller
{
    public function index()
    {
        $appName = SystemConfig::firstOrCreate(
            ['config_key' => 'app_name'],
            ['config_value' => 'ARC Management', 'description' => 'Nama Aplikasi']
        );

        $logs = ActivityLog::with('user.role')
            ->latest()
            ->paginate(10);

        return view('settings.index', compact('appName', 'logs'));
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

        ActivityLog::record(
            'Update Pengaturan Sistem',
            'Mengubah nama aplikasi menjadi "'.$request->app_name.'".',
            $config
        );

        return redirect()->route('settings.index')->with('success', 'Konfigurasi berhasil disimpan!');
    }
}
