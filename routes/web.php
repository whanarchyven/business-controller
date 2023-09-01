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
    Route::patch('/coordinator/leads/{lead}/change', [App\Http\Controllers\CoordinatorController::class, 'changeManager'])->name('coordinator.leads.changemanager');
    Route::get('/coordinator/manager/{manager}', [App\Http\Controllers\LeadsController::class, 'managerCard'])->name('coordinator.managercard');
    Route::patch('/coordinator/managers/{manager}/status', [\App\Http\Controllers\ManagerController::class, 'changeManagerStatus'])->name('coordinator.manager.status');
    Route::patch('/coordinator/manager/leads/{lead}', [App\Http\Controllers\LeadsController::class, 'changeLeadStatus'])->name('coordinator.manager.leads.status');
    Route::patch('/coordinator/manager/leads/close/{lead}', [App\Http\Controllers\LeadsController::class, 'closeLeadMeeting'])->name('coordinator.manager.leads.close');

});


Route::get('/repairs/', [App\Http\Controllers\RepairsController::class, 'index'])->name('repairs.index');
Route::get('/repairs/{repair}', [App\Http\Controllers\RepairsController::class, 'edit'])->name('repairs.edit');
Route::patch('/repairs/{repair}', [App\Http\Controllers\RepairsController::class, 'update'])->name('repairs.update');
Route::patch('/repairs/{repair}/update', [App\Http\Controllers\RepairsController::class, 'editRepairViaLead'])->name('repairs.leads.update');


Route::group(['middleware' => 'role:director'], function () {
    Route::get('/director/', [App\Http\Controllers\DirectorController::class, 'controlTable'])->name('director.managers');
    Route::get('/director/leads/{lead}/edit', [App\Http\Controllers\LeadsController::class, 'edit'])->name('director.leads.edit');
    Route::patch('/director/leads/{lead}', [App\Http\Controllers\DirectorController::class, 'update'])->name('director.leads.update');
    Route::patch('/director/leads/decline/{lead}', [App\Http\Controllers\DirectorController::class, 'declineLead'])->name('director.leads.decline');
    Route::patch('/director/leads/{lead}/manage', [App\Http\Controllers\DirectorController::class, 'manageLead'])->name('director.leads.manage');
    Route::patch('/director/leads/{lead}/change', [App\Http\Controllers\DirectorController::class, 'changeManager'])->name('director.leads.changemanager');
    Route::get('/director/manager/{manager}', [App\Http\Controllers\LeadsController::class, 'managerCard'])->name('director.managercard');
    Route::patch('/director/managers/{manager}/status', [\App\Http\Controllers\ManagerController::class, 'changeManagerStatus'])->name('director.manager.status');
    Route::patch('/director/manager/leads/{lead}', [App\Http\Controllers\LeadsController::class, 'changeLeadStatus'])->name('director.manager.leads.status');
    Route::patch('/director/manager/leads/close/{lead}', [App\Http\Controllers\LeadsController::class, 'closeLeadMeeting'])->name('director.manager.leads.close');
    Route::patch('/director/plan/change', [App\Http\Controllers\DirectorController::class, 'changePlan'])->name('director.changeplan');
    Route::get('/director/daily', [App\Http\Controllers\DirectorController::class, 'daily'])->name('director.daily');

    Route::get('/director/leads/{lead}/accept', [App\Http\Controllers\DirectorController::class, 'acceptLeadView'])->name('director.leads.accept');
    Route::patch('/director/leads/{lead}/close', [App\Http\Controllers\DirectorController::class, 'closeLead'])->name('director.close.lead');
    Route::patch('/director/leads/{lead}/close/null', [App\Http\Controllers\DirectorController::class, 'closeLeadNull'])->name('director.leads.close.null');


    Route::get('/director/nomenclature', [App\Http\Controllers\DirectorController::class, 'nomenclature'])->name('director.nomenclature');
    Route::get('/director/nomenclature/add', [App\Http\Controllers\DirectorController::class, 'addNomenclature'])->name('director.add.nomenclature');
    Route::get('/director/nomenclature/{nomenclature}/edit', [App\Http\Controllers\DirectorController::class, 'editNomenclature'])->name('director.edit.nomenclature');
    Route::patch('/director/nomenclature/{nomenclature}/update', [App\Http\Controllers\DirectorController::class, 'updateNomenclature'])->name('director.update.nomenclature');
    Route::post('/director/nomenclature/store', [App\Http\Controllers\DirectorController::class, 'storeNomenclature'])->name('director.store.nomenclature');


    Route::get('/director/receipt/new', [App\Http\Controllers\DirectorController::class, 'receipt'])->name('director.receipt');
    Route::post('/director/receipt/store', [App\Http\Controllers\DirectorController::class, 'newReceipt'])->name('director.receipt.store');

    Route::get('/director/expenses/', [App\Http\Controllers\DirectorController::class, 'expense'])->name('director.expense');
    Route::get('/director/expenses/{repair}/new', [App\Http\Controllers\DirectorController::class, 'newExpense'])->name('director.expense.new');
    Route::post('/director/expense/{repair}/store', [App\Http\Controllers\DirectorController::class, 'expenseStore'])->name('director.expense.store');

    Route::get('/director/employers/', [App\Http\Controllers\DirectorController::class, 'newUserView'])->name('director.employers.new');
    Route::post('/director/employers/', [App\Http\Controllers\DirectorController::class, 'storeNewUser'])->name('director.employers.store');


});
