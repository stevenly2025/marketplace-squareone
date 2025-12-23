<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Buyer\OrderController as BuyerOrderController;
use App\Http\Controllers\Buyer\ReviewController;
use App\Http\Controllers\Buyer\BuyerDashboardController;
use App\Http\Controllers\Seller\SellerDashboardController;
use App\Http\Controllers\Seller\OrderManagementController;
use App\Http\Controllers\Seller\ReportController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\SellerRegisterController;
use App\Http\Middleware\CheckProfileComplete;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SquareOne Web Routes
|--------------------------------------------------------------------------
*/

// --- GUEST ROUTES (Bisa diakses tanpa login) ---
Route::get('/', [HomeController::class, 'index'])->name('home');

// Detail Produk SquareOne (Sorting Review Foto Prioritas)
Route::get('/product/{slug}', function ($slug) {
    $product = \App\Models\Product::where('slug', $slug)
        ->with(['seller', 'category', 'reviews.user'])
        ->withCount('reviews')
        ->withAvg('reviews', 'rating')
        ->withSum('orderItems', 'quantity')
        ->firstOrFail();

    // SORTING MANUAL: Review dengan gambar duluan, baru tanggal terbaru
    $sortedReviews = $product->reviews->sortByDesc(function ($review) {
        return [$review->image ? 1 : 0, $review->created_at];
    });

    $product->setRelation('reviews', $sortedReviews);

    return view('products.show', compact('product'));
})->name('products.show');


// 🔥 ROUTE KHUSUS REGISTRASI SELLER (Hanya untuk Tamu/Guest) 🔥
Route::middleware('guest')->group(function () {
    Route::get('seller/register', [SellerRegisterController::class, 'create'])->name('seller.register');
    Route::post('seller/register', [SellerRegisterController::class, 'store']);
});


// --- AUTH ROUTES (Wajib Login & Tidak Di-ban) ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    // ✅ TRAFFIC POLICE ROUTE (Redirect Sesuai Role)
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->role === 'superadmin') {
            return redirect()->route('admin.dashboard');
        } 
        elseif ($user->role === 'seller') {
            return redirect()->route('seller.dashboard'); 
        } 
        else {
            return redirect()->route('buyer.dashboard');
        }
    })->name('dashboard');

    // Chat System
    Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
    Route::get('/chats/{userId}', [ChatController::class, 'show'])->name('chats.show');
    Route::post('/chats', [ChatController::class, 'store'])->name('chats.store');
    Route::patch('/chats/{chat}/read', [ChatController::class, 'markAsRead'])->name('chats.read');

    // Pengaturan Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

    // ==========================================
    // --- BUYER ROUTES (Khusus Pembeli) ---
    // ==========================================
    Route::middleware(['role:buyer'])->prefix('buyer')->name('buyer.')->group(function () {
        Route::get('/dashboard', [BuyerDashboardController::class, 'index'])->name('dashboard');

        // Keranjang - ✅ DIPERBAIKI: Tambah route cadangan cart.store
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add/{productId}', [CartController::class, 'store'])->name('cart.add');
        Route::post('/cart/store', [CartController::class, 'store'])->name('cart.store'); // ✅ Route cadangan
        Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/destroy/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
        
        // Checkout System - ✅ DIPERBAIKI: Urutan route & direct checkout
        Route::middleware([CheckProfileComplete::class])->group(function () {
            // ✅ Direct Checkout (Beli Sekarang) - HARUS DI ATAS checkout.index
            Route::post('/checkout/direct', [CheckoutController::class, 'direct'])->name('checkout.direct');
            
            // ✅ Regular Checkout (Dari Keranjang)
            Route::match(['get', 'post'], '/checkout', [CheckoutController::class, 'index'])->name('checkout.index'); 
            
            // ✅ Process Payment
            Route::post('/checkout/process', [CheckoutController::class, 'store'])->name('checkout.store'); 
        });

        // Orders - ✅ DIPERBAIKI: Tambah route cancel order
        Route::get('/orders', [BuyerOrderController::class, 'index'])->name('orders');
        Route::get('/orders/{id}', [BuyerOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{id}/complete', [CheckoutController::class, 'complete'])->name('orders.complete');
        Route::post('/orders/{id}/cancel', [BuyerOrderController::class, 'cancel'])->name('orders.cancel'); // ✅ DITAMBAHKAN
        Route::delete('/orders/{id}', [BuyerOrderController::class, 'destroy'])->name('orders.destroy');
        
        Route::post('/review/store', [ReviewController::class, 'store'])->name('review.store');
    });

    // ==========================================
    // --- SELLER ROUTES (Khusus Penjual/Toko) ---
    // ==========================================
    Route::middleware(['role:seller'])->prefix('seller')->name('seller.')->group(function () {
        
        Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', ProductController::class);
        
        Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders');
        Route::get('/orders/{id}', [OrderManagementController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}', [OrderManagementController::class, 'updateStatus'])->name('orders.update');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });

    // ==========================================
    // --- SUPERADMIN ROUTES (Admin Utama) ---
    // ==========================================
    Route::middleware(['role:superadmin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('categories', CategoryController::class);
        Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
        Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
        Route::delete('/banners/{id}', [BannerController::class, 'destroy'])->name('banners.destroy');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/ban', [UserController::class, 'toggleBan'])->name('users.ban');

        Route::post('/reset-transactions', function () {
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
            \App\Models\Order::truncate();          
            \App\Models\OrderItem::truncate();      
            \App\Models\Review::truncate();         
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            return back()->with('success', '🧹 Data Penjualan & Review BERHASIL dikosongkan!');
        })->name('reset.transactions');
    });
});

require __DIR__.'/auth.php';