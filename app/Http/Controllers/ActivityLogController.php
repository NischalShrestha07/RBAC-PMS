<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view activity logs');

        $query = ActivityLog::with('user')
            ->select('activity_logs.*')
            ->latest();

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->date_from)->startOfDay(),
                Carbon::parse($request->date_to)->endOfDay()
            ]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('log_uid', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $logs = $query->paginate(50);

        $users = User::whereIn('id', ActivityLog::distinct()->pluck('user_id'))->get();
        $actions = ActivityLog::distinct()->pluck('action');
        $models = ActivityLog::distinct()->whereNotNull('model_type')->pluck('model_type');
        $statuses = ActivityLog::distinct()->pluck('status');

        $notifications = $this->getNotifications();

        return view('admin.logs.index', compact(
            'logs',
            'users',
            'actions',
            'models',
            'statuses',
            'notifications'
        ));
    }

    public function show($id)
    {
        $this->authorize('view activity logs');

        $log = ActivityLog::with(['user', 'model'])->findOrFail($id);
        $notifications = $this->getNotifications();

        return view('admin.logs.show', compact('log', 'notifications'));
    }

    public function stats()
    {
        $this->authorize('view activity logs');

        $stats = [
            'total_logs' => ActivityLog::count(),
            'today_logs' => ActivityLog::whereDate('created_at', today())->count(),
            'top_users' => ActivityLog::select('user_id', DB::raw('count(*) as total'))
                ->with('user')
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->orderBy('total', 'desc')
                ->take(10)
                ->get(),
            'recent_activities' => ActivityLog::with('user')
                ->latest()
                ->take(20)
                ->get(),
            'actions_distribution' => ActivityLog::select('action', DB::raw('count(*) as total'))
                ->groupBy('action')
                ->orderBy('total', 'desc')
                ->get(),
            'models_distribution' => ActivityLog::select('model_type', DB::raw('count(*) as total'))
                ->whereNotNull('model_type')
                ->groupBy('model_type')
                ->orderBy('total', 'desc')
                ->get(),
        ];

        $notifications = $this->getNotifications();

        return view('admin.logs.stats', compact('stats', 'notifications'));
    }

    public function export(Request $request)
    {
        $this->authorize('export activity logs');

        $query = ActivityLog::with('user');

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->date_from)->startOfDay(),
                Carbon::parse($request->date_to)->endOfDay()
            ]);
        }

        $logs = $query->get();

        $filename = 'activity-logs-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Log ID',
                'Timestamp',
                'User',
                'Action',
                'Model',
                'Description',
                'IP Address',
                'Status',
                'Changes'
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->log_uid,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user ? $log->user->name : 'System',
                    $log->getFormattedActionAttribute(),
                    $log->model_type ? class_basename($log->model_type) : 'N/A',
                    $log->description,
                    $log->ip_address,
                    ucfirst($log->status),
                    $log->changes_summary
                ]);
            }

            fclose($file);
        };

        // Log the export
        app(\App\Services\ActivityLogger::class)->logExport('activity_logs', [
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'total_records' => $logs->count()
        ]);

        return response()->stream($callback, 200, $headers);
    }

    public function cleanup(Request $request)
    {
        $this->authorize('manage activity logs');

        $request->validate([
            'days' => 'required|integer|min:30|max:365'
        ]);

        $cutoffDate = now()->subDays($request->days);
        $deletedCount = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        // Log the cleanup
        app(\App\Services\ActivityLogger::class)->log(
            'cleanup',
            "Cleaned up {$deletedCount} activity logs older than {$request->days} days",
            null,
            [],
            [],
            'success'
        );

        return redirect()->route('logs.index')
            ->with('success', "Successfully deleted {$deletedCount} logs older than {$request->days} days.");
    }
}
