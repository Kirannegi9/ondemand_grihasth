<?php

use Illuminate\Support\Facades\Route;

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

Route::get('lang/change', [App\Http\Controllers\LangController::class, 'change'])->name('changeLang');

Route::post('setToken', [App\Http\Controllers\Auth\AjaxController::class, 'setToken'])->name('setToken');
Route::get('register', function () {
    return view('auth.register');
})->name('register');
Route::get('register/phone', function () {
    return view('auth.phone_register');
})->name('register.phone');


Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

Route::get('/users', [App\Http\Controllers\HomeController::class, 'users'])->name('users');

Route::get('/services', [App\Http\Controllers\ServiceController::class, 'index'])->name('services');
Route::get('/services/create', [App\Http\Controllers\ServiceController::class, 'create'])->name('services.create');
Route::get('/services/edit/{id}', [App\Http\Controllers\ServiceController::class, 'edit'])->name('services.edit');

Route::get('/bookings', [App\Http\Controllers\BookingController::class, 'index'])->name('bookings');
Route::get('/bookings/edit/{id}', [App\Http\Controllers\BookingController::class, 'edit'])->name('bookings.edit');
Route::get('/bookings/print/{id}', [App\Http\Controllers\BookingController::class, 'print'])->name('bookings.print');
Route::post('order-status-notification', [App\Http\Controllers\BookingController::class, 'sendNotification'])->name('order-status-notification');

Route::get('/workers', [App\Http\Controllers\WorkersController::class, 'index'])->name('workers');
Route::get('/workers/create', [App\Http\Controllers\WorkersController::class, 'create'])->name('workers.create');
Route::get('/workers/edit/{id}', [App\Http\Controllers\WorkersController::class, 'edit'])->name('workers.edit');

Route::get('/coupons', [App\Http\Controllers\CouponController::class, 'index'])->name('coupons');
Route::get('/coupons/create', [App\Http\Controllers\CouponController::class, 'create'])->name('coupons.create');
Route::get('/coupons/edit/{id}', [App\Http\Controllers\CouponController::class, 'edit'])->name('coupons.edit');

Route::get('/wallettransaction', [App\Http\Controllers\TransactionController::class, 'index'])->name('wallettransaction');

Route::get('/users/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('user.profile');

Route::get('forgot-password', [App\Http\Controllers\Auth\LoginController::class, 'forgotPassword'])->name('forgot-password');

Route::get('/payouts', [App\Http\Controllers\PayoutsController::class, 'index'])->name('payouts');

Route::get('/payouts/create', [App\Http\Controllers\PayoutsController::class, 'create'])->name('payouts.create');

Route::post('send-email', [App\Http\Controllers\SendEmailController::class, 'sendMail'])->name('sendMail');

Route::post('store-firebase-service', [App\Http\Controllers\HomeController::class,'storeServiceFile'])->name('storeServiceFile');