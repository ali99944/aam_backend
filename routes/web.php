<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\DeliveryCompanyLoginController;
use Illuminate\Support\Facades\Route;





Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('subcategories', App\Http\Controllers\Admin\SubCategoryController::class);
    Route::resource('brands', App\Http\Controllers\Admin\BrandController::class);
    Route::resource('discounts', App\Http\Controllers\Admin\DiscountController::class);
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    Route::patch('products/{product}/toggle-featured', [App\Http\Controllers\Admin\ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
    Route::resource('languages', App\Http\Controllers\Admin\LanguageController::class);
    Route::resource('seo', App\Http\Controllers\Admin\SeoController::class);
    // Route::resource('settings', App\Http\Controllers\Admin\SettingsController::class);

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('index');
        Route::put('/', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('update');
    });

    Route::prefix('locations')->name('locations.')->group(function () {
        Route::resource('currencies', App\Http\Controllers\Admin\CurrencyController::class);
    });

    Route::prefix('locations/timezones')->name('timezones.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\TimezoneController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\TimezoneController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\TimezoneController::class, 'store'])->name('store');
        Route::get('/{timezone}/edit', [App\Http\Controllers\Admin\TimezoneController::class, 'edit'])->name('edit');
        Route::put('/{timezone}', [App\Http\Controllers\Admin\TimezoneController::class, 'update'])->name('update');
        Route::delete('/{timezone}', [App\Http\Controllers\Admin\TimezoneController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('locations/cities')->name('cities.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\CityController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\CityController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\CityController::class, 'store'])->name('store');
        Route::get('/{city}/edit', [App\Http\Controllers\Admin\CityController::class, 'edit'])->name('edit');
        Route::put('/{city}', [App\Http\Controllers\Admin\CityController::class, 'update'])->name('update');
        Route::delete('/{city}', [App\Http\Controllers\Admin\CityController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('locations/countries')->name('countries.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\CountryController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\CountryController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\CountryController::class, 'store'])->name('store');
        Route::get('/{country}/edit', [App\Http\Controllers\Admin\CountryController::class, 'edit'])->name('edit');
        Route::put('/{country}', [App\Http\Controllers\Admin\CountryController::class, 'update'])->name('update');
        Route::delete('/{country}', [App\Http\Controllers\Admin\CountryController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('locations/states')->name('states.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\StateController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\StateController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\StateController::class, 'store'])->name('store');
        Route::get('/{state}/edit', [App\Http\Controllers\Admin\StateController::class, 'edit'])->name('edit');
        Route::put('/{state}', [App\Http\Controllers\Admin\StateController::class, 'update'])->name('update');
        Route::delete('/{state}', [App\Http\Controllers\Admin\StateController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('policies')->name('policies.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PolicyController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\PolicyController::class, 'create'])->name('create'); // Usually not needed
        Route::post('/', [App\Http\Controllers\Admin\PolicyController::class, 'store'])->name('store'); // Usually not needed
        Route::get('/{policy}/preview', [App\Http\Controllers\Admin\PolicyController::class, 'show'])->name('show'); // Preview Route
        Route::get('/{policy}/edit', [App\Http\Controllers\Admin\PolicyController::class, 'edit'])->name('edit');
        Route::put('/{policy}', [App\Http\Controllers\Admin\PolicyController::class, 'update'])->name('update');
        Route::delete('/{policy}', [App\Http\Controllers\Admin\PolicyController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('contact-messages')->name('contact-messages.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('index');
        Route::get('/{contactMessage}', [App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('show');
        Route::delete('/{contactMessage}', [App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('destroy');
        // Optional: Route::patch('/{contactMessage}/unread', [App\Http\Controllers\Admin\ContactMessageController::class, 'markUnread'])->name('markUnread');
    });

    Route::prefix('finance/expense-categories')->name('expense-categories.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ExpenseCategoryController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\ExpenseCategoryController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\ExpenseCategoryController::class, 'store'])->name('store');
        Route::get('/{expenseCategory}/edit', [App\Http\Controllers\Admin\ExpenseCategoryController::class, 'edit'])->name('edit');
        Route::put('/{expenseCategory}', [App\Http\Controllers\Admin\ExpenseCategoryController::class, 'update'])->name('update');
        Route::delete('/{expenseCategory}', [App\Http\Controllers\Admin\ExpenseCategoryController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('finance/expenses')->name('expenses.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ExpenseController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\ExpenseController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\ExpenseController::class, 'store'])->name('store');
        Route::get('/{expense}/edit', [App\Http\Controllers\Admin\ExpenseController::class, 'edit'])->name('edit');
        Route::put('/{expense}', [App\Http\Controllers\Admin\ExpenseController::class, 'update'])->name('update');
        Route::delete('/{expense}', [App\Http\Controllers\Admin\ExpenseController::class, 'destroy'])->name('destroy');
    });

        // --- Customer Management ---
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('index');
            // Route::get('/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('show'); // Optional show view
            Route::delete('/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('destroy');
            Route::post('/{customer}/ban', [App\Http\Controllers\Admin\CustomerController::class, 'ban'])->name('ban');
            Route::post('/{customer}/unban', [App\Http\Controllers\Admin\CustomerController::class, 'unban'])->name('unban');
            // Add routes for edit/update if you allow admins to edit customer details
            // Route::get('/{customer}/edit', [App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('edit');
            // Route::put('/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('update');
        });

        Route::prefix('logistics/delivery-companies')->name('delivery-companies.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\DeliveryCompanyController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\DeliveryCompanyController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\DeliveryCompanyController::class, 'store'])->name('store');
            Route::get('/{deliveryCompany}/edit', [App\Http\Controllers\Admin\DeliveryCompanyController::class, 'edit'])->name('edit');
            Route::put('/{deliveryCompany}', [App\Http\Controllers\Admin\DeliveryCompanyController::class, 'update'])->name('update');
            Route::delete('/{deliveryCompany}', [App\Http\Controllers\Admin\DeliveryCompanyController::class, 'destroy'])->name('destroy');
        });


        Route::prefix('logistics/delivery-personnel')->name('delivery-personnel.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\DeliveryPersonnelController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\DeliveryPersonnelController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\DeliveryPersonnelController::class, 'store'])->name('store');
            Route::get('/{deliveryPersonnel}/edit', [App\Http\Controllers\Admin\DeliveryPersonnelController::class, 'edit'])->name('edit'); // Use singular binding name
            Route::put('/{deliveryPersonnel}', [App\Http\Controllers\Admin\DeliveryPersonnelController::class, 'update'])->name('update');
            Route::delete('/{deliveryPersonnel}', [App\Http\Controllers\Admin\DeliveryPersonnelController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('logistics/delivery-fees')->name('delivery-fees.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\DeliveryFeeController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\DeliveryFeeController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\DeliveryFeeController::class, 'store'])->name('store');
            Route::get('/{deliveryFee}/edit', [App\Http\Controllers\Admin\DeliveryFeeController::class, 'edit'])->name('edit');
            Route::put('/{deliveryFee}', [App\Http\Controllers\Admin\DeliveryFeeController::class, 'update'])->name('update');
            Route::delete('/{deliveryFee}', [App\Http\Controllers\Admin\DeliveryFeeController::class, 'destroy'])->name('destroy');
        });


        Route::prefix('marketing/offers')->name('offers.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\OfferController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\OfferController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\OfferController::class, 'store'])->name('store');
            Route::get('/{offer}/edit', [App\Http\Controllers\Admin\OfferController::class, 'edit'])->name('edit');
            Route::put('/{offer}', [App\Http\Controllers\Admin\OfferController::class, 'update'])->name('update');
            Route::delete('/{offer}', [App\Http\Controllers\Admin\OfferController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('content/faq-categories')->name('faq-categories.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\FaqCategoryController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\FaqCategoryController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\FaqCategoryController::class, 'store'])->name('store');
            Route::get('/{faqCategory}/edit', [App\Http\Controllers\Admin\FaqCategoryController::class, 'edit'])->name('edit');
            Route::put('/{faqCategory}', [App\Http\Controllers\Admin\FaqCategoryController::class, 'update'])->name('update');
            Route::delete('/{faqCategory}', [App\Http\Controllers\Admin\FaqCategoryController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('content/faqs')->name('faqs.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\FaqController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\FaqController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\FaqController::class, 'store'])->name('store');
            Route::get('/{faq}/edit', [App\Http\Controllers\Admin\FaqController::class, 'edit'])->name('edit');
            Route::put('/{faq}', [App\Http\Controllers\Admin\FaqController::class, 'update'])->name('update');
            Route::delete('/{faq}', [App\Http\Controllers\Admin\FaqController::class, 'destroy'])->name('destroy');
        });


        // Route::prefix('content/store-pages')->name('store-pages.')->group(function () {
        //     Route::get('/', [App\Http\Controllers\Admin\StorePageController::class, 'index'])->name('index');
        //     Route::get('/create', [App\Http\Controllers\Admin\StorePageController::class, 'create'])->name('create');
        //     Route::post('/', [App\Http\Controllers\Admin\StorePageController::class, 'store'])->name('store');
        //     Route::get('/{storePage}/edit', [App\Http\Controllers\Admin\StorePageController::class, 'edit'])->name('edit');
        //     Route::put('/{storePage}', [App\Http\Controllers\Admin\StorePageController::class, 'update'])->name('update');
        //     Route::delete('/{storePage}', [App\Http\Controllers\Admin\StorePageController::class, 'destroy'])->name('destroy');

        //     // --- Nested Section Routes ---
        //     Route::prefix('/{storePage}/sections')->name('sections.')->group(function() {
        //          Route::post('/', [App\Http\Controllers\Admin\StorePageSectionController::class, 'store'])->name('store');
        //          Route::get('/{section}/edit', [App\Http\Controllers\Admin\StorePageSectionController::class, 'edit'])->name('edit');
        //          Route::put('/{section}', [App\Http\Controllers\Admin\StorePageSectionController::class, 'update'])->name('update');
        //          Route::delete('/{section}', [App\Http\Controllers\Admin\StorePageSectionController::class, 'destroy'])->name('destroy');
        //     });
        //     // --- End Nested Section Routes ---
        // });

        Route::prefix('store-pages')->name('store-pages.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\StorePageController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\StorePageController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\StorePageController::class, 'store'])->name('store');
            Route::get('/{storePage}', [App\Http\Controllers\Admin\StorePageController::class, 'show'])->name('show');
            Route::get('/{storePage}/edit', [App\Http\Controllers\Admin\StorePageController::class, 'edit'])->name('edit');
            Route::put('/{storePage}', [App\Http\Controllers\Admin\StorePageController::class, 'update'])->name('update');
            Route::delete('/{storePage}', [App\Http\Controllers\Admin\StorePageController::class, 'destroy'])->name('destroy');

            // Nested Section Routes
            Route::prefix('{storePage}/sections')->name('sections.')->group(function () {
                Route::get('/create', [App\Http\Controllers\Admin\StorePageSectionController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\Admin\StorePageSectionController::class, 'store'])->name('store');
                Route::get('/{section}/edit', [App\Http\Controllers\Admin\StorePageSectionController::class, 'edit'])->name('edit'); // Changed from /{section}
                Route::put('/{section}', [App\Http\Controllers\Admin\StorePageSectionController::class, 'update'])->name('update');
                Route::delete('/{section}', [App\Http\Controllers\Admin\StorePageSectionController::class, 'destroy'])->name('destroy');
            });
        });


        Route::prefix('payment-methods')->name('payment-methods.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\PaymentMethodController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\PaymentMethodController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\PaymentMethodController::class, 'store'])->name('store');
            Route::get('/{paymentMethod}/edit', [App\Http\Controllers\Admin\PaymentMethodController::class, 'edit'])->name('edit');
            Route::put('/{paymentMethod}', [App\Http\Controllers\Admin\PaymentMethodController::class, 'update'])->name('update');
            Route::delete('/{paymentMethod}', [App\Http\Controllers\Admin\PaymentMethodController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('index');
            Route::get('/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('show');
            Route::put('/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('update-status'); // Specific route for status update
            Route::delete('/{order}', [App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('destroy');
            // Add routes for creating/updating payments, deliveries, invoices related to an order if needed
        });

        Route::prefix('templates')->name('templates.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\TemplateController::class, 'index'])->name('index');
            Route::get('/preview/{templateKey}', [App\Http\Controllers\Admin\TemplateController::class, 'preview'])->name('preview');
        });


            // --- Action Request Management ---
    Route::prefix('action-requests')->name('action-requests.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ActionRequestController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\ActionRequestController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\ActionRequestController::class, 'store'])->name('store');
        // Process route (handles approve/reject) - Use POST or PATCH/PUT
        Route::post('/{actionRequest}/process', [App\Http\Controllers\Admin\ActionRequestController::class, 'process'])->name('process');
        // Route::patch('/{actionRequest}/process', [App\Http\Controllers\Admin\ActionRequestController::class, 'process'])->name('process');
        // No edit/update/show usually
        // Route::delete('/{actionRequest}', [App\Http\Controllers\Admin\ActionRequestController::class, 'destroy'])->name('destroy'); // Optional delete
    });


    Route::prefix('finance/payments')->name('payments.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('index');
        // No other actions defined for this controller
    });


    Route::prefix('finance/suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SupplierController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\SupplierController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\SupplierController::class, 'store'])->name('store');
        Route::get('/{supplier}/edit', [App\Http\Controllers\Admin\SupplierController::class, 'edit'])->name('edit');
        Route::put('/{supplier}', [App\Http\Controllers\Admin\SupplierController::class, 'update'])->name('update');
        Route::delete('/{supplier}', [App\Http\Controllers\Admin\SupplierController::class, 'destroy'])->name('destroy');
        // Add routes for balance adjustments/payments later
    });

    // --- Support Ticket Management ---
    Route::prefix('support/tickets')->name('support-tickets.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SupportTicketController::class, 'index'])->name('index');
        Route::get('/{supportTicket}', [App\Http\Controllers\Admin\SupportTicketController::class, 'show'])->name('show');
        Route::post('/{supportTicket}/reply', [App\Http\Controllers\Admin\SupportTicketController::class, 'storeReply'])->name('store-reply');
        Route::put('/{supportTicket}/details', [App\Http\Controllers\Admin\SupportTicketController::class, 'updateDetails'])->name('update-details'); // For status/priority/assignee
        Route::delete('/{supportTicket}', [App\Http\Controllers\Admin\SupportTicketController::class, 'destroy'])->name('destroy');
    });
    // --- End Support Ticket Management ---

});

Route::prefix('delivery-company')->name('delivery-company.')->group(function () {
    Route::get('/', function () {
        return view('delivery_company.dashboard', [
            'stats' => [
                'totalPersonnel' => 5,
                'activePersonnel' => 4,
                'totalDeliveries' => 100,
                'pendingDeliveries' => 20,
                'completedToday' => 15,
            ],

            'recentPersonnel' => [],
            'recentDeliveries' => [],
            'recentInvoices' => [],
            'recentPayments' => [],
            'recentSuppliers' => [],
            'recentActionRequests' => [],
        ]);
    })->name('dashboard');

});