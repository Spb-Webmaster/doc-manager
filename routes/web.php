<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Axios\AxiosController;
use App\Http\Controllers\Cabinet\CabinetController;
use App\Http\Controllers\Cabinet\ContractsController;
use App\Http\Controllers\Cabinet\ContractorsController;
use App\Http\Controllers\Cabinet\ActPdfController;
use App\Http\Controllers\Cabinet\ActsController;
use App\Http\Controllers\Cabinet\InvoicePdfController;
use App\Http\Controllers\Cabinet\InvoicesController;
use App\Http\Controllers\Cabinet\SmartInvoicesController;
use App\Http\Controllers\Cabinet\SettingsController;
use App\Http\Controllers\DaData\DaDataController;
use App\Http\Controllers\FancyBox\FancyBoxController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/** Главная **/
Route::get('/', [HomeController::class, 'index'])->name('home');
/** ///Главная **/


/** Кабинет **/
Route::middleware('auth')->group(function () {
    Route::get('/cabinet', [CabinetController::class, 'index'])->name('cabinet');
    Route::get('/cabinet/settings', [SettingsController::class, 'index'])->name('cabinet.settings');
    Route::post('/cabinet/requisites', [SettingsController::class, 'saveRequisites'])->name('cabinet.requisites.save');
    Route::post('/cabinet/bank-accounts', [SettingsController::class, 'storeBankAccount'])->name('cabinet.bank-accounts.store');
    Route::delete('/cabinet/bank-accounts/{bankAccount}', [SettingsController::class, 'destroyBankAccount'])->name('cabinet.bank-accounts.destroy');
    Route::post('/cabinet/bank-accounts/reorder', [SettingsController::class, 'reorderBankAccounts'])->name('cabinet.bank-accounts.reorder');
    Route::post('/cabinet/profile', [SettingsController::class, 'saveProfile'])->name('cabinet.profile.save');
    Route::post('/cabinet/notifications', [SettingsController::class, 'saveNotifications'])->name('cabinet.notifications.save');
    Route::post('/cabinet/password', [SettingsController::class, 'changePassword'])->name('cabinet.password.change');
    Route::post('/cabinet/account/delete', [SettingsController::class, 'markAccountDelete'])->name('cabinet.account.delete');
    Route::get('/cabinet/invoices', [InvoicesController::class, 'index'])->name('cabinet.invoices');
    Route::get('/cabinet/invoices/create', [InvoicesController::class, 'create'])->name('cabinet.invoices.create');
    Route::get('/cabinet/invoices/next-number', [InvoicesController::class, 'nextNumber'])->name('cabinet.invoices.next-number');
    Route::get('/cabinet/invoices/{invoice}', [InvoicesController::class, 'show'])->name('cabinet.invoices.show');
    Route::post('/cabinet/invoices', [InvoicesController::class, 'store'])->name('cabinet.invoices.store');
    Route::post('/cabinet/invoices/bulk-delete', [InvoicesController::class, 'bulkDestroy'])->name('cabinet.invoices.bulk-delete');
    Route::delete('/cabinet/invoices/{invoice}', [InvoicesController::class, 'destroy'])->name('cabinet.invoices.destroy');
    Route::get('/cabinet/invoices/{invoice}/pdf', [InvoicePdfController::class, 'download'])->name('cabinet.invoices.pdf');
    Route::get('/cabinet/templates', [SmartInvoicesController::class, 'index'])->name('cabinet.templates');
    Route::get('/cabinet/templates/{smartInvoice}', [SmartInvoicesController::class, 'showTemplate'])->name('cabinet.templates.show');
    Route::patch('/cabinet/templates/{smartInvoice}/toggle', [SmartInvoicesController::class, 'toggleActive'])->name('cabinet.templates.toggle');
    Route::delete('/cabinet/templates/{smartInvoice}', [SmartInvoicesController::class, 'destroyTemplate'])->name('cabinet.templates.destroy');
    Route::post('/cabinet/smart-invoices', [SmartInvoicesController::class, 'store'])->name('cabinet.smart-invoices.store');
    Route::get('/cabinet/acts', [ActsController::class, 'index'])->name('cabinet.acts');
    Route::get('/cabinet/acts/create', [ActsController::class, 'create'])->name('cabinet.acts.create');
    Route::get('/cabinet/acts/next-number', [ActsController::class, 'nextNumber'])->name('cabinet.acts.next-number');
    Route::get('/cabinet/acts/{act}', [ActsController::class, 'show'])->name('cabinet.acts.show');
    Route::post('/cabinet/acts', [ActsController::class, 'store'])->name('cabinet.acts.store');
    Route::post('/cabinet/acts/bulk-delete', [ActsController::class, 'bulkDestroy'])->name('cabinet.acts.bulk-delete');
    Route::delete('/cabinet/acts/{act}', [ActsController::class, 'destroy'])->name('cabinet.acts.destroy');
    Route::get('/cabinet/acts/{act}/pdf', [ActPdfController::class, 'download'])->name('cabinet.acts.pdf');
    Route::get('/cabinet/contractors', [ContractorsController::class, 'index'])->name('cabinet.contractors');
    Route::get('/cabinet/contractors/create', [ContractorsController::class, 'create'])->name('cabinet.contractors.create');
    Route::post('/cabinet/contractors', [ContractorsController::class, 'store'])->name('cabinet.contractors.store');
    Route::patch('/cabinet/contractors/{contractor}', [ContractorsController::class, 'update'])->name('cabinet.contractors.update');
    Route::delete('/cabinet/contractors/{contractor}', [ContractorsController::class, 'destroy'])->name('cabinet.contractors.destroy');
    Route::get('/cabinet/contractors/{contractor}/invoices', [ContractorsController::class, 'invoices'])->name('cabinet.contractor.invoices');
    Route::get('/cabinet/contractors/{contractor}/acts', [ContractorsController::class, 'acts'])->name('cabinet.contractor.acts');
    Route::get('/cabinet/contractors/{contractor}/contracts', [ContractsController::class, 'index'])->name('cabinet.contracts.index');
    Route::post('/cabinet/contractors/{contractor}/contracts', [ContractsController::class, 'store'])->name('cabinet.contracts.store');
    Route::patch('/cabinet/contracts/{contract}', [ContractsController::class, 'update'])->name('cabinet.contracts.update');
    Route::delete('/cabinet/contracts/{contract}', [ContractsController::class, 'destroy'])->name('cabinet.contracts.destroy');
    Route::post('/cabinet/contracts/reorder', [ContractsController::class, 'reorder'])->name('cabinet.contracts.reorder');
});
/** ///Кабинет **/


/** DaData **/
Route::middleware('auth')->prefix('dadata')->name('dadata.')->controller(DaDataController::class)->group(function () {
    Route::post('/party', 'party')->name('party');
    Route::post('/bank',  'bank')->name('bank');
});
/** ///DaData **/


/** Auth **/
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
/** ///Auth **/


/** FancyBox AJAX **/
Route::controller(FancyBoxController::class)->group(function () {
    Route::post('/fancybox-ajax', 'fancybox');
});
/** ///FancyBox AJAX **/

/** Axios async forms **/
Route::controller(AxiosController::class)->group(function () {
    Route::post('/upload-form-async', 'async');
    Route::post('/call-me-blue', 'callMeBlue');
});
/** ///Axios async forms **/
