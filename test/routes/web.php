<?php

// BE CONTROLLER
use App\Http\Controllers\Admin_DashboardController;
use App\Http\Controllers\Admin_CategoryController;
use App\Http\Controllers\Admin_OrderController;
use App\Http\Controllers\Admin_OrderItemController;
use App\Http\Controllers\Admin_ProductController;
use App\Http\Controllers\Admin_BrandController;
use App\Http\Controllers\Admin_CouponController;
use App\Http\Controllers\Admin_StaffController;
use App\Http\Controllers\Admin_BlogController;
use App\Http\Controllers\Admin_CategoryBlogController;
use App\Http\Controllers\Admin_CommentController;
use App\Http\Controllers\Admin_UserController;
use App\Http\Controllers\Admin_ImageItemController;
use App\Http\Controllers\Admin_TransportController;
use App\Http\Controllers\Admin_WardController;
use App\Http\Controllers\Admin_RoleController;
use App\Http\Controllers\Admin_PermissionController;
use App\Http\Controllers\Admin_PermissionRoleController;
use App\Http\Controllers\Admin_NewsletterController;
use App\Http\Controllers\Admin_DistrictController;
use App\Http\Controllers\Admin_ProvinceController;
// FE CONTROLLER
use App\Http\Controllers\User_HomeController;
use App\Http\Controllers\User_AccountController;
use App\Http\Controllers\User_ProductsController;
use App\Http\Controllers\User_CartController;
use App\Http\Controllers\User_CheckOutController;
use App\Http\Controllers\User_BlogController;
use App\Http\Controllers\User_CommentBlogController;
use App\Http\Controllers\User_WishListController;
use Illuminate\Routing\RouteUri;
// OTHERS
use App\Http\Controllers\PasswordSetupController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\FetchChartDataController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ShippingController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Luật Chung:
|
| 1) URL tên FE bắt đầu là home rồi level xuống, thí dụ home/products hoặc home/products/detail
|
| 2) Route phải có tên và nên được đặt tên dựa theo thanh truyền URL.
|    (ví dụ tren url home/user thì nên để tên là home.user.'tên hàm' của lớp đó)
|
| 3) Bên Admin phải có prefix là 'admin', còn bên Customer(User) thì phải có prefix là 'home'
|
| 4) Tên của Controller phải có User_ hoặc Admin_ để phân biệt bên BE và FE
|
|
*/


// Trả về Trang ban đầu
Route::get('/', function () {
    return redirect('/home');
})->middleware(['countVisitor']);

Route::get('/admin', function () {
    return redirect('/admin/dashboard');
});

Auth::routes(['verify' => true]); //Auth để kiểm tra có verify email của user if not -> trang login, else -> trang home

//Github routes
Route::get('login/github', [App\Http\Controllers\Auth\LoginController::class, 'redirectToGithub'])->name('login.github');
Route::get('login/github/callback', [App\Http\Controllers\Auth\LoginController::class, 'handleGithubCallback']);


// Google login
Route::get('login/google', [App\Http\Controllers\Auth\LoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [App\Http\Controllers\Auth\LoginController::class, 'handleGoogleCallback']);

// Facebook login
Route::get('login/facebook', [App\Http\Controllers\Auth\LoginController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('login/facebook/callback', [App\Http\Controllers\Auth\LoginController::class, 'handleFacebookCallback']);

// Twitter login
Route::get('login/twitter', [App\Http\Controllers\Auth\LoginController::class, 'redirectToTwitter'])->name('login.twitter');
Route::get('login/twitter/callback', [App\Http\Controllers\Auth\LoginController::class, 'handleTwitterCallback']);


//Another Address
Route::get('/another-address', [User_CheckOutController::class, 'another_address']);

//Check-Out-Button
Route::post('/check-out-shopping', [User_CheckOutController::class, 'check_out_shopping']);
//FRONT END
// Select CITY - DISTRICT - WARD -> FEE
Route::post('select-delivery',[User_CartController::class, 'select_delivery']);

Route::post('calculate-fee',[User_CartController::class, 'calculate_fee']);

//Add Product to cart
Route::post('roll-button', [User_CartController::class, 'roll_button']);

Route::post('check/coupon', [User_CartController::class, 'check_coupon']);

Route::post('/add-to-cart',[User_CartController::class, 'add_to_cart']);

Route::get('/cart',[User_CartController::class, 'view_cart']);

Route::post('/update-cart-quantity',[User_CartController::class, 'update_cart_quantity']);

Route::post('/delete-cart-product', [User_CartController::class, 'delete_cart_product']);

//route for autocompleted search bar
Route::get('find', [User_ProductsController::class, 'find'])->name('find');

Route::prefix('home')->name('home.')->group(function () {
     //trả về trang home có list item đầy đủ
    Route::get('/', [User_HomeController::class, 'index'])
        ->name('index');

    //Show chi tiết sản phẩm bên trang products của home
    Route::get('products/{id?}', [User_ProductsController::class, 'index'])
    ->name('products.index'); //Show chi tiết sản phẩm bên trang products của home


    Route::get('single-product/{id?}', [User_ProductsController::class, 'single_product'])
    ->name('single_product');

    Route::post('single-product/{id}/post', [User_ProductsController::class, 'postComment'])
    ->name('post');
});


// CHECK OUT
Route::get('/checkout', [User_CheckOutController::class , 'index'])->name('checkout.index'); //Về Trang Check Out

//SETUP PASSWORD FOR STAFF
Route::get('/auth/passwordset/{token}', [PasswordSetupController::class,'passwordset']);

// FETCH DATA FOR CHART
Route::get('/fetch-order-data', [FetchChartDataController::class,'fetchOrderByProvince']);
Route::get('/fetch-daily-order-data', [FetchChartDataController::class,'fetchDailyOrder']);
Route::get('/fetch-product-sale-data', [FetchChartDataController::class,'fetchSalesPerProduct']);
Route::get('/fetch-users-value-data', [FetchChartDataController::class,'fetchValuePerUser']);

// FETCH DATA FOR DATATABLE
Route::post('/fetch-order', [FetchChartDataController::class,'fetchOrder']);
Route::post('/fetch-user', [FetchChartDataController::class,'fetchUser']);
Route::post('/fetch-ward', [FetchChartDataController::class,'fetchWard']);
Route::post('/fetch-district', [FetchChartDataController::class,'fetchDistrict']);
Route::post('/fetch-province', [FetchChartDataController::class,'fetchProvince']);


//BACK END

// Có VErify
// User Dashboard
Route::prefix('home')->middleware(['auth' , 'verified', 'countVisitor' ,'checkRoles:user,staff'])->group(function() {
    Route::get('user/account', [User_AccountController::class , 'index'])->name('account.index');
    Route::post('user/account/upload', [User_AccountController::class , 'upload'])->name('account.upload');
    Route::post('user/account/update', [User_AccountController::class , 'update'])->name('account.update');
});


//Admin Dashboard
Route::prefix('admin')->name('admin.')->middleware(['auth' , 'countVisitor' ,'checkRoles:staff'])->group(function () {
    // Dash Board
    Route::resource('dashboard' , Admin_DashboardController::class);

    // Product
    Route::get('product/export', [Admin_ProductController::class, 'export'])->name('product.export');
    Route::get('product/status/{id}', [Admin_ProductController::class, 'status'] );
    Route::get('product/restore/{id}', [Admin_ProductController::class, 'restore'] );
    Route::get('product/trash', [Admin_ProductController::class, 'showTrash'] );
    Route::get('product/trash/{id}', [Admin_ProductController::class, 'forceDelete'] )->name('product.trash.forceDelete');
    Route::resource('product', Admin_ProductController::class);
    Route::resource('category', Admin_CategoryController::class);
    Route::resource('brand', Admin_BrandController::class);
    Route::get('coupon/export', [Admin_CouponController::class, 'export'])->name('coupon.export');
    Route::resource('coupon', Admin_CouponController::class);

    // Image Item
    Route::get('ImageItem/{id}', [Admin_ImageItemController::class, 'index']);
    Route::post('ImageItem', [Admin_ImageItemController::class, 'store']);
    // Route::post('ImageItem/destroy', [Admin_ImageItemController::class, 'destroy']);

    // Order
    Route::get('order/export', [Admin_OrderController::class, 'export'])->name('order.export');
    Route::get('order/restore/{id}', [Admin_OrderController::class, 'restore'] );
    Route::get('order/trash', [Admin_OrderController::class, 'showTrash'] );
    Route::resource('order', Admin_OrderController::class);
    Route::resource('order.item', Admin_OrderItemController::class);

    // Staff
    Route::post('staff/calendar/action', [Admin_StaffController::class, 'action']); //FullCalender Action
    Route::resource('staff', Admin_StaffController::class);

    // User
    Route::resource('user', Admin_UserController::class);


    // Blog
    Route::get('comment/{id}', [Admin_CommentController::class, 'index']);
    Route::post('comment/delete/{id}', [Admin_CommentController::class, 'delete']);

    Route::get('delete/{id}', [Admin_BlogController::class, 'destroy']);
    Route::get('blog/trash/{id}', [Admin_BlogController::class, 'forceDelete'] )->name('blog.trash.forceDelete');
    Route::get('blog/restore/{id}', [Admin_BlogController::class, 'restore'] );
    Route::get('blog/trash', [Admin_BlogController::class, 'showTrash'] );
    Route::get('published-blog/{id}', [Admin_BlogController::class, 'published_blog']);
    Route::get('unhidden/{id}', [Admin_BlogController::class, 'unhidden'] );
    Route::get('hidden/{id}', [Admin_BlogController::class, 'hidden'] );
    Route::resource('blog', Admin_BlogController::class);

    Route::get('ImageItem/{id}', [Admin_ImageItemController::class, 'index']);
    Route::post('ImageItem', [Admin_ImageItemController::class, 'store']);
    Route::get('ImageItem/edit/{id}/{ImageItems}', [Admin_ImageItemController::class, 'edit']);
    Route::post('ImageItem/update/{id}/{ImageItems}', [Admin_ImageItemController::class, 'update']);
    Route::post('ImageItem/delete/{id}/{ImageItem_id}', [Admin_ImageItemController::class, 'delete']);


    // Transport
    Route::get('transport/restore/{id}', [Admin_TransportController::class, 'restore'] );
    Route::get('transport/trash', [Admin_TransportController::class, 'showTrash'] );
    Route::resource('transport', Admin_TransportController::class);

    //ward
    Route::resource('ward', Admin_WardController::class );

    //District
    Route::resource('district', Admin_DistrictController::class );

    //Province
    Route::resource('province', Admin_ProvinceController::class );

    // Authorization
    Route::resource('role', Admin_RoleController::class);

    Route::resource('permission', Admin_PermissionController::class);

    Route::get('permission_role/list', [Admin_PermissionRoleController::class, 'index'])->name('permission_role.index');
    Route::post('permission_role/list', [Admin_PermissionRoleController::class, 'index'])->name('permission_role.index');
    Route::get('permission_role/create/{role_id?}', [Admin_PermissionRoleController::class, 'create'])->name('permission_role.create');
    Route::post('permission_role/create', [Admin_PermissionRoleController::class, 'store'])->name('permission_role.store');
    Route::delete('permission_role/delete/{role_id}&{permission_id}', [Admin_PermissionRoleController::class, 'destroy'])->name('permission_role.destroy');
    // Route::resource('permission_role', Admin_PermissionRoleController::class);

    //Newsletter
    Route::post('/newsletter/delete', Admin_NewsletterController::class.'@delete');
    Route::get('/newsletter/listsendmail', Admin_NewsletterController::class.'@list_send_mail');
    Route::post('/newsletter/sendmail', Admin_NewsletterController::class.'@send_mail');
    Route::resource('newsletter', Admin_NewsletterController::class);


    // Misc
    Route::post('order/calculate-fee',[Admin_OrderController::class, 'shipping_fee']);
    Route::post('fetch/product', Admin_ProductController::class.'@fetchProduct');

    // Backup routes
    Route::get('backup', BackupController::class.'@index');
    Route::get('backup/create', BackupController::class.'@create');
    Route::get('backup/download/{file_name}', BackupController::class.'@download');
    Route::get('backup/delete/{file_name}', BackupController::class.'@delete');
});





Route::get('clear-cache', function () {
    Artisan::call('cache:clear');
    return back()->with('success', "Cache Cleared");
})->name('clear-cache');

//URL TRẢ VỀ VIEW -> Cho development thôi

Route::get('/about', function () {
    return view('pages.about');
});

Route::get('/contact', function () {
    return view('pages.contact');
});


//Category/Tag BLOG ADMIN
Route::post('/add-to-category-blog-by-input', [Admin_CategoryBlogController::class, 'add_category_blog_by_input']);

Route::post('/show-list-category-blog', [Admin_CategoryBlogController::class, 'show_list_category_blog']);


Route::post('/add-to-tag-blog-by-input', [Admin_CategoryBlogController::class, 'add_to_tag_blog_by_input']);

Route::post('/show-list-tag-blog', [Admin_CategoryBlogController::class, 'show_list_tag_blog']);

Route::post('/add-tag', [Admin_CategoryBlogController::class, 'add_tag']);

 Route::post('/show-tag-blog', [Admin_CategoryBlogController::class, 'show_tag_blog']);

 Route::post('/delete-tag-blog', [Admin_CategoryBlogController::class, 'delete_tag_blog']);

//---------------------BLOG--------------------------//
//BLOG USER

Route::get('/blog',[User_BlogController::class, 'index']);

Route::get('/blog/details/{slug}', [User_BlogController::class, 'blog_details']);

Route::post('/comment/blog', [User_CommentBlogController::class, 'comment_blog']);

Route::get('blog/category/{id}', [User_BlogController::class, 'blog_category']);


//WISH LIST
Route::post('/add-to-wishlist', [User_WishListController::class, 'add_to_wishlist']);

Route::post('/roll-button-wishlist', [User_WishListController::class, 'roll_button_wishlist']);

Route::post('/delete-button-wishlist', [User_WishListController::class, 'delete_button_wishlist']);

Route::get('email-success', function () {
    view('sendmail.email_order_success');
});

//Subscribe Email Address
Route::post('/subscribe-email', [User_HomeController::class, 'subscribe_email']);
