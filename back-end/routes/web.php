<?php

use App\Http\Controllers;
use App\Http\Controllers\Admin\HistorieLoginController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\API\APIVerifiedEmailsController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
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

// Nhóm các route mà chỉ người dùng chưa đăng nhập mới có thể truy cập
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'postLogin'])->name('postLogin');
    Route::get('/register', [LoginController::class, 'register'])->name('register');
    Route::post('/register', [LoginController::class, 'postRegister'])->name('postRegister');
    Route::get('/forgotPWD', [LoginController::class, 'forgotpassword'])->name('forgotpassword');
    Route::post('/forgotPWD/', [LoginController::class, 'postForgotpassword'])->name('postForgotpassword');
    Route::get('password/reset/{token}',
        [ChangePasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/changPassword',
        [ChangePasswordController::class, 'changPassword'])->name('changPassword');
});

// Nhóm các route mà chỉ người dùng đã đăng nhập mới có thể truy cập
Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
//    Route::get('/profile/{id}', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/profile/{id}', [ProfileController::class, 'update'])->name('profile_update');
    Route::delete('/profile/delete/{id}', [ProfileController::class, 'delete'])->name('profile.delete');
    Route::get('/home', [Controllers\Admin\UserController::class, 'manageUsers'])->name('home');
    // Route mặc định cho tất cả người dùng
    Route::get('/', [Controllers\Admin\UserController::class, 'manageUsers'])->name('index');


});

Route::get('/admin/histories', [HistorieLoginController::class, 'manageHistories'])
    ->name('admin.histories')
    ->middleware(['checkPermission:View_History']);

Route::middleware(['checkPermission:View_Users'])->group(function () {
    Route::middleware(['checkPermission:Add_Users'])->group(function () {
        Route::get('admin/addUser', [Controllers\Admin\UserController::class, 'addUser'])->name('admin.addUser');
        Route::put('admin/addUser', [Controllers\Admin\UserController::class, 'storeUser'])->name('admin.addUser');
    });

    Route::middleware(['checkPermission:Edit_Users'])->group(function () {
        Route::post('/admin/updateUser/{id}',
            [Controllers\Admin\UserController::class, 'updateUser'])->name('admin.updateUser');
    });
    Route::middleware(['checkPermission:Delete_Users'])->group(function () {
        Route::delete('/admin/delete-selected-users',
            [Controllers\Admin\UserController::class, 'deleteSelectedUsers'])->name('admin.deleteSelectedUsers');

    });
    Route::get('/user/{id}', [Controllers\Admin\UserController::class, 'show'])->name('user.show');

    Route::get('/admin/users', [Controllers\Admin\UserController::class, 'manageUsers'])->name('admin.users');
    Route::get('/search-users', [Controllers\Admin\UserController::class, 'search'])->name('searchUsers');

    Route::get('/admin/', [Controllers\Admin\UserController::class, 'manageUsers'])->name('admin.index')    ;
    Route::get('/admin/dashboard', [Controllers\Admin\UserController::class, 'manageUsers'])->name('admin.dashboard');
});


Route::middleware(['checkPermission:View_Role'])->group(function () {

    Route::get('/admin/roles', [RoleController::class, 'manageRoles'])->name('admin.roles');
    Route::get('/admin/permissons',
        [RoleController::class, 'managePermissions'])->name('admin.permissions');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');


    Route::get('/roles/{id}', [RoleController::class, 'show'])->name('roles.show');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RoleController::class, 'delete'])->name('roles.delete');
    Route::get('/sanpham', [HomeController::class, 'product'])->name('product');

});



// Route xác thực email
Route::get('/verify-email/{token}',
    [APIVerifiedEmailsController::class, 'verifyEmail'])->name('verifyEmail');

