<?php

// use Artisan;
use App\Http\Controllers\MailController;
use App\Mail\welcomemail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Auth::routes();

Route::post('/import', [App\Http\Controllers\HomeController::class, 'import']);
Route::get('/multidiv', [App\Http\Controllers\MultiDivController::class, 'index'])->name('Mutiple Dividend');
Route::post('/storedata', [App\Http\Controllers\HomeController::class, 'storedata']);
Route::get('/allfiles', [App\Http\Controllers\HomeController::class, 'allfiles']);
Route::get('/process-ftp-files', [App\Http\Controllers\HomeController::class, 'processFTPfiles']);
Route::get('/search', [App\Http\Controllers\HomeController::class, 'search']);
Route::get('/investor-search', [App\Http\Controllers\HomeController::class, 'investorSearch']);

// Dk -> UserSearch
Route::get('/user_portal', [App\Http\Controllers\UserController::class, 'userPortal'])->name('user.portal')->withoutMiddleware(['auth']);
Route::get('/user_search', [App\Http\Controllers\UserController::class, 'userSearch'])->withoutMiddleware(['auth']);
Route::get('/user-search', [App\Http\Controllers\UserController::class, 'UserSearchResult'])->withoutMiddleware(['auth']);
Route::post('/get-unique-dividend-details', [App\Http\Controllers\UserController::class, 'getUniqueDividentDetails'])->withoutMiddleware(['auth']);


// python API call
Route::post('/getMultipleDividendData', [App\Http\Controllers\MultiDivController::class, 'getMultipleDividendData']);
Route::post('/processMultiDividend', [App\Http\Controllers\MultiDivController::class, 'processMultiDividend']);





//Get company
Route::get('/getcompany', [App\Http\Controllers\MultiDivController::class, 'getcompany']);
//Get file list
Route::get('/getfilelist/{cin}', [App\Http\Controllers\MultiDivController::class, 'getMultipleDividendFile']);
//Get xfer date
Route::get('/getxferdate/{log_id}', [App\Http\Controllers\MultiDivController::class, 'getMultipleDividendXfer']);
//Left Table
Route::post('/dividentlist', [App\Http\Controllers\MultiDivController::class, 'getdividentlist']);
// Right Table
Route::post('/multipledividend', [App\Http\Controllers\MultiDivController::class, 'getmultipledividend']);


//delete a members details
Route::post('/deletemembersdata', [App\Http\Controllers\MultiDivController::class, 'deletemembersdata']);


// route mail
Route::post('/sendmail', [MailController::class, 'sendmail']);

// EOD
Route::get('/eod', [MailController::class, 'eod_mail']);

//
Route::post('/storedividend', [App\Http\Controllers\HomeController::class, 'store_dividend']);


Route::get('/uploaded-details', [App\Http\Controllers\DownloadexcelController::class, 'index']);

Route::post('export', [App\Http\Controllers\DownloadexcelController::class, 'export']);

//excel download
Route::get('/excel-download/{file_name}', function ($file_name = null) {

    $path = storage_path() . "/excel/$file_name";
    if (file_exists($path)) {
        return Response::download($path);
    }
});

Route::get('/log-download', function () {
    $path = storage_path() . "/logs/laravel.log";
    if (file_exists($path)) {
        return Response::download($path);
    }
});

Route::get('create-excel/{id}/{name}', [App\Http\Controllers\DownloadexcelController::class, 'excelCreate']);

Route::get('/folio-header-deatails', [App\Http\Controllers\FolioDetailsController::class, 'folioHeaderDeatails']);

Route::post('/folio-header-data', [App\Http\Controllers\FolioDetailsController::class, 'folioHeaderData']);

Route::post('/folio-dividend-data', [App\Http\Controllers\FolioDetailsController::class, 'folioDividenData']);
// Admin
Route::middleware(['auth', 'user-role:admin'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});
// artisan
Route::get('/route-cache-clear', [App\Http\Controllers\ArtisanController::class, 'routeCacheClear'])->name('route_cache_clear');
Route::get('/cache-clear', [App\Http\Controllers\ArtisanController::class, 'cacheClear'])->name('cache_clear');
Route::get('/config-cache', [App\Http\Controllers\ArtisanController::class, 'configCache'])->name('config_cache');
Route::get('/view-clear', [App\Http\Controllers\ArtisanController::class, 'viewClear'])->name('view_clear');
Route::get('/migrate', [App\Http\Controllers\ArtisanController::class, 'migrate'])->name('migrate');
Route::get('/seed', [App\Http\Controllers\ArtisanController::class, 'seed'])->name('seed');
Route::get('/optimize', [App\Http\Controllers\ArtisanController::class, 'optimize'])->name('optimize');
Route::get('/schedule-run', [App\Http\Controllers\ArtisanController::class, 'scheduleRun'])->name('schedule_run');
Route::get('/cors', [App\Http\Controllers\ArtisanController::class, 'cors'])->name('cors');
