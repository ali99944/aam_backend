<?php

use App\Http\Controllers\Api\BrandApiController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CityApiController;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\PaymentMethodApiController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\StoreContentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController; // Alias to avoid conflicts if needed
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\DeliveryFeeApiController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\FaqCategoryController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\PolicyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SeoController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\SubCategoryController;

Route::prefix('categories')->name('api.categories.')->group(function () { // Added name prefix for potential clarity
    Route::get('/', [ApiCategoryController::class, 'index'])->name('index');                // api.categories.index
    Route::get('/{id}', [ApiCategoryController::class, 'show'])->name('show');                // api.categories.index
});


// --- SubCategories API ---
Route::prefix('sub-categories')->name('api.subcategories.')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\SubCategoryController::class, 'index'])->name('index');
    Route::get('/slug/{slug}', [App\Http\Controllers\Api\SubCategoryController::class, 'byCategorySlug'])->name('index');
});
// --- End SubCategories API ---


Route::prefix('cart')->name('api.cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index'); // Get cart contents
    Route::post('/', [CartController::class, 'store'])->name('store'); // Add item
    Route::put('/items/{cartItemId}', [CartController::class, 'update'])->name('update'); // Update quantity
    Route::delete('/items/{cartItemId}', [CartController::class, 'destroy'])->name('destroy'); // Remove item
    Route::post('/clear', [CartController::class, 'clear'])->name('clear'); // Clear entire cart

    // Route to merge guest cart after login (Requires Authentication)
    Route::post('/merge', [CartController::class, 'mergeGuestCart'])
         ->middleware('auth:customer') // Use your customer guard
         ->name('merge');
});


Route::prefix('catalog')->name('api.catalog.')->group(function() {
    Route::get('/categories', [ApiCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category}/subcategories', [ApiCategoryController::class, 'subCategories'])->name('categories.subcategories');
    Route::get('/subcategories', [SubCategoryController::class, 'index'])->name('subcategories.index');
    // Add routes for Products listing etc. here
});


Route::post('/contact-messages', [ContactMessageController::class, 'store'])->name('api.contact.store');

Route::prefix('auth/customer')->name('api.customer.auth.')->group(function () {
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('register');
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('login');
    Route::post('/forgot-password', [CustomerAuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/verify-otp', [CustomerAuthController::class, 'verifyOtp'])->name('verify-otp');
    Route::post('/send-otp', [CustomerAuthController::class, 'sendOtp'])->name('send-otp');
    Route::post('/reset-password', [CustomerAuthController::class, 'resetPassword'])->name('reset-password');

    // Routes requiring authentication (using the customer sanctum guard)
    Route::middleware('auth:customer')->group(function () {
        Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
        Route::get('/user', [CustomerAuthController::class, 'user'])->name('user');
         // Add other authenticated customer routes here (e.g., update profile)
    });
});


Route::prefix('content')->name('api.content.')->group(function () {
    // Get full page with sections by page key
    Route::get('/pages/{pageKey}', [StoreContentController::class, 'getPageByKey'])->name('page.show');

    // Get a specific section directly by its unique key
    Route::get('/sections/{sectionKey}', [StoreContentController::class, 'getSectionByKey'])->name('section.show.direct');

    // Get a specific section belonging to a specific page (RECOMMENDED)
    Route::get('/pages/{pageKey}/sections/{sectionKey}', [StoreContentController::class, 'getPageSectionByKey'])->name('page.section.show');

     // Get all sections for a specific page
     Route::get('/pages/{pageKey}/sections', [StoreContentController::class, 'getPageSections'])->name('page.sections.index');
});


Route::prefix('products')->name('api.products.')->group(function() {
    Route::get('/', [ProductController::class, 'index'])->name('index'); // List products
    Route::get('/{id}', [ProductController::class, 'show'])->name('show'); // Get single product (by ID or SKU)

     Route::get('/listings/just-arrived', [ProductController::class, 'justArrived'])->name('listings.just-arrived');
    Route::get('/listings/featured', [ProductController::class, 'featured'])->name('listings.featured');
    Route::get('/listings/top-discounts', [ProductController::class, 'topDiscounts'])->name('listings.top-discounts');
    Route::get('/listings/recommended', [ProductController::class, 'recommended'])->name('listings.recommended');

    // Favorite Toggling (Requires Authentication)
    Route::post('/{product}/favorite', [ProductController::class, 'toggleFavorite'])
         ->middleware('auth:customer') // Your customer auth guard
         ->name('toggle-favorite');
});
// Route for products by sub-category (could be under catalog or products)
Route::get('/subcategories/{subCategoryId}/products', [ProductController::class, 'bySubCategory'])
     ->name('api.subcategories.products');


     Route::prefix('orders')->name('api.orders.')->group(function () {
        // Submit order request (handles guest/auth)
        Route::get('/', [OrderApiController::class, 'index'])->middleware('auth:customer')->name('index');
        Route::get('/{id}', [OrderApiController::class, 'get'])->middleware('auth:customer')->name('get');
        Route::post('/', [OrderApiController::class, 'store'])->name('store');
        // Track order using track code (public)
        Route::get('/track/{trackCode}', [OrderApiController::class, 'showByTrackCode'])->name('show.track');
        // Request cancellation (requires auth)
        Route::post('/{order}/cancel', [OrderApiController::class, 'requestCancellation'])
             ->middleware('auth:customer') // Use your customer guard
             ->name('cancel.request');
    });

    Route::get('/cities', [CityApiController::class, 'index'])->name('api.cities.index');
    Route::get('/brands', [BrandController::class, 'index'])->name('api.brands.index');
    Route::get('/languages', [LanguageController::class, 'index'])->name('api.languages.index');
    // Consider if listing all active discounts is desired publicly
    Route::get('/discounts', [DiscountController::class, 'index'])->name('api.discounts.index');Route::get('/payment-methods', [PaymentMethodApiController::class, 'index'])->name('api.payment-methods.index');
Route::get('/delivery-fees', [DeliveryFeeApiController::class, 'index'])->name('api.delivery-fees.index');
Route::get('/offers', [OfferController::class, 'index'])->name('api.delivery-fees.index');


Route::prefix('profile')->name('api.profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show'); // Get profile
    Route::post('/', [ProfileController::class, 'update'])->name('update'); // Update profile (use POST for file uploads)
    // Route::put('/', [ProfileController::class, 'update'])->name('update'); // Use PUT if not uploading files
    Route::put('/password', [ProfileController::class, 'changePassword'])->name('password.change'); // Change password
})->middleware('auth:sanctum,customer');




Route::prefix('support/tickets')->name('support-tickets.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\SupportTicketController::class, 'index'])->name('index');
    Route::get('/{supportTicket}', [App\Http\Controllers\Admin\SupportTicketController::class, 'show'])->name('show');
    Route::post('/{supportTicket}/reply', [App\Http\Controllers\Admin\SupportTicketController::class, 'storeReply'])->name('store-reply');
    Route::put('/{supportTicket}/details', [App\Http\Controllers\Admin\SupportTicketController::class, 'updateDetails'])->name('update-details'); // For status/priority/assignee
    Route::delete('/{supportTicket}', [App\Http\Controllers\Admin\SupportTicketController::class, 'destroy'])->name('destroy');
});

Route::prefix('seo')->name('seo.')->group(function () {
    Route::get('/', [SeoController::class, 'index'])->name('api.seo.show');
    Route::get('/{key}', [SeoController::class, 'getSeoByKey'])->name('api.seo.show');
});

Route::prefix('faq-categories')->name('faq-categories.')->group(function () {
    Route::get('/', [FaqCategoryController::class, 'index'])->name('api.faq.categories');
    Route::get('/{key}/faqs', [FaqController::class, 'index'])->name('api.faq.single');
});


Route::middleware('auth:sanctum')->prefix('support/tickets')->name('api.support-tickets.')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\SupportTicketApiController::class, 'index'])->name('index'); // List user's tickets
    Route::post('/', [App\Http\Controllers\Api\SupportTicketApiController::class, 'store'])->name('store'); // Create new ticket
    Route::get('/{supportTicket}', [App\Http\Controllers\Api\SupportTicketApiController::class, 'show'])->name('show'); // View single ticket + replies
    Route::post('/{supportTicket}/reply', [App\Http\Controllers\Api\SupportTicketApiController::class, 'storeReply'])->name('store-reply'); // Add reply
});


Route::get('/settings', [SettingsController::class, 'index'])->name('api.settings.index');

Route::prefix('policies')->name('api.policies.')->group(function() {
    Route::get('/', [PolicyController::class, 'index'])->name('index'); // List policies
    Route::get('/{policyKey}', [PolicyController::class, 'show'])->name('show'); // Get single policy by key
});

// --- Public Frontend Endpoints ---
Route::get('/banners', [App\Http\Controllers\Api\BannerController::class, 'index'])->name('api.banners.index');
// --- Public Frontend Endpoints ---
Route::get('/testimonials', [App\Http\Controllers\Api\TestimonialController::class, 'index'])->name('api.testimonials.index');