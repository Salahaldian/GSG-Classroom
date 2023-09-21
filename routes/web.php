<?php

use App\Http\Controllers\Admin\TwoFactourAuthenticationController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ClassroomPeopleController;
use App\Http\Controllers\ClassworkController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\JoinClassroomController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PlansController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\SubscriptionsController;
use App\Http\Controllers\TopicsController;
use App\Http\Controllers\Webhooks\StripeController;
use App\Http\Middleware\ApplyUserPreferences;
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

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/admin/2fa', [TwoFactourAuthenticationController::class, 'create']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// require __DIR__.'/auth.php';

Route::get('plans', [PlansController::class, 'index'])
    ->name('plans');

Route::middleware(['auth:web,admin'])->group(function () {

    Route::get('subscriptions/{subscription}/pay', [PaymentsController::class, 'create'])
        ->name('checkout');

    Route::post('subscriptions', [SubscriptionsController::class, 'store'])
        ->name('subscriptions.store');

    Route::post('payments', [PaymentsController::class, 'store'])
        ->name('payments.store');

    Route::get('/payments/{subscription}/success', [PaymentsController::class, 'success'])
        ->name('payments.success');

    Route::get('/payments/{subscription}/cancel', [PaymentsController::class, 'cancel'])
        ->name('payments.cancel');

    Route::prefix('/classrooms/trashed')
        ->as('classrooms.')
        ->controller(ClassroomController::class)
        ->group(function () {
            Route::get('/', 'trashed')->name('trashed');
            Route::put('/{classroom}', 'restore')->name('restore');
            Route::delete('/{classroom}', 'forceDelete')->name('force-delete');
    });

    Route::get('/classrooms/{classroom}/join', [JoinClassroomController::class, 'create'])
        ->middleware('signed')
        ->name('classrooms.join');

    Route::post('/classrooms/{classroom}/join', [JoinClassroomController::class, 'store']);

    Route::get('/classrooms/{classroom}/chat', [ClassroomController::class, 'chat'])->name('classrooms.chat');

    Route::resources([
        'topic' => TopicsController::class,
        'classrooms' => ClassroomController::class
    ]);

    Route::resource('classrooms.classworks', ClassworkController::class);

    Route::get('classrooms/{classroom}/people', [ClassroomPeopleController::class, 'index'])
        ->name('classrooms.people');

    Route::delete('classrooms/{classroom}/people', [ClassroomPeopleController::class, 'destroy'])
        ->name('classrooms.people.destroy');

    Route::post('comments', [CommentController::class, 'store'])
        ->name('comments.store');

    Route::post('classwork/{classwork}/submissions', [SubmissionController::class, 'store'])
        ->name('submissions.store');

    Route::get('classwork/{classwork}/file', [SubmissionController::class, 'file'])
        ->name('submissions.file');
});

Route::post('/payments/stripe/webhook', StripeController::class);
