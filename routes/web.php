<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminSlideController;

use App\Http\Controllers\Hotel\HomeController;
use App\Http\Controllers\Hotel\AboutController;
use App\Http\Controllers\Hotel\BlogController;



Route::get('/', function () {
    return view('welcome');
});


/*Hotel */

Route::get('/',[HomeController::class, 'index'])->name('home');
Route::get('/about',[AboutController::class, 'index'])->name('about');
Route::get('/blog',[BlogController::class, 'index'])->name('blog');


/* Admin */
Route::get('/admin/home',[AdminHomeController::class, 'index'])->name('admin_home')->middleware('admin:admin');
Route::get('/admin/login',[AdminLoginController::class, 'index'])->name('admin_login');
Route::post('/admin/login-submit',[AdminLoginController::class,'login_submit'])->name('admin_login_submit');
Route::get('/admin/forget-password',[AdminLoginController::class, 'forget_password'])->name('admin_forget_password');
Route::get('/admin/logout',[AdminLoginController::class, 'logout'])->name('admin_logout');
Route::post('/admin/forget-password-submit',[AdminLoginController::class,'forget_password_submit'])->name('admin_forget_password_submit');
Route::get('/admin/reset-password/{token}/{email}',[AdminLoginController::class, 'reset_password'])->name('admin_reset_password');

Route::get('/admin/edit-profile', [AdminProfileController::class,'index'])->name('admin_profile')->middleware('admin:admin');
Route::post('/admin/edit-profile-submit',[AdminProfileController::class,'profile_submit'])->name('admin_profile_submit');

Route::get('/admin/slide/view',[AdminSlideController::class, 'index'])->name('admin_slide_view')->middleware('admin:admin');
Route::get('/admin/slide/add',[AdminSlideController::class, 'add'])->name('admin_slide_add')->middleware('admin:admin');
Route::post('/admin/slide/store',[AdminSlideController::class, 'store'])->name('admin_slide_store')->middleware('admin:admin');
Route::get('/admin/slide/edit/{id}',[AdminSlideController::class, 'edit'])->name('admin_slide_edit')->middleware('admin:admin');
Route::post('/admin/slide/update/{id}',[AdminSlideController::class, 'update'])->name('admin_slide_update')->middleware('admin:admin');
Route::get('/admin/slide/delete/{id}',[AdminSlideController::class, 'delete'])->name('admin_slide_delete')->middleware('admin:admin');
