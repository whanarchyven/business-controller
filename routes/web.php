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
    Route::get('/card/{user}/', [App\Http\Controllers\LeadsController::class, 'getLeadsByOperatorId'])->name('card.operator');
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
    Route::patch('/coordinator/leads/decline/{lead}', [App\Http\Controllers\DirectorController::class, 'declineLead'])->name('coordinator.leads.decline');
    Route::patch('/coordinator/leads/{lead}/manage', [App\Http\Controllers\DirectorController::class, 'manageLead'])->name('coordinator.leads.manage');
    Route::patch('/coordinator/leads/{lead}/change', [App\Http\Controllers\DirectorController::class, 'changeManager'])->name('coordinator.leads.changemanager');
    Route::get('/coordinator/manager/{manager}', [App\Http\Controllers\LeadsController::class, 'managerCard'])->name('coordinator.managercard');
    Route::patch('/coordinator/managers/{manager}/status', [\App\Http\Controllers\ManagerController::class, 'changeManagerStatus'])->name('coordinator.manager.status');
    Route::patch('/coordinator/manager/leads/{lead}', [App\Http\Controllers\LeadsController::class, 'changeLeadStatus'])->name('coordinator.manager.leads.status');
    Route::patch('/coordinator/manager/leads/close/{lead}', [App\Http\Controllers\LeadsController::class, 'closeLeadMeeting'])->name('coordinator.manager.leads.close');
    Route::get('/coordinator/manager/{manager}/operative', [App\Http\Controllers\LeadsController::class, 'managerOperative'])->name('coordinator.manager.operative');
    Route::get('/coordinator/changecity/{city}', [App\Http\Controllers\CoordinatorController::class, 'changeCity'])->name('coordinator.city.change');

});


Route::get('/repairs/', [App\Http\Controllers\RepairsController::class, 'index'])->name('repairs.index');
Route::get('/repairs/search/', [App\Http\Controllers\RepairsController::class, 'search'])->name('repairs.search');

Route::post('/repairs/search/', [App\Http\Controllers\RepairsController::class, 'doSearch'])->name('repairs.do.search');

Route::get('/repairs/{repair}', [App\Http\Controllers\RepairsController::class, 'edit'])->name('repairs.edit');
Route::patch('/repairs/{repair}', [App\Http\Controllers\RepairsController::class, 'update'])->name('repairs.update');
Route::patch('/repairs/{repair}/update', [App\Http\Controllers\RepairsController::class, 'editRepairViaLead'])->name('repairs.leads.update');
Route::post('/repairs/{repair}/duplicate', [App\Http\Controllers\RepairsController::class, 'duplicate'])->name('repairs.duplicate');


Route::get('/card/manager/{manager}', [App\Http\Controllers\LeadsController::class, 'managerCard'])->name('director.managercard');
Route::get('/card/operator/{user}', [App\Http\Controllers\LeadsController::class, 'getLeadsByOperatorId'])->name('director.operatorcard');
Route::get('/card/master/{master}', [App\Http\Controllers\RepairsController::class, 'masterCard'])->name('director.mastercard');
Route::get('/card/director/{director}', [App\Http\Controllers\DirectorController::class, 'directorCard'])->name('director.directorcard');

Route::patch('/director/leads/{lead}/sendPhone', [App\Http\Controllers\DirectorController::class, 'sendPhone'])->name('director.leads.sendPhone');
Route::patch('/director/leads/{lead}/sendAddress', [App\Http\Controllers\DirectorController::class, 'sendAddress'])->name('director.leads.sendAddress');
Route::patch('/director/leads/{lead}/change', [App\Http\Controllers\DirectorController::class, 'changeManager'])->name('director.leads.changemanager');
Route::patch('/director/leads/decline/{lead}', [App\Http\Controllers\DirectorController::class, 'declineLead'])->name('director.leads.decline');
Route::group(['middleware' => 'role:director'], function () {
    Route::get('/director/', [App\Http\Controllers\DirectorController::class, 'controlTable'])->name('director.managers');
    Route::get('/director/leads/{lead}/edit', [App\Http\Controllers\LeadsController::class, 'edit'])->name('director.leads.edit');
    Route::patch('/director/leads/{lead}', [App\Http\Controllers\DirectorController::class, 'update'])->name('director.leads.update');
    Route::patch('/director/leads/{lead}/manage', [App\Http\Controllers\DirectorController::class, 'manageLead'])->name('director.leads.manage');



    Route::post('/director/director/{director}/addworkday', [App\Http\Controllers\DirectorController::class, 'addWorkDay'])->name('director.add.workday');
    Route::delete('/director/director/{director}/removeworkday', [App\Http\Controllers\DirectorController::class, 'removeWorkDay'])->name('director.delete.workday');

    Route::patch('/director/managers/{manager}/status', [\App\Http\Controllers\ManagerController::class, 'changeManagerStatus'])->name('director.manager.status');
    Route::patch('/director/manager/leads/{lead}', [App\Http\Controllers\LeadsController::class, 'changeLeadStatus'])->name('director.manager.leads.status');
    Route::patch('/director/manager/leads/close/{lead}', [App\Http\Controllers\LeadsController::class, 'closeLeadMeeting'])->name('director.manager.leads.close');
    Route::get('/director/manager/{manager}/operative', [App\Http\Controllers\LeadsController::class, 'managerOperative'])->name('director.manager.operative');


    Route::patch('/director/plan/change', [App\Http\Controllers\DirectorController::class, 'changePlan'])->name('director.changeplan');
    Route::get('/director/daily', [App\Http\Controllers\DirectorController::class, 'daily'])->name('director.daily');

    Route::get('/director/leads/{lead}/accept', [App\Http\Controllers\DirectorController::class, 'acceptLeadView'])->name('director.leads.accept');
    Route::patch('/director/leads/{lead}/close', [App\Http\Controllers\DirectorController::class, 'closeLead'])->name('director.close.lead');
    Route::patch('/director/leads/{lead}/close/null', [App\Http\Controllers\DirectorController::class, 'closeLeadNull'])->name('director.leads.close.null');
    Route::patch('/director/leads/{lead}/delete', [App\Http\Controllers\LeadsController::class, 'deleteLead'])->name('director.lead.delete');


    Route::get('/director/nomenclature', [App\Http\Controllers\DirectorController::class, 'nomenclature'])->name('director.nomenclature');
    Route::get('/director/nomenclature/add', [App\Http\Controllers\DirectorController::class, 'addNomenclature'])->name('director.add.nomenclature');
    Route::get('/director/nomenclature/{nomenclature}/edit', [App\Http\Controllers\DirectorController::class, 'editNomenclature'])->name('director.edit.nomenclature');
    Route::patch('/director/nomenclature/{nomenclature}/update', [App\Http\Controllers\DirectorController::class, 'updateNomenclature'])->name('director.update.nomenclature');
    Route::post('/director/nomenclature/store', [App\Http\Controllers\DirectorController::class, 'storeNomenclature'])->name('director.store.nomenclature');


    Route::get('/director/receipt/new', [App\Http\Controllers\DirectorController::class, 'receipt'])->name('director.receipt');
    Route::post('/director/receipt/store', [App\Http\Controllers\DirectorController::class, 'newReceipt'])->name('director.receipt.store');

    Route::get('/director/expenses/', [App\Http\Controllers\RepairsController::class, 'expenseMaterialShow'])->name('director.expense');
    Route::get('/director/expenses/{repair}/new', [App\Http\Controllers\DirectorController::class, 'newExpense'])->name('director.expense.new');
    Route::get('/director/expenses/{repair}/decline', [App\Http\Controllers\DirectorController::class, 'declineExpense'])->name('director.expense.decline');
    Route::post('/director/expense/{repair}/store', [App\Http\Controllers\DirectorController::class, 'expenseStore'])->name('director.expense.store');


    Route::get('/director/employers/managers', [App\Http\Controllers\DirectorController::class, 'managersView'])->name('director.employers.managers');
    Route::get('/director/employers/operator', [App\Http\Controllers\DirectorController::class, 'operatorsView'])->name('director.employers.operators');
    Route::get('/director/employers/coordinator', [App\Http\Controllers\DirectorController::class, 'coordinatorsView'])->name('director.employers.coordinators');
    Route::get('/director/employers/masters', [App\Http\Controllers\DirectorController::class, 'mastersView'])->name('director.employers.masters');
    Route::get('/director/employers/directors', [App\Http\Controllers\DirectorController::class, 'directorsView'])->name('director.employers.directors');

    Route::get('/director/employers/new', [App\Http\Controllers\DirectorController::class, 'newUserView'])->name('director.employers.new');
    Route::get('/director/employers/{user}/edit', [App\Http\Controllers\DirectorController::class, 'updateUserView'])->name('director.employers.edit');
    Route::patch('/director/employers/{user}', [App\Http\Controllers\DirectorController::class, 'updateUser'])->name('director.employers.update');
    Route::delete('/director/employers/{user}', [App\Http\Controllers\DirectorController::class, 'deleteUser'])->name('director.employers.delete');

    Route::post('/director/employers/{user}/restore', [App\Http\Controllers\DirectorController::class, 'restoreUser'])->name('director.employers.restore');

    Route::post('/director/employers/store', [App\Http\Controllers\DirectorController::class, 'storeNewUser'])->name('director.employers.store');

    Route::get('/director/changecity/{city}', [App\Http\Controllers\DirectorController::class, 'changeCity'])->name('admin.city.change');

    Route::get('/director/getcity/', [App\Http\Controllers\DirectorController::class, 'getCity'])->name('admin.city.get');

    Route::get('/director/transactions/', [App\Http\Controllers\DirectorController::class, 'getTransactionsView'])->name('director.transactions');

    Route::get('/director/transactions/mainoffice', [App\Http\Controllers\DirectorController::class, 'getMainOffice'])->name('director.transactions.mainoffice');

    Route::get('/director/transactions/search', [App\Http\Controllers\DirectorController::class, 'searchTransactions'])->name('director.transactions.search');
    Route::post('/director/transactions/search', [App\Http\Controllers\DirectorController::class, 'doSearchTransaction'])->name('director.transactions.do.search');

    Route::get('/director/transactions/new', [App\Http\Controllers\TransactionController::class, 'newTransactionView'])->name('director.transactions.new');
    Route::post('/director/transactions/store', [App\Http\Controllers\TransactionController::class, 'storeNewTransaction'])->name('director.transactions.store');
    Route::get('/director/transactions/{transaction}', [App\Http\Controllers\DirectorController::class, 'showTransactionDocs'])->name('director.transactions.docs');


    Route::get('/director/bonuses', [App\Http\Controllers\BonusController::class, 'index'])->name('director.bonuses');
    Route::get('/director/deductions', [App\Http\Controllers\BonusController::class, 'deduction'])->name('director.deductions');


    Route::post('/director/bonuses/{user}/store', [App\Http\Controllers\BonusController::class, 'createBonus'])->name('director.bonuses.create');


    Route::patch('/director/bonuses/{bonus}/pay', [App\Http\Controllers\BonusController::class, 'payBonus'])->name('director.bonuses.pay');
    Route::delete('/director/bonuses/{bonus}/delete', [App\Http\Controllers\BonusController::class, 'deleteBonus'])->name('director.bonus.delete');
    Route::delete('/director/deductions/{bonus}/delete', [App\Http\Controllers\BonusController::class, 'deleteDeduction'])->name('director.deductions.delete');


    Route::get('/salary/{user}', [App\Http\Controllers\SalaryController::class, 'getMasterSalary'])->name('director.salary');

    Route::get('/avance/week/', [App\Http\Controllers\DirectorController::class, 'avanceView'])->name('director.avance.week');

    Route::get('/avance/week/operator', [App\Http\Controllers\DirectorController::class, 'avanceOperatorView'])->name('director.avance.operator');

    Route::get('/avance/month/', [App\Http\Controllers\DirectorController::class, 'avanceMonthView'])->name('director.avance.month');
    Route::post('/avance/week/pay', [App\Http\Controllers\DirectorController::class, 'payAvance'])->name('director.avance.pay');

    Route::get('/salary/avance/pay', [App\Http\Controllers\DirectorController::class, 'salaryView'])->name('director.salary.pay');

    Route::get('/salary/avance/operator/pay', [App\Http\Controllers\DirectorController::class, 'operatorSalaryView'])->name('director.salary.pay.operator');

    Route::patch('/salary/avance/pay/{user}/', [App\Http\Controllers\DirectorController::class, 'paySalary'])->name('director.salary.payall');


    Route::get('/director/statistic/sells', [App\Http\Controllers\DirectorController::class, 'getSellsView'])->name('director.statistic.sells');
    Route::get('/director/statistic/posygramm', [App\Http\Controllers\DirectorController::class, 'posyGramm'])->name('director.statistic.posygramm');
    Route::get('/director/statistic/posygramm/cities', [App\Http\Controllers\DirectorController::class, 'posyCitites'])->name('director.statistic.posygramm.cities');


    Route::get('/director/gsm/managers', [App\Http\Controllers\GsmController::class, 'indexGsm'])->name('director.gsm.show.managers');
    Route::get('/director/gsm/masters', [App\Http\Controllers\GsmController::class, 'indexGsmMaster'])->name('director.gsm.show.masters');
    Route::post('/director/gsm',[App\Http\Controllers\GsmController::class,'createGsm'])->name('director.gsm.add');
    Route::patch('/director/gsm/{gsm}/pay',[App\Http\Controllers\GsmController::class,'payGsm'])->name('director.gsm.pay');

    Route::post('/director/gsm/payall',[App\Http\Controllers\GsmController::class,'payAllGsm'])->name('director.gsm.payall');


});
