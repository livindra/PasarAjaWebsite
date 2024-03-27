<?php

use App\Http\Controllers\LayoutsController;
use App\Http\Controllers\Website\UploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', function () {
    return view('home');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/forgot', function () {
    return view('forgot');
});

Route::get('/verifikasi', function () {
    return view('verifikasi');
});

Route::get('/ganti', function () {
    return view('ganti');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/upload', [UploadController::class, 'upload'])->name('upload');
Route::post('/upload/proses', [UploadController::class, 'proses_upload'])->name('upload.proses');

Route::get('/layouts', [LayoutsController::class, 'index']);

Route::get('/dropzone', [UploadController::class, 'dropzone'])->name('dropzone');
Route::post('/dropzone/store', [UploadController::class, 'dropzone_strore']) ->name('dropzone.store');