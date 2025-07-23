<?php
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BlogContronller;
use App\Http\Controllers\MoMoController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\VnPayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductSpecificationController;
use App\Http\Controllers\ProductDescriptionController;
use App\Http\Controllers\ProductFavoriteController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\ProductCategoryController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Tất cả route ở đây được tự động gắn prefix là /api
*/
Route::prefix('admin')->group(function () {
    Route::get('/users', [AdminUserController::class, 'getAllUsers']);
    Route::put('/update-user/{id}', [AdminUserController::class, 'updateUser']);
    Route::get('/reviews', [AdminReviewController::class, 'getUserReviews']);
    Route::delete('/delete-reviews/{id}', [AdminReviewController::class, 'deleteReview']);
    Route::get('/orders', [AdminOrderController::class, 'getOrders']);
    Route::delete('/orders/{id}', [AdminOrderController::class, 'deleteOrder']);
    Route::get('/product/smartphone', [ProductCategoryController::class, 'getSmartPhone']);
    Route::get('/product/laptop', [ProductCategoryController::class, 'getLaptop']);
    Route::get('/product/headphone', [ProductCategoryController::class, 'getHeadPhone']);
    Route::get('/product/keyboard', [ProductCategoryController::class, 'getKeyboard']);
    Route::get('/product/mouse', [ProductCategoryController::class, 'getMouse']);
    Route::get('/product/camera', [ProductCategoryController::class, 'getCamera']);
    Route::get('/product/smartwatch', [ProductCategoryController::class, 'getSmartWatch']);
    Route::get('/product/charging', [ProductCategoryController::class, 'getChargingAccessory']);
    Route::get('/product/tv', [ProductCategoryController::class, 'getTV']);
    Route::get('/product/air-conditioner', [ProductCategoryController::class, 'getAirConditioner']);
    Route::get('/products/stock-statistics', [ProductController::class, 'getStockStatistics']);
    Route::get('/group-by-category', [ProductController::class, 'getProductsGroupedByCategory']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::put('update/products/{id}', [ProductCategoryController::class, 'update']);
    Route::get('/categories', [ProductCategoryController::class, 'index']);
    Route::get('edit/products/{id}', [ProductCategoryController::class, 'show']);
    Route::post('create/products', [ProductCategoryController::class, 'store']);
    Route::get('/promotion-types', [ProductController::class, 'getPromotionTypes']);

});

// ==============================
//  PUBLIC PRODUCT ROUTES
// ==============================
Route::prefix('products')->group(function () {
    Route::get('/promoted-aboutus', [ProductController::class, 'getPromotedProducts']);
    Route::get('/list', [ProductController::class, 'getAllProduct']);
    Route::get('/top-five', [ProductController::class, 'getTopFiveProducts']);
    Route::get('/top-images', [CategoryController::class, 'GetImage']);
    Route::get('/search', [ProductController::class, 'getProductByKeyWord']);
    Route::get('/all-categories', [CategoryController::class, 'index']);


});
// ==============================
// AUTH ROUTES (Public)
// ==============================
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-otp', [AuthController::class, 'verify']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});



// ==============================
// AUTH ROUTES (Protected via Sanctum)
// ==============================

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    // Lấy thông tin người dùng đã đăng nhập
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});

// ==============================
// ADMIN ROUTES (Protected)
// ==============================
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    // Route::get('/dashboard', [AdminController::class, 'getStats']);

});

// ==============================
// USER ROUTES (Protected)
// ==============================

Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    Route::get('/cart', [ProductController::class, 'getItemInProductCartByUserId']);
    Route::put('/cart/{id}/quantity', [ProductController::class, 'updateQuantity']);
    Route::delete('/cart/{id}', [ProductController::class, 'removeCartItem']);
    Route::delete('/cart', [ProductController::class, 'emptyCart']);
    Route::post('/cart/apply-coupon', [ProductController::class, 'applyCoupon']);
    Route::post('/cart/checkout', [ProductController::class, 'checkout']);
    Route::post('/cart/add', [ProductController::class, 'addToCart']);
    Route::get('/order/confirmation', [OrderController::class, 'getConfirmationDetails']);
    Route::post('/order/confirm-payment', [OrderController::class, 'confirmPayment']);
    Route::get('/order-history', [OrderController::class, 'getOrderHistoryByDate']);

     Route::put('/orders/{orderId}/update-info', [OrderController::class, 'updateOrderInfo']);
    Route::post('/orders/create', [OrderController::class, 'createOrder']);
    Route::delete('/order-history/delete', [OrderController::class, 'deleteHistory']);
});

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/dashboard/summary', [DashboardController::class, 'getSummary']);
    Route::get('/dashboard/monthly-revenue', [DashboardController::class, 'getMonthlyRevenue']);
    Route::get('/dashboard/revenue-by-category', [DashboardController::class, 'getRevenueByCategory']);
    Route::get('/dashboard/order-status-distribution', [DashboardController::class, 'getOrderStatusDistribution']);
    Route::get('/dashboard/top-selling-products', [DashboardController::class, 'getTopSellingProducts']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/product/{productId}/detail', [ProductController::class, 'getProductDetail']);
    Route::get('/product/{productId}/related', [ProductController::class, 'getRelatedProducts']);
    Route::post('/product/add-to-cart', [ProductController::class, 'add_to_cart']);
    Route::post('/product/add-to-wishlist', [ProductController::class, 'add_to_wishlist']);
    Route::post('/product/remove-from-wishlist', [ProductController::class, 'removeFromWishlist']);
    Route::post('/product/buy-now', [ProductController::class, 'buyNow']);
    Route::get('/reviews/product/{productId}', [ReviewController::class, 'getReviewsByProduct']);
    Route::get('/specification/product/{productId}', [ProductSpecificationController::class, 'getByProductId']);
    Route::get('/description/product/{productId}', [ProductDescriptionController::class, 'getByProductId']);
    Route::post('/product/{productId}/review', [ReviewController::class, 'storeReview']);
});

Route::prefix('user')->group(function () {
    Route::get('/getUserId', [UserController::class, 'getCurrentUserId']);
    Route::get('/{id}', [UserController::class, 'getUserById']);
    Route::put('/update-profile/{id}', [UserController::class, 'updateProfile']);
    Route::put('/change-password/{id}', [UserController::class, 'changePassword']);
    Route::put('/update-avatar/{id}', [UserController::class, 'updateAvatar']);

    // Blogs
    Route::get('/blogs/index', [BlogContronller::class, 'index']);
    Route::get('/blogs/status', [BlogContronller::class, 'getStatusBlog']);
    Route::get('/blogs/categories', [CategoryController::class, 'getCategoriesByID']);
    Route::get('/blog/{id}', [BlogContronller::class, 'show']);
    // search 
    Route::get('/search/index', [SearchController::class, 'multiSearch']);
    // Products
    Route::get('/product/index', [ProductController::class, 'index']);
    Route::get('/product/promoted', [ProductController::class, 'getPromotedProducts']);
    Route::get('/product/categories', [ProductController::class, 'getProductCategories']);



    Route::get('/wishlist/{id}', [ProductFavoriteController::class, 'getUserFavorites']);
    Route::delete('/delete/wishlist/{id}', [ProductFavoriteController::class, 'destroy']);
    Route::post('/wishlist/add', [ProductFavoriteController::class, 'add']);
    // Order
    Route::post('/orders/create', [OrderController::class, 'createOrder']);
    Route::put('/orders/{orderId}/update-info', [OrderController::class, 'updateOrderInfo']);
    Route::delete('/order-history/delete', [OrderController::class, 'deleteHistory']);
    // VnPay
    Route::post('/vnpay/create-payment', [VnPayController::class, 'createPayment'])->name('vnpay.create');
    Route::get('/vnpay/return', [VnPayController::class, 'handleReturn'])->name('vnpay.return');
    Route::post('/momo/create-payment', [MoMoController::class, 'momoPayment']);
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::post('/orders/confirm-payment', [PaymentController::class, 'confirm']);
    Route::post('/orders/create', [OrderController::class, 'create']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);


    Route::get('/vnpay-payment', [VnpayController::class, 'createPayment'])->name('vnpay.payment');
    Route::get('/vnpay-return', [VnpayController::class, 'handleReturn'])->name('vnpay.return');
    Route::post('/create-payment', [VnpayController::class, 'createPayment']);
    Route::get('/vnpay-return', [VnpayController::class, 'vnpayReturn']);
});




