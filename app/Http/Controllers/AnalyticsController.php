<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\KpiError;
use App\Models\KpiProfit;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    // =========================================================================
    // ANALYTICS DASHBOARD (tim_analytics facing)
    // =========================================================================

    public function dashboard(Request $request)
    {
        $period = $request->get('period', 'monthly'); // weekly | monthly | yearly

        // --- 1. User Session Stats ---
        $sessionStats = $this->getSessionStats($period);

        // --- 2. Active Users (Last 30 days, including ongoing sessions) ---
        // Exclude system users (tim_analytics) from business-level KPIs
        $totalUsersQuery = User::whereNotIn('role', ['tim_analytics']);
        if (auth()->user()->koperasi_id) {
            $totalUsersQuery->where('koperasi_id', auth()->user()->koperasi_id);
        }
        $totalUsers = $totalUsersQuery->count();

        $activeUsersQuery = UserSession::where('login_at', '>=', now()->subDays(30))
            ->whereHas('user', function($q) {
                $q->whereNotIn('role', ['tim_analytics']);
                if (auth()->user()->koperasi_id) {
                    $q->where('koperasi_id', auth()->user()->koperasi_id);
                }
            });
        $activeUsers = $activeUsersQuery->distinct('user_id')->count('user_id');

        // --- 3. Error Rate KPI ---
        $totalErrors    = KpiError::count();
        $resolvedErrors = KpiError::where('resolved', true)->count();
        $errorRate      = $totalErrors > 0 ? round((($totalErrors - $resolvedErrors) / $totalErrors) * 100, 1) : 0;

        // --- 4. Activity by Hour (Last 30 days) ---
        $activityByHour = UserSession::selectRaw('HOUR(login_at) as hour, COUNT(*) as count')
            ->where('login_at', '>=', now()->subDays(30))
            ->whereHas('user', function($q) {
                $q->whereNotIn('role', ['tim_analytics']);
                if (auth()->user()->koperasi_id) {
                    $q->where('koperasi_id', auth()->user()->koperasi_id);
                }
            })
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour');

        // --- 5. Recent Errors (last 5) ---
        $recentErrors = KpiError::with('reporter')->orderBy('created_at', 'desc')->limit(5)->get();

        // --- 6. Usage chart data (filtering to real users only) ---
        $usageChart = $this->getUsageChartData($period);

        // --- 7. Error Trend (last 12 periods) ---
        $errorTrend = $this->getErrorTrendData();

        // --- 8. Latest profit data ---
        $latestProfit = KpiProfit::orderBy('period', 'desc')->first();

        // --- 9. Avg session duration (real users, closed sessions) ---
        $avgDuration = UserSession::whereNotNull('duration_seconds')
            ->whereHas('user', function($q) {
                $q->whereNotIn('role', ['tim_analytics']);
                if (auth()->user()->koperasi_id) {
                    $q->where('koperasi_id', auth()->user()->koperasi_id);
                }
            })
            ->avg('duration_seconds');
        $avgDurationMin = round(($avgDuration ?? 0) / 60, 1);

        // --- 10. Total sessions (real users) ---
        $totalSessions = UserSession::whereHas('user', function($q) {
            $q->whereNotIn('role', ['tim_analytics']);
            if (auth()->user()->koperasi_id) {
                $q->where('koperasi_id', auth()->user()->koperasi_id);
            }
        })->count();

        return view('analytics.dashboard', compact(
            'sessionStats', 'totalUsers', 'activeUsers',
            'errorRate', 'totalErrors', 'resolvedErrors',
            'activityByHour', 'recentErrors', 'usageChart',
            'errorTrend', 'latestProfit', 'avgDurationMin',
            'totalSessions', 'period'
        ));
    }

    // =========================================================================
    // USAGE ANALYTICS
    // =========================================================================

    public function usage(Request $request)
    {
        $period   = $request->get('period', 'monthly');
        $userId   = $request->get('user_id');
        
        $preset = $request->get('preset');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if ($preset) {
            switch ($preset) {
                case '7d':
                    $dateFrom = now()->subDays(7)->toDateString();
                    $dateTo = now()->toDateString();
                    break;
                case '30d':
                    $dateFrom = now()->subDays(30)->toDateString();
                    $dateTo = now()->toDateString();
                    break;
                case 'this_month':
                    $dateFrom = now()->startOfMonth()->toDateString();
                    $dateTo = now()->toDateString();
                    break;
                case 'last_month':
                    $dateFrom = now()->subMonth()->startOfMonth()->toDateString();
                    $dateTo = now()->subMonth()->endOfMonth()->toDateString();
                    break;
            }
        }

        $dateFrom = $dateFrom ?: now()->subDays(30)->toDateString();
        $dateTo = $dateTo ?: now()->toDateString();

        // Per-user session table (real users only)
        $query = UserSession::with('user')
            ->whereHas('user', function($q) {
                $q->whereNotIn('role', ['tim_analytics']);
                if (auth()->user()->koperasi_id) {
                    $q->where('koperasi_id', auth()->user()->koperasi_id);
                }
            })
            ->whereBetween('login_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $rawSessions = $query->orderBy('login_at', 'desc')->get();

        // Aggregate per user
        $perUser = $rawSessions->groupBy('user_id')->map(function ($sessions) {
            $user     = $sessions->first()->user;
            $total    = $sessions->count();
            $avgSec   = $sessions->whereNotNull('duration_seconds')->avg('duration_seconds') ?? 0;
            $totalSec = $sessions->whereNotNull('duration_seconds')->sum('duration_seconds');
            $lastSeen = $sessions->max('login_at');
            return [
                'user'          => $user,
                'total_sessions'=> $total,
                'avg_duration'  => round($avgSec / 60, 1),
                'total_time'    => round($totalSec / 60, 0),
                'last_seen'     => $lastSeen,
            ];
        })->values()->sortByDesc('total_sessions');

        // Chart: sessions per day
        $dailyChart = $rawSessions->groupBy(fn($s) => \Carbon\Carbon::parse($s->login_at)->format('Y-m-d'))
            ->map->count()
            ->sortKeys();

        // Access pattern by hour
        $hourlyPattern = $rawSessions->groupBy(fn($s) => \Carbon\Carbon::parse($s->login_at)->format('H'))
            ->map->count()
            ->sortKeys();

        // Duration distribution
        $durationBuckets = [
            '< 5 min'   => $rawSessions->filter(fn($s) => ($s->duration_seconds ?? 0) < 300)->count(),
            '5-15 min'  => $rawSessions->filter(fn($s) => ($s->duration_seconds ?? 0) >= 300 && ($s->duration_seconds ?? 0) < 900)->count(),
            '15-30 min' => $rawSessions->filter(fn($s) => ($s->duration_seconds ?? 0) >= 900 && ($s->duration_seconds ?? 0) < 1800)->count(),
            '30-60 min' => $rawSessions->filter(fn($s) => ($s->duration_seconds ?? 0) >= 1800 && ($s->duration_seconds ?? 0) < 3600)->count(),
            '> 60 min'  => $rawSessions->filter(fn($s) => ($s->duration_seconds ?? 0) >= 3600)->count(),
        ];

        $usersQuery = User::whereNotIn('role', ['tim_analytics']);
        if (auth()->user()->koperasi_id) {
            $usersQuery->where('koperasi_id', auth()->user()->koperasi_id);
        }
        $users = $usersQuery->orderBy('nama')->get();

        return view('analytics.usage', compact(
            'perUser', 'rawSessions', 'dailyChart', 'hourlyPattern',
            'durationBuckets', 'users', 'userId', 'dateFrom', 'dateTo', 'period'
        ));
    }

    // =========================================================================
    // ERROR RATE
    // =========================================================================

    public function errors(Request $request)
    {
        $period    = $request->get('period'); // e.g. "2026-01"
        $errorType = $request->get('error_type');

        $query = KpiError::with('reporter')->orderBy('created_at', 'desc');
        
        if (auth()->user()->koperasi_id) {
            $query->whereHas('reporter', function($q) {
                $q->where('koperasi_id', auth()->user()->koperasi_id);
            });
        }

        if ($period)    $query->where('period', 'LIKE', $period . '%');
        if ($errorType) $query->where('error_type', $errorType);

        $errorLogs = $query->paginate(20)->withQueryString();
        
        $summaryQuery = KpiError::selectRaw('
            period,
            COUNT(*) as total,
            SUM(resolved = 1) as resolved_count,
            SUM(resolved = 0) as unresolved_count,
            ROUND(SUM(resolved = 1) / COUNT(*) * 100, 1) as resolution_rate
        ');

        if (auth()->user()->koperasi_id) {
            $summaryQuery->whereHas('reporter', function($q) {
                $q->where('koperasi_id', auth()->user()->koperasi_id);
            });
        }

        $summary = $summaryQuery->groupBy('period')->orderBy('period', 'desc')->get();

        // Monthly error count for chart
        $errorTrend = KpiError::selectRaw('period, COUNT(*) as count')
            ->groupBy('period')
            ->orderBy('period')
            ->limit(12)
            ->get();

        // Totals
        $totalErrors    = KpiError::count();
        $resolved       = KpiError::where('resolved', true)->count();
        $unresolved     = $totalErrors - $resolved;
        $resolutionRate = $totalErrors > 0 ? round($resolved / $totalErrors * 100, 1) : 0;
        $errorRate      = $totalErrors > 0 ? round($unresolved / $totalErrors * 100, 1) : 0;

        // Human Error Rate (Deletion Tracking)
        $deleteLogs = ActivityLog::where('action', 'LIKE', 'DELETE_%');
        
        if (auth()->user()->koperasi_id) {
            $deleteLogs->whereHas('user', function($q) {
                $q->where('koperasi_id', auth()->user()->koperasi_id);
            });
        }

        if ($period) {
            $deleteLogs->where('created_at', 'LIKE', $period . '%');
        }
        $totalDeletions = $deleteLogs->count();
        $deletionsByType = $deleteLogs->selectRaw('action, count(*) as count')->groupBy('action')->pluck('count', 'action');

        // Rate: Deletion vs Activity
        $activeProduksi = \App\Models\ProduksiHarian::count();
        $activeKasbon   = \App\Models\Kasbon::count();
        $totalActive    = $activeProduksi + $activeKasbon;
        $humanErrorRate = ($totalActive + $totalDeletions) > 0 
            ? round(($totalDeletions / ($totalActive + $totalDeletions)) * 100, 2) 
            : 0;

        return view('analytics.errors', compact(
            'errorLogs', 'summary', 'errorTrend',
            'totalErrors', 'resolved', 'unresolved',
            'resolutionRate', 'errorRate', 'period', 'errorType',
            'totalDeletions', 'deletionsByType', 'humanErrorRate'
        ));
    }

    // =========================================================================
    // PROFIT ANALYTICS
    // =========================================================================

    public function profit(Request $request)
    {
        $profits = KpiProfit::orderBy('period', 'asc')->get();

        $labels        = $profits->pluck('period');
        $revenueAfter  = $profits->pluck('revenue_after');
        $revenueBefore = $profits->pluck('revenue_before');
        $profitAfter   = $profits->map(fn($p) => ($p->revenue_after - $p->cost_after));
        $profitBefore  = $profits->map(fn($p) => ($p->revenue_before - $p->cost_before));
        $milkBefore    = $profits->pluck('milk_volume_before');
        $milkAfter     = $profits->pluck('milk_volume_after');

        $latestProfit = $profits->last();

        return view('analytics.profit', compact(
            'profits', 'labels', 'revenueAfter', 'revenueBefore',
            'profitAfter', 'profitBefore', 'milkBefore', 'milkAfter', 'latestProfit'
        ));
    }

    // =========================================================================
    // ADMIN: KPI DATA MANAGEMENT
    // =========================================================================

    public function kpiIndex()
    {
        $profits    = KpiProfit::orderBy('period', 'desc')->get();
        $errorLogs  = KpiError::with('reporter')->orderBy('created_at', 'desc')->limit(10)->get();
        $users      = User::whereNotIn('role', ['tim_analytics'])->orderBy('nama')->get();
        $errorTypes = ['salary_calc' => 'Perhitungan Pembayaran', 'data_entry' => 'Pencatatan Data', 'other' => 'Lainnya'];

        return view('analytics.kpi_input', compact('profits', 'errorLogs', 'users', 'errorTypes'));
    }

    public function storeProfit(Request $request)
    {
        $request->validate([
            'period'         => 'required|string|max:20',
            'revenue_before' => 'nullable|numeric|min:0',
            'revenue_after'  => 'nullable|numeric|min:0',
            'cost_before'    => 'nullable|numeric|min:0',
            'cost_after'     => 'nullable|numeric|min:0',
            'milk_volume_before' => 'nullable|numeric|min:0',
            'milk_volume_after'  => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
        ]);

        KpiProfit::updateOrCreate(
            ['period' => $request->period],
            array_merge($request->only([
                'period', 'revenue_before', 'revenue_after',
                'cost_before', 'cost_after', 'milk_volume_before',
                'milk_volume_after', 'notes',
            ]))
        );

        return back()->with('success', 'Data profit berhasil disimpan.');
    }

    public function destroyProfit($id)
    {
        KpiProfit::findOrFail($id)->delete();
        return back()->with('success', 'Data profit berhasil dihapus.');
    }

    public function storeError(Request $request)
    {
        $request->validate([
            'error_type'  => 'required|in:salary_calc,data_entry,other',
            'description' => 'required|string',
            'period'      => 'nullable|string|max:20',
        ]);

        KpiError::create([
            'error_type'  => $request->error_type,
            'description' => $request->description,
            'period'      => $request->period ?? now()->format('Y-m'),
            'reported_by' => auth()->id(),
            'resolved'    => false,
        ]);

        return back()->with('success', 'Error berhasil dicatat.');
    }

    public function updateError(Request $request, $id)
    {
        $error = KpiError::findOrFail($id);
        $error->resolved    = $request->boolean('resolved');
        $error->resolved_at = $error->resolved ? now() : null;
        $error->save();

        return back()->with('success', 'Status error berhasil diperbarui.');
    }

    public function destroyError($id)
    {
        KpiError::findOrFail($id)->delete();
        return back()->with('success', 'Data error berhasil dihapus.');
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function getSessionStats(string $period): array
    {
        $dateFrom = match ($period) {
            'weekly'  => now()->subWeek(),
            'yearly'  => now()->subYear(),
            default   => now()->subMonth(),
        };

        // EXCLUDE tim_analytics from all engagement stats
        $sessions  = UserSession::where('login_at', '>=', $dateFrom)
            ->whereHas('user', function($q) {
                $q->whereNotIn('role', ['tim_analytics']);
                if (auth()->user()->koperasi_id) {
                    $q->where('koperasi_id', auth()->user()->koperasi_id);
                }
            })->get();
            
        $completed = $sessions->whereNotNull('duration_seconds');

        return [
            'total'        => $sessions->count(),
            'avg_duration' => round($completed->avg('duration_seconds') / 60 ?? 0, 1),
            'max_duration' => round(($completed->max('duration_seconds') ?? 0) / 60, 1),
            'unique_users' => $sessions->unique('user_id')->count(),
        ];
    }

    private function getUsageChartData(string $period): array
    {
        if ($period === 'weekly') {
            $days = 7;
            $fmt  = '%Y-%m-%d';
        } elseif ($period === 'yearly') {
            $days = 365;
            $fmt  = '%Y-%m';
        } else {
            $days = 30;
            $fmt  = '%Y-%m-%d';
        }

        $dateFrom = now()->subDays($days);

        $rows = UserSession::where('login_at', '>=', $dateFrom)
            ->whereHas('user', function($q) {
                $q->whereNotIn('role', ['tim_analytics']);
                if (auth()->user()->koperasi_id) {
                    $q->where('koperasi_id', auth()->user()->koperasi_id);
                }
            })
            ->selectRaw("DATE_FORMAT(login_at, '$fmt') as label, COUNT(*) as total")
            ->groupBy('label')
            ->orderBy('label', 'asc')
            ->pluck('total', 'label');

        if ($rows->isEmpty()) {
            return [
                'labels' => [now()->format($fmt === '%Y-%m' ? 'Y-m' : 'Y-m-d')],
                'data'   => [0],
            ];
        }

        return [
            'labels' => $rows->keys()->toArray(),
            'data'   => $rows->values()->toArray(),
        ];
    }

    private function getErrorTrendData(): array
    {
        $rows = KpiError::selectRaw("period, COUNT(*) as total, SUM(resolved = 1) as resolved_count")
            ->groupBy('period')
            ->orderBy('period')
            ->limit(12)
            ->get();

        return [
            'labels'     => $rows->pluck('period')->toArray(),
            'total'      => $rows->pluck('total')->toArray(),
            'resolved'   => $rows->pluck('resolved_count')->toArray(),
        ];
    }
}
