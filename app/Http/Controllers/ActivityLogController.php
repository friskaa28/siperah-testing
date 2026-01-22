<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 50);
        $logs = $query->paginate($perPage)->withQueryString();
        $users = User::orderBy('nama')->get();
        
        // Get unique actions for filter
        $actions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('activity_log.index', compact('logs', 'users', 'actions'));
    }

    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);
        return view('activity_log.show', compact('log'));
    }

    public function clear(Request $request)
    {
        // Only allow clearing logs older than specified days
        $days = $request->input('days', 90);
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();
        
        ActivityLog::log(
            'CLEAR_ACTIVITY_LOGS',
            "Menghapus {$deleted} log aktivitas yang lebih dari {$days} hari"
        );

        return back()->with('success', "Berhasil menghapus {$deleted} log aktivitas lama.");
    }

    public function undoAction($id)
    {
        $log = ActivityLog::find($id);
        if (!$log) {
            return back()->with('error', 'Log aktivitas tidak ditemukan.');
        }

        if ($log->action === 'SIGN_SALARY_SLIP') {
            $idslip = $log->reference_id;

            // Fallback for legacy logs: parse ID from description "(ID: 123)"
            if (empty($idslip)) {
                if (preg_match('/\(ID:\s*(\d+)\)/', $log->description, $matches)) {
                    $idslip = $matches[1];
                }
            }

            if (empty($idslip)) {
                return back()->with('error', 'Data referensi (ID Slip) tidak ditemukan pada log ini. Aksi ini mungkin dibuat sebelum fitur pembatalan diaktifkan.');
            }

            try {
                $gajiRepo = app(GajiController::class);
                $result = $gajiRepo->undoSign($idslip);
                
                if ($result) {
                    return back()->with('success', 'Tanda tangan berhasil dibatalkan dan slip gaji telah dibuka kuncinya.');
                } else {
                    return back()->with('error', 'Gagal membatalkan tanda tangan. Data mungkin tidak ditemukan.');
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Undo Action Error: " . $e->getMessage());
                return back()->with('error', 'Gagal membatalkan tanda tangan: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'Aksi ini tidak dapat dibatalkan.');
    }
}
