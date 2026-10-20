<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    // nilai filter di view => nama role di database
    private const ROLE_MAP = [
        'admin'      => 'admin',
        'production' => 'produksi',
    ];

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $logs = ActivityLog::with('user.role')
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . $search . '%';

                $q->where(fn ($w) => $w
                    ->where('description', 'ilike', $like)
                    ->orWhere('action', 'ilike', $like)
                    ->orWhere('entity_type', 'ilike', $like)
                    ->orWhereRaw('metadata::text ilike ?', [$like])
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'ilike', $like)));
            })
            ->when($this->active($request, 'module'), fn ($q) =>
                $q->where('entity_type', $request->module))
            ->when($this->active($request, 'user') && isset(self::ROLE_MAP[$request->user]), fn ($q) =>
                $q->whereHas('user.role', fn ($r) => $r->where('name', self::ROLE_MAP[$request->user])))
            ->when($this->since($request->query('period')), fn ($q, $since) =>
                $q->where('created_at', '>=', $since))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total'      => ActivityLog::count(),
            'today'      => ActivityLog::where('created_at', '>=', now()->startOfDay())->count(),
            'admin'      => ActivityLog::whereHas('user.role', fn ($r) => $r->where('name', 'admin'))->count(),
            'production' => ActivityLog::whereHas('user.role', fn ($r) => $r->where('name', 'produksi'))->count(),
        ];

        return view('admin.activity-logs.index', compact('logs', 'summary'));
    }

    private function active(Request $request, string $key): bool
    {
        return $request->filled($key) && $request->query($key) !== 'all';
    }

    private function since(?string $period)
    {
        return match ($period) {
            'today'  => now()->startOfDay(),
            '7days'  => now()->subDays(7)->startOfDay(),
            '30days' => now()->subDays(30)->startOfDay(),
            default  => null,
        };
    }
}