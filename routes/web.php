<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminSlideController;
use App\Http\Controllers\Admin\AdminFeatureController;
use App\Http\Controllers\Admin\AdminTestimonialController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminPhotoController;
use App\Http\Controllers\Admin\AdminVideoController;
use App\Http\Controllers\Admin\AdminFaqController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\AdminAmenityController;
use App\Http\Controllers\Admin\AdminRoomController;



use App\Http\Controllers\Hotel\HomeController;
use App\Http\Controllers\Hotel\AboutController;
use App\Http\Controllers\Hotel\BlogController;
use App\Http\Controllers\Hotel\PhotoController;
use App\Http\Controllers\Hotel\VideoController;
use App\Http\Controllers\Hotel\FaqController;
use App\Http\Controllers\Hotel\SubscriberController;



Route::get('/', function () {
    return view('welcome');
});


/*Hotel */

Route::get('/',[HomeController::class, 'index'])->name('home');
Route::get('/about',[AboutController::class, 'index'])->name('about');
Route::get('/blog',[BlogController::class, 'index'])->name('blog');
Route::get('/post/{id}',[BlogController::class, 'single_post'])->name('post');
Route::get('/photo-gallery',[PhotoController::class, 'index'])->name('photo_gallery');
Route::get('/video-gallery',[VideoController::class, 'index'])->name('video_gallery');
Route::get('/faq',[FaqController::class, 'index'])->name('faq');
Route::post('/subscriber/send-email', [SubscriberController::class, 'send_email'])->name('subscriber_send_email');
Route::get('/subscriber/verify/{email}/{token}', [SubscriberController::class, 'verify'])->name('subscriber_verify');


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

Route::get('/admin/feature/view',[AdminFeatureController::class, 'index'])->name('admin_feature_view')->middleware('admin:admin');
Route::get('/admin/feature/add',[AdminFeatureController::class, 'add'])->name('admin_feature_add')->middleware('admin:admin');
Route::post('/admin/feature/store',[AdminFeatureController::class, 'store'])->name('admin_feature_store')->middleware('admin:admin');
Route::get('/admin/feature/edit/{id}',[AdminFeatureController::class, 'edit'])->name('admin_feature_edit')->middleware('admin:admin');
Route::post('/admin/feature/update/{id}',[AdminFeatureController::class, 'update'])->name('admin_feature_update')->middleware('admin:admin');
Route::get('/admin/feature/delete/{id}',[AdminFeatureController::class, 'delete'])->name('admin_feature_delete')->middleware('admin:admin');

Route::get('/admin/testimonial/view',[AdminTestimonialController::class, 'index'])->name('admin_testimonial_view')->middleware('admin:admin');
Route::get('/admin/testimonial/add',[AdminTestimonialController::class, 'add'])->name('admin_testimonial_add')->middleware('admin:admin');
Route::post('/admin/testimonial/store',[AdminTestimonialController::class, 'store'])->name('admin_testimonial_store')->middleware('admin:admin');
Route::get('/admin/testimonial/edit/{id}',[AdminTestimonialController::class, 'edit'])->name('admin_testimonial_edit')->middleware('admin:admin');
Route::post('/admin/testimonial/update/{id}',[AdminTestimonialController::class, 'update'])->name('admin_testimonial_update')->middleware('admin:admin');
Route::get('/admin/testimonial/delete/{id}',[AdminTestimonialController::class, 'delete'])->name('admin_testimonial_delete')->middleware('admin:admin');

Route::get('/admin/post/view',[AdminPostController::class, 'index'])->name('admin_post_view')->middleware('admin:admin');
Route::get('/admin/post/add',[AdminPostController::class, 'add'])->name('admin_post_add')->middleware('admin:admin');
Route::post('/admin/post/store',[AdminPostController::class, 'store'])->name('admin_post_store')->middleware('admin:admin');
Route::get('/admin/post/edit/{id}',[AdminPostController::class, 'edit'])->name('admin_post_edit')->middleware('admin:admin');
Route::post('/admin/post/update/{id}',[AdminPostController::class, 'update'])->name('admin_post_update')->middleware('admin:admin');
Route::get('/admin/post/delete/{id}',[AdminPostController::class, 'delete'])->name('admin_post_delete')->middleware('admin:admin');

Route::get('/admin/photo/view',[AdminPhotoController::class, 'index'])->name('admin_photo_view')->middleware('admin:admin');
Route::get('/admin/photo/add',[AdminPhotoController::class, 'add'])->name('admin_photo_add')->middleware('admin:admin');
Route::post('/admin/photo/store',[AdminPhotoController::class, 'store'])->name('admin_photo_store')->middleware('admin:admin');
Route::get('/admin/photo/edit/{id}',[AdminPhotoController::class, 'edit'])->name('admin_photo_edit')->middleware('admin:admin');
Route::post('/admin/photo/update/{id}',[AdminPhotoController::class, 'update'])->name('admin_photo_update')->middleware('admin:admin');
Route::get('/admin/photo/delete/{id}',[AdminPhotoController::class, 'delete'])->name('admin_photo_delete')->middleware('admin:admin');

Route::get('/admin/video/view',[AdminVideoController::class, 'index'])->name('admin_video_view')->middleware('admin:admin');
Route::get('/admin/video/add',[AdminVideoController::class, 'add'])->name('admin_video_add')->middleware('admin:admin');
Route::post('/admin/video/store',[AdminVideoController::class, 'store'])->name('admin_video_store')->middleware('admin:admin');
Route::get('/admin/video/edit/{id}',[AdminVideoController::class, 'edit'])->name('admin_video_edit')->middleware('admin:admin');
Route::post('/admin/video/update/{id}',[AdminVideoController::class, 'update'])->name('admin_video_update')->middleware('admin:admin');
Route::get('/admin/video/delete/{id}',[AdminVideoController::class, 'delete'])->name('admin_video_delete')->middleware('admin:admin');

Route::get('/admin/faq/view',[AdminFaqController::class, 'index'])->name('admin_faq_view')->middleware('admin:admin');
Route::get('/admin/faq/add',[AdminFaqController::class, 'add'])->name('admin_faq_add')->middleware('admin:admin');
Route::post('/admin/faq/store',[AdminFaqController::class, 'store'])->name('admin_faq_store')->middleware('admin:admin');
Route::get('/admin/faq/edit/{id}',[AdminFaqController::class, 'edit'])->name('admin_faq_edit')->middleware('admin:admin');
Route::post('/admin/faq/update/{id}',[AdminFaqController::class, 'update'])->name('admin_faq_update')->middleware('admin:admin');
Route::get('/admin/faq/delete/{id}',[AdminFaqController::class, 'delete'])->name('admin_faq_delete')->middleware('admin:admin');

Route::get('/admin/amenity/view',[AdminAmenityController::class, 'index'])->name('admin_amenity_view')->middleware('admin:admin');
Route::get('/admin/amenity/add',[AdminAmenityController::class, 'add'])->name('admin_amenity_add')->middleware('admin:admin');
Route::post('/admin/amenity/store',[AdminAmenityController::class, 'store'])->name('admin_amenity_store')->middleware('admin:admin');
Route::get('/admin/amenity/edit/{id}',[AdminAmenityController::class, 'edit'])->name('admin_amenity_edit')->middleware('admin:admin');
Route::post('/admin/amenity/update/{id}',[AdminAmenityController::class, 'update'])->name('admin_amenity_update')->middleware('admin:admin');
Route::get('/admin/amenity/delete/{id}',[AdminAmenityController::class, 'delete'])->name('admin_amenity_delete')->middleware('admin:admin');


Route::get('/admin/room/view',[AdminRoomController::class, 'index'])->name('admin_room_view')->middleware('admin:admin');
Route::get('/admin/room/add',[AdminRoomController::class, 'add'])->name('admin_room_add')->middleware('admin:admin');
Route::post('/admin/room/store',[AdminRoomController::class, 'store'])->name('admin_room_store')->middleware('admin:admin');
Route::get('/admin/room/edit/{id}',[AdminRoomController::class, 'edit'])->name('admin_room_edit')->middleware('admin:admin');
Route::post('/admin/room/update/{id}',[AdminRoomController::class, 'update'])->name('admin_room_update')->middleware('admin:admin');
Route::get('/admin/room/delete/{id}',[AdminRoomController::class, 'delete'])->name('admin_room_delete')->middleware('admin:admin');


Route::get('/admin/page/about',[AdminPageController::class, 'about'])->name('admin_page_about')->middleware('admin:admin');
Route::post('/admin/page/about/update',[AdminPageController::class, 'about_update'])->name('admin_page_about_update')->middleware('admin:admin');
