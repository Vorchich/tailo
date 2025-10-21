<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SeamstressController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\Api\UserSizeController;
use App\Http\Controllers\Api\GPT\GPTController;
use App\Http\Controllers\Api\NotepadController;
use App\Http\Controllers\Api\NotepadFolderController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\SeamstressStatisticController;
use App\Http\Controllers\Apple\AppleCalbackController;
use App\Http\Controllers\Apple\AppleController;
use App\Http\Controllers\Auth\AppleAuthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SendEmailVerificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Middleware\SeamstressAccessMiddleware;
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
    Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/auth/register', [RegisteredUserController::class, 'store'])
                ->middleware('guest')
                ->name('register');

    Route::post('/auth/login', [AuthenticatedSessionController::class, 'store'])
                    ->middleware('guest')
                    ->name('login');

    Route::post('/auth/request-password-reset', [PasswordResetLinkController::class, 'store'])
                    ->middleware('guest')
                    ->name('password.email');

    Route::post('/auth/reset-password', [NewPasswordController::class, 'store'])
                    ->middleware('guest')
                    ->name('password.store');

    Route::post('/auth/confirm-registration', VerifyEmailController::class)
                    ->name('verification.verify');

    Route::post('/auth/logout', [AuthenticatedSessionController::class, 'destroy'])
                    ->middleware('auth')
                    ->name('logout');

    Route::post('/auth/send-email', [SendEmailVerificationController::class, 'send'])
                    ->name('send.email');

    Route::post('/auth/google_login', [GoogleAuthController::class, 'login']);

    Route::post('/auth/apple_login', [AppleAuthController::class, 'login']);

    Route::prefix('apple')->group(function () {
        Route::post('/callback', [AppleCalbackController::class, 'callback']);
        Route::post('/subscribe_validate', [AppleController::class, 'subscribe_validate'])->middleware('auth:sanctum');
        Route::get('/is_subscribe', [AppleController::class, 'is_subscribe'])->middleware('auth:sanctum');
    });
    Route::get('/sizes', [UserSizeController::class, 'sizes']);
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{post}/show', [PostController::class, 'show']);

    Route::get('/seamstresses/{user}/increment-view', [SeamstressStatisticController::class, 'incrementView']);
    Route::get('/seamstresses/{user}/reset-views', [SeamstressStatisticController::class, 'resetViews']);
    Route::get('/seamstresses/{user}/statistic', [SeamstressStatisticController::class, 'statistic']);

    Route::middleware('auth:sanctum')->group(function(){
        Route::get('/profile', [ProfileController::class, 'profile']);
        Route::get('/profile/activities', [ProfileController::class, 'activities']);
        Route::delete('/profile/delete/{user}', [ProfileController::class, 'destroy']);
        Route::post('/profile/edit', [ProfileController::class, 'edit']);
        Route::delete('/profile/image', [ProfileController::class, 'deleteImage']);
        Route::post('/profile/set-firebase-token', [ProfileController::class, 'setFirebaseToken']);

        Route::get('/profile/books', [ProfileController::class, 'books']);
        Route::post('/profile/books/edit', [ProfileController::class, 'addBook']);
        Route::post('/profile/role/switch', [ProfileController::class, 'switchRole']);
        Route::get('/profile/{user}/orders', [ProfileController::class, 'orders']);
        Route::get('/profile/{user}/orders/{order}/show', [ProfileController::class, 'order']);
        Route::post('/sizes/pdf', [UserSizeController::class, 'pdf']);
        Route::get('/profile/sizes', [UserSizeController::class, 'index']);
        Route::post('/profile/sizes/edit', [UserSizeController::class, 'update']);
        Route::get('/profile/send-code', [ProfileController::class, 'sendCode']);

        Route::get('/support/queries', [SupportController::class, 'index']);
        Route::post('/support/queries', [SupportController::class, 'create']);
        Route::post('/support/queries/{application}/response', [SupportController::class, 'response']);

        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{category}/show', [CategoryController::class, 'show']);
        // Route::get('/categories/create', [CategoryController::class, 'create']);

            Route::post('/seamstresses/portfolio/create', [SeamstressController::class, 'addImages']);
            Route::delete('/seamstresses/portfolio/{portfolio}', [SeamstressController::class, 'deletePortfolio']);

        Route::get('/order/statuses', [SeamstressController::class, 'statuses']);
        Route::get('/seamstresses', [SeamstressController::class, 'index']);
        Route::get('/customers', [SeamstressController::class, 'customers']);
        Route::post('/seamstresses/{seamstress}/orders', [SeamstressController::class, 'orders']);
        Route::get('/seamstresses/{seamstress}/orders/{order}/show', [SeamstressController::class, 'order']);
        Route::post('/seamstresses/{seamstress}/submit-measurements', [OrderController::class, 'create']);

        Route::post('/order/{order}/status/edit', [OrderController::class, 'statusChange']);
        Route::get('/order/{order}/show', [OrderController::class, 'show']);
        Route::post('/order/{order}/review', [OrderController::class, 'orderReview']);

        Route::post('/seamstresses/orders/{order}/submit', [OrderController::class, 'seamstressSubmit']);
        Route::post('/customers/orders/{order}/submit', [OrderController::class, 'customerSubmit']);

        Route::get('/order/{order}/message', [MessageController::class, 'index']);
        Route::post('/order/{order}/message/create', [MessageController::class, 'create']);
        Route::get('/order/{order}/message/update', [MessageController::class, 'update']);

        Route::post('/customers/{user}/pre-order', [OrderController::class, 'preOrder']);
        Route::post('/pre-order/{order}/confirm', [OrderController::class, 'confirm']);

        Route::get('/books', [BookController::class, 'index']);
        Route::get('/books/{book}/show', [BookController::class, 'show']);
        Route::post('/books/{book}/send', [BookController::class, 'send']);
        Route::post('/books/{book}/trial/send', [BookController::class, 'trialSend']);

        Route::get('/notepads', [NotepadController::class, 'index']);
        Route::get('/notepads/{notepad}', [NotepadController::class, 'show']);
        Route::post('/notepads/create', [NotepadController::class, 'create']);
        Route::post('/notepads/{notepad}/edit', [NotepadController::class, 'edit']);
        Route::post('/notepads/{notepad}/text/create', [NotepadController::class, 'createText']);
        Route::post('/notepads/{notepad}/text/{text}/edit', [NotepadController::class, 'editText']);
        Route::post('/notepads/{notepad}/file/create', [NotepadController::class, 'createFile']);
        Route::post('/notepads/{notepad}/file/{file}/edit', [NotepadController::class, 'updateFile']);
        Route::post('/notepads/{notepad}/files/create', [NotepadController::class, 'createFiles']);
        Route::delete('/notepads/{notepad}/text/{text}', [NotepadController::class, 'deleteText']);
        Route::delete('/notepads/{notepad}', [NotepadController::class, 'delete']);
        Route::delete('/notepads/{notepad}/files/{file}', [NotepadController::class, 'deleteFile']);

        Route::get('/notepads/{notepad}/notepad-folders', [NotepadFolderController::class, 'index']);
        Route::get('/notepads/{notepad}/notepad-folders/{notepadFolder}', [NotepadFolderController::class, 'show']);
        Route::post('/notepads/{notepad}/notepad-folders/create', [NotepadFolderController::class, 'create']);
        Route::post('/notepads/{notepad}/notepad-folders/{notepadFolder}/edit', [NotepadFolderController::class, 'edit']);
        Route::post('/notepads/{notepad}/notepad-folders/{notepadFolder}/text/create', [NotepadFolderController::class, 'createText']);
        Route::post('/notepads/{notepad}/notepad-folders/{notepadFolder}/text/{text}/edit', [NotepadFolderController::class, 'editText']);
        Route::delete('/notepads/{notepad}/notepad-folders/{notepadFolder}/text/{text}', [NotepadFolderController::class, 'deleteText']);
        Route::post('/notepads/{notepad}/notepad-folders/{notepadFolder}/file/create', [NotepadFolderController::class, 'createFile']);
        Route::post('/notepads/{notepad}/notepad-folders/{notepadFolder}/file/{file}/edit', [NotepadFolderController::class, 'updateFile']);
        Route::post('/notepads/{notepad}/notepad-folders/{notepadFolder}/files/create', [NotepadFolderController::class, 'createFiles']);
        Route::delete('/notepads/{notepad}/notepad-folders/{notepadFolder}', [NotepadFolderController::class, 'delete']);
        Route::delete('/notepads/{notepad}/notepad-folders/{notepadFolder}/files/{file}', [NotepadFolderController::class, 'deleteFile']);

        Route::post('/permissions/user/{user}/create', [PermissionController::class, 'setPermission']);
        Route::get('/permissions/notepads', [PermissionController::class, 'getNotepads']);
        Route::get('/permissions/notepads/{notepad}/show', [PermissionController::class, 'getNotepad']);
        Route::get('/permissions/notepads/{notepad}/show', [PermissionController::class, 'getNotepad']);

        Route::delete('/permissions/user/{user}/model/{model}/id/{id}', [PermissionController::class, 'delete']);

        Route::prefix('gpt')->group(function () {

            Route::post('/send-message', [GPTController::class, 'sendMessage']);

        });
});






