<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EngineerLogController;
use App\Http\Controllers\PubaliController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminLogController;
use App\Http\Controllers\EblController;
use App\Http\Controllers\CblController;
use App\Http\Controllers\IbblController;
use App\Http\Controllers\MtbController;
use App\Http\Controllers\AdvanceController;
use App\Http\Controllers\ServiceReportController;

Route::middleware('auth')->group(function () {

    // convence routes (accessible by engineers)
    Route::get('/convencecheck', [EngineerLogController::class, 'index'])->name('convencecheck');

    Route::get('/engineer/logs/create', [EngineerLogController::class,'create'])->name('engineer_logs.create');
    Route::post('/engineer/logs/store', [EngineerLogController::class,'store'])->name('engineer_logs.store');
    // Report Routes (accessible by engineers)
    Route::get('/reports', [ReportController::class, 'index'])->name('report.index');
    Route::delete('/reports/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');
    Route::post('/reports/{id}/update', [ReportController::class,'update'])->name('report.update');
    Route::get('/dashboard', [ReportController::class, 'index'])->name('report.index');
   
   
    // Month-wise view (accessible by all authenticated users)
    Route::get('/engineer/logs/month/{engineer}/{month}', [EngineerLogController::class, 'monthView'])->name('engineer_logs.month_view');
    Route::middleware(['role:admin,checker,verify'])->group(function () {
    // Month-wise actions

    Route::post('/engineer/logs/bulk-approve/{engineer}/{month}', [EngineerLogController::class, 'bulkApprove'])->name('engineer_logs.bulk_approve');
        Route::post('/engineer/logs/bulk-verify/{engineer}/{month}', [EngineerLogController::class, 'bulkVerify'])->name('engineer_logs.bulk_verify');
        Route::post('/engineer/logs/bulk-complete/{engineer}/{month}', [EngineerLogController::class, 'bulkComplete'])->name('engineer_logs.bulk_complete');
        Route::delete('/engineer/logs/month-delete/{engineer}/{month}', [EngineerLogController::class, 'monthDelete'])->name('engineer_logs.month_delete');
        Route::get('/engineer/logs/export-excel/{engineer}/{month}', [EngineerLogController::class, 'exportMonthExcel'])->name('engineer_logs.export_month');
        Route::get('/engineer/logs/export-pdf/{engineer}/{month}', [EngineerLogController::class, 'exportMonthPdf'])->name('engineer_logs.export_month_pdf');
        Route::post('/engineer/logs/update-entry', [EngineerLogController::class, 'updateEntry'])->name('engineer_logs.update_entry');
         Route::get('/advances/list-by-date', [AdvanceController::class, 'indexByDate'])->name('advances.list_by_date');
    });



    Route::middleware('restrict.engineer')->group(function () {
       

        // Pubali Routes
        Route::get('/pubali', [PubaliController::class, 'index'])->name('pubali.index');
        Route::post('/pubali', [PubaliController::class, 'store'])->name('pubali.store');
        Route::put('/pubali/{id}', [PubaliController::class, 'update'])->name('pubali.update');
        Route::delete('/pubali/{id}', [PubaliController::class, 'destroy'])->name('pubali.destroy');
        Route::post('/pubali/{id}/assign', [PubaliController::class, 'assign'])->name('pubali.assign');
        Route::post('/pubali/import', [PubaliController::class, 'import'])->name('pubali.import');

        // Mtb Routes
        Route::get('/mtb', [MtbController::class, 'index'])->name('mtb.index');
        Route::post('/mtb', [MtbController::class, 'store'])->name('mtb.store');
        Route::put('/mtb/{id}', [MtbController::class, 'update'])->name('mtb.update');
        Route::delete('/mtb/{id}', [MtbController::class, 'destroy'])->name('mtb.destroy');
        Route::post('/mtb/{id}/assign', [MtbController::class, 'assign'])->name('mtb.assign');
        Route::post('/mtb/import', [MtbController::class, 'import'])->name('mtb.import');

        // Ebl Routes
        Route::get('/ebl', [EblController::class, 'index'])->name('ebl.index');
        Route::post('/ebl', [EblController::class, 'store'])->name('ebl.store');
        Route::put('/ebl/{id}', [EblController::class, 'update'])->name('ebl.update');
        Route::delete('/ebl/{id}', [EblController::class, 'destroy'])->name('ebl.destroy');
        Route::post('/ebl/{id}/assign', [EblController::class, 'assign'])->name('ebl.assign');
        Route::post('/ebl/import', [EblController::class, 'import'])->name('ebl.import');

        // Ibbl Routes
        Route::get('/ibbl', [IbblController::class, 'index'])->name('ibbl.index');
        Route::post('/ibbl', [IbblController::class, 'store'])->name('ibbl.store');
        Route::put('/ibbl/{id}', [IbblController::class, 'update'])->name('ibbl.update');
        Route::delete('/ibbl/{id}', [IbblController::class, 'destroy'])->name('ibbl.destroy');
        Route::post('/ibbl/{id}/assign', [IbblController::class, 'assign'])->name('ibbl.assign');
        Route::post('/ibbl/import', [IbblController::class, 'import'])->name('ibbl.import');

        // Cbl Routes
        Route::get('/cbl', [CblController::class, 'index'])->name('cbl.index');
        Route::post('/cbl', [CblController::class, 'store'])->name('cbl.store');
        Route::put('/cbl/{id}', [CblController::class, 'update'])->name('cbl.update');
        Route::delete('/cbl/{id}', [CblController::class, 'destroy'])->name('cbl.destroy');
        Route::post('/cbl/{id}/assign', [CblController::class, 'assign'])->name('cbl.assign');
        Route::post('/cbl/import', [CblController::class, 'import'])->name('cbl.import');

        //convence admin routes
       

        // View in "Excel-style" table
        Route::get('/admin/logs/{id}', [AdminLogController::class, 'view'])->name('admin.logs.view');

        // Change status
        Route::post('/admin/logs/{id}/status', [AdminLogController::class, 'changeStatus'])->name('admin.logs.status');

        // Delete
        Route::delete('/admin/logs/{id}', [AdminLogController::class, 'delete'])->name('admin.logs.delete');

        Route::get('/admin/logs', [AdminLogController::class, 'index'])->name('admin.logs.index');
        Route::get('/admin/logs/{id}', [AdminLogController::class, 'viwe'])->name('admin.logs.viwe');
        Route::get('/admin/logs/{id}/export-view', [AdminLogController::class, 'exportView'])->name('admin.logs.exportView');
        Route::post('/admin/logs/update-cell', [AdminLogController::class, 'updateCell'])->name('admin.logs.updateCell');
        // optional download
        Route::get('/admin/logs/{id}/download', [AdminLogController::class, 'downloadExcel'])->name('admin.logs.download');

      
    });
});

Route::middleware('auth')->group(function () {
    // Additional auth routes if needed

      // Advance Routes
        Route::get('advances', [AdvanceController::class, 'index'])->name('advances.index');
        Route::get('advances/create', [AdvanceController::class, 'create'])->name('advances.create');
        Route::post('advances', [AdvanceController::class, 'store'])->name('advances.store');
        Route::get('/advances/list-by-name', [AdvanceController::class, 'indexByName'])->name('advances.list_by_name');
        // Route::get('/advances/list-by-date', [AdvanceController::class, 'indexByDate'])->name('advances.list_by_date');
        Route::post('/advances/{id}/update-status', [AdvanceController::class, 'updateStatus'])->middleware('role:admin,checker,verify')->name('advances.update_status');

   // Service Report Routes
   Route::get('/service-report', [ServiceReportController::class, 'index'])->name('service-reports.index');
   Route::get('/service-report/create', [ServiceReportController::class, 'create'])->name('service-reports.create');
   Route::post('/service-report', [ServiceReportController::class, 'store'])->name('service-reports.store');
   Route::get('/service-report/{id}', [ServiceReportController::class, 'show'])->name('service-reports.show');
   Route::put('/service-report/{id}', [ServiceReportController::class, 'update'])->name('service-reports.update');
   Route::delete('/service-report/{id}', [ServiceReportController::class, 'destroy'])->name('service-reports.destroy');
});




Auth::routes();

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect('/dashboard');
        } elseif ($user->isPblManager()) {
            return redirect('/pubali');
        } elseif ($user->isMtbManager()) {
            return redirect('/mtb');
        } elseif ($user->isEblManager()) {
            return redirect('/ebl');
        } elseif ($user->isIbblManager()) {
            return redirect('/ibbl');
        } elseif ($user->isCtManager()) {
            return redirect('/cbl');
        } else {
            return redirect('/convencecheck');
        }
    }
    return redirect('/login');
})->name('home');
