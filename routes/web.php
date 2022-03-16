<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;

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
    // $headers = [
    //     'Content-Type'        => 'application/pdf',
    //     'Content-Disposition' => 'attachment; filename="'. $recording_data->file_name .'"',
    // ];

    $path = 'http://www.africau.edu/images/default/sample.pdf'; // external url

    return response(file_get_contents($path), 200, [
        'Content-Type' => 'application/pdf'
    ]);

    return response()->stream(function () {
        readfile($path); 
    }, 200, ['Content-Type' => 'application/pdf']);

    // return response()->file(Storage::path('company-pdf/company.pdf'));
    // return Storage::response('http://www.africau.edu/images/default/sample.pdf');
    // return response()->download('http://www.africau.edu/images/default/sample.pdf');

    // return view('welcome');
});

Route::get('/company', [CompanyController::class, 'store']);
