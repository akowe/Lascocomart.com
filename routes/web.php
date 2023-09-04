<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FcmgProductController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController; 
use App\Http\Controllers\CardPaymentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderItem;
use App\Models\Product;
use App\Models\FcmgProduct;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\CooperativeController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\RatingController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Auth\CoopController;
use App\Http\Controllers\Auth\SellerController;
use App\Http\Controllers\Auth\LoginController;
 
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
Auth::routes(['verify' => true]);
Route::get('/', function () {
    return view('index');
});
 //Clear route cache
 Route::get('/route-clear', function() {
    \Artisan::call('route:clear');
    return 'Routes cache cleared';
});

//Clear config cache
Route::get('/config-clear', function() {
    \Artisan::call('config:clear');
    return 'Config cache cleared';
}); 

// Clear application cache
Route::get('/cache-clear', function() {
    \Artisan::call('cache:clear');
    return 'Application cache cleared';
});

// Clear view cache
Route::get('/view-clear', function() {
    \Artisan::call('view:clear');
    return 'View cache cleared';
});

// Clear cache using reoptimized class
Route::get('/optimize-clear', function() {
    \Artisan::call('optimize:clear');
    return 'View cache cleared';
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/')->with('success','Verification successful');
})->middleware(['auth', 'signed'])->name('verification.verify');

//register users
Route::get('cooperative-register', [App\Http\Controllers\Auth\CoopController::class, 'registerCooperative'])->name('cooperative-register');
Route::get('member-register', [App\Http\Controllers\Auth\CoopController::class, 'registerMember'])->name('member-register');
Route::post('create-member', [App\Http\Controllers\Auth\CoopController::class, 'createMember'])->name('create-member');
Route::get('seller-register', [App\Http\Controllers\Auth\SellerController::class, 'registerSeller'])->name('seller-register');

//Users authentication on login pages for all admins
Route::get('superadmin', [App\Http\Controllers\SuperAdminController::class, 'index'])->name('superadmin');
Route::get('cooperative', [App\Http\Controllers\CooperativeController::class, 'index'])->name('cooperative');
Route::get('merchant', [App\Http\Controllers\MerchantController::class, 'index'])->name('merchant');
Route::get('dashboard', [App\Http\Controllers\MembersController::class, 'index'])->name('dashboard');
Route::get('fcmg', [App\Http\Controllers\FcmgController::class, 'index'])->name('fcmg');

Route::middleware(['auth'])->group(function (){
Route::get('add-review/{product_id}/userreview', [ReviewController::class, 'add']); 
Route::post('add-review', [ReviewController::class, 'create']);
});
//product
Route::get('/', [App\Http\Controllers\ProductController::class, 'index']);  
Route::get('cart', [App\Http\Controllers\ProductController::class, 'cart'])->name('cart');
Route::get('add-to-cart/{id}', [App\Http\Controllers\ProductController::class, 'addToCart'])->name('add.to.cart');
Route::get('add-to-wish/{id}', [App\Http\Controllers\ProductController::class, 'addWishList'])->name('add.to.wish');
Route::get('wishlist', [App\Http\Controllers\ProductController::class, 'wishlist'])->name('wishlist');
Route::post('remove-from-wish', [App\Http\Controllers\ProductController::class, 'removeWishlist'])->name('remove.from.wish');

Route::post('update-cart', [App\Http\Controllers\ProductController::class, 'update'])->name('update.cart');
Route::post('remove-from-cart', [App\Http\Controllers\ProductController::class, 'remove'])->name('remove.from.cart');
Route::get('/category/', [App\Http\Controllers\CategoriesController::class,'category'])->name('category');
Route::match(['get', 'post'],'checkout', [App\Http\Controllers\ProductController::class, 'checkout']); 
Route::get('confirm_order',[App\Http\Controllers\OrderController::class, 'confirm_order'])->name('confirm_order');
Route::post('order', [App\Http\Controllers\OrderController::class, 'order'])->name('order'); 
//cancel an order.
Route::post('/cancel_order', [App\Http\Controllers\MembersController::class, 'cancel_order'])->name('cancel_order');
// from  product preview page 
Route::get('add-cart/{id}', [ProductController::class, 'addToCartPreview'])->name('add.cart');
Route::get('preview/{prod_name}', [App\Http\Controllers\ProductController::class, 'preview'])->name('preview');
// view cooperative members
Route::get('members', [App\Http\Controllers\CooperativeController::class, 'members'])->name('members');
Route::delete('cooperative/member/delete', [App\Http\Controllers\CooperativeController::class, 'deleteMember'])->name('delete_member');
// view cooperative members
Route::get('fcmgmembers', [App\Http\Controllers\FcmgController::class, 'fcmgmembers'])->name('fcmgmembers');
// add credit for members
Route::post('/credit_limit', [App\Http\Controllers\VoucherController::class, 'credit_limit'])->name('credit_limit');
Route::post('limit', [App\Http\Controllers\VoucherController::class, 'limit'])->name('limit');
//cooperative admin see's invoice of his/her members only
Route::get('invoice/{order_number}', [App\Http\Controllers\CooperativeController::class, 'invoice'])->name('invoice');
//members see's their own invoice 
Route::get('member_invoice/{order_number}', [App\Http\Controllers\MembersController::class, 'member_invoice'])->name('member_invoice');
//Seller admin see's invoice of  paid order
Route::get('order_invoice/{order_number}', [App\Http\Controllers\MerchantController::class, 'invoice'])->name('order_invoice');
//Super admin  see's all invoice 
Route::get('sales_invoice/{order_number}', [App\Http\Controllers\SuperAdminController::class, 'sales_invoice'])->name('sales_invoice');
Route::get('order/{order_number}', [App\Http\Controllers\SuperAdminController::class, 'order_details'])->name('order_details');
Route::get('sales-details', [App\Http\Controllers\SuperAdminController::class, 'salesDetails'])->name('sales-details');
// Super mark an order as paid
Route::post('/mark_paid', [App\Http\Controllers\SuperAdminController::class, 'mark_paid'])->name('mark_paid');
Route::get('products_list', [App\Http\Controllers\SuperAdminController::class, 'products_list'])->name('products_list');
Route::get('removed_product', [App\Http\Controllers\SuperAdminController::class, 'removed_product'])->name('removed_product');
Route::get('users_list', [App\Http\Controllers\SuperAdminController::class, 'users_list'])->name('users_list');
Route::put('user_update/{id}', [App\Http\Controllers\SuperAdminController::class, 'user_update'])->name('user_update');
Route::get('user_edit/{id}', [App\Http\Controllers\SuperAdminController::class, 'user_edit'])->name('user_edit');

Route::get('transactions', [App\Http\Controllers\SuperAdminController::class, 'transactions'])->name('transactions');
// Super mark an order as paid
Route::post('/approved', [App\Http\Controllers\SuperAdminController::class, 'approved'])->name('approved');
Route::post('allocate_fund', [App\Http\Controllers\SuperAdminController::class, 'allocateFund'])->name('allocate_fund');
Route::get('order-history', [App\Http\Controllers\SuperAdminController::class, 'orderHistory'])->name('order-history');
Route::get('funds-allocated', [App\Http\Controllers\SuperAdminController::class, 'fundsAllocated'])->name('funds-allocated');
// paystack integration
// Route::post('/pay', [App\Http\Controllers\PaymentController::class, 'redirectToGateway'])->name('pay');
// // paystack callback url
// Route::get('/payment/callback',  [App\Http\Controllers\PaymentController::class, 'handleGatewayCallback']);
Route::post('/pay', [App\Http\Controllers\CardPaymentController::class, 'redirectToGateway'])->name('pay');
// paystack callback url
Route::get('/payment/callback',  [App\Http\Controllers\CardPaymentController::class, 'handleGatewayCallback']);
// paystack integration
Route::post('/fcmgpay', [App\Http\Controllers\FcmgPaymentController::class, 'redirectToGateway'])->name('fcmgpay');
//merchant upload product
Route::get('product', [App\Http\Controllers\MerchantController::class, 'product'])->name('product');
Route::post('upload-image', [App\Http\Controllers\MerchantController::class, 'store']);
Route::get('all-products', [App\Http\Controllers\MerchantController::class, 'allProducts'])->name('all-products');
//soft delete.
Route::post('/remove_product', [App\Http\Controllers\MerchantController::class, 'remove_product'])->name('remove_product');
Route::get('sales_preview', [App\Http\Controllers\MerchantController::class, 'sales_preview'])->name('sales_preview');
//Fcmg upload product
Route::get('fcmgproduct', [App\Http\Controllers\FcmgController::class, 'fcmgproduct'])->name('fcmgproduct');
Route::post('fcmgupload-image', [App\Http\Controllers\FcmgController::class, 'fcmgstore']);
Route::get('fcmgall_products', [App\Http\Controllers\FcmgController::class, 'fcmgall_products'])->name('fcmgall_products');
//soft delete.
Route::post('/fcmgremove_product', [App\Http\Controllers\FcmgController::class, 'fcmgremove_product'])->name('fcmgremove_product');
Route::get('fcmgsales_preview', [App\Http\Controllers\FcmgController::class, 'fcmgsales_preview'])->name('fcmgsales_preview');
//Cooperative upload product
Route::get('add_new_product', [App\Http\Controllers\CooperativeController::class, 'addProduct'])->name('add_new_product');
Route::get('credit', [App\Http\Controllers\CooperativeController::class, 'members'])->name('credit');
Route::post('coopupload-image', [App\Http\Controllers\CooperativeController::class, 'coopstore']);
Route::get('coopall_products', [App\Http\Controllers\CooperativeController::class, 'coopall_products'])->name('coopall_products');
//soft delete.
Route::post('/coopremove_product', [App\Http\Controllers\CooperativeController::class, 'coopremove_product'])->name('coopremove_product');
Route::get('coopsales_preview', [App\Http\Controllers\CooperativeController::class, 'coopsales_preview'])->name('coopsales_preview');
Route::get('fcmgproductsview', [App\Http\Controllers\CooperativeController::class, 'fcmgproductsview'])->name('fcmgproductsview');
Route::get('fcmgcart', [CooperativeController::class, 'fcmgcart'])->name('fcmgcart');
Route::get('fcmgaddToCart/{id}', [CooperativeController::class, 'fcmgaddToCart'])->name('fcmgaddToCart');
//Route::patch('fcmg-update-cart', [CooperativeController::class, 'fcmgupdate'])->name('fcmg-update.cart');
Route::delete('fcmg-remove-cart', [CooperativeController::class, 'fcmgremove'])->name('fcmg.remove.cart');
Route::match(['get', 'post'],'fcmgcheckout', [CooperativeController::class, 'fcmgcheckout']); 
Route::get('fcmgconfirm_order',[OrderController::class, 'fcmgconfirm_order'])->name('fcmgconfirm_order');
Route::post('fcmgorder', [OrderController::class, 'fcmgorder'])->name('fcmgorder'); 
Route::get('about', [App\Http\Controllers\ProductController::class, 'about_us'])->name('about');

Route::put('about_update/{id}', [App\Http\Controllers\SuperAdminController::class, 'about_update'])->name('about_update');
Route::get('about_edit/{id}', [App\Http\Controllers\SuperAdminController::class, 'about_edit'])->name('about_edit');
Route::get('about_us', [App\Http\Controllers\SuperAdminController::class, 'about'])->name('about_us');
//privacy page 
Route::get('privacy', [App\Http\Controllers\SuperAdminController::class, 'privacy'])->name('privacy');
Route::get('privacy_edit/{id}', [App\Http\Controllers\SuperAdminController::class, 'privacy_edit'])->name('privacy_edit');
Route::put('privacy_update/{id}', [App\Http\Controllers\SuperAdminController::class, 'privacy_update'])->name('privacy_update');
Route::get('privacy_policy', [App\Http\Controllers\ProductController::class, 'privacy_policy'])->name('privacy_policy');
//refund page
Route::get('refund', [App\Http\Controllers\SuperAdminController::class, 'refund'])->name('refund');
Route::get('refund_edit/{id}', [App\Http\Controllers\SuperAdminController::class, 'refund_edit'])->name('refund_edit');
Route::put('refund_update/{id}', [App\Http\Controllers\SuperAdminController::class, 'refund_update'])->name('refund_update');
Route::get('return_policy', [App\Http\Controllers\ProductController::class, 'return_policy'])->name('return_policy');
//terms and condition page
Route::get('tandc', [App\Http\Controllers\SuperAdminController::class, 'tandc'])->name('tandc');
Route::get('tandc_edit/{id}', [App\Http\Controllers\SuperAdminController::class, 'tandc_edit'])->name('tandc_edit');
Route::put('tandc_update/{id}', [App\Http\Controllers\SuperAdminController::class, 'tandc_update'])->name('tandc_update');
Route::get('terms', [App\Http\Controllers\ProductController::class, 'terms'])->name('terms');
//update profile
Route::get('profile', [App\Http\Controllers\MembersController::class, 'profile'])->name('profile');
Route::post('/update_profile', [App\Http\Controllers\MembersController::class, 'update_profile'])->name('update_profile');
Route::post('/update_profile_image', [App\Http\Controllers\MembersController::class, 'updateProfileImage'])->name('update_profile_image');
Route::post('/update_certificate', [App\Http\Controllers\MembersController::class, 'updateCertificate'])->name('update_certificate');

Route::post('newsletter', [App\Http\Controllers\NewsletterController::class, 'store']);
Route::get('subscribers', [App\Http\Controllers\NewsletterController::class, 'subscribers'])->name('subscribers');
Route::post('coop_insert', [App\Http\Controllers\Auth\CoopController::class, 'coop_insert'])->name('coop_insert');
Route::post('fcmgs_insert', [App\Http\Controllers\Auth\FcmgsController::class, 'fcmgs_insert'])->name('fcmgs_insert');
Route::post('seller_insert', [App\Http\Controllers\Auth\SellerController::class, 'seller_insert'])->name('seller_insert');
// Route::get('payout', [App\Http\Controllers\MerchantController::class, 'payout'])->name('payout');
//Route::get('/addcredit',  [App\Http\Controllers\VoucherController::class, 'load_wallet'])->name('addcredit');
Route::post('/addcredit',  [App\Http\Controllers\VoucherController::class, 'load_wallet'])->name('addcredit');
//Route::get('/payout',  [App\Http\Controllers\VoucherController::class, 'withdraw'])->name('payout');
Route::post('/payout',  [App\Http\Controllers\VoucherController::class, 'withdraw'])->name('payout');
Route::get('request_fund', [App\Http\Controllers\VoucherController::class, 'requestFund'])->name('request_fund');    
//in app notification
Route::post('request_fund_wallet', [App\Http\Controllers\FundRequestController::class,'fundRequest'])->name('request_fund_wallet');
Route::get('/mark-as-read', [App\Http\Controllers\FundRequestController::class,'markAllNotificationAsRead'])->name('mark-as-read');
Route::get('/read/{id}', [App\Http\Controllers\FundRequestController::class,'readNotification'])->name('read');
Route::get('/fundrequest', [App\Http\Controllers\FundRequestController::class,'fundrequest'])->name('fundrequest');
Route::post('member_request_fund_wallet', [App\Http\Controllers\FundRequestController::class, 'memberFundWallet'])->name('member_request_fund_wallet'); 
Route::get('/new-product', [App\Http\Controllers\NotificationController::class,'allNewProductNotification'])->name('new-product');
Route::get('/read-product/{id}', [App\Http\Controllers\NotificationController::class,'readAProductNotification'])->name('read-product');
Route::post('order-delivery/{id}', [App\Http\Controllers\NotificationController::class, 'orderDelivered'])->name('order-delivery');
Route::get('/product-delivered', [App\Http\Controllers\NotificationController::class,'allProductDeliveredNotification'])->name('product-delivered');
Route::get('/read-product-delivered/{id}', [App\Http\Controllers\NotificationController::class,'readAProductDeliveredNotification'])->name('read-product-delivered');
Route::post('order-received/{id}', [App\Http\Controllers\NotificationController::class, 'orderReceived'])->name('order-received');
Route::get('/product-received', [App\Http\Controllers\NotificationController::class,'allProductReceivedNotification'])->name('product-received');
Route::get('/read-product-received/{id}', [App\Http\Controllers\NotificationController::class,'readAProductReceivedNotification'])->name('read-product-received');
Route::get('/read-all-payment', [App\Http\Controllers\NotificationController::class,'allNewCardPaymentNotification'])->name('read-all-payment');
Route::get('/read-seller-payment/{id}', [App\Http\Controllers\NotificationController::class,'readACardPaymentNotification'])->name('read-seller-payment');
Route::get('/read-company-payment/{id}', [App\Http\Controllers\NotificationController::class,' '])->name('read-company-payment');
Route::get('/read-all-order', [App\Http\Controllers\NotificationController::class,'allNewOrderNotification'])->name('read-all-order');
Route::get('/read-admin-order/{id}', [App\Http\Controllers\NotificationController::class,'readAnOrderNotification'])->name('read-admin-order');
Route::get('/read-company-order/{id}', [App\Http\Controllers\NotificationController::class,'readAnOrderSuperadmin'])->name('read-company-order');
Route::get('/read-all-cancel-order', [App\Http\Controllers\NotificationController::class,'AdminCancelOrderNotification'])->name('read-all-cancel-order');
Route::get('/read-cancel-order/{id}', [App\Http\Controllers\NotificationController::class,'readAdminCancelOrderNotification'])->name('read-cancel-order');

//Bank transfer
Route::get('bank-payment', [App\Http\Controllers\BankTransferController::class,'bankPayment'])->name('bank-payment');
Route::post('bank-transfer', [App\Http\Controllers\BankTransferController::class,'bankTransfer'])->name('bank-transfer');
Route::get('payment-bank-tranfer/{reference}/{order_id}/{order_amount}', [App\Http\Controllers\BankTransferController::class,'banTransferPayment']);
Route::get('admin-order-history', [App\Http\Controllers\CooperativeController::class, 'adminOrderHistory'])->name('admin-order-history');
Route::get('admin-products', [App\Http\Controllers\CooperativeController::class, 'adminProducts'])->name('admin-products');
Route::post('/order-update', [CooperativeController::class, 'approveOrder'])->name('order-update');
Route::get('cancel-new-order/{id}', [CooperativeController::class, 'cancelMemberNewOrder'])->name('cancel-new-order');
Route::post('/order-cancel', [CooperativeController::class, 'cancelOrder'])->name('order-cancel');
Route::get('view-canceled-orders', [App\Http\Controllers\CooperativeController::class, 'viewCanceledOrders'])->name('view-canceled-orders');
Route::get('autocomplete', [CategoriesController::class,'autocomplete'])->name('autocomplete');
