<?php

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
Auth::routes();
Route::get('/', function () { return view('/home'); })->middleware('auth');
Route::get('/home', 'HomeController@index')->name('home')->middleware('auth');

Route::get('applications/application_list', 'ApplicationController@fetch_data');
Route::get('applications/past_applications', 'ApplicationController@past_applications');
Route::get('applications/mark_as_incomplete', 'PendingApprovalsController@mark_as_incomplete');
Route::get('applications/redeem_promo_code', 'ApplicationController@redeemPromoCode');
Route::resource('applications','ApplicationController')->middleware('CheckRole:Encoder,Admin');

Route::get('cashier/receive_payment','ApplicationController@showPaymentForm')->name('cashier.receive_payment')->middleware('CheckRole:Cashier');
Route::get('cashier/confirm_payment','ApplicationController@retrievePaymentForm');
Route::get('cashier/customer_payment','ApplicationController@markCustomerAsPaid')->name('cashier.customer_payment');
Route::get('cashier/download_report','ApplicationController@downloadReport')->name('cashier.download_report')->middleware('CheckRole:Cashier,Accounting');

Route::get('partner_companies/getpartners', 'PartnerCompanyController@getPartnerCompanies');
Route::resource('partner_companies', 'PartnerCompanyController')->middleware('CheckRole:Admin');

Route::get('application_batches/checklist', 'ApplicationBatchController@showChecklist')->name('application_batches.checklist')->middleware('CheckRole:Encoder');
Route::get('application_batches/checklist_pdf', 'ApplicationBatchController@downloadChecklist')->name('download_checklist_pdf');
Route::get('application_batches/finalize_batch_page', 'ApplicationBatchController@showFinalizeBatchPage')->name('show_finalize_batch_page')->middleware('CheckRole:Encoder');
Route::get('application_batches/finalize_batch', 'ApplicationBatchController@finalizeBatchContents')->name('finalize_batch');
Route::resource('application_batches', 'ApplicationBatchController')->middleware('CheckRole:Encoder,Admin');

Route::get('admin/users', 'AdminController@userList')->name('admin.users')->middleware('CheckRole:Admin');
Route::get('admin/users/{id}/edit', 'AdminController@editUser')->name('admin.edit')->middleware('CheckRole:Admin');
Route::post('admin/users/{id}', 'AdminController@updateUser')->name('admin.update')->middleware('CheckRole:Admin');
Route::get('admin/branches', 'AdminController@branchList')->name('admin.branches')->middleware('CheckRole:Admin');
Route::get('admin/addbranch', 'AdminController@addBranch')->name('admin.addbranch')->middleware('CheckRole:Admin');
Route::get('admin/updatebranch', 'AdminController@updateBranch')->name('admin.updatebranch')->middleware('CheckRole:Admin');
Route::get('admin/pending_approvals', 'AdminController@pendingApprovals')->name('admin.approvals')->middleware('CheckRole:Admin');

Route::get('admin/promo_codes', 'PromoCodeController@index')->name('admin.promo_codes')->middleware('CheckRole:Admin');
Route::post('admin/promo_codes', 'PromoCodeController@store')->name('admin.promo_codes.store')->middleware('CheckRole:Admin');
Route::get('admin/promo_codes/create', 'PromoCodeController@create')->name('admin.create_promo_code')->middleware('CheckRole:Admin');
Route::get('admin/promo_codes/{id}/edit', 'PromoCodeController@edit')->name('admin.promo_codes.edit')->middleware('CheckRole:Admin');
Route::post('admin/promo_codes/{id}', 'PromoCodeController@update')->name('admin.promo_codes.update')->middleware('CheckRole:Admin');

Route::resource('account_receivables', 'AccountReceivableController');
