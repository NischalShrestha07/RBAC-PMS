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

        $logs = $query->paginate(10);

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
}
