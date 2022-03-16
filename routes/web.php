<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Mail\TestMail;
use App\Events\ResetList;

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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


Route::get('/show', [UserController::class, 'show'])->name('user.show');
Route::get('/create', [UserController::class, 'create'])->name('user.create');
Route::post('/store', [UserController::class, 'store'])->name('user.store');
Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
Route::post('/location', [UserController::class, 'location'])->name('user.location');
Route::get('user/{id}', [UserController::class, 'destroy'])->name('user.destroy');

Route::get('test', function(){
    $details = [
        'name' => 'test user',
        'email' => 'angalo3000@gmail.com'
    ];
    broadcast(new ResetList($details))->toOthers();
});

Route::get('send-mail', function () {
    $details = [
        'name' => 'test user',
        'email' => 'angalo3000@gmail.com'
    ];
    Mail::send('email.sendMail', $details, function($message) use ($details) {
       $message->to($details['email'], $details['name'])->subject('Registration successfully');
       $message->from('test@gmail.com','Angelo Cenidoza');
    });
    dd("Email is Sent.");
});

require __DIR__.'/auth.php';
