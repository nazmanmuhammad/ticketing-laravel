<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Ticket\TicketList;
use App\Livewire\Ticket\TicketCreate;
use App\Livewire\Ticket\TicketDetail;
use App\Livewire\AccessRequest\AccessRequestList;
use App\Livewire\AccessRequest\AccessRequestCreate;
use App\Livewire\AccessRequest\AccessRequestDetail;
use App\Livewire\AccessRequest\PendingApprovals as AccessPendingApprovals;
use App\Livewire\ChangeRequest\ChangeRequestList;
use App\Livewire\ChangeRequest\ChangeRequestCreate;
use App\Livewire\ChangeRequest\ChangeRequestDetail;
use App\Livewire\ChangeRequest\ChangeCalendar;
use App\Livewire\Role\RoleList;
use App\Livewire\Role\RoleForm;
use App\Livewire\User\UserList;
use App\Livewire\User\UserForm;
use App\Livewire\User\UserProfile;
use App\Livewire\Report\ReportDashboard;
use App\Livewire\Report\TicketReport;
use App\Livewire\Report\AccessReport;
use App\Livewire\Report\ChangeReport;
use App\Livewire\Settings\CategorySettings;
use App\Livewire\Settings\SystemSettings;
use App\Livewire\Settings\SlaSettings;
use App\Livewire\Settings\WorkflowSettings;
use App\Livewire\Settings\CannedResponseSettings;
use App\Livewire\Task\TaskList;
use App\Livewire\Settings\TeamSettings;
use App\Livewire\Settings\EmailSettings;
use App\Livewire\Settings\GeneralSettings;
use App\Http\Controllers\AuthController;

// Landing page
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('landing');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', UserProfile::class)->name('profile');

    // Tasks
    Route::get('/tasks', TaskList::class)->name('tasks.index');

    // Tickets
    Route::prefix('tickets')->group(function () {
        Route::get('/', TicketList::class)->name('tickets.index')->middleware('can:tickets.view');
        Route::get('/create', TicketCreate::class)->name('tickets.create')->middleware('can:tickets.create');
        Route::get('/{ticket}', TicketDetail::class)->name('tickets.show')->middleware('can:tickets.view');
    });

    // Access Requests
    Route::prefix('access-requests')->group(function () {
        Route::get('/', AccessRequestList::class)->name('access-requests.index')->middleware('can:access_requests.view');
        Route::get('/create', AccessRequestCreate::class)->name('access-requests.create')->middleware('can:access_requests.create');
        Route::get('/pending', AccessPendingApprovals::class)->name('access-requests.pending')->middleware('can:access_requests.approve');
        Route::get('/{accessRequest}/edit', AccessRequestCreate::class)->name('access-requests.edit')->middleware('can:access_requests.create');
        Route::get('/{accessRequest}', AccessRequestDetail::class)->name('access-requests.show')->middleware('can:access_requests.view');
    });

    // Change Requests
    Route::prefix('change-requests')->group(function () {
        Route::get('/', ChangeRequestList::class)->name('change-requests.index')->middleware('can:change_requests.view');
        Route::get('/create', ChangeRequestCreate::class)->name('change-requests.create')->middleware('can:change_requests.create');
        Route::get('/calendar', ChangeCalendar::class)->name('change-requests.calendar')->middleware('can:change_requests.view');
        Route::get('/{changeRequest}/edit', ChangeRequestCreate::class)->name('change-requests.edit')->middleware('can:change_requests.create');
        Route::get('/{changeRequest}', ChangeRequestDetail::class)->name('change-requests.show')->middleware('can:change_requests.view');
    });

    // Roles & Permissions
    Route::prefix('roles')->group(function () {
        Route::get('/', RoleList::class)->name('roles.index')->middleware('can:roles.view');
        Route::get('/create', RoleForm::class)->name('roles.create')->middleware('can:roles.create');
        Route::get('/{role}/edit', RoleForm::class)->name('roles.edit')->middleware('can:roles.edit');
    });

    // Users
    Route::prefix('users')->group(function () {
        Route::get('/', UserList::class)->name('users.index')->middleware('can:users.view');
        Route::get('/create', UserForm::class)->name('users.create')->middleware('can:users.create');
        Route::get('/{user}/edit', UserForm::class)->name('users.edit')->middleware('can:users.edit');
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/', ReportDashboard::class)->name('reports.dashboard')->middleware('can:reports.view');
        Route::get('/tickets', TicketReport::class)->name('reports.tickets')->middleware('can:reports.view');
        Route::get('/access', AccessReport::class)->name('reports.access')->middleware('can:reports.view');
        Route::get('/changes', ChangeReport::class)->name('reports.changes')->middleware('can:reports.view');
    });

    // Settings
    Route::prefix('settings')->middleware('can:settings.view')->group(function () {
        Route::get('/categories', CategorySettings::class)->name('settings.categories');
        Route::get('/systems', SystemSettings::class)->name('settings.systems');
        Route::get('/sla', SlaSettings::class)->name('settings.sla');
        Route::get('/workflows', WorkflowSettings::class)->name('settings.workflows');
        Route::get('/canned-responses', CannedResponseSettings::class)->name('settings.canned-responses');
        Route::get('/teams', TeamSettings::class)->name('settings.teams');
        Route::get('/email', EmailSettings::class)->name('settings.email');
        Route::get('/general', GeneralSettings::class)->name('settings.general');
    });
});
