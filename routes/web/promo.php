<?php

use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\BannerImageController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', config('jetstream.auth_session')])->group(function () {

    //promo code
    Route::group(['prefix' => 'promo-code', 'middleware' => 'permission:manage-promo'], function () {
        Route::get('/', [PromoCodeController::class, 'index'])->name('promocode.index');
        Route::middleware('remove_empty_query')->get('/list', [PromoCodeController::class, 'list'])->name('promocode.list');
        Route::get('/userList', [PromoCodeController::class, 'userList'])->name('promocode.userList');
        Route::middleware(['permission:add-promo'])->get('/create', [PromoCodeController::class, 'create'])->name('promocode.create');
        Route::middleware(['permission:edit-promo'])->post('/store', [PromoCodeController::class, 'store'])->name('promocode.store');
        Route::get('/edit/{id}', [PromoCodeController::class, 'edit'])->name('promocode.edit');
        Route::get('/fetch', [PromoCodeController::class, 'fetchServiceLocation'])->name('promocode.fetchServiceLocation'); //service location list
        Route::post('/update/{promo}', [PromoCodeController::class, 'update'])->name('promocode.update');
        Route::delete('/delete/{promo}', [PromoCodeController::class, 'destroy'])->name('promocode.delete');
        Route::post('/update-status', [PromoCodeController::class, 'updateStatus'])->name('promocode.updateStatus');
        Route::get('/history/{promo}', [PromoCodeController::class, 'history'])->name('promocode.history');
        Route::middleware('remove_empty_query')->get('/historyList', [PromoCodeController::class, 'historyList'])->name('promocode.historyList');
    });

    //banner image
    Route::group(['prefix' => 'banner-image', 'middleware' => 'permission:banner_image'], function () {
        Route::get('/', [BannerImageController::class, 'index'])->name('bannerimage.index');
        Route::middleware(['permission:add_banner_image'])->get('/create', [BannerImageController::class, 'create'])->name('bannerimage.create');
        Route::get('/list', [BannerImageController::class, 'list'])->name('bannerimage.list');
        Route::post('/update/{bannerimage}', [BannerImageController::class, 'update'])->name('bannerimage.update');
        Route::post('/store', [BannerImageController::class, 'store'])->name('bannerimage.store');
        Route::middleware(['permission:edit_banner_image'])->get('/edit/{id}', [BannerImageController::class, 'edit'])->name('bannerimage.edit');
        Route::delete('/delete/{bannerimage}', [BannerImageController::class, 'destroy'])->name('bannerimage.delete');
        Route::post('/update-status', [BannerImageController::class, 'updateStatus'])->name('bannerimage.updateStatus');
    });
});
