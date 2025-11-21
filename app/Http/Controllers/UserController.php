<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Commitment;
use App\Models\Department;
use App\Models\Installment;
use App\Models\Pdc;
use App\Models\PdcInstallment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function login()
    {
        return view('login');
    }

    public function superAdminDashboard()
    {
        $this->authorize('view dashboard');

        $this->updateData();
        $notifications = $this->getNotifications();
        $menu = $this->getSidebarMenu();

        // Super Admin specific data
        $totalUsers = User::count();
        $totalDepartments = Department::count();
        $totalRoles = Role::count();
        $user = Auth::user();
        $departmentIds = [$user->department_id];


        $activeCommitmentAmount = Commitment::with('installments')
            ->whereNotIn('status', ['Complete', 'Failed'])
            ->get()
            ->sum(function ($commitment) {
                return $commitment->installments
                    ->whereNotIn('status', ['Complete', 'Failed'])
                    ->sum(function ($installment) {
                        return $installment->amount - $installment->paid_amount;
                    });
            });

        $activePdcAmount = Pdc::with('installments')
            ->whereNotIn('status', ['Complete', 'Failed'])
            ->get()
            ->sum(function ($pdc) {
                return $pdc->installments
                    ->whereNotIn('status', ['Complete', 'Failed'])
                    ->sum(function ($installment) {
                        return $installment->amount - $installment->paid_amount;
                    });
            });

        // Total sums for active commitments and PDCs (exclude Complete and Failed)
        $commitmentTotal = Commitment::whereNotIn('status', ['Complete', 'Failed'])->sum('total_amount');
        $pdcTotal = Pdc::whereNotIn('status', ['Complete', 'Failed'])->sum('commitment_amount');

        // Overdue calculations for active commitments and PDCs
        $overdueCommitmentTotal = Agent::with([
            'commitments' => function ($query) {
                $query->whereNotIn('status', ['Complete', 'Failed'])->with('installments');
            }
        ])
            ->get()
            ->sum(function ($agent) {
                return $agent->commitments->flatMap(function ($commitment) {
                    return $commitment->installments->where('status', 'Overdue');
                })->sum('amount');
            });
        $overduePdcTotal = Agent::with([
            'pdcs' => function ($query) {
                $query->whereNotIn('status', ['Complete', 'Failed'])->with('installments');
            }
        ])
            ->get()
            ->sum(function ($agent) {
                return $agent->pdcs->flatMap(function ($pdc) {
                    return $pdc->installments->where('status', 'Overdue');
                })->sum('amount');
            });

        // Count totals for active commitments and PDCs
        $commitmentCount = Commitment::whereNotIn('status', ['Complete', 'Failed'])->count();
        $pdcCount = Pdc::whereNotIn('status', ['Complete', 'Failed'])->count();
        $agentCount = Agent::count();


        // Party data
        $party = Client::with([
            'commitments' => fn($query) => $query->whereNotIn('status', ['Complete', 'Failed'])->with('installments'),
            'pdcs' => fn($query) => $query->whereNotIn('status', ['Complete', 'Failed'])->with('installments')
        ])
            ->latest()->take(10)
            ->get()
            ->map(function ($client) {
                $client->total_amount = $client->commitments->sum('total_amount');
                $client->paid_amount = $client->commitments->flatMap->installments->where('status', 'Complete')->sum('amount');
                $client->pending_amount = $client->commitments->flatMap->installments->whereIn('status', ['Pending', 'Overdue'])->sum('amount');
                $client->commitment_amount = $client->pdcs->sum('commitment_amount');
                $client->pdc_collected = $client->pdcs->flatMap->installments->where('status', 'Complete')->sum('amount');
                $client->pdc_pending = $client->pdcs->flatMap->installments->whereIn('status', ['Pending', 'Overdue'])->sum('amount');
                return $client;
            })
            ->filter(fn($client) => ($client->total_amount > 0 || $client->commitment_amount > 0));

        $agents = Agent::with([
            'commitments' => function ($query) {
                $query->whereNotIn('status', ['Complete', 'Failed'])->whereNull('deleted_at')->with(['installments', 'client', 'agent']);
            },
            'pdcs' => function ($query) {
                $query->whereNotIn('status', ['Complete', 'Failed'])->whereNull('deleted_at')->with(['installments', 'client', 'agent']);
            }
        ])
            ->get()
            ->map(function ($agent) {
                $agent->total_amount = $agent->commitments->sum('total_amount');
                $agent->paid_amount = $agent->commitments->flatMap->installments->where('status', 'Complete')->sum('amount');
                $agent->pending_amount = $agent->commitments->flatMap->installments->whereIn('status', ['Pending', 'Overdue'])->sum('amount');
                $agent->commitment_amount = $agent->pdcs->sum('commitment_amount');
                $agent->pdc_collected = $agent->pdcs->flatMap->installments->where('status', 'Complete')->sum('amount');
                $agent->pdc_pending = $agent->pdcs->flatMap->installments->whereIn('status', ['Pending', 'Overdue'])->sum('amount');
                return $agent;
            })
            ->filter(fn($agent) => ($agent->total_amount > 0 || $agent->commitment_amount > 0));


        // Fetch filtered data
        $recentCommitments = Commitment::whereNotIn('status', ['Complete', 'Failed'])->with('client')->with('agent')->latest()->take(5)->get();
        $commitments = Commitment::whereNotIn('status', ['Complete', 'Failed'])->with(['agent', 'client'])->get();
        $pdcsData = Pdc::whereNotIn('status', ['Complete', 'Failed'])->with(['agent', 'client'])->latest()->limit(5)->get();
        $notifications = $this->getNotifications();
        $totalOverdue = Commitment::where('due_date', '<', Carbon::today())->whereNotIn('status', ['Complete', 'Failed'])->count();


        $departmentAgents = Department::with(['agents.commitments', 'agents.pdcs'])->get()->map(function ($department) {

            $agentsStats = $department->agents->map(function ($agent) {
                return [
                    'agent' => $agent,
                    'commitments_total'   => $agent->commitments->count(),
                    'commitments_postponed' => $agent->commitments->where('status', 'Postponed')->count(),
                    'commitments_success'   => $agent->commitments->where('status', 'Complete')->count(),
                    'pdcs_total'   => $agent->pdcs->count(),
                    'pdcs_postponed' => $agent->pdcs->where('status', 'Postponed')->count(),
                    'pdcs_success'   => $agent->pdcs->where('status', 'Complete')->count(),
                ];
            });

            return [
                'department' => $department,
                'agents' => $agentsStats->filter(fn($s) => $s['commitments_total'] > 0 || $s['pdcs_total'] > 0),
            ];
        });

        $departments = Department::where('is_active', true)->get();

        $departmentData = [];

        foreach ($departments as $dept) {
            $departmentData[$dept->id] = [
                'commitments' => Commitment::where('department_id', $dept->id)
                    ->whereNotIn('status', ['Complete', 'Failed'])
                    ->latest()
                    ->take(5)
                    ->get(),

                'pdcs' => Pdc::where('department_id', $dept->id)
                    ->whereNotIn('status', ['Complete', 'Failed'])
                    ->latest()
                    ->take(5)
                    ->get(),
            ];
        }

        return view('dashboard.super-admin', compact('notifications', 'departments', 'departmentData', 'menu', 'totalUsers', 'totalDepartments', 'totalRoles', 'activeCommitmentAmount', 'activePdcAmount', 'commitmentTotal', 'pdcTotal', 'overdueCommitmentTotal', 'overduePdcTotal', 'commitmentCount', 'pdcCount', 'agentCount', 'party', 'agents', 'recentCommitments',  'commitments', 'pdcsData', 'totalOverdue', 'departmentAgents'));
    }

    public function logout()
    {
        Auth::logout();
        session()->forget('role');

        return redirect()->route('login')->with('success', 'Logged Out Successfully.');
    }

    public function userLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $remember = $request->has('remember');

        // Attempt Login
        if (Auth::attempt($request->only('email', 'password'), $remember)) {
            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Your account is deactivated. Please contact administrator.');
            }

            // Store primary role in session (using Spatie)
            $primaryRole = $user->getRoleNames()->first();
            session(['role' => $primaryRole]);

            return $this->redirectToDashboard();
        }

        return redirect()->route('login')->with('error', 'Email & Password are incorrect.');
    }

    public function redirectToDashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $user = Auth::user();

        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account is deactivated. Please contact administrator.');
        }

        // Always redirect to unified dashboard
        return redirect()->route('dashboard.unified');
    }

    /**
     * Unified Dashboard for ALL roles - Single View
     */
    public function dynamicDashboard()
    {
        $this->authorize('view dashboard');

        $user = Auth::user();
        $this->updateData(); // Your existing data update method

        $notifications = $this->getNotifications();
        $menu = $this->getSidebarMenu();

        // Super Admin specific data
        $totalUsers = User::count();
        $totalDepartments = Department::where('is_active', true)->count();
        $totalRoles = Role::count();
        $user = Auth::user();
        $departmentIds = [$user->department_id];


        $activeCommitmentAmount = Commitment::with('installments')
            ->whereNotIn('status', ['Complete', 'Failed'])
            ->get()
            ->sum(function ($commitment) {
                return $commitment->installments
                    ->whereNotIn('status', ['Complete', 'Failed'])
                    ->sum(function ($installment) {
                        return $installment->amount - $installment->paid_amount;
                    });
            });

        $activePdcAmount = Pdc::with('installments')
            ->whereNotIn('status', ['Complete', 'Failed'])
            ->get()
            ->sum(function ($pdc) {
                return $pdc->installments
                    ->whereNotIn('status', ['Complete', 'Failed'])
                    ->sum(function ($installment) {
                        return $installment->amount - $installment->paid_amount;
                    });
            });

        // Total sums for active commitments and PDCs (exclude Complete and Failed)
        $commitmentTotal = Commitment::whereNotIn('status', ['Complete', 'Failed'])->sum('total_amount');
        $pdcTotal = Pdc::whereNotIn('status', ['Complete', 'Failed'])->sum('commitment_amount');

        // Overdue calculations for active commitments and PDCs
        $overdueCommitmentTotal = Agent::with([
            'commitments' => function ($query) {
                $query->whereNotIn('status', ['Complete', 'Failed'])->with('installments');
            }
        ])
            ->get()
            ->sum(function ($agent) {
                return $agent->commitments->flatMap(function ($commitment) {
                    return $commitment->installments->where('status', 'Overdue');
                })->sum('amount');
            });
        $overduePdcTotal = Agent::with([
            'pdcs' => function ($query) {
                $query->whereNotIn('status', ['Complete', 'Failed'])->with('installments');
            }
        ])
            ->get()
            ->sum(function ($agent) {
                return $agent->pdcs->flatMap(function ($pdc) {
                    return $pdc->installments->where('status', 'Overdue');
                })->sum('amount');
            });

        // Count totals for active commitments and PDCs
        $commitmentCount = Commitment::whereNotIn('status', ['Complete', 'Failed'])->count();
        $pdcCount = Pdc::whereNotIn('status', ['Complete', 'Failed'])->count();
        $agentCount = Agent::count();


        // Party data
        $party = Client::with([
            'commitments' => fn($query) => $query->whereNotIn('status', ['Complete', 'Failed'])->with('installments'),
            'pdcs' => fn($query) => $query->whereNotIn('status', ['Complete', 'Failed'])->with('installments')
        ])
            ->latest()->take(10)
            ->get()
            ->map(function ($client) {
                $client->total_amount = $client->commitments->sum('total_amount');
                $client->paid_amount = $client->commitments->flatMap->installments->where('status', 'Complete')->sum('amount');
                $client->pending_amount = $client->commitments->flatMap->installments->whereIn('status', ['Pending', 'Overdue'])->sum('amount');
                $client->commitment_amount = $client->pdcs->sum('commitment_amount');
                $client->pdc_collected = $client->pdcs->flatMap->installments->where('status', 'Complete')->sum('amount');
                $client->pdc_pending = $client->pdcs->flatMap->installments->whereIn('status', ['Pending', 'Overdue'])->sum('amount');
                return $client;
            })
            ->filter(fn($client) => ($client->total_amount > 0 || $client->commitment_amount > 0));

        $agents = Agent::with([
            'commitments' => function ($query) {
                $query->whereNotIn('status', ['Complete', 'Failed'])->whereNull('deleted_at')->with(['installments', 'client', 'agent']);
            },
            'pdcs' => function ($query) {
                $query->whereNotIn('status', ['Complete', 'Failed'])->whereNull('deleted_at')->with(['installments', 'client', 'agent']);
            }
        ])
            ->get()
            ->map(function ($agent) {
                $agent->total_amount = $agent->commitments->sum('total_amount');
                $agent->paid_amount = $agent->commitments->flatMap->installments->where('status', 'Complete')->sum('amount');
                $agent->pending_amount = $agent->commitments->flatMap->installments->whereIn('status', ['Pending', 'Overdue'])->sum('amount');
                $agent->commitment_amount = $agent->pdcs->sum('commitment_amount');
                $agent->pdc_collected = $agent->pdcs->flatMap->installments->where('status', 'Complete')->sum('amount');
                $agent->pdc_pending = $agent->pdcs->flatMap->installments->whereIn('status', ['Pending', 'Overdue'])->sum('amount');
                return $agent;
            })
            ->filter(fn($agent) => ($agent->total_amount > 0 || $agent->commitment_amount > 0));


        // Fetch filtered data
        $recentCommitments = Commitment::whereNotIn('status', ['Complete', 'Failed'])->with('client')->with('agent')->latest()->take(5)->get();
        $commitments = Commitment::whereNotIn('status', ['Complete', 'Failed'])->with(['agent', 'client'])->get();
        $pdcsData = Pdc::whereNotIn('status', ['Complete', 'Failed'])->with(['agent', 'client'])->latest()->limit(5)->get();
        $notifications = $this->getNotifications();
        $totalOverdue = Commitment::where('due_date', '<', Carbon::today())->whereNotIn('status', ['Complete', 'Failed'])->count();


        $departmentAgents = Department::with(['agents.commitments', 'agents.pdcs'])->where('is_active', true)->get()->map(function ($department) {

            $agentsStats = $department->agents->map(function ($agent) {
                return [
                    'agent' => $agent,
                    'commitments_total'   => $agent->commitments->count(),
                    'commitments_postponed' => $agent->commitments->where('status', 'Postponed')->count(),
                    'commitments_success'   => $agent->commitments->where('status', 'Complete')->count(),
                    'pdcs_total'   => $agent->pdcs->count(),
                    'pdcs_postponed' => $agent->pdcs->where('status', 'Postponed')->count(),
                    'pdcs_success'   => $agent->pdcs->where('status', 'Complete')->count(),
                ];
            });

            return [
                'department' => $department,
                'agents' => $agentsStats->filter(fn($s) => $s['commitments_total'] > 0 || $s['pdcs_total'] > 0),
            ];
        });

        $departments = Department::where('is_active', true)->get();

        $departmentData = [];

        foreach ($departments as $dept) {
            $departmentData[$dept->id] = [
                'commitments' => Commitment::where('department_id', $dept->id)
                    ->whereNotIn('status', ['Complete', 'Failed'])
                    ->latest()
                    ->take(5)
                    ->get(),

                'pdcs' => Pdc::where('department_id', $dept->id)
                    ->whereNotIn('status', ['Complete', 'Failed'])
                    ->latest()
                    ->take(5)
                    ->get(),
            ];
        }

        // Get dashboard data based on user's permissions
        $dashboardData = $this->getPermissionBasedDashboardData($user);

        return view('dashboard.dynamic', compact(
            'user',
            'notifications',
            'menu',
            'dashboardData',
            'departments',
            'departmentData',
            'totalUsers',
            'totalDepartments',
            'totalRoles',
            'activeCommitmentAmount',
            'activePdcAmount',
            'commitmentTotal',
            'pdcTotal',
            'overdueCommitmentTotal',
            'overduePdcTotal',
            'commitmentCount',
            'pdcCount',
            'agentCount',
            'party',
            'agents',
            'recentCommitments',
            'commitments',
            'pdcsData',
            'totalOverdue',
            'departmentAgents'
        ));
    }

    /**
     * Get dashboard data based on user's permissions
     */
    private function getPermissionBasedDashboardData($user)
    {
        $data = [];

        // Always include basic user info
        $data['user'] = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->getRoleNames()->first() ?? 'User',
            'department' => $user->department->name ?? '--',
            'last_login' => $user->last_login_at
        ];

        // Financial Overview - based on financial permissions
        if ($user->hasPermissionTo('view financial reports') || $user->hasPermissionTo('view dashboard')) {
            $data['financial_overview'] = $this->getFinancialOverview($user);
        }

        // Commitments Data - based on commitment permissions
        if ($user->hasAnyPermission(['view commitments', 'manage commitments', 'view dashboard'])) {
            $data['commitments'] = $this->getCommitmentsData($user);
        }

        // PDCs Data - based on PDC permissions
        if ($user->hasAnyPermission(['view pdcs', 'manage pdcs', 'view dashboard'])) {
            $data['pdcs'] = $this->getPdcsData($user);
        }

        // User Management Data - for admin roles
        if ($user->hasAnyPermission(['manage users', 'manage roles'])) {
            $data['user_management'] = $this->getUserManagementData();
        }

        // Department Data - for department-specific access
        if ($user->hasPermissionTo('view departments') || $user->department_id) {
            $data['department_data'] = $this->getDepartmentData($user);
        }

        // Agent Data - for sales and management
        if ($user->hasAnyPermission(['view agents', 'manage agents', 'view team'])) {
            $data['agent_data'] = $this->getAgentData($user);
        }

        // Recent Activities - for all users
        $data['recent_activities'] = $this->getRecentActivities($user);

        return $data;
    }

    /**
     * Financial Overview Data
     */
    private function getFinancialOverview($user)
    {
        $query = Commitment::whereNotIn('status', ['Complete', 'Failed']);
        $pdcQuery = Pdc::whereNotIn('status', ['Complete', 'Failed']);

        // Apply department filter if user is not super admin
        if (!$user->hasRole('Super Admin') && $user->department_id) {
            $query->where('department_id', $user->department_id);
            $pdcQuery->where('department_id', $user->department_id);
        }

        $activeCommitments = $query->get();
        $activePdcs = $pdcQuery->get();

        return [
            'active_commitments_amount' => $activeCommitments->sum('total_amount'),
            'active_pdcs_amount' => $activePdcs->sum('commitment_amount'),
            'overdue_commitments' => $this->getOverdueAmount($activeCommitments),
            'overdue_pdcs' => $this->getOverdueAmount($activePdcs, true),
            'total_commitments' => $activeCommitments->count(),
            'total_pdcs' => $activePdcs->count(),
        ];
    }

    /**
     * Commitments Data with permission-based filtering
     */
    private function getCommitmentsData($user)
    {
        $query = Commitment::with(['client', 'agent', 'installments'])
            ->whereNotIn('status', ['Complete', 'Failed']);

        // Apply filters based on user permissions
        if (!$user->hasPermissionTo('view all commitments') && $user->department_id) {
            $query->where('department_id', $user->department_id);
        }

        if ($user->hasPermissionTo('view own commitments only')) {
            $query->where('created_by', $user->id);
        }

        return [
            'recent' => $query->latest()->take(5)->get(),
            'total' => $query->count(),
            'status_summary' => $query->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
        ];
    }

    /**
     * PDCs Data with permission-based filtering
     */
    private function getPdcsData($user)
    {
        $query = Pdc::with(['client', 'agent', 'installments'])
            ->whereNotIn('status', ['Complete', 'Failed']);

        // Apply filters based on user permissions
        if (!$user->hasPermissionTo('view all pdcs') && $user->department_id) {
            $query->where('department_id', $user->department_id);
        }

        if ($user->hasPermissionTo('view own pdcs only')) {
            $query->where('created_by', $user->id);
        }

        return [
            'recent' => $query->latest()->take(5)->get(),
            'total' => $query->count(),
            'status_summary' => $query->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
        ];
    }

    /**
     * User Management Data for admin roles
     */
    private function getUserManagementData()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_roles' => Role::count(),
            'recent_users' => User::with('roles')->latest()->take(5)->get()
        ];
    }

    /**
     * Department-specific data
     */
    private function getDepartmentData($user)
    {
        $departmentId = $user->department_id;

        if (!$departmentId) {
            return [];
        }

        // For users who don't have access to all departments, only show their department data
        if (!$user->hasPermissionTo('access all departments') && !$user->hasRole('Super Admin')) {
            return [
                'department_commitments' => Commitment::where('department_id', $departmentId)
                    ->whereNotIn('status', ['Complete', 'Failed'])
                    ->count(),
                'department_pdcs' => Pdc::where('department_id', $departmentId)
                    ->whereNotIn('status', ['Complete', 'Failed'])
                    ->count(),
                'department_agents' => Agent::where('department_id', $departmentId)->count()
            ];
        }

        // For users with access to all departments, you can return all data or specific logic
        return [
            'department_commitments' => Commitment::whereNotIn('status', ['Complete', 'Failed'])->count(),
            'department_pdcs' => Pdc::whereNotIn('status', ['Complete', 'Failed'])->count(),
            'department_agents' => Agent::count()
        ];
    }

    /**
     * Agent and Team data
     */
    private function getAgentData($user)
    {
        $query = Agent::with(['commitments', 'pdcs']);

        if ($user->department_id && !$user->hasPermissionTo('view all agents')) {
            $query->where('department_id', $user->department_id);
        }

        $agents = $query->get()->map(function ($agent) {
            return [
                'id' => $agent->id,
                'name' => $agent->name,
                'department' => $agent->department->name ?? 'N/A',
                'total_commitments' => $agent->commitments->whereNotIn('status', ['Complete', 'Failed'])->count(),
                'total_pdcs' => $agent->pdcs->whereNotIn('status', ['Complete', 'Failed'])->count(),
                'commitments_amount' => $agent->commitments->whereNotIn('status', ['Complete', 'Failed'])->sum('total_amount'),
                'pdcs_amount' => $agent->pdcs->whereNotIn('status', ['Complete', 'Failed'])->sum('commitment_amount'),
            ];
        });

        return [
            'agents' => $agents,
            'total_agents' => $agents->count(),
            'top_performers' => $agents->sortByDesc('commitments_amount')->take(5)
        ];
    }

    /**
     * Recent Activities across the system
     */
    private function getRecentActivities($user)
    {
        $activities = [];

        // Recent commitments
        if ($user->can('view commitments')) {
            $commitmentQuery = Commitment::with('client');
            if (!$user->can('view all commitments') && $user->department_id) {
                $commitmentQuery->where('department_id', $user->department_id);
            }
            $activities['commitments'] = $commitmentQuery->latest()->take(3)->get();
        }

        // Recent PDCs
        if ($user->can('view pdcs')) {
            $pdcQuery = Pdc::with('client');
            if (!$user->can('view all pdcs') && $user->department_id) {
                $pdcQuery->where('department_id', $user->department_id);
            }
            $activities['pdcs'] = $pdcQuery->latest()->take(3)->get();
        }

        return $activities;
    }

    /**
     * Calculate overdue amounts
     */
    private function getOverdueAmount($items, $isPdc = false)
    {
        return $items->sum(function ($item) use ($isPdc) {
            return $item->installments
                ->where('due_date', '<', Carbon::today())
                ->whereNotIn('status', ['Complete', 'Failed'])
                ->sum('amount');
        });
    }

    public function showProfile()
    {
        $this->authorize('view profile');

        $recentCommitments = Commitment::where('created_by', Auth::id())
            ->with(['agent'])
            ->orderBy('created_at', 'asc')
            ->get();

        $userId = Auth::id();
        $commitments = Commitment::where('created_by', $userId)->with('agent')->get();
        $notifications = $this->getNotifications();
        $user = Auth::user();

        // Use Spatie roles instead of legacy role field
        $primaryRole = $user->getRoleNames()->first();

        // Map Spatie roles to views
        $viewMap = [
            'Super Admin' => 'profile.superProfile',
            'Admin' => 'profile.superProfile', // or create admin profile view
            'Accountant' => 'profile.superProfile', // or create accountant profile view
            // Add other role mappings as needed
        ];

        $view = $viewMap[$primaryRole] ?? 'profile.superProfile';
        return view($view, compact('user', 'recentCommitments', 'notifications', 'commitments'));
    }
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'No authenticated user found');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',

            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Save inside Laravel public folder (allowed by open_basedir)
            $destinationPath = public_path('uploads/images');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $image->move($destinationPath, $imageName);

            // Store only file name in DB
            $user->image = $imageName;
        }

        try {
            $user->save();
            return redirect()->back()->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saving profile: ' . $e->getMessage());
        }
    }


    public function changePassword(Request $request)
    {
        $user = Auth::user();

        // Validate input
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        // Update password
        $user->password = Hash::make($request->new_password); // Ensure password is hashed
        $user->save();

        return redirect()->back()->with('success', 'Password changed successfully.');
    }


    public function index(Request $request)
    {
        $this->authorize('manage users');

        $query = User::with(['roles', 'department', 'branch']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role != '') {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filter by status
        if ($request->has('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(10);
        $assignableRoles = $this->getAssignableRoles();

        $notifications = $this->getNotifications();

        return view('admin.users.index', compact('users', 'assignableRoles', 'notifications'));
    }

    public function create()
    {
        $this->authorize('manage users');

        $assignableRoles = $this->getAssignableRoles();
        $departments = Department::active()->get();
        $branches = Branch::where('is_active', true)->get();
        $notifications = $this->getNotifications();


        return view('admin.users.create', compact('assignableRoles', 'departments', 'branches', 'notifications'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage users');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'department_id' => 'required|exists:departments,id',
            'branch_id' => 'required|exists:branches,id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
            'phone' => 'nullable|string|max:20',
        ]);

        // Validate role assignment permissions
        $assignableRoles = $this->getAssignableRoles()->pluck('name')->toArray();
        foreach ($request->roles as $roleName) {
            if (!in_array($roleName, $assignableRoles)) {
                return redirect()->back()->with('error', "You cannot assign the role: {$roleName}")->withInput();
            }
        }

        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'department_id' => $request->department_id,
                    'branch_id' => $request->branch_id,
                    'phone' => $request->phone,
                    'is_active' => true,
                ]);

                $user->syncRoles($request->roles);
            });

            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        $this->authorize('manage users');

        $user->load(['roles', 'department', 'branch']);
        $notifications = $this->getNotifications();

        return view('admin.users.show', compact('user', 'notifications'));
    }

    public function edit(User $user)
    {
        $this->authorize('manage users');

        // Prevent users from editing their own account through this interface
        if ($user->id === Auth::id()) {
            return redirect()->route('profile.update')->with('error', 'Please use profile page to edit your own account.');
        }
        $notifications = $this->getNotifications();

        $assignableRoles = $this->getAssignableRoles();
        $departments = Department::active()->get();
        $branches = Branch::where('is_active', true)->get();
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('admin.users.edit', compact('user', 'assignableRoles', 'departments', 'branches', 'userRoles', 'notifications'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('manage users');

        // Prevent users from modifying their own account
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot modify your own account through this interface.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'department_id' => 'required|exists:departments,id',
            'branch_id' => 'required|exists:branches,id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
            'phone' => 'nullable|string|max:20',
        ]);

        // Validate role assignment permissions
        $assignableRoles = $this->getAssignableRoles()->pluck('name')->toArray();
        foreach ($request->roles as $roleName) {
            if (!in_array($roleName, $assignableRoles)) {
                return redirect()->back()->with('error', "You cannot assign the role: {$roleName}")->withInput();
            }
        }

        try {
            DB::transaction(function () use ($request, $user) {
                $oldRoles = $user->getRoleNames()->implode(', ');

                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'department_id' => $request->department_id,
                    'branch_id' => $request->branch_id,
                    'phone' => $request->phone,
                ]);

                $user->syncRoles($request->roles);
            });

            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        $this->authorize('manage users');

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->hasRole('Super Admin') && !Auth::user()->hasRole('Super Admin')) {
            return redirect()->back()->with('error', 'Cannot delete Super Admin user.');
        }

        try {
            $user->delete();

            return redirect()->route('users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    public function toggleStatus(User $user)
    {
        $this->authorize('manage users');

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        if ($this->isSuperiorUser($user)) {
            return redirect()->back()->with('error', 'You cannot deactivate this user.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "User {$status} successfully.");
    }

    public function permissions(User $user)
    {
        $this->authorize('manage users');

        $user->load('roles.permissions');
        $permissions = $user->getAllPermissions()->groupBy('group');
        $notifications = $this->getNotifications();

        return view('admin.users.permissions', compact('user', 'permissions', 'notifications'));
    }

    /**
     * Get roles that the current user can assign
     */
    private function getAssignableRoles()
    {
        $user = auth()->user();

        if ($user->hasRole('Super Admin')) {
            return Role::where('is_active', true)->get();
        }

        if ($user->hasRole('Admin')) {
            return Role::where('is_active', true)
                ->where('name', '!=', 'Super Admin')
                ->get();
        }

        return Role::where('is_active', true)
            ->whereIn('name', ['Staff', 'Viewer'])
            ->get();
    }

    /**
     * Check if user is superior to current user
     */
    private function isSuperiorUser(User $targetUser)
    {
        $currentUser = auth()->user();

        if ($currentUser->hasRole('Super Admin')) {
            return false;
        }

        if ($targetUser->hasRole('Super Admin')) {
            return true;
        }

        if ($currentUser->hasRole('Admin') && $targetUser->hasRole('Admin')) {
            return true;
        }

        return false;
    }

    /**
     * Get user's effective permissions
     */
    public function getUserPermissions($id)
    {
        $this->authorize('manage users');
        $notifications = $this->getNotifications();
        $user = User::with('roles.permissions')->findOrFail($id);
        $permissions = $user->getAllPermissions()->groupBy('group');

        return view('admin.users.permissions', compact('user', 'permissions', 'notifications'));
    }
}
