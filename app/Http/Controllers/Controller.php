<?php

namespace App\Http\Controllers;

use App\Models\Commitment;
use App\Models\Installment;
use App\Models\Pdc;
use App\Models\PdcInstallment;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    // Check permission using Spatie
    protected function checkPermission($permission)
    {
        if (!Auth::user()->can($permission)) {
            abort(403, 'Unauthorized action.');
        }
    }

    // Get sidebar menu based on permissions
    protected function getSidebarMenu()
    {
        $user = Auth::user();
        $menu = [];

        if ($user->canAccessModule('dashboard')) {
            $menu[] = [
                'name' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'bi-speedometer2',
                'permission' => 'view dashboard'
            ];
        }

        if ($user->canAccessModule('commitments')) {
            $menu[] = [
                'name' => 'Commitments',
                'route' => 'commitments.index',
                'icon' => 'bi-journal-check',
                'permission' => 'view commitments'
            ];
        }

        if ($user->canAccessModule('pdcs')) {
            $menu[] = [
                'name' => 'PDCs',
                'route' => 'pdcs.index',
                'icon' => 'bi-receipt-cutoff',
                'permission' => 'view pdcs'
            ];
        }

        if ($user->canAccessModule('agents')) {
            $menu[] = [
                'name' => 'Salesman',
                'route' => 'agents.index',
                'icon' => 'bi-people-fill',
                'permission' => 'view agents'
            ];
        }

        if ($user->canAccessModule('clients')) {
            $menu[] = [
                'name' => 'Clients/Parties',
                'route' => 'clients.index',
                'icon' => 'bi-person-lines-fill',
                'permission' => 'view clients'
            ];
        }

        if ($user->canAccessModule('installments')) {
            $menu[] = [
                'name' => 'Installments',
                'route' => 'installments.index',
                'icon' => 'bi-calendar-check',
                'permission' => 'view installments'
            ];
        }

        if ($user->canAccessModule('reports')) {
            $menu[] = [
                'name' => 'Reports',
                'route' => 'reports.index',
                'icon' => 'bi-graph-up',
                'permission' => 'view reports'
            ];
        }

        if ($user->canAccessModule('cheque_locations')) {
            $menu[] = [
                'name' => 'Cheque Location',
                'route' => 'cheque.locations.index',
                'icon' => 'bi-geo-alt-fill',
                'permission' => 'view cheque locations'
            ];
        }

        // Admin only menus
        if ($user->canAccessModule('users')) {
            $menu[] = [
                'name' => 'User Management',
                'route' => 'users.management',
                'icon' => 'bi-person-gear',
                'permission' => 'manage users'
            ];
        }

        return $menu;
    }

    public function getNotifications($type = null, $offset = 0, $limit = null)
    {
        $user = Auth::user(); // Get the logged-in user

        // Fetch PDC Notifications
        $pdcQuery = Pdc::with([
            'installments' => function ($query) {
                $query->where('status', '<>', 'Complete')
                    ->orderBy('due_date', 'asc')
                    ->select('id', 'pdc_id', 'due_date', 'amount', 'status', 'viewed');
            },
            'agent:id,name',
            'client:id,name,address,phone,department'
        ])
            ->select('id', 'commitment_amount', 'agent_id', 'client_id')
            ->when($user->role !== 'Super Admin', function ($query) use ($user) {
                return $query->whereHas('agent', function ($q) use ($user) {
                    $q->where('department', $user->role);
                });
            })
            ->whereHas('installments', function ($query) {
                $query->where('status', '<>', 'Complete');
            });

        // Fetch Commitment Notifications
        $commitmentQuery = Commitment::with([
            'installments' => function ($query) {
                $query->where('status', '<>', 'Complete')
                    ->orderBy('due_date', 'asc')
                    ->select('id', 'commitment_id', 'due_date', 'amount', 'status', 'viewed');
            },
            'agent:id,name',
            'client:id,name,address,phone,company,department'
        ])
            ->select('id', 'total_amount', 'agent_id', 'client_id')
            ->when($user->role !== 'Super Admin', function ($query) use ($user) {
                return $query->whereHas('agent', function ($q) use ($user) {
                    $q->where('department', $user->role);
                });
            })
            ->whereHas('installments', function ($query) {
                $query->where('status', '<>', 'Complete');
            });

        // If loading more via AJAX
        if ($type && $limit) {
            if ($type === 'pdc') {
                $pdcNotifications = $pdcQuery->skip($offset)->take($limit)->get();
                return $this->formatNotificationsForAjax($pdcNotifications, 'pdc');
            } elseif ($type === 'commitment') {
                $commitmentNotifications = $commitmentQuery->skip($offset)->take($limit)->get();
                return $this->formatNotificationsForAjax($commitmentNotifications, 'commitment');
            }
        }

        // Initial load - get first 10 of each
        $pdcNotifications = $pdcQuery->take(10)->get();
        $commitmentNotifications = $commitmentQuery->take(10)->get();

        // Count all notifications for badges
        $pdcInstallmentCount = $pdcQuery->get()->sum(fn($pdc) => $pdc->installments->where('viewed', false)->count());
        $commitmentInstallmentCount = $commitmentQuery->get()->sum(fn($commitment) => $commitment->installments->where('viewed', false)->count());

        return [
            'Commitment Notification' => $commitmentNotifications,
            'PDC Notification' => $pdcNotifications,
            'commitmentCount' => $commitmentInstallmentCount,
            'pdcCount' => $pdcInstallmentCount,
            'hasMorePdc' => $pdcQuery->count() > 10,
            'hasMoreCommitment' => $commitmentQuery->count() > 10,
        ];
    }

    private function formatNotificationsForAjax($notifications, $type)
    {
        $formatted = [];

        foreach ($notifications as $notification) {
            if ($notification->installments->isNotEmpty() && $notification->client) {
                foreach ($notification->installments as $installment) {
                    $formatted[] = [
                        'id' => $installment->id,
                        'notification_id' => $notification->id,
                        'type' => $type,
                        'client_name' => $notification->client->name,
                        'client_phone' => $notification->client->phone ?? 'N/A',
                        'agent_name' => $notification->agent->name ?? 'N/A',
                        'amount' => number_format($installment->amount, 2),
                        'due_date' => \Carbon\Carbon::parse($installment->due_date)->format('M d, Y'),
                        'status' => $installment->status,
                        'viewed' => $installment->viewed,
                        'view_url' => $type === 'pdc'
                            ? route('pdc.show', $notification->id)
                            : route('commitments.show', $notification->id)
                    ];
                }
            }
        }

        return $formatted;
    }



    public function updateData()
    {

        Installment::whereDate('due_date', '<', Carbon::today())
            ->where('status', 'Pending')
            ->update(['status' => 'Overdue']);
        PdcInstallment::whereDate('due_date', '<', Carbon::today())
            ->where('status', 'Pending')
            ->update(['status' => 'Overdue']);


        Installment::whereDate('due_date', '>=', Carbon::today())
            ->where('status', 'Overdue')
            ->update(['status' => 'Pending']);

        PdcInstallment::whereDate('due_date', '>=', Carbon::today())
            ->where('status', 'Overdue')
            ->update(['status' => 'Pending']);

        Installment::whereRaw('paid_amount >= amount')
            ->where('status', '!=', 'Complete')
            ->update(['status' => 'Complete']);

        $uCommits = Commitment::with('installments')->get();

        foreach ($uCommits as $commit) {
            if ($commit->status === 'Failed') {
                continue;
            }

            $hasInstallments = $commit->installments()->exists();
            if ($hasInstallments) {
                $allComplete = $commit->installments->every(fn($installment) => $installment->status === 'Complete');
                $hasPending = $commit->installments->contains(fn($installment) => $installment->status === 'Pending');
                $hasOverdue = $commit->installments->contains(fn($installment) => $installment->status === 'Overdue');

                if ($hasOverdue) {
                    $commit->status = 'Overdue';
                } elseif ($hasPending && !$hasOverdue) {
                    $commit->status = 'Pending';
                } elseif ($allComplete) {
                    $commit->status = 'Complete';
                }
            } else {
                Log::info("Commitment ID: {$commit->id} has no installments, preserving status: {$commit->status}");
            }

            $totalAmount = $commit->activeInstallments()->sum('amount');
            $paidAmount = $commit->activeInstallments()->sum('paid_amount');
            $commit->total_amount = $totalAmount;
            $commit->pending_amount = $totalAmount - $paidAmount;
            $commit->save();
        }


        $uPdc = Pdc::with('installments')->get();

        foreach ($uPdc as $pdc) {
            if ($pdc->status === 'Failed') {
                continue;
            }

            $haspdcInstallments = $pdc->installments()->exists();
            if ($haspdcInstallments) {
                $allComplete = $pdc->installments->every(fn($installment) => $installment->status === 'Complete');
                $hasPending = $pdc->installments->contains(fn($installment) => $installment->status === 'Pending');
                $hasOverdue = $pdc->installments->contains(fn($installment) => $installment->status === 'Overdue');

                if ($hasOverdue) {
                    $pdc->status = 'Overdue';
                } elseif ($hasPending && !$hasOverdue) {
                    $pdc->status = 'Pending';
                } elseif ($allComplete) {
                    $pdc->status = 'Complete';
                }
            } else {
                Log::info("PDC ID: {$pdc->id} has no installments, preserving status: {$pdc->status}");
            }
            $pdc->commitment_amount = $pdc->activeInstallments()->sum('amount');
            $pdc->save();
        }
    }

    // Get accessible data based on user role
    protected function getAccessibleData($model, $filters = [])
    {
        $user = Auth::user();
        $query = $model::query();

        // Super Admin sees everything
        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        // Admin sees their department data
        if ($user->hasRole('Admin') || $user->hasRole('Manager') || $user->hasRole('Accountant')) {
            if (method_exists($model, 'department')) {
                $query->whereHas('department', function ($q) use ($user) {
                    $q->where('id', $user->department_id);
                });
            } elseif (in_array('department_id', \Schema::getColumnListing((new $model)->getTable()))) {
                $query->where('department_id', $user->department_id);
            }
        }

        // Sales Agents and Staff see only their created data
        if ($user->hasRole('Sales Agent') || $user->hasRole('Staff')) {
            if (in_array('created_by', \Schema::getColumnListing((new $model)->getTable()))) {
                $query->where('created_by', $user->id);
            }
        }

        // Apply additional filters
        foreach ($filters as $filter) {
            $query = $filter($query);
        }

        return $query;
    }
}
