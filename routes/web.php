<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResiController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

// Public Blog API
Route::get('/api/posts', [PostController::class, 'apiIndex'])->name('api.posts.index');
Route::get('/api/posts/{slug}', [PostController::class, 'apiShow'])->name('api.posts.show');

// Public tracking API
Route::get('/track/{resiNum}', [ResiController::class, 'track'])->name('resi.track.api');

// Auth routes
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated routes
Route::middleware(['auth.bja'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Clients — search must be before resource to avoid {client} matching "search"
    Route::get('clients/search', [ClientController::class, 'search'])->name('clients.search');
    Route::resource('clients', ClientController::class);

    // Invoices
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'printView'])->name('invoices.print');
    Route::resource('invoices', InvoiceController::class);

    // Quotations
    Route::post('quotations/preview-temp', [QuotationController::class, 'previewTemp'])->name('quotations.preview-temp');
    Route::get('quotations/{quotation}/print', [QuotationController::class, 'printView'])->name('quotations.print');
    Route::resource('quotations', QuotationController::class);

    // Leads — extra routes before resource
    Route::post('leads/bulk-delete', [LeadController::class, 'bulkDestroy'])->name('leads.bulk-destroy');
    Route::post('leads/delete-all', [LeadController::class, 'destroyAll'])->name('leads.destroy-all');
    Route::post('leads/import', [LeadController::class, 'importCsv'])->name('leads.import');
    Route::get('leads/template', [LeadController::class, 'downloadTemplate'])->name('leads.template');
    Route::get('leads/stats', [LeadController::class, 'stats'])->name('leads.stats');
    Route::resource('leads', LeadController::class);

    // Blog
    Route::resource('posts', PostController::class);

    // Tracking Resi
    Route::post('resi/{resi}/add-status', [ResiController::class, 'addStatus'])->name('resi.add-status');
    Route::resource('resi', ResiController::class)->except(['destroy']);

    // Reports
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/sales/export', [ReportController::class, 'exportCsv'])->name('reports.sales.export');
    Route::post('reports/sales/target', [ReportController::class, 'saveTarget'])->name('reports.sales.target')->middleware('admin.only');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile');
    Route::post('settings/bank', [SettingController::class, 'updateBank'])->name('settings.bank');
    Route::post('settings/revenue', [SettingController::class, 'updateRevenue'])->name('settings.revenue');
    Route::post('settings/logo', [SettingController::class, 'uploadLogo'])->name('settings.logo');

    // Users — admin only
    Route::middleware(['admin.only'])->group(function () {
        Route::resource('users', UserController::class);
    });
});
