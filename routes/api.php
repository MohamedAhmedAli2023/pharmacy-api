<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PrescriptionController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\SettingsController;
use App\Http\Controllers\API\MedicineController;
use App\Http\Controllers\API\OrderTrackingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| All routes are prefixed with /api/
*/

// Public Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Protected Routes (require JWT authentication)
Route::middleware(['jwt.auth'])->group(function () {
    // Auth Routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });

    // User Management
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']); // List users (admin)
        Route::get('/{id}', [UserController::class, 'show']); // View user
        Route::put('/{id}', [UserController::class, 'update']); // Update user
        Route::delete('/{id}', [UserController::class, 'destroy']); // Delete user
    });

    // Medicine Routes
    Route::prefix('medicines')->group(function () {
        Route::post('/', [MedicineController::class, 'store'])->middleware('role:pharmacist');
        Route::get('/', [MedicineController::class, 'index']);
        Route::get('/{id}', [MedicineController::class, 'show']);
        Route::put('/{id}', [MedicineController::class, 'update'])->middleware('role:pharmacist');
        Route::delete('/{id}', [MedicineController::class, 'destroy'])->middleware('role:pharmacist');
    });

    // Order Management

        Route::apiResource('orders', OrderController::class);
        Route::prefix('orders')->group(function () {
        Route::post('/from-cart', [OrderController::class, 'storeFromCart']);
        Route::post('/{id}/confirm', [OrderController::class, 'confirm']);
        Route::put('/{id}/status', [OrderTrackingController::class, 'updateStatus'])->middleware('role:pharmacist');
        Route::get('/{id}/track', [OrderTrackingController::class, 'track']);
     });

    // Cart Management
    Route::apiResource('carts', CartController::class);
    // Payment Management
    Route::prefix('payments')->group(function () {
        Route::post('/{order_id}', [PaymentController::class, 'store']);
        Route::get('/{id}', [PaymentController::class, 'show']);
        Route::post('/{id}/refund', [PaymentController::class, 'refund']);
    });
    // Temporary for testing Stripe redirects
    Route::get('/payments/success', [PaymentController::class, 'success']);
    Route::get('/payments/cancel', [PaymentController::class, 'cancel']);

    // Notification Management
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']); // List notifications
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']); // Mark as read
    });

    // Prescription Management
    Route::prefix('prescriptions')->group(function () {
        Route::post('/', [PrescriptionController::class, 'store']); // Upload prescription
        Route::get('/{id}', [PrescriptionController::class, 'show']); // View prescription
        Route::put('/{id}/review', [PrescriptionController::class, 'review']); // Review by pharmacist
    });
    // Report Management (Dashboard)
    Route::prefix('reports')->group(function () {
        Route::get('/sales', [ReportController::class, 'salesReport']); // Sales stats
        Route::get('/orders', [ReportController::class, 'orders']); // Order stats
        Route::get('/financials', [ReportController::class, 'financials']); // Financial reports
    });

    // Settings Management
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index']); // List settings
        Route::put('/{key}', [SettingsController::class, 'update']); // Update setting
    });
});
