<?php

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Auth::routes();

Route::get('/leads/', [App\Http\Controllers\LeadsController::class, 'indexOperator'])->name('leads.index');
Route::get('/leads/create', [App\Http\Controllers\LeadsController::class, 'create'])->name('leads.create');
Route::get('/leads/{lead}/edit', [App\Http\Controllers\LeadsController::class, 'edit'])->name('leads.edit');
Route::patch('/leads/{lead}', [App\Http\Controllers\LeadsController::class, 'update'])->name('leads.update');
Route::get('/leads/declined', [App\Http\Controllers\LeadsController::class, 'getDeclined'])->name('leads.declined');
Route::post('/leads', [App\Http\Controllers\LeadsController::class, 'store'])->name('leads.store');


Route::group(['middleware' => 'role:operator'], function () {
//    Route::get('/leads/', [App\Http\Controllers\LeadsController::class, 'indexOperator'])->name('leads.index');
//    Route::get('/leads/create', [App\Http\Controllers\LeadsController::class, 'create'])->name('leads.create');
//    Route::get('/leads/{lead}/edit', [App\Http\Controllers\LeadsController::class, 'edit'])->name('leads.edit');
//    Route::patch('/leads/{lead}', [App\Http\Controllers\LeadsController::class, 'update'])->name('leads.update');
//    Route::get('/leads/declined', [App\Http\Controllers\LeadsController::class, 'getDeclined'])->name('leads.declined');
//    Route::post('/leads', [App\Http\Controllers\LeadsController::class, 'store'])->name('leads.store');
    Route::get('/card/', [App\Http\Controllers\LeadsController::class, 'getLeadsByOperatorId'])->name('card.operator');
});

Route::group(['middleware' => 'role:manager'], function () {
    Route::get('/manager/leads/', [App\Http\Controllers\LeadsController::class, 'getManagerLeads'])->name('manager.leads');
    Route::patch('/manager/leads/{lead}', [App\Http\Controllers\LeadsController::class, 'changeLeadStatus'])->name('leads.status');
    Route::patch('/manager/leads/close/{lead}', [App\Http\Controllers\LeadsController::class, 'closeLeadMeeting'])->name('leads.close');
    Route::patch('/manager/leads/decline/{lead}', [App\Http\Controllers\LeadsController::class, 'declineLead'])->name('leads.decline');
    Route::get('/manager/card/', [App\Http\Controllers\LeadsController::class, 'managerCard'])->name('manager.card');
});

Route::group(['middleware' => 'role:coordinator'], function () {

    Route::get('/coordinator/', [App\Http\Controllers\CoordinatorController::class, 'controlTable'])->name('coordinator.managers');
    Route::get('/coordinator/leads/{lead}/edit', [App\Http\Controllers\LeadsController::class, 'edit'])->name('coordinator.leads.edit');
    Route::patch('/coordinator/leads/{lead}', [App\Http\Controllers\CoordinatorController::class, 'update'])->name('coordinator.leads.update');
    Route::patch('/coordinator/leads/decline/{lead}', [App\Http\Controllers\CoordinatorController::class, 'declineLead'])->name('coordinator.leads.decline');
    Route::patch('/coordinator/leads/{lead}/manage', [App\Http\Controllers\CoordinatorController::class, 'manageLead'])->name('coordinator.leads.manage');
    Route::get('/coordinator/manager/{manager}', [App\Http\Controllers\LeadsController::class, 'managerCard'])->name('coordinator.managercard');
    Route::patch('/coordinator/managers/{manager}/status', [\App\Http\Controllers\ManagerController::class, 'changeManagerStatus'])->name('coordinator.manager.status');
    Route::patch('/coordinator/manager/leads/{lead}', [App\Http\Controllers\LeadsController::class, 'changeLeadStatus'])->name('coordinator.manager.leads.status');
    Route::patch('/coordinator/manager/leads/close/{lead}', [App\Http\Controllers\LeadsController::class, 'closeLeadMeeting'])->name('coordinator.manager.leads.close');

});
