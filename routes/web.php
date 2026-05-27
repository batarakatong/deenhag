<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\FilePreviewController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\WahaWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/products', [StorefrontController::class, 'products'])->name('products.index');
Route::get('/products/{product:slug}', [StorefrontController::class, 'product'])->name('products.show');
Route::post('/products/{product:slug}/calculate', [StorefrontController::class, 'calculate'])->name('products.calculate');
Route::get('/track-order', [StorefrontController::class, 'track'])->name('track');
Route::post('/webhooks/waha', WahaWebhookController::class)->name('webhooks.waha');
Route::get('/track-order/files/{file}', [FilePreviewController::class, 'publicOrderFile'])->name('track.files.show');
Route::get('/media/{path}', [FilePreviewController::class, 'media'])->where('path', '.*')->name('media.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});
Route::get('/two-factor-challenge', [AuthController::class, 'challenge'])->name('2fa.challenge');
Route::post('/two-factor-challenge', [AuthController::class, 'verifyTwoFactor'])->name('2fa.verify');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/logout-now', [AuthController::class, 'logout'])->middleware('auth')->name('logout.get');

Route::middleware('auth')->group(function () {
    Route::get('/files/cart-items/{item}/design', [FilePreviewController::class, 'cartDesign'])->name('files.cart-design');
    Route::get('/files/order-files/{file}', [FilePreviewController::class, 'orderFile'])->name('files.order-file');
});

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/dashboard', [CustomerOrderController::class, 'dashboard'])->name('customer.dashboard');
    Route::get('/account', [CustomerProfileController::class, 'edit'])->name('customer.profile.edit');
    Route::patch('/account', [CustomerProfileController::class, 'update'])->name('customer.profile.update');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product:slug}', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/items/{item}', [CartController::class, 'destroy'])->name('cart.items.destroy');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('customer.orders.index');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    Route::post('/orders/{order}/payment-proof', [CustomerOrderController::class, 'uploadProof'])->name('customer.payments.proof');
    Route::post('/orders/{order}/revision-file', [CustomerOrderController::class, 'uploadRevision'])->name('customer.orders.revision-file');
    Route::get('/notifications', [CustomerOrderController::class, 'notifications'])->name('customer.notifications.index');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('orders/{order}/revision-file', [AdminOrderController::class, 'uploadRevision'])->name('orders.revision-file');
    Route::get('orders/{order}/service-order', [AdminOrderController::class, 'serviceOrderPrint'])->name('orders.service-order');
    Route::get('orders/{order}/invoice.pdf', [AdminOrderController::class, 'invoicePdf'])->name('orders.invoice-pdf');
    Route::get('payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::post('payments/{payment}/confirm', [AdminPaymentController::class, 'confirm'])->name('payments.confirm');
    Route::post('payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');
    Route::resource('materials', MaterialController::class)->only(['index', 'store', 'update']);
    Route::post('materials/{material}/adjust', [MaterialController::class, 'adjust'])->name('materials.adjust');
    Route::get('production', [ProductionController::class, 'index'])->name('production.index');
    Route::patch('production/orders/{order}', [ProductionController::class, 'updateOrder'])->name('production.orders.update');
    Route::get('production/steps', [ProductionController::class, 'steps'])->name('production.steps');
    Route::post('production/steps', [ProductionController::class, 'storeStep'])->name('production.steps.store');
    Route::patch('production/steps/{step}', [ProductionController::class, 'updateStep'])->name('production.steps.update');
    Route::delete('production/steps/{step}', [ProductionController::class, 'destroyStep'])->name('production.steps.destroy');
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/stocks', [ReportController::class, 'stocks'])->name('reports.stocks');
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class)->only(['index', 'create', 'store', 'show', 'update']);
    Route::post('customers/{customer}/reset-password', [\App\Http\Controllers\Admin\CustomerController::class, 'resetPassword'])->name('customers.reset-password');
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/waha-test', [SettingController::class, 'testWaha'])->name('settings.waha-test');
    Route::get('about-greentech', [SettingController::class, 'about'])->name('settings.about');
    Route::get('exports/products', [ExportController::class, 'products'])->name('exports.products');
    Route::get('exports/categories', [ExportController::class, 'categories'])->name('exports.categories');
    Route::get('exports/orders', [ExportController::class, 'orders'])->name('exports.orders');
    Route::get('exports/payments', [ExportController::class, 'payments'])->name('exports.payments');
    Route::get('exports/materials', [ExportController::class, 'materials'])->name('exports.materials');
});
