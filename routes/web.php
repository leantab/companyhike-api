<?php

use App\Http\Controllers\TestController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/home', function () {
    return view('dashboard');
})->name('dashboard');


/* ADMIN ROUTES */
// Route::prefix('admin')->middleware(['auth:sanctum', 'verified'])->group(function () {
//     Route::get('/', function () {
//         return view('dashboard');
//     })->name('admin_dash');

//     Route::get('users', [AdminUserCont::class, 'index'])->name('admin_users');
//     Route::get('users/create', [AdminUserCont::class, 'create']);
//     Route::post('users/store', [AdminUserCont::class, 'store'])->name('admin_create_user');
//     Route::get('users/{user}', [AdminUserCont::class, 'show']);
//     Route::get('users/{user}/edit', [AdminUserCont::class, 'edit'])->name('admin_edit_user');
//     Route::post('users/update', [AdminUserCont::class, 'update']);
//     Route::get('users/{user}/inactivate', [AdminUserCont::class, 'inactivate']);
//     Route::get('users/{user}/activate', [AdminUserCont::class, 'activate']);

//     Route::get('communities', [AdminComCont::class, 'index'])->name('admin_communities');
//     Route::get('communities/create', [AdminComCont::class, 'create'])->name('admin_communities_create');
//     Route::post('communities/store', [AdminComCont::class, 'store']);
//     Route::get('communities/{community}', [AdminComCont::class, 'show']);

//     Route::get('games', [AdminGamesCont::class, 'index'])->name('admin_games');

//     Route::get('licenses', [AdminLicensesCont::class, 'index'])->name('admin_licenses');
// });


Route::get('/setLang/{lang}', function ($lang) {
    if (!in_array($lang, ['en', 'es', 'pt'])) {
        abort(400);
    }

    // session()->put('language', $lang);
    session(['my_locale' => $lang]);
    if (auth()->check()) {
        auth()->user()->language = $lang;
        auth()->user()->save();
    }
    //return response()->json(true);
    return redirect()->back();
});


Route::get('/documentation', function () {
    return view('swagger.index');
});

Route::get('/game/create_test/{version}', [TestController::class, 'createTestGameScenario']);
Route::get('/game/check', [TestController::class, 'createTestGameScenario']);
Route::get('/game/get_ceo_vars/{game_id}/{user_id}', [TestController::class, 'getCeoVariables']);
