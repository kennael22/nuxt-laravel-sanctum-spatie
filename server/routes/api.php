<?php
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolesAndPermissionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Route::post('/login', [AuthenticatedSessionController::class, 'store'])
//     ->middleware('guest')
//     ->name('login');
// Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
//     ->middleware('auth:sanctum')
//     ->name('logout');
// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([

    'middleware' => 'auth:sanctum',
    'prefix' => 'auth'

], function () {
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->withoutMiddleware('auth:sanctum');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::get('user', [AuthenticatedSessionController::class, 'me']);
    // Route::get('user', 'UserController@getAuthUser');
    Route::get('all_users', 'UserController@getAllUsers');
    Route::get('fetch_auth', 'UserController@fetchAuthUser');

    //new routes
    Route::customRoleResource('roles', RoleController::class);
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

    //roles and permissions
    Route::get('roles_and_permissions', [RolesAndPermissionsController::class, 'getRolesAndPermissions']);
    Route::get('permissions', [RolesAndPermissionsController::class, 'getPermissions']);
    Route::post('manage_roles_and_permissions/{role?}', [RolesAndPermissionsController::class, 'manageRolesAndPermissions']);

   
});
