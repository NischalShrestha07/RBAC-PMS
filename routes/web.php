<?php

use App\Http\Controllers\AgentCommitmentController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ChequeMenuController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommitmentController;
use App\Http\Controllers\CommitmentFollowupController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\InstallmentPdcController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PdcController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckDashboardAccess;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckDepartmentAccess;
use Illuminate\Support\Facades\Route;

// ======================== AUTH GUEST ROUTES ========================
Route::group(['middleware' => 'admin.guest'], function () {
    Route::get('/', [UserController::class, 'login'])->name('login');
    Route::post('/login', [UserController::class, 'userLogin'])->name('login.authenticate');
});

// ======================== AUTH PROTECTED ROUTES ========================
Route::middleware(['auth'])->group(function () {
    // Unified Dashboard Route - MUST BE DEFINED FIRST
    Route::get('/dashboard', [UserController::class, 'unifiedDashboard'])
        ->name('dashboard.unified')
        ->middleware([CheckDashboardAccess::class]);

    // Update redirect route
    Route::get('/dashboard/redirect', [UserController::class, 'redirectToDashboard'])
        ->name('dashboard.redirect');

    // ======================== USER, ROLE, DEPARTMENT MGMT ========================
    // User Management Routes with permission middleware
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index')
            ->middleware(CheckPermission::class . ':manage users');

        Route::get('/create', [UserController::class, 'create'])->name('create')
            ->middleware(CheckPermission::class . ':manage users');

        Route::post('/', [UserController::class, 'store'])->name('store')
            ->middleware(CheckPermission::class . ':manage users');

        Route::get('/{user}', [UserController::class, 'show'])->name('show')
            ->middleware(CheckPermission::class . ':manage users');

        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit')
            ->middleware(CheckPermission::class . ':manage users');

        Route::put('/{user}', [UserController::class, 'update'])->name('update')
            ->middleware(CheckPermission::class . ':manage users');

        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy')
            ->middleware(CheckPermission::class . ':manage users');

        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status')
            ->middleware(CheckPermission::class . ':manage users');

        Route::get('/{user}/permissions', [UserController::class, 'permissions'])->name('permissions')
            ->middleware(CheckPermission::class . ':manage users');
    });

    // Department Management Routes with department access control
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index')
            ->middleware([CheckPermission::class . ':view departments', CheckDepartmentAccess::class]);

        Route::get('/create', [DepartmentController::class, 'create'])->name('create')
            ->middleware([CheckPermission::class . ':manage departments', CheckDepartmentAccess::class]);

        Route::post('/', [DepartmentController::class, 'store'])->name('store')
            ->middleware([CheckPermission::class . ':manage departments', CheckDepartmentAccess::class]);

        Route::get('/{department}', [DepartmentController::class, 'show'])->name('show')
            ->middleware([CheckPermission::class . ':view departments', CheckDepartmentAccess::class]);

        Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('edit')
            ->middleware([CheckPermission::class . ':manage departments', CheckDepartmentAccess::class]);

        Route::put('/{department}', [DepartmentController::class, 'update'])->name('update')
            ->middleware([CheckPermission::class . ':manage departments', CheckDepartmentAccess::class]);

        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy')
            ->middleware([CheckPermission::class . ':manage departments', CheckDepartmentAccess::class]);

        Route::post('/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('toggle-status')
            ->middleware([CheckPermission::class . ':manage departments', CheckDepartmentAccess::class]);
    });

    // Role Management Routes with permission middleware
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index')
            ->middleware(CheckPermission::class . ':manage roles');

        Route::get('/create', [RoleController::class, 'create'])->name('create')
            ->middleware(CheckPermission::class . ':manage roles');

        Route::post('/', [RoleController::class, 'store'])->name('store')
            ->middleware(CheckPermission::class . ':manage roles');

        Route::get('/{role}', [RoleController::class, 'show'])->name('show')
            ->middleware(CheckPermission::class . ':manage roles');

        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit')
            ->middleware(CheckPermission::class . ':manage roles');

        Route::put('/{role}', [RoleController::class, 'update'])->name('update')
            ->middleware(CheckPermission::class . ':manage roles');

        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy')
            ->middleware(CheckPermission::class . ':manage roles');

        Route::post('/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('toggle-status')
            ->middleware(CheckPermission::class . ':manage roles');

        Route::post('/{role}/duplicate', [RoleController::class, 'duplicate'])->name('duplicate')
            ->middleware(CheckPermission::class . ':manage roles');
    });

    // ======================== COMMITMENTS ========================
    Route::prefix('commitments')->group(function () {
        Route::get('/', [CommitmentController::class, 'index'])->name('commitments.index')
            ->middleware([CheckPermission::class . ':view commitments', CheckDepartmentAccess::class]);

        Route::get('/create', [CommitmentController::class, 'create'])->name('commitments.create')
            ->middleware([CheckPermission::class . ':create commitments', CheckDepartmentAccess::class]);

        Route::post('/', [CommitmentController::class, 'store'])->name('commitments.store')
            ->middleware([CheckPermission::class . ':create commitments', CheckDepartmentAccess::class]);

        Route::get('/{id}', [CommitmentController::class, 'showCommitment'])->name('commitments.show')
            ->middleware([CheckPermission::class . ':view commitments', CheckDepartmentAccess::class]);

        Route::get('/{id}/edit', [CommitmentController::class, 'editCommitment'])->name('commitments.edit')
            ->middleware([CheckPermission::class . ':edit commitments', CheckDepartmentAccess::class]);

        Route::put('/{id}', [CommitmentController::class, 'update'])->name('commitments.update')
            ->middleware([CheckPermission::class . ':edit commitments', CheckDepartmentAccess::class]);

        Route::delete('/{id}', [CommitmentController::class, 'destroy'])->name('commitments.destroy')
            ->middleware([CheckPermission::class . ':delete commitments', CheckDepartmentAccess::class]);

        Route::post('{id}/fail', [CommitmentController::class, 'failCommitment'])->name('commitments.fail')
            ->middleware([CheckPermission::class . ':edit commitments', CheckDepartmentAccess::class]);

        Route::post('installments/{id}/fail', [CommitmentController::class, 'failInstallment'])->name('installments.fail')
            ->middleware([CheckPermission::class . ':edit commitments', CheckDepartmentAccess::class]);
    });

    // ======================== PDCs ========================
    Route::prefix('pdcs')->group(function () {
        Route::get('/', [PdcController::class, 'index'])->name('pdcs.index')
            ->middleware([CheckPermission::class . ':view pdcs', CheckDepartmentAccess::class]);

        Route::get('/create', [PdcController::class, 'create'])->name('pdcs.create')
            ->middleware([CheckPermission::class . ':create pdcs', CheckDepartmentAccess::class]);

        Route::post('/', [PdcController::class, 'store'])->name('pdcs.store')
            ->middleware([CheckPermission::class . ':create pdcs', CheckDepartmentAccess::class]);

        Route::get('/{id}', [PdcController::class, 'showPdc'])->name('pdcs.show')
            ->middleware([CheckPermission::class . ':view pdcs', CheckDepartmentAccess::class]);

        Route::get('/{id}/edit', [PdcController::class, 'editPdc'])->name('pdcs.edit')
            ->middleware([CheckPermission::class . ':edit pdcs', CheckDepartmentAccess::class]);

        Route::put('/{id}', [PdcController::class, 'update'])->name('pdcs.update')
            ->middleware([CheckPermission::class . ':edit pdcs', CheckDepartmentAccess::class]);

        Route::delete('/{id}', [PdcController::class, 'destroy'])->name('pdcs.destroy')
            ->middleware([CheckPermission::class . ':delete pdcs', CheckDepartmentAccess::class]);

        Route::post('pdcs/{id}/fail', [PdcController::class, 'failPdc'])->name('pdcs.fail')
            ->middleware([CheckPermission::class . ':edit pdcs', CheckDepartmentAccess::class]);

        Route::post('pdc_installments/{id}/fail', [PdcController::class, 'failPdcInstallment'])->name('pdcInstallments.fail')
            ->middleware([CheckPermission::class . ':edit pdcs', CheckDepartmentAccess::class]);
    });

    // ======================== CLIENTS ========================
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('clients.index')
            ->middleware([CheckPermission::class . ':view clients', CheckDepartmentAccess::class]);

        Route::get('/create', [ClientController::class, 'create'])->name('clients.create')
            ->middleware([CheckPermission::class . ':create clients', CheckDepartmentAccess::class]);

        Route::post('/', [ClientController::class, 'store'])->name('clients.store')
            ->middleware([CheckPermission::class . ':create clients', CheckDepartmentAccess::class]);

        Route::get('/{client}', [ClientController::class, 'show'])->name('clients.show')
            ->middleware([CheckPermission::class . ':view clients', CheckDepartmentAccess::class]);

        Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit')
            ->middleware([CheckPermission::class . ':edit clients', CheckDepartmentAccess::class]);

        Route::put('/{client}', [ClientController::class, 'update'])->name('clients.update')
            ->middleware([CheckPermission::class . ':edit clients', CheckDepartmentAccess::class]);

        Route::delete('/{id}', [ClientController::class, 'destroy'])->name('clients.destroy')
            ->middleware([CheckPermission::class . ':delete clients', CheckDepartmentAccess::class]);

        Route::get('commitment/{id}', [CommitmentController::class, 'show'])->name('client.commitment.show')
            ->middleware([CheckPermission::class . ':view commitments', CheckDepartmentAccess::class]);

        Route::get('commitment/{id}/edit', [CommitmentController::class, 'edit'])->name('client.commitment.edit')
            ->middleware([CheckPermission::class . ':edit commitments', CheckDepartmentAccess::class]);

        Route::get('pdc/{id}', [PdcController::class, 'show'])->name('client.pdc.show')
            ->middleware([CheckPermission::class . ':view pdcs', CheckDepartmentAccess::class]);

        Route::get('pdc/{id}/edit', [PdcController::class, 'edit'])->name('client.pdc.edit')
            ->middleware([CheckPermission::class . ':edit pdcs', CheckDepartmentAccess::class]);
    });

    // ======================== PAYMENT ROUTES ========================
    Route::post('installments/pay/{id}', [CommitmentController::class, 'pay'])->name('installments.pay')
        ->middleware(CheckPermission::class . ':create transactions');

    Route::post('pdcInstallments/pay/{id}', [PdcController::class, 'pay'])->name('pdcInstallments.pay')
        ->middleware(CheckPermission::class . ':create transactions');

    // ======================== PROFILE ROUTES ========================
    Route::get('/profile', [UserController::class, 'showProfile'])->name('view.profile')
        ->middleware(CheckPermission::class . ':view profile');

    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update')
        ->middleware(CheckPermission::class . ':edit profile');

    Route::put('/profile/change-password', [UserController::class, 'changePassword'])->name('change-password')
        ->middleware(CheckPermission::class . ':change password');

    // ======================== FOLLOWUP ROUTES ========================
    Route::post('commitments/{id}/followup', [CommitmentController::class, 'followup'])->name('commitments.followup')
        ->middleware(CheckPermission::class . ':create followups');

    Route::post('pdcs/{id}/followup', [PdcController::class, 'followupPdc'])->name('pdcs.followup')
        ->middleware(CheckPermission::class . ':create followups');

    // ======================== AGENT ROUTES ========================
    Route::prefix('salesman')->group(function () {
        Route::get('/', [AgentController::class, 'index'])->name('agents.index')
            ->middleware([CheckPermission::class . ':view agents', CheckDepartmentAccess::class]);

        Route::get('/create', [AgentController::class, 'create'])->name('agents.create')
            ->middleware([CheckPermission::class . ':create agents', CheckDepartmentAccess::class]);

        Route::post('/', [AgentController::class, 'store'])->name('agents.store')
            ->middleware([CheckPermission::class . ':create agents', CheckDepartmentAccess::class]);

        Route::get('/{id}', [AgentController::class, 'show'])->name('agents.show')
            ->middleware([CheckPermission::class . ':view agents', CheckDepartmentAccess::class]);

        Route::get('/{id}/edit', [AgentController::class, 'edit'])->name('agents.edit')
            ->middleware([CheckPermission::class . ':edit agents', CheckDepartmentAccess::class]);

        Route::put('/{id}', [AgentController::class, 'update'])->name('agents.update')
            ->middleware([CheckPermission::class . ':edit agents', CheckDepartmentAccess::class]);

        Route::delete('/{id}', [AgentController::class, 'destroy'])->name('agents.destroy')
            ->middleware([CheckPermission::class . ':delete agents', CheckDepartmentAccess::class]);
    });

    // ======================== INSTALLMENTS & PDCs ========================
    Route::get('/installments-pdcs', [InstallmentPdcController::class, 'index'])->name('installments_pdcs.index')
        ->middleware(CheckPermission::class . ':view installments');

    Route::prefix('installments')->group(function () {
        Route::get('/', [InstallmentPdcController::class, 'index'])->name('installments.index')
            ->middleware([CheckPermission::class . ':view installments', CheckDepartmentAccess::class]);

        Route::get('/create', [InstallmentPdcController::class, 'create'])->name('installments.create')
            ->middleware([CheckPermission::class . ':create installments', CheckDepartmentAccess::class]);

        Route::post('/', [InstallmentPdcController::class, 'store'])->name('installments.store')
            ->middleware([CheckPermission::class . ':create installments', CheckDepartmentAccess::class]);

        Route::get('/{id}', [InstallmentPdcController::class, 'show'])->name('installments.show')
            ->middleware([CheckPermission::class . ':view installments', CheckDepartmentAccess::class]);

        Route::get('/{id}/edit', [InstallmentPdcController::class, 'edit'])->name('installments.edit')
            ->middleware([CheckPermission::class . ':edit installments', CheckDepartmentAccess::class]);

        Route::put('/{id}', [InstallmentPdcController::class, 'update'])->name('installments.update')
            ->middleware([CheckPermission::class . ':edit installments', CheckDepartmentAccess::class]);

        Route::delete('/{id}', [InstallmentPdcController::class, 'destroy'])->name('installments.destroy')
            ->middleware([CheckPermission::class . ':delete installments', CheckDepartmentAccess::class]);
    });

    // ======================== NOTIFICATIONS ========================
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index')
        ->middleware(CheckPermission::class . ':view notifications');

    Route::post('/notifications/mark-viewed', [NotificationController::class, 'markNotificationAsViewed'])->name('notifications.read')
        ->middleware(CheckPermission::class . ':mark notifications read');

    Route::post('notifications/load-more', [NotificationController::class, 'loadMoreNotifications'])->name('notifications.load-more')
        ->middleware(CheckPermission::class . ':view notifications');

    // ======================== CHEQUE LOCATIONS ========================
    Route::prefix('cheque-locations')->group(function () {
        Route::get('/', [ChequeMenuController::class, 'index'])->name('cheque.locations.index')
            ->middleware(CheckPermission::class . ':view cheque locations');

        Route::get('/create', [ChequeMenuController::class, 'create'])->name('cheque.locations.create')
            ->middleware(CheckPermission::class . ':create cheque locations');

        Route::post('/', [ChequeMenuController::class, 'store'])->name('cheque.locations.store')
            ->middleware(CheckPermission::class . ':create cheque locations');

        Route::get('/{id}', [ChequeMenuController::class, 'show'])->name('cheque.locations.show')
            ->middleware(CheckPermission::class . ':view cheque locations');

        Route::get('/{id}/edit', [ChequeMenuController::class, 'edit'])->name('cheque.locations.edit')
            ->middleware(CheckPermission::class . ':edit cheque locations');

        Route::put('/{id}', [ChequeMenuController::class, 'update'])->name('cheque.locations.update')
            ->middleware(CheckPermission::class . ':edit cheque locations');

        Route::delete('/{id}', [ChequeMenuController::class, 'destroy'])->name('cheque.locations.destroy')
            ->middleware(CheckPermission::class . ':delete cheque locations');
    });

    // ======================== PERMISSION MANAGEMENT ========================
    Route::resource('permissions', PermissionController::class)
        ->middleware(CheckPermission::class . ':manage roles');
});

// ======================== LOGOUT + DASHBOARD REDIRECT ========================
Route::get('/logout', [UserController::class, 'logout'])->name('logout');

// REMOVE THIS CONFLICTING ROUTE - it's causing the issue
// Route::middleware(['auth'])->get('/dashboard', [UserController::class, 'redirectToDashboard'])->name('dashboard');
