<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('login');
});

Route::get('data-test', ['as' => 'login', 'uses' => 'Admin\DataTestController@dataTest']);
Route::get('clear', ['as' => 'clear', 'uses' => 'Admin\AdminAuthController@clear']);
Route::get('login', ['as' => 'login', 'uses' => 'Admin\AdminAuthController@getLogin']);
Route::post('admin', ['as' => 'admin', 'uses' => 'Admin\AdminAuthController@postLogin']);
Route::get('logout', ['as' => 'logout', 'uses' => 'Admin\AdminAuthController@getLogout']);

Route::group(['namespace' => 'Admin', 'middleware' => ['auth']], function () {
    // Dashboard
    //Route::get('dashboard', ['middleware' => 'acl:dashboard', 'as' => 'admin.dashboard', 'uses' => 'DashboardController@index']);
    Route::get('dashboard', ['as' => 'admin.dashboard', 'uses' => 'DashboardController@getIndex']);
    Route::get('/', ['as' => 'admin.dashboard', 'uses' => 'DashboardController@getIndex']);
    Route::post('postDashboardNote', ['as' => 'admin.dashboard.note.post', 'uses' => 'DashboardController@postDashboardNote']);

    // USER
    Route::get('user/{id}/single-edit', ['middleware' => 'acl:edit_user', 'as' => 'admin.user.loggedin.edit', 'uses' => 'AdminUserController@getEditSingle']);
    Route::post('user/{id}/update/{single?}', ['middleware' => 'acl:edit_user', 'as' => 'admin.user.update', 'uses' => 'AdminUserController@putUpdate']);


    // Admin User
    Route::get('admin-users', ['middleware' => 'acl:view_admin_user', 'as' => 'admin.admin-user', 'uses' => 'AdminUserController@getIndex']);
    Route::get('admin-user/new', ['middleware' => 'acl:add_admin_user', 'as' => 'admin.admin-user.new', 'uses' => 'AdminUserController@getCreate']);
    Route::post('admin-user/store', ['middleware' => 'acl:add_admin_user', 'as' => 'admin.admin-user.store', 'uses' => 'AdminUserController@postStore']);
    Route::get('admin-user/{id}/edit', ['middleware' => 'acl:edit_admin_user', 'as' => 'admin.admin-user.edit', 'uses' => 'AdminUserController@getEdit']);
    Route::post('admin-user/{id}/update', ['middleware' => 'acl:edit_admin_user', 'as' => 'admin.admin-user.update', 'uses' => 'AdminUserController@putUpdate']);
    Route::get('admin-user/{id}/delete', ['middleware' => 'acl:delete_admin_user', 'as' => 'admin.admin-user.delete', 'uses' => 'AdminUserController@getDelete']);

    // User-Group
    Route::get('user-group', ['middleware' => 'acl:view_user_group', 'as' => 'admin.user-group', 'uses' => 'UserGroupController@getIndex']);
    Route::get('user-group/new', ['middleware' => 'acl:new_user_group', 'as' => 'admin.user-group.new', 'uses' => 'UserGroupController@getCreate']);
    Route::post('user-group/store', ['middleware' => 'acl:new_user_group', 'as' => 'admin.user-group.store', 'uses' => 'UserGroupController@postStore']);
    Route::get('user-group/{id}/edit', ['middleware' => 'acl:edit_user_group', 'as' => 'admin.user-group.edit', 'uses' => 'UserGroupController@getEdit']);
    Route::post('user-group/{id}/update', ['middleware' => 'acl:edit_user_group', 'as' => 'admin.user-group.update', 'uses' => 'UserGroupController@putUpdate']);
    Route::get('user-group/{id}/delete', ['middleware' => 'acl:delete_user_group', 'as' => 'admin.user-group.delete', 'uses' => 'UserGroupController@getDelete']);

    // User-Group
    Route::get('assign-access', ['middleware' => 'acl:assign_user_access', 'as' => 'admin.assign-access', 'uses' => 'AssignAccessController@getIndex']);
    Route::post('assign-access', ['middleware' => 'acl:assign_user_access', 'as' => 'admin.assign-access', 'uses' => 'AssignAccessController@postIndex']);

    // Role
    Route::get('role', ['middleware' => 'acl:view_role', 'as' => 'admin.role', 'uses' => 'RoleController@getIndex']);
    Route::get('role/new', ['middleware' => 'acl:add_role', 'as' => 'admin.role.new', 'uses' => 'RoleController@getCreate']);
    Route::post('role/store', ['middleware' => 'acl:add_role', 'as' => 'admin.role.store', 'uses' => 'RoleController@postStore']);
    Route::get('role/{id?}/edit', ['middleware' => 'acl:edit_role', 'as' => 'admin.role.edit', 'uses' => 'RoleController@getEdit']);
    Route::post('role/{id}/update', ['middleware' => 'acl:edit_role', 'as' => 'admin.role.update', 'uses' => 'RoleController@postUpdate']);
    Route::get('role/{id}/delete', ['middleware' => 'acl:delete_role', 'as' => 'admin.role.delete', 'uses' => 'RoleController@getDelete']);

    // Permission-Group
    Route::get('permission-group', ['middleware' => 'acl:view_menu', 'as' => 'admin.permission-group', 'uses' => 'PermissionGroupController@getIndex']);
    Route::get('permission-group/new', ['middleware' => 'acl:new_menu', 'as' => 'admin.permission-group.new', 'uses' => 'PermissionGroupController@getCreate']);
    Route::post('permission-group/store', ['middleware' => 'acl:new_menu', 'as' => 'admin.permission-group.store', 'uses' => 'PermissionGroupController@postStore']);
    Route::get('permission-group/{id}/edit', ['middleware' => 'acl:edit_menu', 'as' => 'admin.permission-group.edit', 'uses' => 'PermissionGroupController@getEdit']);
    Route::post('permission-group/{id}/update', ['middleware' => 'acl:edit_menu', 'as' => 'admin.permission-group.update', 'uses' => 'PermissionGroupController@putUpdate']);
    Route::get('permission-group/{id}/delete', ['middleware' => 'acl:delete_menu', 'as' => 'admin.permission-group.delete', 'uses' => 'PermissionGroupController@getDelete']);

    // permission
    Route::get('permission', ['middleware' => 'acl:view_action', 'as' => 'admin.permission', 'uses' => 'PermissionController@getIndex']);
    Route::get('permission/new', ['middleware' => 'acl:new_action', 'as' => 'admin.permission.new', 'uses' => 'PermissionController@getCreate']);
    Route::post('permission/store', ['middleware' => 'acl:new_action', 'as' => 'admin.permission.store', 'uses' => 'PermissionController@postStore']);
    Route::get('permission/{id}/edit', ['middleware' => 'acl:edit_action', 'as' => 'admin.permission.edit', 'uses' => 'PermissionController@getEdit']);
    Route::post('permission/{id}/update', ['middleware' => 'acl:edit_action', 'as' => 'admin.permission.update', 'uses' => 'PermissionController@putUpdate']);
    Route::get('permission/{id}/delete', ['middleware' => 'acl:delete_action', 'as' => 'admin.permission.delete', 'uses' => 'PermissionController@getDelete']);

    //product
    Route::get('property', ['middleware' => 'acl:view_property', 'as' => 'admin.product.list', 'uses' => 'ProductController@getIndex']);
    Route::post('property_list', ['middleware' => 'acl:view_property', 'as' => 'ajax.property.list', 'uses' => 'DataTableController@getProperty']);

    // Route::get('product-search-list', ['middleware' => 'acl:view_product_list', 'as' => 'admin.product.searchlist', 'uses' => 'ProductController@getProductSearch']);
    // Route::post('product-search-list', ['middleware' => 'acl:view_product_list', 'as' => 'admin.searchlist.view.post', 'uses' => 'ProductController@getProductSearchList']);
    // Route::get('product-list/{id}/view', ['middleware' => 'acl:view_product_list', 'as' => 'admin.product.searchlist.view', 'uses' => 'ProductController@getView']);
    // Route::get('product-list/{id}/edit', ['middleware' => 'acl:edit_product_list', 'as' => 'admin.product.searchlist.edit', 'uses' => 'ProductController@getEdit']);


    Route::get('ajax-get-area/{id}', ['as' => 'getarea', 'uses' => 'ProductController@getArea']);
//    Route::get('property-sale-rent',['middleware' => 'acl:view_product', 'as' => 'admin.property.rent.edit', 'uses' => 'ProductController@getEditRentndex']);
//    Route::get('property-sale-roommate',['middleware' => 'acl:view_product', 'as' => 'admin.property.roommate.edit', 'uses' => 'ProductController@getEditRoommatendex']);
    Route::get('property/new', ['middleware' => 'acl:new_product', 'as' => 'admin.product.create', 'uses' => 'ProductController@getCreate']);
    Route::post('property/store', ['middleware' => 'acl:new_product', 'as' => 'admin.product.store', 'uses' => 'ProductController@postStore']);
    Route::get('property/{id}/edit', ['middleware' => 'acl:edit_product', 'as' => 'admin.product.edit', 'uses' => 'ProductController@getEdit']);
    Route::get('property/{id}/activity', ['middleware' => 'acl:edit_product_activity', 'as' => 'admin.product.activity', 'uses' => 'ProductController@getaAtivity']);
    Route::get('property/{id}/view', ['middleware' => 'acl:view_product', 'as' => 'admin.product.view', 'uses' => 'ProductController@getView']);
    Route::post('property/{id}/update', ['middleware' => 'acl:edit_product', 'as' => 'admin.product.update', 'uses' => 'ProductController@putUpdate']);
    Route::post('ajax-listings-delete_img', ['middleware' => 'acl:edit_product', 'as' => 'admin.product.delete_image', 'uses' => 'ProductController@postDeleteImage']);


    Route::get('property/ajax-listing-variant', ['middleware' => 'acl:edit_product', 'as' => 'admin.product.ajax.get.variant', 'uses' => 'ProductController@addListingVariant']);
    Route::get('property/ajax-property-type/{id}', ['middleware' => 'acl:edit_product', 'as' => 'admin.product.ajax.get.property_type', 'uses' => 'ProductController@getPropertyType']);

    Route::post('product_variant/store', ['middleware' => 'acl:new_product_variant', 'as' => 'admin.product_variant.store', 'uses' => 'ProductController@postStoreProductVariant']);
    Route::post('product_variant/{id}/update', ['middleware' => 'acl:edit_product_variant', 'as' => 'admin.product_variant.update', 'uses' => 'ProductController@putUpdateProductVariant']);
    Route::get('product_variant/{id}/delete', ['middleware' => 'acl:delete_product_variant', 'as' => 'admin.product_variant.delete', 'uses' => 'ProductController@getDeleteProductVariant']);
    //ajax route for product module
    Route::get('prod_img_delete/{id}', ['middleware' => 'acl:delete_product', 'as' => 'admin.product.img_delete', 'uses' => 'ProductController@getDeleteImage']);
    Route::get('prod_subcategory/{id}', ['middleware' => 'acl:new_product', 'as' => 'product.prod_subcategory.', 'uses' => 'ProductController@getSubcat']);
    Route::get('prod_model/{id}', ['middleware' => 'acl:new_product', 'as' => 'product.prod_model', 'uses' => 'ProductController@getProdModel']);
    Route::get('get_hscode_by_scat/{id?}', ['middleware' => 'acl:new_product', 'as' => 'get_hscode_by_scat', 'uses' => 'ProductController@getHscode']);
    Route::post('product-search', ['middleware' => 'acl:new_product', 'as' => 'admin.product_search', 'uses' => 'ProductController@getProductSearchList']);
    Route::post('product/search-back', ['middleware' => 'acl:new_product', 'as' => 'admin.add_to_mother_page', 'uses' => 'ProductController@getProductSearchGoBack']);


    //product-model
    Route::get('product-model', ['middleware' => 'acl:view_model', 'as' => 'admin.product-model', 'uses' => 'ProductModelController@getIndex']);
    Route::get('product-model/new', ['middleware' => 'acl:new_model', 'as' => 'admin.product-model.new', 'uses' => 'ProductModelController@getCreate']);
    Route::post('product-model/store', ['middleware' => 'acl:new_model', 'as' => 'admin.product-model.store', 'uses' => 'ProductModelController@postStore']);
    Route::get('product-model/{id}/edit', ['middleware' => 'acl:edit_model', 'as' => 'admin.product-model.edit', 'uses' => 'ProductModelController@getEdit']);
    Route::post('product-model/{id}/update', ['middleware' => 'acl:edit_model', 'as' => 'admin.product-model.update', 'uses' => 'ProductModelController@putUpdate']);
    Route::get('product-model/{id}/delete', ['middleware' => 'acl:delete_model', 'as' => 'admin.product-model.delete', 'uses' => 'ProductModelController@getDelete']);

    //product-color

    Route::get('product-color', ['middleware' => 'acl:view_color', 'as' => 'admin.product.color.list', 'uses' => 'ColorsController@getIndex']);
    Route::get('product-color/new', ['middleware' => 'acl:new_color', 'as' => 'admin.product.color.create', 'uses' => 'ColorsController@getCreate']);
    Route::post('product-color/store', ['middleware' => 'acl:new_color', 'as' => 'admin.product.color.store', 'uses' => 'ColorsController@postStore']);
    Route::get('product-color/{id}/edit', ['middleware' => 'acl:edit_color', 'as' => 'admin.product.color.edit', 'uses' => 'ColorsController@getEdit']);
    Route::post('product-color/{id}/update', ['middleware' => 'acl:edit_color', 'as' => 'admin.product.color.update', 'uses' => 'ColorsController@postUpdate']);
    Route::get('product-color/{id}/delete', ['middleware' => 'acl:delete_color', 'as' => 'admin.product.color.delete', 'uses' => 'ColorsController@getDelete']);


    //product-size
    Route::get('product-size', ['middleware' => 'acl:view_size', 'as' => 'admin.product-size', 'uses' => 'ProductSizeController@getIndex']);
    Route::get('product-size/new', ['middleware' => 'acl:new_size', 'as' => 'admin.product-size.new', 'uses' => 'ProductSizeController@getCreate']);
    Route::post('product-size/store', ['middleware' => 'acl:new_size', 'as' => 'admin.product-size.store', 'uses' => 'ProductSizeController@postStore']);
    Route::get('product-size/{id}/edit', ['middleware' => 'acl:edit_size', 'as' => 'admin.product-size.edit', 'uses' => 'ProductSizeController@getEdit']);
    Route::post('product-size/{id}/update', ['middleware' => 'acl:edit_size', 'as' => 'admin.product-size.update', 'uses' => 'ProductSizeController@putUpdate']);
    Route::get('product-size/{id}/delete', ['middleware' => 'acl:delete_size', 'as' => 'admin.product-size.delete', 'uses' => 'ProductSizeController@getDelete']);


    //product-model
    Route::get('product-model', ['middleware' => 'acl:view_model', 'as' => 'admin.product-model', 'uses' => 'ProductModelController@getIndex']);
    Route::get('product-model/new', ['middleware' => 'acl:new_model', 'as' => 'admin.product-model.new', 'uses' => 'ProductModelController@getCreate']);
    Route::post('product-model/store', ['middleware' => 'acl:new_model', 'as' => 'admin.product-model.store', 'uses' => 'ProductModelController@postStore']);
    Route::get('product-model/{PK_NO}/edit', ['middleware' => 'acl:edit_model', 'as' => 'admin.product-model.edit', 'uses' => 'ProductModelController@getEdit']);
    Route::post('product-model/{PK_NO}/update', ['middleware' => 'acl:edit_model', 'as' => 'admin.product-model.update', 'uses' => 'ProductModelController@putUpdate']);
    Route::get('product-model/{PK_NO}/delete', ['middleware' => 'acl:delete_model', 'as' => 'admin.product-model.delete', 'uses' => 'ProductModelController@getDelete']);


    //Brand
    Route::get('product-brand', ['middleware' => 'acl:view_brand', 'as' => 'product.brand.list', 'uses' => 'BrandController@getIndex']);
    Route::get('product-brand/new', ['middleware' => 'acl:new_brand', 'as' => 'product.brand.create', 'uses' => 'BrandController@getCreate']);
    Route::post('product-brand/store', ['middleware' => 'acl:new_brand', 'as' => 'product.brand.store', 'uses' => 'BrandController@postStore']);
    Route::get('product-brand/{id}/edit', ['middleware' => 'acl:edit_brand', 'as' => 'product.brand.edit', 'uses' => 'BrandController@postEdit']);
    Route::post('product-brand/{id}/update', ['middleware' => 'acl:edit_brand', 'as' => 'product.brand.update', 'uses' => 'BrandController@postUpdate']);
    Route::get('product-brand/{id}/delete', ['middleware' => 'acl:delete_brand', 'as' => 'product.brand.delete', 'uses' => 'BrandController@getDelete']);

    //Account Source
    // Route::get('account', ['middleware' => 'acl:view_account_source', 'as' => 'admin.account.list', 'uses' => 'AccountController@getIndex']);
    // Route::get('account/new', ['middleware' => 'acl:new_account_source', 'as' => 'account.source.create', 'uses' => 'AccountController@getCreate']);
    // Route::post('account/store', ['middleware' => 'acl:new_account_source', 'as' => 'account.store', 'uses' => 'AccountController@postAccSource']);
    // Route::get('account/{id}/delete', ['middleware' => 'acl:delete_account_source', 'as' => 'account.source.delete', 'uses' => 'AccountController@getDelete']);
    // Route::post('account/{id}/update', ['middleware' => 'acl:edit_account_source', 'as' => 'account.source.update', 'uses' => 'AccountController@putUpdate']);

    //Transaction
    Route::get('transaction', ['middleware' => 'acl:view_transaction', 'as' => 'admin.transaction.list', 'uses' => 'TransactionController@getIndex']);
    Route::get('transaction/create', ['middleware' => 'acl:new_transaction', 'as' => 'admin.transaction.create', 'uses' => 'TransactionController@getEdit']);
    Route::post('transaction/store', ['middleware' => 'acl:new_transaction', 'as' => 'admin.transaction.store', 'uses' => 'TransactionController@postStore']);
    Route::get('transaction/{id}/edit', ['middleware' => 'acl:edit_transaction', 'as' => 'admin.transaction.edit', 'uses' => 'TransactionController@getEdit']);
    Route::post('transaction/{id}/update', ['middleware' => 'acl:edit_transaction', 'as' => 'admin.transaction.update', 'uses' => 'TransactionController@postUpdate']);
    Route::get('refund-request', ['middleware' => 'acl:view_refund_request', 'as' => 'admin.refund_request', 'uses' => 'TransactionController@getRefundRequest']);
    Route::get('refund-request/{id}/edit', ['middleware' => 'acl:edit_refund_request', 'as' => 'admin.refund_request.edit', 'uses' => 'TransactionController@editRefundRequest']);
    Route::post('refund-request/{id}/update', ['middleware' => 'acl:edit_refund_request', 'as' => 'admin.refund_request.update', 'uses' => 'TransactionController@updateRefundRequest']);
    Route::post('refund_request', ['middleware' => 'acl:view_refund_request', 'as' => 'ajax.refund-request.list', 'uses' => 'DataTableController@getRefundRequest']);

    Route::get('recharge-request', ['middleware' => 'acl:view_recharge_request', 'as' => 'admin.recharge_request', 'uses' => 'TransactionController@getRechargeRequest']);
    Route::get('recharge-request/{id}/edit', ['middleware' => 'acl:edit_recharge_request', 'as' => 'admin.recharge_request.edit', 'uses' => 'TransactionController@editRechargeRequest']);
    Route::post('recharge-request/{id}/update', ['middleware' => 'acl:edit_recharge_request', 'as' => 'admin.recharge_request.update', 'uses' => 'TransactionController@updateRechargeRequest']);
    Route::post('recharge_request', ['middleware' => 'acl:view_recharge_request', 'as' => 'ajax.recharge-request.list', 'uses' => 'DataTableController@getRechargeRequest']);

    // Route::get('recharge-request', ['middleware' => 'acl:view_recharge_request', 'as' => 'admin.recharge_request', 'uses' => 'TransactionController@getRechargeRequest']);
    Route::get('agent-commission', ['middleware' => 'acl:view_recharge_request', 'as' => 'admin.agent_commission', 'uses' => 'TransactionController@getAgentCommission']);


    //Pages
    Route::get('pages', ['middleware' => 'acl:view_pages', 'as' => 'admin.pages.list', 'uses' => 'PagesController@getIndex']);
    Route::get('pages/create', ['middleware' => 'acl:new_pages', 'as' => 'admin.pages.create', 'uses' => 'PagesController@getCreate']);
    Route::post('pages/store', ['middleware' => 'acl:new_pages', 'as' => 'admin.pages.store', 'uses' => 'PagesController@postStore']);
    Route::get('pages/{id}/edit', ['middleware' => 'acl:edit_pages', 'as' => 'admin.pages.edit', 'uses' => 'PagesController@getEdit']);
    Route::post('pages/{id}/update', ['middleware' => 'acl:edit_pages', 'as' => 'admin.pages.update', 'uses' => 'PagesController@postUpdate']);
    Route::get('pages/{id}/delete', ['middleware' => 'acl:delete_pages', 'as' => 'admin.pages.delete', 'uses' => 'PagesController@getDelete']);

    // Pages Category
    Route::get('pages-category', ['middleware' => 'acl:view_pages_category', 'as' => 'admin.pages-category.list', 'uses' => 'PagesCategoryController@getIndex']);
    Route::get('pages-category/create', ['middleware' => 'acl:new_pages_category', 'as' => 'admin.pages-category.create', 'uses' => 'PagesCategoryController@getCreate']);
    Route::post('pages-category/store', ['middleware' => 'acl:new_pages_category', 'as' => 'admin.pages-category.store', 'uses' => 'PagesCategoryController@postStore']);
    Route::get('pages-category/{id}/edit', ['middleware' => 'acl:edit_pages_category', 'as' => 'admin.pages-category.edit', 'uses' => 'PagesCategoryController@getEdit']);
    Route::post('pages-category/{id}/update', ['middleware' => 'acl:edit_pages_category', 'as' => 'admin.pages-category.update', 'uses' => 'PagesCategoryController@postUpdate']);
    Route::get('pages-category/{id}/delete', ['middleware' => 'acl:delete_pages_category', 'as' => 'admin.pages-category.delete', 'uses' => 'PagesCategoryController@getDelete']);

    //Account Bank Name
    // Route::get('account-bank', ['middleware' => 'acl:view_account_name', 'as' => 'account.bank.list', 'uses' => 'BankAccountController@getIndex']);
    // Route::get('account-bank/new', ['middleware' => 'acl:new_account_name', 'as' => 'account.bank.create', 'uses' => 'BankAccountController@getCreateBank']);
    // Route::post('account-bank/store', ['middleware' => 'acl:new_account_name', 'as' => 'account.bank.store', 'uses' => 'BankAccountController@postStore']);
    // Route::post('account-bank/store', ['middleware' => 'acl:new_account_name', 'as' => 'account.bank.store.single', 'uses' => 'BankAccountController@postStoreSingle']);
    // Route::post('account-bank/{id}/update', ['middleware' => 'acl:edit_account_name', 'as' => 'account.bank.update', 'uses' => 'BankAccountController@putUpdate']);
    // Route::get('account-bank/{id}/delete', ['middleware' => 'acl:delete_account_name', 'as' => 'account.name.delete', 'uses' => 'BankAccountController@getDelete']);
    // Route::get('payment-bank', ['middleware' => 'acl:view_payment_bank', 'as' => 'admin.payment_bank.list', 'uses' => 'PaymentBankController@getIndex']);
    // Route::get('payment-bank/new', ['middleware' => 'acl:new_payment', 'as' => 'admin.payment_bank.create', 'uses' => 'PaymentBankController@getCreate']);
    // Route::post('payment-bank/store', ['middleware' => 'acl:new_payment', 'as' => 'admin.payment_bank.store', 'uses' => 'PaymentBankController@postStore']);
    // Route::post('payment-bank/store', ['middleware' => 'acl:new_payment', 'as' => 'admin.payment_bank.store', 'uses' => 'PaymentBankController@postStore']);
    // Route::post('account-bank/store', ['middleware' => 'acl:new_payment', 'as' => 'account.bank.store.single', 'uses' => 'PaymentBankController@postStoreSingle']);
    // Route::post('account-bank/{id}/update', ['middleware' => 'acl:edit_account_name', 'as' => 'account.bank.update', 'uses' => 'PaymentBankController@putUpdate']);

    // Route::get('account-bank/{id}/delete', ['middleware' => 'acl:delete_account_name', 'as' => 'account.name.delete', 'uses' => 'PaymentBankController@getDelete']);


    //Account payment method
    // Route::post('account-method/{id}/update', ['middleware' => 'acl:edit_payment_method', 'as' => 'account.bank.method.update', 'uses' => 'AccountMethodController@putUpdate']);
    // Route::get('account-method/{id}/delete', ['middleware' => 'acl:delete_payment_method', 'as' => 'account.method.delete', 'uses' => 'AccountMethodController@getDelete']);
    // Route::post('account-method/store', ['middleware' => 'acl:new_payment_method', 'as' => 'account.method.store', 'uses' => 'AccountMethodController@postStore']);

    //Agent Section

    Route::get('agent/new', ['middleware' => 'acl:new_agent', 'as' => 'agent.create', 'uses' => 'AgentController@getCreate']);
    Route::post('agent/store', ['middleware' => 'acl:new_agent', 'as' => 'admin.agent.store', 'uses' => 'AgentController@postStore']);
    Route::get('agent/list', ['middleware' => 'acl:view_agent', 'as' => 'admin.agent.list', 'uses' => 'AgentController@getIndex']);
    Route::get('agent/{id}/edit', ['middleware' => 'acl:edit_agent', 'as' => 'admin.agent.edit', 'uses' => 'AgentController@getEdit']);
    Route::post('agent/update/{id}', ['middleware' => 'acl:edit_agent', 'as' => 'admin.agent.update', 'uses' => 'AgentController@postUpdate']);
    Route::get('agent/{id}/delete', ['middleware' => 'acl:delete_agent', 'as' => 'admin.agent.delete', 'uses' => 'AgentController@getDelete']);

    //Reseller Section

    Route::get('owner', ['middleware' => 'acl:view_owner', 'as' => 'admin.owner.list', 'uses' => 'OwnerController@getIndex']);
    Route::get('owner/{id}/view', ['middleware' => 'acl:view_owner', 'as' => 'admin.owner.view', 'uses' => 'OwnerController@getView']);
    Route::get('owner/{id}/edit', ['middleware' => 'acl:edit_owner', 'as' => 'admin.owner.edit', 'uses' => 'OwnerController@getEdit']);
    Route::post('owner/{id}/update', ['middleware' => 'acl:edit_owner', 'as' => 'admin.owner.update', 'uses' => 'OwnerController@postUpdate']);
    Route::get('owner/{id}/payment', ['middleware' => 'acl:view_owner', 'as' => 'admin.owner.payment', 'uses' => 'OwnerController@getPayment']);
    Route::post('owner/{id}/payment', ['middleware' => 'acl:view_owner', 'as' => 'admin.owner.payment.store', 'uses' => 'OwnerController@postPayment']);
    Route::get('owner/{id}/payment/{pay_id}/', ['middleware' => 'acl:view_owner', 'as' => 'admin.owner.payment.view', 'uses' => 'OwnerController@getPaymentView']);
    Route::get('owner/{id}/password', ['middleware' => 'acl:edit_owner', 'as' => 'admin.owner.password.edit', 'uses' => 'OwnerController@getPasswordEdit']);
    Route::post('owner/{id}/password/update', ['middleware' => 'acl:edit_owner', 'as' => 'admin.owner.password.update', 'uses' => 'OwnerController@postPasswordUpdate']);
    Route::get('owner/{id}/recharge', ['middleware' => 'acl:view_owner_payment', 'as' => 'admin.owner.recharge', 'uses' => 'OwnerController@getRecharge']);
    Route::post('owner/{id}/recharge', ['middleware' => 'acl:add_owner_payment', 'as' => 'admin.owner.recharge', 'uses' => 'OwnerController@postRecharge']);
    Route::post('owner_list', ['middleware' => 'acl:view_owner', 'as' => 'ajax.owner.list', 'uses' => 'DataTableController@getOwner']);

    /* Route::post('reseller/all_reseller', 'DatatableController@all_reseller');
    Route::get('reseller/new', ['middleware' => 'acl:new_reseller', 'as' => 'admin.reseller.create', 'uses' => 'ResellerController@getCreate']);
    Route::post('reseller/store', ['middleware' => 'acl:new_reseller', 'as' => 'admin.reseller.store', 'uses' => 'ResellerController@postStore']);
    Route::get('reseller/{id}/edit', ['middleware' => 'acl:edit_reseller', 'as' => 'admin.reseller.edit', 'uses' => 'ResellerController@getEdit']);
    Route::get('reseller/{id}/payment-history', ['middleware' => 'acl:edit_reseller', 'as' => 'admin.reseller.payment_history', 'uses' => 'ResellerController@getPaymentHistory']);
    Route::post('reseller/update/{id}', ['middleware' => 'acl:edit_reseller', 'as' => 'admin.reseller.update', 'uses' => 'ResellerController@postUpdate']);
    Route::get('reseller/{id}/delete', ['middleware' => 'acl:delete_reseller', 'as' => 'admin.reseller.delete', 'uses' => 'ResellerController@getDelete']);
    Route::get('reseller/{id}/view', ['middleware' => 'acl:view_reseller', 'as' => 'admin.reseller.view', 'uses' => 'ResellerController@getView']);

    */


    //shop category
    Route::get('shop/category/list', ['middleware' => 'acl:view_shop_category', 'as' => 'admin.shop.category.list', 'uses' => 'ShopCategoryController@getIndex']);

    Route::get('shop/cateogry/new', ['middleware' => 'acl:new_shopcategory', 'as' => 'admin.shop.category.create', 'uses' => 'ShopCategoryController@getCreate']);

    Route::post('shop/category/store', ['middleware' => 'acl:new_shop_category', 'as' => 'admin.shop.category.store', 'uses' => 'ShopCategoryController@postStore']);

    Route::get('shop-category/{id}/edit', ['middleware' => 'acl:edit_shop_category', 'as' => 'admin.shop.category.edit', 'uses' => 'ShopCategoryController@getEdit']);

    Route::post('shop-category/{id}/update', ['middleware' => 'acl:edit_shop_category', 'as' => 'admin.shop.category.update', 'uses' => 'ShopCategoryController@postUpdate']);

    Route::get('shop-category/{id}/delete', ['middleware' => 'acl:delete_shop_category', 'as' => 'admin.shop.category.delete', 'uses' => 'ShopCategoryController@getDelete']);


    // Route::get('reseller', ['middleware' => 'acl:view_reseller', 'as' => 'reseller.list', 'uses' => 'ResellerController@getIndex']);
    // Route::get('reseller/new', ['middleware' => 'acl:new_reseller', 'as' => 'reseller.create', 'uses' => 'ResellerController@getCreate']);
    // Route::post('reseller/store', ['middleware' => 'acl:new_reseller', 'as' => 'admin.reseller.store', 'uses' => 'ResellerController@postStore']);
    // Route::get('reseller/{id}/edit', ['middleware' => 'acl:edit_reseller', 'as' => 'admin.reseller.edit', 'uses' => 'ResellerController@getEdit']);
    // Route::post('reseller/update/{id}', ['middleware' => 'acl:edit_reseller', 'as' => 'admin.reseller.update', 'uses' => 'ResellerController@postUpdate']);
    // Route::get('reseller/{id}/delete', ['middleware' => 'acl:delete_reseller', 'as' => 'admin.reseller.delete', 'uses' => 'ResellerController@getDelete']);

    //Inventory

    // Route::get('inventory', ['middleware' => 'acl:view_inventory', 'as' => 'product.inventory.list', 'uses' => 'InventoryController@getIndex']);
    // Route::get('inventory/new', ['middleware' => 'acl:new_brand', 'as' => 'product.inventory.create', 'uses' => 'InventoryController@getCreate']);
    // Route::post('inventory/store', ['middleware' => 'acl:new_brand', 'as' => 'product.inventory.store', 'uses' => 'InventoryController@postStore']);
    // Route::post('inventory/{id}/edit', ['middleware' => 'acl:edit_brand', 'as' => 'product.inventory.edit', 'uses' => 'InventoryController@postEdit']);
    // Route::post('inventory/{id}/update', ['middleware' => 'acl:update_brand', 'as' => 'product.inventory.edit', 'uses' => 'InventoryController@postUpdate']);
    // Route::post('inventory/{id}/delete', ['middleware' => 'acl:delete_brand', 'as' => 'product.inventory.delete', 'uses' => 'InventoryController@postDelete']);

    // Procurement =====
    // Vendor
    Route::get('vendor', ['middleware' => 'acl:view_vendor', 'as' => 'admin.vendor', 'uses' => 'VendorController@getIndex']);
    Route::get('vendor/new', ['middleware' => 'acl:new_vendor', 'as' => 'admin.vendor.new', 'uses' => 'VendorController@getCreate']);
    Route::post('vendor/store', ['middleware' => 'acl:new_vendor', 'as' => 'admin.vendor.store', 'uses' => 'VendorController@postStore']);
    Route::get('vendor/{id}/edit', ['middleware' => 'acl:edit_vendor', 'as' => 'admin.vendor.edit', 'uses' => 'VendorController@getEdit']);
    Route::post('vendor/{id}/update', ['middleware' => 'acl:edit_vendor', 'as' => 'admin.vendor.update', 'uses' => 'VendorController@postUpdate']);
    Route::get('vendor/{id}/delete', ['middleware' => 'acl:delete_vendor', 'as' => 'admin.vendor.delete', 'uses' => 'VendorController@getDelete']);

    // Stack In
    Route::get('invoice', ['middleware' => 'acl:view_invoice', 'as' => 'admin.invoice', 'uses' => 'InvoiceController@getIndex']);
    Route::get('invoice/new', ['middleware' => 'acl:new_invoice', 'as' => 'admin.invoice.new', 'uses' => 'InvoiceController@getCreate']);
    Route::get('invoice/{id}/edit', ['middleware' => 'acl:edit_invoice', 'as' => 'admin.invoice.edit', 'uses' => 'InvoiceController@getEdit']);
    Route::post('invoice/{id}/update', ['middleware' => 'acl:edit_invoice', 'as' => 'admin.invoice.update', 'uses' => 'InvoiceController@postUpdate']);
    Route::get('invoice/{id}/product', ['middleware' => 'acl:new_invoice', 'as' => 'admin.invoice.get-product', 'uses' => 'InvoiceController@getProductBySubCategory']);
    Route::post('invoice/store', ['middleware' => 'acl:new_invoice', 'as' => 'admin.invoice.store', 'uses' => 'InvoiceController@postStore']);
    Route::get('invoice/{id}/delete', ['middleware' => 'acl:delete_invoice', 'as' => 'admin.invoice.delete', 'uses' => 'InvoiceController@getDelete']);
    Route::get('bank_acc/{id}', ['middleware' => 'acl:new_invoice', 'as' => 'admin.bank_acc', 'uses' => 'InvoiceController@getBankAcc']);
    Route::get('imvoice_img_delete/{id}', ['middleware' => 'acl:delete_invoice', 'as' => 'admin.imvoice_img_delete', 'uses' => 'InvoiceController@getImgDelete']);

    //Invoice Details
    Route::get('invoice-details/{id}', ['middleware' => 'acl:view_invoice_details', 'as' => 'admin.invoice-details', 'uses' => 'InvoiceDetailsController@getIndex']);
    Route::get('invoice-details/{id}/new', ['middleware' => 'acl:new_invoice_details', 'as' => 'admin.invoice-details.new', 'uses' => 'InvoiceDetailsController@getCreate']);
    Route::get('invoice-details/{id}/delete', ['middleware' => 'acl:delete_invoice_details', 'as' => 'admin.invoice-details.delete', 'uses' => 'InvoiceDetailsController@getDelete']);
    Route::post('invoice-details/variant/list', ['middleware' => 'acl:view_invoice_details', 'as' => 'admin.invoice-details.variant-list', 'uses' => 'InvoiceDetailsController@getVariantListById']);
    Route::get('invoice-details/variant/{bar_code}/list', ['middleware' => 'acl:view_invoice_details', 'as' => 'admin.invoice-details.bar-code/variant-list', 'uses' => 'InvoiceDetailsController@getVariantListByBarCode']);
    Route::get('invoice-details/{id}/product', ['middleware' => 'acl:view_invoice_details', 'as' => 'admin.invoice-details.get-product', 'uses' => 'InvoiceDetailsController@getProductBySubCategory']);
    Route::post('invoice-details/store', ['middleware' => 'acl:new_invoice_details', 'as' => 'admin.invoice-details.store', 'uses' => 'InvoiceDetailsController@postStore']);
    Route::get('invoice-product-details/{id}/{type}', ['middleware' => 'acl:view_invoice_details', 'as' => 'admin.invoice-product-details.get-product', 'uses' => 'InvoiceDetailsController@getProductByInvoice']);


    Route::get('product-variant/search/{bar_code}', ['middleware' => 'acl:view_invoice_details', 'as' => 'admin.product-search', 'uses' => 'InvoiceDetailsController@getVariantListByQueryString']);
    Route::get('invoice_processing', ['middleware' => 'acl:view_invoice_processing', 'as' => 'admin.invoice_processing', 'uses' => 'InvoiceController@invoiceProcessing']);
    Route::get('invoice/stock/{id}/delete', ['middleware' => 'acl:delete_invoice_processing', 'as' => 'admin.stock.delete', 'uses' => 'InvoiceController@getStockDelete']);
    Route::post('invoice_processing/store', ['middleware' => 'acl:new_invoice_processing', 'as' => 'admin.invoice_processing.new', 'uses' => 'InvoiceController@postStoreInvoiceProcessing']);
    Route::get('invoice-qbentry/{id}', ['middleware' => 'acl:view_invoice', 'as' => 'admin.invoice-qbentry', 'uses' => 'InvoiceController@invoiceQBentry']);
    Route::get('invoice-loyalty-claime/{id}', ['middleware' => 'acl:edit_invoice', 'as' => 'admin.loyalty-claime', 'uses' => 'InvoiceController@invoiceLoyaltyClaime']);
    Route::get('invoice-vat-claime/{id}', ['middleware' => 'acl:edit_invoice', 'as' => 'admin.vat-claime', 'uses' => 'InvoiceController@invoiceVatClaime']);
    Route::post('invoice-to-stock/{id}', ['middleware' => 'acl:edit_invoice', 'as' => 'admin.invoice-to-stock', 'uses' => 'InvoiceController@invoiceToStock']);


    //VAT processing
    Route::get('vat-processing', ['middleware' => 'acl:view_vat_processing', 'as' => 'admin.vat_processing', 'uses' => 'VatProcessingController@getIndex']);


    //Product ===
    //Category
    Route::get('category', ['middleware' => 'acl:view_category', 'as' => 'product.category.list', 'uses' => 'CategoryController@getIndex']);
    Route::get('category/new', ['middleware' => 'acl:new_category', 'as' => 'product.category.create', 'uses' => 'CategoryController@getCreate']);
    Route::post('category/store', ['middleware' => 'acl:new_category', 'as' => 'product.category.store', 'uses' => 'CategoryController@postStore']);
    Route::get('category/{id}/edit', ['middleware' => 'acl:edit_category', 'as' => 'product.category.edit', 'uses' => 'CategoryController@getEdit']);
    Route::post('category/{id}/update', ['middleware' => 'acl:edit_category', 'as' => 'product.category.update', 'uses' => 'CategoryController@postUpdate']);
    Route::get('category/{id}/delete', ['middleware' => 'acl:delete_category', 'as' => 'product.category.delete', 'uses' => 'CategoryController@getDelete']);

    //Sub Category
    Route::get('sub_category', ['middleware' => 'acl:view_sub_category', 'as' => 'admin.sub_category.list', 'uses' => 'SubCategoryController@getIndex']);
    Route::get('sub_category/new', ['middleware' => 'acl:new_sub_category', 'as' => 'admin.sub_category.create', 'uses' => 'SubCategoryController@getCreate']);
    Route::post('sub_category/store', ['middleware' => 'acl:new_sub_category', 'as' => 'admin.sub_category.store', 'uses' => 'SubCategoryController@postStore']);
    Route::get('sub_category/{id}/edit', ['middleware' => 'acl:edit_sub_category', 'as' => 'admin.sub_category.edit', 'uses' => 'SubCategoryController@getEdit']);
    Route::post('sub_category/{id}/update', ['middleware' => 'acl:edit_sub_category', 'as' => 'admin.sub_category.update', 'uses' => 'SubCategoryController@postUpdate']);
    Route::get('sub_category/{id}/delete', ['middleware' => 'acl:delete_sub_category', 'as' => 'admin.sub_category.delete', 'uses' => 'SubCategoryController@getDelete']);

    // Order Management
    Route::get('order', ['middleware' => 'acl:view_order', 'as' => 'admin.order.list', 'uses' => 'OrderController@getIndex']);
    Route::get('order/cancelrequest', ['middleware' => 'acl:view_order', 'as' => 'admin.order.cancelrequest', 'uses' => 'OrderController@getCancelRequest']);
    Route::post('order/{id}/cancel', ['middleware' => 'acl:cancel_order', 'as' => 'admin.order.cancel', 'uses' => 'OrderController@postCancel']);
    Route::get('order-altered', ['middleware' => 'acl:view_order', 'as' => 'admin.order_alter.list', 'uses' => 'OrderController@getAlteredIndex']);
    Route::get('default-order', ['middleware' => 'acl:view_order', 'as' => 'admin.order_default.list', 'uses' => 'OrderController@getDefaultIndex']);
    Route::get('default-order-action', ['middleware' => 'acl:view_order', 'as' => 'admin.order_default_action.list', 'uses' => 'OrderController@getDefaultActionIndex']);
    Route::get('default-order-penalty', ['middleware' => 'acl:view_order', 'as' => 'admin.order_default_penalty.list', 'uses' => 'OrderController@getDefaultPenaltyIndex']);
    Route::get('revert-default-order/{id}', ['middleware' => 'acl:edit_order', 'as' => 'admin.order_revert.default', 'uses' => 'OrderController@getDefaultRevert']);
    Route::get('order/canceled', ['middleware' => 'acl:view_order', 'as' => 'admin.order.canceled', 'uses' => 'OrderController@getCancelOrder']);
    Route::post('order/{id}/return', ['middleware' => 'acl:return_order', 'as' => 'admin.order.return', 'uses' => 'OrderController@postReturnOrder']);
    // Route::get('order/new', ['middleware' => 'acl:new_order', 'as' => 'admin.order.new', 'uses' => 'OrderController@getCreate']);
    Route::post('order/store', ['middleware' => 'acl:new_order', 'as' => 'admin.order.store', 'uses' => 'OrderController@postStore']);

    Route::post('order/all_order', ['middleware' => 'acl:view_order', 'as' => 'admin.order.all_order', 'uses' => 'DatatableController@getAllOrder']);
    Route::post('order/cancel_order', ['middleware' => 'acl:view_order', 'as' => 'admin.order.cancel_order', 'uses' => 'DatatableController@getCancelOrder']);
    Route::post('order/altered_order', ['middleware' => 'acl:view_order', 'as' => 'admin.order.altered_order', 'uses' => 'DatatableController@getAlteredOrder']);
    Route::post('order/default_order', ['middleware' => 'acl:view_order', 'as' => 'admin.order.default_order', 'uses' => 'DatatableController@getDefaultOrder']);
    Route::post('order/default_order_action', ['middleware' => 'acl:view_order', 'as' => 'admin.order.default_order_action', 'uses' => 'DatatableController@getDefaultOrderAction']);
    Route::post('order/default_order_penalty', ['middleware' => 'acl:view_order', 'as' => 'admin.order.default_order_penalty', 'uses' => 'DatatableController@getDefaultOrderPenalty']);

    // Route::get('order/{id}/edit', ['middleware' => 'acl:edit_order', 'as' => 'admin.order.edit', 'uses' => 'OrderController@getEdit']);
    // Route::post('order/{id}/update', ['middleware' => 'acl:edit_order', 'as' => 'admin.order.update', 'uses' => 'OrderController@postUpdate']);
    Route::get('order/{id}/delete', ['middleware' => 'acl:delete_order', 'as' => 'admin.order.delete', 'uses' => 'OrderController@getDelete']);
    Route::post('order_admin_hold', ['middleware' => 'acl:edit_order', 'as' => 'admin.order_admin_hold', 'uses' => 'OrderController@postAdminHold']);
    //Route::post('order_self_pickup', ['middleware' => 'acl:edit_order', 'as' => 'admin.order_self_pickup', 'uses' => 'OrderController@postSelfPickup']);
    Route::post('order/rtc-transfer', ['middleware' => 'acl:edit_order', 'as' => 'admin.order.rtc_transfer', 'uses' => 'OrderController@postSelfPickup']);
    Route::post('order/rtc-transfer-ajax', ['middleware' => 'acl:edit_order', 'as' => 'admin.order.rtc_transfer_ajax', 'uses' => 'OrderController@postSelfPickupAjax']);

    //DISPATCH
    Route::get('dispatch', ['middleware' => 'acl:view_dispatch', 'as' => 'admin.dispatch.list', 'uses' => 'DispatchController@getDispatchList']);
    Route::get('dispatched', ['middleware' => 'acl:view_dispatched', 'as' => 'admin.dispatched.list', 'uses' => 'DispatchController@getDispatchedList']);
    Route::get('order/{id}/dispatch', ['middleware' => 'acl:edit_dispatch', 'as' => 'admin.order.dispatch', 'uses' => 'DispatchController@getDispatch']);
    Route::post('order/{id}/dispatchstore', ['middleware' => 'acl:edit_dispatch', 'as' => 'admin.order.dispatchstore', 'uses' => 'DispatchController@postDispatch']);
    Route::post('mark-pickup-list', ['middleware' => 'acl:edit_dispatch', 'as' => 'admin.order.dispatch.mark_pickup', 'uses' => 'DispatchController@postMarkPickup']);
    Route::get('collect-order/{id?}', ['middleware' => 'acl:view_order_collect', 'as' => 'admin.order_collect.list', 'uses' => 'DispatchController@getOrderCollectList']);
    Route::get('collect-order-batch', ['middleware' => 'acl:view_batch_collect', 'as' => 'admin.batch_collect.list', 'uses' => 'DispatchController@getBatchCollectList']);
    Route::get('collect-order-item/{id}', ['middleware' => 'acl:view_item_collect', 'as' => 'admin.item_collect.list', 'uses' => 'DispatchController@getItemCollectList']);
    Route::get('revert-from-batch/{id}', ['middleware' => 'acl:view_item_collect', 'as' => 'admin.item_revert.batch', 'uses' => 'DispatchController@getRevertbatch']);
    Route::post('assign-order-item', ['middleware' => 'acl:assign_item_collect', 'as' => 'admin.order_item.assign', 'uses' => 'DispatchController@postAssignOrderItem']);
    Route::post('bulk-assign-logistic-user', ['middleware' => 'acl:assign_item_collect', 'as' => 'admin.order_bulk_item.assign', 'uses' => 'DispatchController@postAssignOrderBulkItem']);

    Route::get('order-batch-list', ['middleware' => 'acl:view_batch_collected', 'as' => 'admin.batch_collected.list', 'uses' => 'DispatchController@getBatchCollectedList']);
    Route::get('order-item-list/{id}', ['middleware' => 'acl:view_item_collected', 'as' => 'admin.item_collected.list', 'uses' => 'DispatchController@getItemCollectedList']);
    Route::get('pending-dispatch-by-app', ['middleware' => 'acl:view_pending_app_dispach', 'as' => 'admin.pending_by_app.dispatch-list', 'uses' => 'DispatchController@getPendingAppDispatch']);
    Route::get('revert-back-to-previous-stage/{id}', ['middleware' => 'acl:edit_dispatch', 'as' => 'admin.revert_dispatch.dispatch', 'uses' => 'DispatchController@getRevertDispatch']);

    Route::post('ajax/special_note_status', ['middleware' => 'acl:view_item_collected', 'as' => 'admin.special_note.status', 'uses' => 'DispatchController@postSpecialNoteStatus']);

    //AJAX
    Route::post('collect-order-datatable', ['middleware' => 'acl:view_order_collect', 'as' => 'admin.order_collect.datalist', 'uses' => 'DatatableController@getOrderCollection']);
    Route::post('collect-item-datatable', ['middleware' => 'acl:view_item_collect', 'as' => 'admin.item_collect.datalist', 'uses' => 'DatatableController@getItemCollection']);
    Route::post('collected-item-datatable', ['middleware' => 'acl:view_item_collected', 'as' => 'admin.item_collected.datalist', 'uses' => 'DatatableController@getItemCollectedList']);

    // Route::post('get-customer-details', ['middleware' => 'acl:new_booking', 'as' => 'admin.booking.getproduct', 'uses' => 'OrderController@getCusInfo']);

    //COLLECTION LIST
    Route::get('collection-list', ['middleware' => 'acl:view_collection_list', 'as' => 'admin.collection.list', 'uses' => 'DispatchController@getCollectionList']);
    Route::get('collection-list/{id}', ['middleware' => 'acl:view_collection_list_breakdown', 'as' => 'admin.collection.list.breakdown', 'uses' => 'DispatchController@getCollectionListBreakdown']);

    //COD/RTC SHELVE STOCK LIST
    Route::get('stock-list/{id}/shelve', ['middleware' => 'acl:view_cod_user_stock_list', 'as' => 'admin.cod_user.stock_list', 'uses' => 'DispatchController@getCodRtcUserStockList']);

    //HS code
    Route::get('hscode', ['middleware' => 'acl:view_hscode', 'as' => 'admin.hscode.list', 'uses' => 'HscodeController@getIndex']);
    Route::get('hscode/new', ['middleware' => 'acl:new_hscode', 'as' => 'admin.hscode.create', 'uses' => 'HscodeController@getCreate']);
    Route::post('hscode/store', ['middleware' => 'acl:new_hscode', 'as' => 'admin.hscode.store', 'uses' => 'HscodeController@postStore']);
    Route::get('hscode/{id}/edit', ['middleware' => 'acl:edit_hscode', 'as' => 'admin.hscode.edit', 'uses' => 'HscodeController@getEdit']);
    Route::post('hscode/{id}/update', ['middleware' => 'acl:edit_hscode', 'as' => 'admin.hscode.update', 'uses' => 'HscodeController@postUpdate']);
    Route::get('hscode/{id}/delete', ['middleware' => 'acl:delete_hscode', 'as' => 'admin.hscode.delete', 'uses' => 'HscodeController@getDelete']);

    //Seeker
    Route::get('seeker', ['middleware' => 'acl:view_seeker', 'as' => 'admin.seeker.list', 'uses' => 'SeekerController@getIndex']);
    Route::get('seeker/{id}/view', ['middleware' => 'acl:view_seeker', 'as' => 'admin.seeker.view', 'uses' => 'SeekerController@getView']);
    Route::get('seeker/{id}/edit', ['middleware' => 'acl:edit_seeker', 'as' => 'admin.seeker.edit', 'uses' => 'SeekerController@getEdit']);
    Route::post('seeker/update', ['middleware' => 'acl:edit_seeker', 'as' => 'admin.seeker.update', 'uses' => 'SeekerController@postUpdate']);
    Route::get('seeker/{id}/payment', ['middleware' => 'acl:view_seeker_payment', 'as' => 'admin.seeker.payment', 'uses' => 'SeekerController@getPayment']);
    Route::get('seeker/{id}/recharge', ['middleware' => 'acl:view_seeker_payment', 'as' => 'admin.seeker.recharge', 'uses' => 'SeekerController@getRecharge']);
    Route::post('seeker/{id}/recharge', ['middleware' => 'acl:add_seeker_payment', 'as' => 'admin.seeker.recharge', 'uses' => 'SeekerController@postRecharge']);
    Route::get('seeker/get_area/{id}', ['middleware' => 'acl:view_seeker_payment', 'as' => 'admin.seeker.get_area', 'uses' => 'SeekerController@getArea']);
    Route::post('seeker_list', ['middleware' => 'acl:view_seeker', 'as' => 'ajax.seeker.list', 'uses' => 'DataTableController@getSeeker']);
    Route::get('payment-account', ['middleware' => 'acl:view_payment_account', 'as' => 'ajax.payment-account.list', 'uses' => 'SeekerController@paymentAccount']);

/*

    Route::get('customer/new', ['middleware' => 'acl:new_customer', 'as' => 'admin.customer.create', 'uses' => 'CustomerController@getCreate']);
    Route::post('customer/store', ['middleware' => 'acl:new_customer', 'as' => 'admin.customer.store', 'uses' => 'CustomerController@postStore']);
    Route::post('customer/blance-transfer', ['middleware' => 'acl:new_customer', 'as' => 'admin.customer.blance_transfer', 'uses' => 'CustomerController@postBlanceTransfer']);
    Route::post('customer/store/booking', ['middleware' => 'acl:new_customer', 'as' => 'admin.customer.store.booking', 'uses' => 'CustomerController@addNewCustomer']);
    Route::get('customer/{id}/edit', ['middleware' => 'acl:edit_customer', 'as' => 'admin.customer.edit', 'uses' => 'CustomerController@getEdit']);
    Route::get('customer/{id}/payment-history', ['middleware' => 'acl:edit_customer', 'as' => 'admin.customer.payment_history', 'uses' => 'CustomerController@getPaymentHistory']);
    Route::post('customer/{id}/update', ['middleware' => 'acl:edit_customer', 'as' => 'admin.customer.update', 'uses' => 'CustomerController@postUpdate']);
    Route::get('customer/{id}/delete', ['middleware' => 'acl:delete_customer', 'as' => 'admin.customer.delete', 'uses' => 'CustomerController@getDelete']);

    */


    Route::get('parent-root/{type}', ['middleware' => 'acl:view_customer', 'as' => 'admn.customer.root', 'uses' => 'CustomerController@getCombo']);
    Route::get('customer/{id}/view', ['middleware' => 'acl:view_customer', 'as' => 'admin.customer.view', 'uses' => 'CustomerController@getView']);
    Route::get('get/{id}/remainingcustomerbalance/', ['middleware' => 'acl:view_customer', 'as' => 'admin.remainingcustomerbalance', 'uses' => 'CustomerController@getRemainingBalance']);
    Route::get('customer/{id}/history', ['middleware' => 'acl:view_customer', 'as' => 'admin.customer.history', 'uses' => 'CustomerController@getHistory']);

    Route::get('customer/{id}/refund/{type}', ['middleware' => 'acl:new_refund', 'as' => 'admin.payment.refund', 'uses' => 'RefundController@getRefund']);
    Route::post('customer/refund/store', ['middleware' => 'acl:new_refund', 'as' => 'admin.paymentrefund.store', 'uses' => 'RefundController@postRefund']);

    Route::get('customer/refund', ['middleware' => 'acl:view_refund', 'as' => 'admin.customer.refund', 'uses' => 'RefundController@getIndex']);
    Route::post('customer/refundrequest/store', ['middleware' => 'acl:new_refund', 'as' => 'admin.customer.refundrequeststore', 'uses' => 'RefundController@postRefundRequest']);
    Route::get('customer/refundrequest', ['middleware' => 'acl:view_refund', 'as' => 'admin.customer.refundrequest', 'uses' => 'RefundController@getrefundRequestList']);
    Route::get('customer/refunded', ['middleware' => 'acl:view_refund', 'as' => 'admin.customer.refunded', 'uses' => 'RefundController@getRefunded']);
    Route::get('customer/refundrequest/{id}/deny', ['middleware' => 'acl:edit_refund', 'as' => 'admin.customer.refundrequest_deny', 'uses' => 'RefundController@getRefundedRequestDeny']);


    //Customer Address
    Route::get('customer-address', ['middleware' => 'acl:view_customer_address', 'as' => 'admin.customer-address.list', 'uses' => 'CustomerAddressController@getIndex']);
    Route::get('customer-address/{id}/new', ['middleware' => 'acl:new_customer_address', 'as' => 'admin.customer-address.create', 'uses' => 'CustomerAddressController@getCreate']);
    Route::post('customer-address/store', ['middleware' => 'acl:new_customer_address', 'as' => 'admin.customer-address.store', 'uses' => 'CustomerAddressController@postStore']);
    Route::get('customer-address/{id}/edit', ['middleware' => 'acl:edit_customer_address', 'as' => 'admin.customer-address.edit', 'uses' => 'CustomerAddressController@getEdit']);
    Route::post('customer-address/{id}/update', ['middleware' => 'acl:edit_customer_address', 'as' => 'admin.customer-address.update', 'uses' => 'CustomerAddressController@postUpdate']);
    Route::get('customer-address/{id}/delete', ['middleware' => 'acl:delete_customer_address', 'as' => 'admin.customer-address.delete', 'uses' => 'CustomerAddressController@getDelete']);
    Route::get('getCustomerAddressEdit/{customer_id}/{id}/{is_reseller?}', ['middleware' => 'acl:edit_customer_address', 'as' => 'admin.customer-address.order_edit', 'uses' => 'CustomerAddressController@getCustomerAddressEdit']);
    Route::get('getCustomerByName/{customer_name}/{type?}', ['middleware' => 'acl:edit_customer_address', 'as' => 'admin.customer-address.order_getcusinfo', 'uses' => 'CustomerAddressController@getCustomerByName']);

    Route::get('get-post-code', ['middleware' => 'acl:new_customer_address', 'as' => 'admin.customer-address.creates', 'uses' => 'CustomerAddressController@search']);

    //AJAX ROUTE FOR CUSTOMER_ADDRESS
    Route::get('customer_state/{id}', ['middleware' => 'acl:new_customer_address', 'as' => 'admin.customer_state', 'uses' => 'CustomerAddressController@getState']);
    Route::get('customer_city/{id}', ['middleware' => 'acl:new_customer_address', 'as' => 'admin.customer_city', 'uses' => 'CustomerAddressController@getCity']);
    Route::get('customer_pCode/{city_id}/{state_id}', ['middleware' => 'acl:new_customer_address', 'as' => 'admin.customer_pCode', 'uses' => 'CustomerAddressController@getPostC']);
    Route::get('customer_city_by_state/{id}', ['middleware' => 'acl:new_customer_address', 'as' => 'admin.customer_city_by_state', 'uses' => 'CustomerAddressController@getCitybyState']);
    Route::get('customer_postage_by_city/{id}', ['middleware' => 'acl:new_customer_address', 'as' => 'admin.customer_postage_by_city', 'uses' => 'CustomerAddressController@getPostagebyCity']);

    //Agent
    Route::get('agents', ['middleware' => 'acl:agent_view', 'as' => 'admin.agents.list', 'uses' => 'AgentsController@getIndex']);
    Route::get('agents/new', ['middleware' => 'acl:agent_new', 'as' => 'admin.agents.create', 'uses' => 'AgentsController@getCreate']);
    Route::post('agents/store', ['middleware' => 'acl:agent_store', 'as' => 'admin.agents.store', 'uses' => 'AgentsController@postStore']);
    Route::get('agents/{id}/edit', ['middleware' => 'acl:agent_edit', 'as' => 'admin.agents.edit', 'uses' => 'AgentsController@getEdit']);
    Route::post('agents/{id}/update', ['middleware' => 'acl:agent_update', 'as' => 'admin.agents.update', 'uses' => 'AgentsController@postUpdate']);
    Route::get('agents/{id}/delete', ['middleware' => 'acl:agent_delete', 'as' => 'admin.agents.delete', 'uses' => 'AgentsController@getDelete']);
    Route::get('agents-earnings/{id}', ['middleware' => 'acl:view_agent_earnings', 'as' => 'admin.agent_earnings', 'uses' => 'AgentsController@getEarnings']);
    Route::get('agents-withdraw_credit', ['middleware' => 'acl:view_agent_earnings', 'as' => 'admin.withdraw_credit', 'uses' => 'AgentsController@getWithdrawCredit']);
    Route::post('agents_list', ['middleware' => 'acl:view_agent', 'as' => 'ajax.agent.list', 'uses' => 'DataTableController@getAgents']);

    //earnings
    //Route::get('earnings', ['as' => 'admin.earnings.list', 'uses' => 'EarningsController@getIndex']);

    //listing pricing
    Route::get('listing-price', ['middleware' => 'acl:listing_price_view', 'as' => 'admin.listing_price.list', 'uses' => 'ListingPriceController@getIndex']);
    Route::post('listing-price/update', ['middleware' => 'acl:listing_price_update', 'as' => 'admin.listing_price.update', 'uses' => 'ListingPriceController@postUpdate']);
    Route::post('listing-lead-price/update', ['middleware' => 'acl:listing_price_update', 'as' => 'admin.listing_lead_price.update', 'uses' => 'ListingPriceController@postLeadPriceUpdate']);

    //Customer Address Type
    Route::get('address-type', ['middleware' => 'acl:view_address_type', 'as' => 'admin.address_type.list', 'uses' => 'AddressController@getIndex']);
    Route::get('address-type/new', ['middleware' => 'acl:new_address_type', 'as' => 'admin.address_type.create', 'uses' => 'AddressController@getCreate']);
    Route::post('address-type/store', ['middleware' => 'acl:new_address_type', 'as' => 'admin.address_type.store', 'uses' => 'AddressController@postStore']);
    Route::get('address-type/{id}/edit', ['middleware' => 'acl:edit_address_type', 'as' => 'admin.address_type.edit', 'uses' => 'AddressController@getEdit']);
    Route::post('address-type/{id}/update', ['middleware' => 'acl:edit_address_type', 'as' => 'admin.address_type.update', 'uses' => 'AddressController@postUpdate']);
    Route::get('address-type/{id}/delete', ['middleware' => 'acl:delete_address_type', 'as' => 'admin.address_type.delete', 'uses' => 'AddressController@getDelete']);

    //GENERAL INFO OF BDFLATS
    Route::get('generalinfo', ['middleware' => 'acl:view_general_info', 'as' => 'admin.generalinfo', 'uses' => 'WebInfoController@getCreate']);
    Route::post('generalinfo/store', ['middleware' => 'acl:new_general_info', 'as' => 'admin.generalinfo.update', 'uses' => 'WebInfoController@postStore']);


    //PROPERTY CATEGORY
    Route::get('property/category', ['middleware' => 'acl:list_box', 'as' => 'admin.property.category', 'uses' => 'PropertyCategoryController@getIndex']);
    Route::get('property/category/new', ['middleware' => 'acl:new_property_category', 'as' => 'property.category.create', 'uses' => 'PropertyCategoryController@getCreate']);
    Route::post('property/category/store', ['middleware' => 'acl:new_property_category', 'as' => 'admin.property.category.store', 'uses' => 'PropertyCategoryController@postStore']);
    Route::get('property/category/{id}/edit', ['middleware' => 'acl:edit_property_category', 'as' => 'property.category.edit', 'uses' => 'PropertyCategoryController@getEdit']);
    Route::post('property/category/{id}/update', ['middleware' => 'acl:edit_property_category', 'as' => 'property.category.update', 'uses' => 'PropertyCategoryController@postUpdate']);

    // Property Condition
    Route::get('property/condition', ['middleware' => 'acl:view_property_condition', 'as' => 'admin.property.condition', 'uses' => 'PropertyConditionController@getIndex']);
    Route::get('property/condition/new', ['middleware' => 'acl:add_property_condition', 'as' => 'admin.property.condition.create', 'uses' => 'PropertyConditionController@getCreate']);
    Route::post('property/condition/store', ['middleware' => 'acl:add_property_condition', 'as' => 'admin.property.condition.store', 'uses' => 'PropertyConditionController@postStore']);
    Route::get('property/condition/{id}/edit', ['middleware' => 'acl:edit_property_condition', 'as' => 'admin.property.condition.edit', 'uses' => 'PropertyConditionController@getEdit']);
    Route::post('property/condition/{id}/update', ['middleware' => 'acl:edit_property_condition', 'as' => 'admin.property.condition.update', 'uses' => 'PropertyConditionController@postUpdate']);
    Route::get('property/condition/{id}/delete', ['middleware' => 'acl:delete_property_condition', 'as' => 'admin.property.condition.delete', 'uses' => 'PropertyConditionController@getDelete']);

    // Property Features
    Route::get('property/features', ['middleware' => 'acl:view_property_features', 'as' => 'admin.property.features', 'uses' => 'PropertyFeaturesController@getIndex']);
    Route::get('property/features/new', ['middleware' => 'acl:add_property_features', 'as' => 'admin.property.features.create', 'uses' => 'PropertyFeaturesController@getCreate']);
    Route::post('property/features/store', ['middleware' => 'acl:add_property_features', 'as' => 'admin.property.features.store', 'uses' => 'PropertyFeaturesController@postStore']);
    Route::get('property/features/{id}/edit', ['middleware' => 'acl:edit_property_features', 'as' => 'admin.property.features.edit', 'uses' => 'PropertyFeaturesController@getEdit']);
    Route::post('property/features/{id}/update', ['middleware' => 'acl:edit_property_features', 'as' => 'admin.property.features.update', 'uses' => 'PropertyFeaturesController@postUpdate']);
    Route::get('property/features/{id}/delete', ['middleware' => 'acl:delete_property_features', 'as' => 'admin.property.features.delete', 'uses' => 'PropertyFeaturesController@getDelete']);

    // Property Floor
    Route::get('property/floor', ['middleware' => 'acl:view_property_floor', 'as' => 'admin.property.floor', 'uses' => 'PropertyFloorController@getIndex']);
    Route::get('property/floor/new', ['middleware' => 'acl:add_property_floor', 'as' => 'admin.property.floor.create', 'uses' => 'PropertyFloorController@getCreate']);
    Route::post('property/floor/store', ['middleware' => 'acl:add_property_floor', 'as' => 'admin.property.floor.store', 'uses' => 'PropertyFloorController@postStore']);
    Route::get('property/floor/{id}/edit', ['middleware' => 'acl:edit_property_floor', 'as' => 'admin.property.floor.edit', 'uses' => 'PropertyFloorController@getEdit']);
    Route::post('property/floor/{id}/update', ['middleware' => 'acl:edit_property_floor', 'as' => 'admin.property.floor.update', 'uses' => 'PropertyFloorController@postUpdate']);
    Route::get('property/floor/{id}/delete', ['middleware' => 'acl:delete_property_floor', 'as' => 'admin.property.floor.delete', 'uses' => 'PropertyFloorController@getDelete']);

    // Property Facing
    Route::get('property/facing', ['middleware' => 'acl:view_property_facing', 'as' => 'admin.property.facing', 'uses' => 'PropertyFacingController@getIndex']);
    Route::get('property/facing/new', ['middleware' => 'acl:add_property_facing', 'as' => 'admin.property.facing.create', 'uses' => 'PropertyFacingController@getCreate']);
    Route::post('property/facing/store', ['middleware' => 'acl:add_property_facing', 'as' => 'admin.property.facing.store', 'uses' => 'PropertyFacingController@postStore']);
    Route::get('property/facing/{id}/edit', ['middleware' => 'acl:edit_property_facing', 'as' => 'admin.property.facing.edit', 'uses' => 'PropertyFacingController@getEdit']);
    Route::post('property/facing/{id}/update', ['middleware' => 'acl:edit_property_facing', 'as' => 'admin.property.facing.update', 'uses' => 'PropertyFacingController@postUpdate']);
    Route::get('property/facing/{id}/delete', ['middleware' => 'acl:delete_property_facing', 'as' => 'admin.property.facing.delete', 'uses' => 'PropertyFacingController@getDelete']);

    // City / Division / Area
    Route::get('property/city', ['middleware' => 'acl:view_city', 'as' => 'admin.city.list', 'uses' => 'CityController@getIndex']);
    Route::get('property/city/new', ['middleware' => 'acl:add_city', 'as' => 'admin.city.new', 'uses' => 'CityController@getCreate']);
    Route::post('property/city/store', ['middleware' => 'acl:add_city', 'as' => 'admin.city.store', 'uses' => 'CityController@postStore']);
    Route::get('property/city/{id}/edit', ['middleware' => 'acl:edit_city', 'as' => 'admin.city.edit', 'uses' => 'CityController@getEdit']);
    Route::post('property/city/{id}/update', ['middleware' => 'acl:edit_city', 'as' => 'admin.city.update', 'uses' => 'CityController@postUpdate']);

    Route::get('property/area', ['middleware' => 'acl:view_area', 'as' => 'admin.area.list', 'uses' => 'AreaController@getIndex']);
    Route::get('property/area/new', ['middleware' => 'acl:add_area', 'as' => 'admin.area.new', 'uses' => 'AreaController@getCreate']);
    Route::get('property/area/get', ['middleware' => 'acl:add_area', 'as' => 'admin.area.get', 'uses' => 'AreaController@getArea']);
    Route::post('property/area/store', ['middleware' => 'acl:add_area', 'as' => 'admin.area.store', 'uses' => 'AreaController@postStore']);
    Route::get('property/area/{id}/edit', ['middleware' => 'acl:edit_area', 'as' => 'admin.area.edit', 'uses' => 'AreaController@getEdit']);
    Route::post('property/area/{id}/update', ['middleware' => 'acl:edit_area', 'as' => 'admin.area.update', 'uses' => 'AreaController@postUpdate']);

    //POSTCODE CITY ADDRESS ADD UPDATE
    Route::get('address-type-city/list', ['middleware' => 'acl:view_city_list', 'as' => 'admin.address_type.city_list_', 'uses' => 'AddressController@getCityList']);
    Route::get('address-type-post-code/list', ['middleware' => 'acl:view_postage_list', 'as' => 'admin.address_type.postage_list_', 'uses' => 'AddressController@getPostageList']);
    Route::get('address-type-post-code/{id?}', ['middleware' => 'acl:edit_postage_list', 'as' => 'admin.address_type.postage_view_', 'uses' => 'AddressController@getPostageAddress']);
    Route::get('address-type-city/{id?}', ['middleware' => 'acl:edit_city_list', 'as' => 'admin.address_type.city_list', 'uses' => 'AddressController@getCityAddress']);
    Route::post('post-address-type-city/{id}', ['middleware' => 'acl:edit_city_list', 'as' => 'admin.customer_address_city.put', 'uses' => 'AddressController@postCityAddress']);
    Route::post('post-address-type-postage/{id}', ['middleware' => 'acl:edit_postage_list', 'as' => 'admin.customer_address_postage.put', 'uses' => 'AddressController@postPostageAddress']);
    //AJAX
    Route::get('customer_state_by_country/{country}', ['middleware' => 'acl:edit_postage_list', 'as' => 'admin.address_type.city_list_ajax', 'uses' => 'AddressController@ajaxStateByCountry']);

    //SEARCH & BOOK
    Route::get('search-&-book', ['middleware' => 'acl:new_search_booking', 'as' => 'admin.booking.search_create', 'uses' => 'SearchBookingController@getCreate']);

    //Booking

    Route::get('booking/new/{id?}/{type?}', ['middleware' => 'acl:new_booking', 'as' => 'admin.booking.create', 'uses' => 'BookingController@getCreate']);
    Route::post('booking/store', ['middleware' => 'acl:new_booking', 'as' => 'admin.booking.store', 'uses' => 'BookingController@postStore']);
    Route::get('booking/{id}/edit', ['middleware' => 'acl:edit_booking', 'as' => 'admin.booking.edit', 'uses' => 'BookingController@getEdit']);
    Route::get('booking/{id}/view', ['middleware' => 'acl:view_booking', 'as' => 'admin.booking.view', 'uses' => 'BookingController@getView']);
    Route::post('booking/{id?}/put/{type?}/{type2?}', ['middleware' => 'acl:edit_booking', 'as' => 'admin.booking.put', 'uses' => 'BookingController@postUpdate']);
    Route::get('booking/{id}/delete', ['middleware' => 'acl:delete_booking', 'as' => 'admin.booking.delete', 'uses' => 'BookingController@getDelete']);
    Route::post('booking/offer-apply', ['middleware' => 'acl:edit_booking', 'as' => 'admin.booking.offer-apply', 'uses' => 'BookingController@postOfferApply']);
    Route::post('check-offer', ['middleware' => 'acl:new_booking', 'as' => 'admin.booking.checkoffer', 'uses' => 'BookingController@postCheckOffer']);

    Route::get('booking/{id?}/{type?}', ['middleware' => 'acl:view_booking', 'as' => 'admin.booking.list', 'uses' => 'BookingController@getIndex']);
    Route::get('get-variant-info', ['middleware' => 'acl:view_booking', 'as' => 'admin.booking.product', 'uses' => 'BookingController@search']);
    Route::get('get-customer-info', ['middleware' => 'acl:view_booking', 'as' => 'admin.booking.product', 'uses' => 'CustomerController@getCustomer']);
    Route::get('get-prd-details', ['middleware' => 'acl:view_booking', 'as' => 'admin.booking.getproduct', 'uses' => 'BookingController@getAllInfo']);
    Route::post('get-customer-details', ['middleware' => 'acl:view_booking', 'as' => 'admin.booking.getproduct', 'uses' => 'BookingController@getCusInfo']);
    Route::get('call-procedure-booking', ['as' => 'admin.booking.procedure', 'uses' => 'BookingController@callProcedure']);


    //Booking to order
    Route::get('booking/{id}/check-offer', ['middleware' => 'acl:edit_booking', 'as' => 'admin.booking.checkoffer', 'uses' => 'BookingController@checkOffer']);
    Route::get('booking-to-order/{id}', ['middleware' => 'acl:edit_booking', 'as' => 'admin.booking_to_order.create', 'uses' => 'BookingToOrderController@getBooking']);

    // Route::get('orderbooking/{id}/book-order/view',['middleware' => 'acl:view_order', 'as' => 'admin.booking_to_order.book-order-view', 'uses' => 'BookingToOrderController@getBookOrderView']);

    Route::get('order/{id}/view', ['middleware' => 'acl:view_order', 'as' => 'admin.booking_to_order.book-order-view', 'uses' => 'BookingToOrderController@getView']);

    // Route::get('orderbooking/{id}/book-order',['middleware' => 'acl:edit_order', 'as' => 'admin.booking_to_order.book-order', 'uses' => 'BookingToOrderController@getBookOrder']);
    Route::get('order/{id}/edit', ['middleware' => 'acl:edit_order', 'as' => 'admin.booking_to_order.book-order', 'uses' => 'BookingToOrderController@getEdit']);

    Route::post('order/senderaddress/{id}/update', ['middleware' => 'acl:edit_order', 'as' => 'admin.senderaddress.update', 'uses' => 'OrderController@updateSenderaddress']);
    Route::post('order/receiveraddress/{id}/update', ['middleware' => 'acl:edit_order', 'as' => 'admin.receiveraddress.update', 'uses' => 'OrderController@updateReceiverAddress']);

    Route::post('booking-to-order/{id}/update', ['middleware' => 'acl:edit_order', 'as' => 'admin.bookingtoorder.update', 'uses' => 'BookingToOrderController@updateBooktoOrder']);
    Route::get('order/{id}/admin-approval', ['middleware' => 'acl:edit_order', 'as' => 'admin.booking_to_order.admin-approval', 'uses' => 'BookingToOrderController@getBookOrderAdminApproval']);
    Route::post('booking-to-order/{id}/admin-approved', ['middleware' => 'acl:edit_order', 'as' => 'admin.bookingtoorder.admin-approved', 'uses' => 'BookingToOrderController@updateBooktoOrderAdminApproved']);

    //AJAX
    Route::get('delete_book_to_order_item/{id}/{type?}/{booking_no?}', ['middleware' => 'acl:edit_order', 'as' => 'admin.booking_to_order_delete_ajax.book-order', 'uses' => 'BookingToOrderController@ajaxDelete']);
    Route::post('update_order_payment', ['middleware' => 'acl:edit_booking', 'as' => 'admin.booking_to_order_payment_ajax.book-order', 'uses' => 'BookingToOrderController@ajaxPayment']);

    Route::get('booking/getCustomerAddress/{id}/{pk_no}/{address_id?}/{reseller_id?}', ['middleware' => 'acl:view_order', 'as' => 'admin.bookingtoorder.getCustomerAddress', 'uses' => 'BookingToOrderController@getCustomerAddress']);

    Route::post('postCustomerAddress', ['middleware' => 'acl:edit_order', 'as' => 'admin.booking_to_order_ajax.postCustomerAddress', 'uses' => 'BookingToOrderController@postCustomerAddress']);

    Route::post('postCustomerAddress2', ['middleware' => 'acl:edit_order', 'as' => 'admin.customerAddress.add', 'uses' => 'BookingToOrderController@postCustomerAddress2']);

    Route::get('checkifCustomerAddressexists/{customer_id}/{type}/{book_id?}', ['middleware' => 'acl:view_order', 'as' => 'admin.bookingtoorder.checkifCustomerAddressexists', 'uses' => 'BookingToOrderController@checkifCustomerAddressexists']);
    Route::get('bookorder/getPayInfo/{order_id}/{is_reseller}', ['middleware' => 'acl:view_order', 'as' => 'admin.bookingtoorder.getPayInfo', 'uses' => 'BookingToOrderController@getPayInfo']);
    Route::post('postUpdatedAddress/{order_id}/{type}', ['middleware' => 'acl:edit_order', 'as' => 'admin.booking_to_order_ajax.postUpdatedAddress', 'uses' => 'BookingToOrderController@postUpdatedAddress']);
    Route::post('postPaymentUncheck', ['middleware' => 'acl:edit_order', 'as' => 'admin.booking_to_order_ajax.postPaymentUncheck', 'uses' => 'BookingToOrderController@postPaymentUncheck']);
    Route::get('getStockExchangeInfo/{id}', ['middleware' => 'acl:edit_order', 'as' => 'admin.booking_to_order_stock_exchange_ajax', 'uses' => 'BookingToOrderController@ajaxExchangeStock']);
    Route::post('getStockExchangeInfo-exchange', ['middleware' => 'acl:edit_order', 'as' => 'admin.post_booking_to_order_stock_exchange_ajax', 'uses' => 'BookingToOrderController@ajaxExchangeStockAction']);

    Route::post('default-order-penalty/{id}', ['middleware' => 'acl:edit_order', 'as' => 'admin.default.order.penalty', 'uses' => 'BookingToOrderController@postDefaultOrderPenalty']);

    //Payment

    Route::get('payment', ['middleware' => 'acl:view_payment', 'as' => 'admin.payment.list', 'uses' => 'PaymentController@getIndex']);

    // Route::get('payment/verify/{id}/{type}',['middleware'=>'acl:edit_payment', 'as'=>'admin.payment.verify', 'uses'=>'PaymentController@getVrify']);

    Route::get('payment/new/{id?}/{type?}', ['middleware' => 'acl:view_payment', 'as' => 'admin.payment.create', 'uses' => 'PaymentController@getCreate']);
    Route::get('payment/{id?}/details', ['middleware' => 'acl:view_payment', 'as' => 'admin.payment.details', 'uses' => 'PaymentController@getDetails']);

    Route::post('payment/store', ['middleware' => 'acl:new_payment', 'as' => 'admin.payment.store', 'uses' => 'PaymentController@postStore']);

    //Route::get('payment/{id}/edit',['middleware'=>'acl:edit_payment', 'as'=>'admin.payment.edit', 'uses'=>'PaymentController@getEdit']);
    //Route::post('payment/{id}/update',['middleware'=>'acl:edit_payment', 'as'=>'admin.payment.update', 'uses'=>'PaymentController@postUpdate']);

    Route::get('payment/{id}/delete', ['middleware' => 'acl:delete_payment', 'as' => 'admin.payment.delete', 'uses' => 'PaymentController@getDelete']);
    Route::get('orderpayment/{id}/delete', ['middleware' => 'acl:delete_payment', 'as' => 'admin.orderpayment.delete', 'uses' => 'PaymentController@getOrderPaymentDelete']);

    Route::post('payment/update-partial', ['middleware' => 'acl:edit_payment', 'as' => 'admin.payment.updatepartial', 'uses' => 'PaymentController@postUpdatePartial']);

    Route::get('payment-processing', ['middleware' => 'acl:view_payment_processing', 'as' => 'admin.payment_processing.list', 'uses' => 'PaymentController@getPaymentProcessing']);
    Route::get('bank-to-other/{id?}', ['middleware' => 'acl:new_bank_to_other', 'as' => 'admin.account_to_other.view', 'uses' => 'PaymentController@getBankToOther']);
    Route::get('bank-to-other-list', ['middleware' => 'acl:view_bank_to_other', 'as' => 'admin.account_to_other_list.view', 'uses' => 'PaymentController@getBankToOtherList']);
    Route::get('party-transfer-details/{id}', ['middleware' => 'acl:view_bank_to_other', 'as' => 'admin.account_to_other.details', 'uses' => 'PaymentController@getBankToOtherDetails']);
    Route::post('add-new-type', ['middleware' => 'acl:new_bank_to_other', 'as' => 'admin.account_to_other.type.store', 'uses' => 'PaymentController@postNewPaymentType']);
    Route::post('bank-to-other-store', ['middleware' => 'acl:new_bank_to_other', 'as' => 'admin.account_to_other.store', 'uses' => 'PaymentController@postbankToOther']);
    Route::get('bank-to-bank/{id?}', ['middleware' => 'acl:new_bank_to_bank', 'as' => 'admin.account_to_bank.view', 'uses' => 'PaymentController@getBankToBank']);
    Route::get('bank-to-bank-list', ['middleware' => 'acl:view_bank_to_bank', 'as' => 'admin.account_to_bank_list.view', 'uses' => 'PaymentController@getBankToBankList']);
    Route::get('internal-transfer-details/{id}', ['middleware' => 'acl:view_bank_to_bank', 'as' => 'admin.account_to_bank.details', 'uses' => 'PaymentController@getBankToBankDetails']);
    Route::post('bank-to-bank-store', ['middleware' => 'acl:new_bank_to_bank', 'as' => 'admin.account_to_bank.store', 'uses' => 'PaymentController@postbankToBank']);

    //DATATABLE
    Route::post('bank-to-other-list-ajax', ['middleware' => 'acl:view_bank_to_other', 'as' => 'admin.account_to_other_ajax.list', 'uses' => 'DatatableController@ajaxbankToOther']);
    Route::post('bank-to-bank-list-ajax', ['middleware' => 'acl:view_bank_to_bank', 'as' => 'admin.account_to_bank_ajax.list', 'uses' => 'DatatableController@ajaxbankToBank']);

    //AJAX
    Route::post('postAccountBalanceInfo', ['middleware' => 'acl:new_bank_to_bank', 'as' => 'admin.account.bank.balance', 'uses' => 'PaymentController@postAccountBalanceInfo']);

    //////////////////// BANK STATEMENT  //////////////////
    Route::get('bank-state', ['middleware' => 'acl:view_bank_state', 'as' => 'admin.bankstate.list', 'uses' => 'BankStateController@getIndex']);
    Route::get('get-bank-state', ['middleware' => 'acl:view_bank_state', 'as' => 'admin.getbankstate.list', 'uses' => 'BankStateController@getMatchingList']);
    Route::post('bank-state/store', ['middleware' => 'acl:new_bank_state', 'as' => 'admin.bankstate.store', 'uses' => 'BankStateController@postStore']);
    Route::get('bank-state/{id}/delete', ['middleware' => 'acl:delete_bank_state', 'as' => 'admin.bankstate.delete', 'uses' => 'BankStateController@getDelete']);
    Route::post('bank-state/delete_bulk', ['middleware' => 'acl:delete_bank_state', 'as' => 'admin.bankstate.delete_bulk', 'uses' => 'BankStateController@postDeleteBulk']);
    Route::post('bank-state/draft-to-save', ['middleware' => 'acl:edit_bank_state', 'as' => 'admin.bankstate.draft_to_save', 'uses' => 'BankStateController@postDraftToSave']);
    Route::post('bank-state/mark-as-used', ['middleware' => 'acl:edit_bank_state', 'as' => 'admin.bankstate.mark_as_used', 'uses' => 'BankStateController@postMarkAsUsed']);
    Route::get('bank-state/verification', ['middleware' => 'acl:edit_bank_state', 'as' => 'admin.bankstate.verification', 'uses' => 'BankStateController@getVerification']);
    Route::post('bank-state/verify', ['middleware' => 'acl:edit_bank_state', 'as' => 'admin.bankstate.verify', 'uses' => 'BankStateController@postVerify']);
    Route::get('bank-state/{id}/unverify', ['middleware' => 'acl:edit_bank_state', 'as' => 'admin.bankstate.unverify', 'uses' => 'BankStateController@getUnVerify']);


    //Shipment
    Route::get('shipment/new/{id?}', ['middleware' => 'acl:new_shipment', 'as' => 'admin.shipment.create', 'uses' => 'ShipmentController@getCreate']);
    Route::post('shipment/store', ['middleware' => 'acl:new_shipment', 'as' => 'admin.shipment.store', 'uses' => 'ShipmentController@postStore']);
    Route::post('shipment/carrier/update', ['middleware' => 'acl:new_shipment', 'as' => 'admin.shipment.carrier', 'uses' => 'ShipmentController@postCarrier']);
    Route::get('shipment/list', ['middleware' => 'acl:view_shipment', 'as' => 'admin.shipment.list', 'uses' => 'ShipmentController@getIndex']);
    Route::get('shipment/processing', ['middleware' => 'acl:view_shipment_processing', 'as' => 'admin.shipment.processing', 'uses' => 'ShipmentController@getProcessingIndex']);
    Route::get('shipment/{id}/new', ['middleware' => 'acl:new_shipment_box', 'as' => 'admin.shipment.new', 'uses' => 'ShipmentController@getShipmentAdd']);
    Route::get('shipment/view/{id}', ['middleware' => 'acl:view_shipment', 'as' => 'admin.shipment.view', 'uses' => 'ShipmentController@getShipment']);
    Route::post('get-box-details', ['middleware' => 'acl:new_shipment_box', 'as' => 'admin.shipment.box', 'uses' => 'ShipmentController@addShipmentBox']);
    Route::post('delete-shipment-box', ['middleware' => 'acl:delete_shipment_box', 'as' => 'admin.shipment.box', 'uses' => 'ShipmentController@deleteShipmentBox']);
    Route::post('update-shipment-status', ['middleware' => 'acl:edit_shipment_processing', 'as' => 'admin.shipment.update', 'uses' => 'ShipmentController@updateShipmentStatus']);
    Route::post('update-shipmentinfo-status/{id}', ['middleware' => 'acl:edit_shipment', 'as' => 'admin.shipping_info.update', 'uses' => 'ShipmentController@updateShipmentInfo']);
    Route::get('shipment-packaging/{id}/{type}', ['middleware' => 'acl:add_packaging', 'as' => 'admin.shipment.packaging', 'uses' => 'ShipmentController@postShipmentPackaging']);
    Route::get('shipment/{id}/invoice', ['middleware' => 'acl:add_packaging', 'as' => 'admin.shipment.invoice', 'uses' => 'ShipmentController@getShipmentInvoice']);


    //admin.packaging.view
    Route::get('packaging/{id}/edit', ['middleware' => 'acl:edit_packaging', 'as' => 'admin.packaging.edit', 'uses' => 'PackagingController@getEdit']);
    Route::get('packaging/{id}/end', ['middleware' => 'acl:edit_packaging', 'as' => 'admin.packaging.end', 'uses' => 'PackagingController@getEndPackaging']);
    Route::post('packingitem/delete', ['middleware' => 'acl:edit_packaging', 'as' => 'admin.packingitem.delete', 'uses' => 'PackagingController@postDeleteItem']);
    Route::get('get-packaginglist-info/{key}/{type}', ['middleware' => 'acl:edit_packaging', 'as' => 'get-packaginglist-info', 'uses' => 'PackagingController@gePackagingListInfo']);
    Route::get('product/get-variant-info-like', ['middleware' => 'acl:edit_packaging', 'as' => 'admin.get-variant-info-like', 'uses' => 'PackagingController@getVariantInfoLike']);
    Route::post('packagingitem-update', ['middleware' => 'acl:edit_packaging', 'as' => 'admin.packagingitemupdate', 'uses' => 'PackagingController@postPackingItemUpdate']);
    Route::post('packagingitem/store', ['middleware' => 'acl:edit_packaging', 'as' => 'admin.packagingitem.store', 'uses' => 'PackagingController@postPackingItemStore']);
    Route::post('packagingbox/store', ['middleware' => 'acl:edit_packaging', 'as' => 'admin.packagingbox.store', 'uses' => 'PackagingController@postPackagingboxStore']);
    Route::get('packaginglist/{shipment_no}/pdf', ['middleware' => 'acl:edit_packaging', 'as' => 'admin.packaginglist.pdf', 'uses' => 'PackagingController@getPackaginglistPDF']);
    Route::get('packaginglist/{shipment_no}/commarcialpdf', ['middleware' => 'acl:edit_packaging', 'as' => 'admin.packaginglist.commarcialpdf', 'uses' => 'PackagingController@getPackaginglistCommarcialpdf']);
    Route::get('packaginglist/{shipment_no}/pdfwithinvoice', ['middleware' => 'acl:edit_packaging', 'as' => 'admin.packaginglist.pdfwithinvoice', 'uses' => 'PackagingController@getPackaginglistPdfWithInvoice']);

    //FAULTY
    Route::get('lost-product/{type}/{id}', ['middleware' => 'acl:view_faulty', 'as' => 'admin.faulty.list', 'uses' => 'FaultyController@getIndex']);

    //AJAX
    Route::get('faulty-checker/{id}', ['middleware' => 'acl:view_faulty', 'as' => 'admin.faulty.put', 'uses' => 'FaultyController@ajaxFaultyChecker']);


    //BOXING
    Route::get('box-type', ['middleware' => 'acl:view_box_type', 'as' => 'admin.box_type.list', 'uses' => 'BoxController@getBoxTypeList']);
    Route::get('box-type-add/{id?}', ['middleware' => 'acl:add_box_type', 'as' => 'admin.box_type.add', 'uses' => 'BoxController@getBoxTypeAdd']);
    Route::get('box-type-delete/{id}', ['middleware' => 'acl:delete_box_type', 'as' => 'admin.box_type.delete', 'uses' => 'BoxController@getBoxTypeDelete']);
    Route::post('box-type-store', ['middleware' => 'acl:add_box_type', 'as' => 'admin.box_type.store', 'uses' => 'BoxController@postBoxTypeStore']);
    Route::get('box', ['middleware' => 'acl:view_box', 'as' => 'admin.box.list', 'uses' => 'BoxController@getIndex']);
    Route::get('not-boxed/list', ['middleware' => 'acl:view_not_boxed', 'as' => 'admin.not_box.list', 'uses' => 'BoxController@getNotBoxed']);
    Route::get('box/view/{id}', ['middleware' => 'acl:view_box', 'as' => 'admin.box.view', 'uses' => 'BoxController@getBox']);
    Route::get('not-box/view/{id}', ['middleware' => 'acl:view_not_boxed', 'as' => 'admin.not_boxed.view', 'uses' => 'BoxController@getNotBox']);
    Route::post('update-box-label', ['middleware' => 'acl:edit_box_label', 'as' => 'admin.box_label.update', 'uses' => 'BoxController@putBoxLabelUpdate']);


    // SHELVING
    Route::get('all-product-list', ['middleware' => 'acl:view_warehouse_stock', 'as' => 'admin.all_product.list', 'uses' => 'ShelveController@getAllProduct']);
    Route::get('shelve', ['middleware' => 'acl:view_warehouse_shelved', 'as' => 'admin.shelve.list', 'uses' => 'ShelveController@getShelveList']);
    Route::get('unshelved-products/{id}/view', ['middleware' => 'acl:view_warehouse_unshelved', 'as' => 'admin.unshelved.view', 'uses' => 'ShelveController@getUnshelvedItem']);
    Route::get('unshelved-products', ['middleware' => 'acl:view_warehouse_unshelved', 'as' => 'admin.unshelved.list', 'uses' => 'ShelveController@getUnshelved']);
    Route::get('shelved-products/{id}/view', ['middleware' => 'acl:view_warehouse_shelved', 'as' => 'admin.shelved.view', 'uses' => 'ShelveController@getShelvedItem']);
    Route::get('stock-price/{id}/view', ['middleware' => 'acl:view_warehouse_stock', 'as' => 'admin.stock_price.view', 'uses' => 'ShelveController@getStockPriceInfo']);
    Route::post('product-details-modal/{type}', ['middleware' => 'acl:view_warehouse_stock', 'as' => 'product.details.modal', 'uses' => 'ShelveController@getProductModal']);
    Route::post('product-details-modal-invoice', ['middleware' => 'acl:view_warehouse_stock', 'as' => 'product.details_invoice.modal', 'uses' => 'ShelveController@getInvoiceProductModal']);
    Route::post('get-warehouse-dropdown', ['middleware' => 'acl:view_warehouse_section', 'as' => 'warehouse.dropdown', 'uses' => 'ShelveController@getWarehouseDropdown']);
    Route::get('add-shelve/{id?}', ['middleware' => 'acl:add_shelve', 'as' => 'admin.shelve.add', 'uses' => 'ShelveController@getShelveStore']);
    Route::post('post-shelve', ['middleware' => 'acl:add_shelve', 'as' => 'admin.shelve.post', 'uses' => 'ShelveController@postStore']);


    //////////////////// CURRENCY //////////////////
    Route::get('currency', ['middleware' => 'acl:view_currency', 'as' => 'admin.currency.list', 'uses' => 'CurrencyController@getIndex']);
    Route::post('update/{id?}', ['middleware' => 'acl:edit_currency', 'as' => 'admin.currency.update', 'uses' => 'CurrencyController@putUpdate']);
    Route::post('store', ['middleware' => 'acl:edit_currency', 'as' => 'admin.currency.store', 'uses' => 'CurrencyController@postStore']);
    Route::get('delete/{id}', ['middleware' => 'acl:delete_currency', 'as' => 'admin.currency.delete', 'uses' => 'CurrencyController@getDelete']);

    /////////////////////////////// DATATABLE ROUTES
    Route::post('customer/all_customer', 'DatatableController@all_customer');
    Route::post('customer/refundlist', 'DatatableController@customerRefundlist');
    Route::post('customer/refundedList', 'DatatableController@customerRefunded');
    Route::post('customer/refundrequestlist', 'DatatableController@customerRefundedRequestList');

    Route::post('all_product_list', 'DatatableController@all_product_list');
    Route::post('unshelved_product_list', 'DatatableController@unshelved_product_list');
    Route::post('shelved_product_list', 'DatatableController@shelved_product_list');
    Route::post('boxed_product_list', 'DatatableController@boxed_product_list');
    Route::post('not_boxed_product_list', 'DatatableController@notBoxed_product_list');
    Route::post('sales_comission_report', 'DatatableController@sales_comission_report');
    Route::post('sales_comission_report_list', 'DatatableController@sales_comission_report_list');

    //////////////////// OFFER TYPE //////////////////
    Route::get('offer-type', ['middleware' => 'acl:view_offer_type', 'as' => 'admin.offer_type.list', 'uses' => 'OfferTypeController@getIndex']);
    Route::get('offer-type/new', ['middleware' => 'acl:new_offer_type', 'as' => 'admin.offer_type.create', 'uses' => 'OfferTypeController@getCreate']);
    Route::post('offer-type/store', ['middleware' => 'acl:new_offer_type', 'as' => 'admin.offer_type.store', 'uses' => 'OfferTypeController@postStore']);
    Route::get('offer-type/{id?}/edit', ['middleware' => 'acl:edit_offer_type', 'as' => 'admin.offer_type.edit', 'uses' => 'OfferTypeController@getEdit']);
    Route::post('offer-type/{id?}/update', ['middleware' => 'acl:edit_offer_type', 'as' => 'admin.offer_type.update', 'uses' => 'OfferTypeController@putUpdate']);
    Route::get('offer-type/{id}/delete', ['middleware' => 'acl:delete_offer_type', 'as' => 'admin.offer_type.delete', 'uses' => 'OfferTypeController@getDelete']);


    //////////////////// OFFER LIST //////////////////
    Route::get('offer-list', ['middleware' => 'acl:view_offer_list', 'as' => 'admin.offer.list', 'uses' => 'OfferController@getIndex']);
    Route::get('offer-list/new', ['middleware' => 'acl:new_offer_list', 'as' => 'admin.offer.create', 'uses' => 'OfferController@getCreate']);
    Route::post('offer-list/store', ['middleware' => 'acl:new_offer_list', 'as' => 'admin.offer.store', 'uses' => 'OfferController@postStore']);
    Route::get('offer-list/{id?}/edit', ['middleware' => 'acl:edit_offer_list', 'as' => 'admin.offer.edit', 'uses' => 'OfferController@getEdit']);
    Route::post('offer-list/{id?}/update', ['middleware' => 'acl:edit_offer_list', 'as' => 'admin.offer.update', 'uses' => 'OfferController@putUpdate']);
    Route::get('offer-list/{id}/delete', ['middleware' => 'acl:delete_offer_list', 'as' => 'admin.offer.delete', 'uses' => 'OfferController@getDelete']);

    //////////////////// OFFER PRIMARY LIST //////////////////
    Route::get('offer-primary-list', ['middleware' => 'acl:view_offer_primary', 'as' => 'admin.offer_primary.list', 'uses' => 'OfferPrimaryController@getIndex']);
    Route::get('offer-primary-list/new', ['middleware' => 'acl:new_offer_primary', 'as' => 'admin.offer_primary.create', 'uses' => 'OfferPrimaryController@getCreate']);
    Route::post('offer-primary-list/store', ['middleware' => 'acl:new_offer_primary', 'as' => 'admin.offer_primary.store', 'uses' => 'OfferPrimaryController@postStore']);
    Route::get('offer-primary-list/{id?}/edit', ['middleware' => 'acl:edit_offer_primary', 'as' => 'admin.offer_primary.edit', 'uses' => 'OfferPrimaryController@getEdit']);
    Route::get('offer-primary-list/{id?}/view', ['middleware' => 'acl:view_offer_primary', 'as' => 'admin.offer_primary.view', 'uses' => 'OfferPrimaryController@getView']);
    Route::post('offer-primary-list/{id?}/update', ['middleware' => 'acl:edit_offer_primary', 'as' => 'admin.offer_primary.update', 'uses' => 'OfferPrimaryController@putUpdate']);
    Route::get('offer-primary-list/{id}/delete', ['middleware' => 'acl:delete_offer_primary', 'as' => 'admin.offer_primary.delete', 'uses' => 'OfferPrimaryController@getDelete']);
    Route::get('offer-primary-list/{id}/add-product', ['middleware' => 'acl:new_offer_primary', 'as' => 'admin.offer_primary.add_product', 'uses' => 'OfferPrimaryController@getAddProduct']);
    Route::post('offer-primary-list/store_product', ['middleware' => 'acl:new_offer_primary', 'as' => 'admin.offer_primary.store_product', 'uses' => 'OfferPrimaryController@postStoreProduct']);
    Route::post('offer-primary-list/add-productlist', ['middleware' => 'acl:new_offer_primary', 'as' => 'admin.offer_primary.productlist', 'uses' => 'OfferPrimaryController@getVariantList']);
    Route::get('offer-primary-list/{id}/delete-product', ['middleware' => 'acl:edit_offer_primary', 'as' => 'admin.offer_primary.deleteproduct', 'uses' => 'OfferPrimaryController@getDeleteProduct']);


    //////////////////// OFFER SECONDARY LIST //////////////////
    Route::get('offer-secondary-list', ['middleware' => 'acl:view_offer_secondary', 'as' => 'admin.offer_secondary.list', 'uses' => 'OfferSecondaryController@getIndex']);
    Route::get('offer-secondary-list/new', ['middleware' => 'acl:new_offer_secondary', 'as' => 'admin.offer_secondary.create', 'uses' => 'OfferSecondaryController@getCreate']);
    Route::post('offer-secondary-list/store', ['middleware' => 'acl:new_offer_secondary', 'as' => 'admin.offer_secondary.store', 'uses' => 'OfferSecondaryController@postStore']);
    Route::get('offer-secondary-list/{id?}/edit', ['middleware' => 'acl:edit_offer_secondary', 'as' => 'admin.offer_secondary.edit', 'uses' => 'OfferSecondaryController@getEdit']);
    Route::get('offer-secondary-list/{id?}/view', ['middleware' => 'acl:view_offer_secondary', 'as' => 'admin.offer_secondary.view', 'uses' => 'OfferSecondaryController@getView']);
    Route::post('offer-secondary-list/{id?}/update', ['middleware' => 'acl:edit_offer_secondary', 'as' => 'admin.offer_secondary.update', 'uses' => 'OfferSecondaryController@putUpdate']);
    Route::get('offer-secondary-list/{id}/delete', ['middleware' => 'acl:delete_offer_secondary', 'as' => 'admin.offer_secondary.delete', 'uses' => 'OfferSecondaryController@getDelete']);
    Route::get('offer-secondary-list/{id}/add-product', ['middleware' => 'acl:new_offer_secondary', 'as' => 'admin.offer_secondary.add_product', 'uses' => 'OfferSecondaryController@getAddProduct']);
    Route::post('offer-secondary-list/store_product', ['middleware' => 'acl:new_offer_secondary', 'as' => 'admin.offer_secondary.store_product', 'uses' => 'OfferSecondaryController@postStoreProduct']);
    Route::post('offer-secondary-list/add-productlist', ['middleware' => 'acl:new_offer_secondary', 'as' => 'admin.offer_secondary.productlist', 'uses' => 'OfferSecondaryController@getVariantList']);
    Route::get('offer-secondary-list/{id}/delete-product', ['middleware' => 'acl:edit_offer_secondary', 'as' => 'admin.offer_secondary.deleteproduct', 'uses' => 'OfferSecondaryController@getDeleteProduct']);


    //////////////////// Shipping Address ////////////////////

    Route::get('shipping-address', ['middleware' => 'acl:view_shipping_address', 'as' => 'admin.shipping-address.list', 'uses' => 'ShippingAddressController@getIndex']);

    Route::get('shipping-address/new', ['middleware' => 'acl:new_shipping_address', 'as' => 'admin.shipping-address.create', 'uses' => 'ShippingAddressController@getCreate']);

    Route::post('shipping-address/store', ['middleware' => 'acl:new_shipping_address', 'as' => 'admin.shipping-address.store', 'uses' => 'ShippingAddressController@postStore']);

    Route::get('shipping-address/{id?}/edit', ['middleware' => 'acl:edit_shipping_address', 'as' => 'admin.shipping-address.edit', 'uses' => 'ShippingAddressController@getEdit']);

    Route::post('shipping-address/{id}/update', ['middleware' => 'acl:edit_shipping_address', 'as' => 'admin.shipping-address.update', 'uses' => 'ShippingAddressController@postUpdate']);

    Route::get('shipping-address/{id}/delete', ['middleware' => 'acl:delete_shipping_address', 'as' => 'admin.shipping-address.delete', 'uses' => 'ShippingAddressController@getDelete']);


    /////////////// Shipment Signature //////////////////

    Route::get('shipment-signature', ['middleware' => 'acl:view_shipment_signature', 'as' => 'admin.shipment-signature.list', 'uses' => 'ShipmentSignController@getIndex']);

    Route::get('shipment-signature/new', ['middleware' => 'acl:new_shipment_signature', 'as' => 'admin.shipment-signature.create', 'uses' => 'ShipmentSignController@getCreate']);

    Route::post('shipment-signature/store', ['middleware' => 'acl:new_shipment_signature', 'as' => 'admin.shipment-signature.store', 'uses' => 'ShipmentSignController@postStore']);

    Route::get('shipment-signature/{id?}/edit', ['middleware' => 'acl:edit_shipment_signature', 'as' => 'admin.shipment-signature.edit', 'uses' => 'ShipmentSignController@getEdit']);

    Route::post('shipment-signature/{id}/update', ['middleware' => 'acl:edit_shipment_signature', 'as' => 'admin.shipment-signature.update', 'uses' => 'ShipmentSignController@postUpdate']);

    Route::get('shipment-signature/{id}/delete', ['middleware' => 'acl:delete_shipment_signature', 'as' => 'admin.shipment-signature.delete', 'uses' => 'ShipmentSignController@getDelete']);

    //Ajax route for signature
    Route::get('signature_img_delete/{id}', ['middleware' => 'acl:delete_shipment_signature', 'as' => 'admin.signature.img_delete', 'uses' => 'ShipmentSignController@getDeleteImage']);

    //////////////////// SMS notification ////////////////////

    Route::get('notification/email', ['middleware' => 'acl:view_notify_email', 'as' => 'admin.notify_email.list', 'uses' => 'NotifySmsController@getEmailIndex']);
    Route::get('notification/email/view/{id}', ['middleware' => 'acl:view_notify_email_body', 'as' => 'admin.notify_email.body', 'uses' => 'NotifySmsController@getEmailBody']);
    Route::get('notification/{id}/email-send', ['middleware' => 'acl:send_notify_email', 'as' => 'admin.notify_email.send', 'uses' => 'NotifySmsController@getSendEmail']);
    Route::get('notification/sms', ['middleware' => 'acl:view_notify_sms', 'as' => 'admin.notify_sms.list', 'uses' => 'NotifySmsController@getIndex']);
    Route::get('notification/{id}/sms-send', ['middleware' => 'acl:send_notify_sms', 'as' => 'admin.notify_sms.send', 'uses' => 'NotifySmsController@getSendSms']);

    //SALES REPORT
    Route::get('sales-report', ['middleware' => 'acl:view_sales_report', 'as' => 'admin.sales_report.list', 'uses' => 'SalesReportController@getIndex']);
    Route::get('sales-report/{id}', ['middleware' => 'acl:view_sales_report', 'as' => 'admin.sales_report.list-item', 'uses' => 'SalesReportController@getComissionReport']);
    Route::get('yet-to-ship', ['middleware' => 'acl:view_yet_to_ship', 'as' => 'admin.yet_to_ship.list', 'uses' => 'SalesReportController@getYetToShip']);
    //AJAX
    Route::get('sales-comission-list-view/{agent_id}/{date}', ['middleware' => 'acl:view_sales_report', 'as' => 'admin.sales_report.list-item-ajax', 'uses' => 'SalesReportController@ajaxComissionReport']);


    //consigment note
    Route::post('order/{id}/consignmentNote', ['middleware' => 'acl:edit_dispatch', 'as' => 'admin.order.consignmentNote', 'uses' => 'PosLazuController@getConsignmentNote']);
    Route::get('ajax/consignment/getTrackingId/{id}', ['middleware' => 'acl:edit_dispatch', 'as' => 'admin.consignment.getTrackingId', 'uses' => 'PosLazuController@getTrackingId']);


    //Payment Method
    Route::get('payment_method',['middleware' => 'acl:view_payment_method', 'as' => 'admin.payment_method.list', 'uses' => 'PaymentMethodController@getIndex']);
    Route::get('payment_method/create',['middleware' => 'acl:new_payment_method', 'as' => 'admin.payment_method.create', 'uses' => 'PaymentMethodController@getCreate']);
    Route::post('payment_method/store',['middleware' => 'acl:new_payment_method', 'as' => 'admin.payment_method.store', 'uses' => 'PaymentMethodController@postStore']);
    Route::get('payment_method/{id}/edit',['middleware' => 'acl:edit_payment_method', 'as' => 'admin.payment_method.edit', 'uses' => 'PaymentMethodController@getEdit']);
    Route::post('payment_method/{id}/update',['middleware' => 'acl:edit_payment_method', 'as' => 'admin.payment_method.update', 'uses' => 'PaymentMethodController@postUpdate']);

    //Payment Method
    Route::get('payment_acc',['middleware' => 'acl:view_payment_acc', 'as' => 'admin.payment_acc.list', 'uses' => 'PaymentBankController@getIndex']);
    Route::get('payment_acc/create',['middleware' => 'acl:new_payment_acc', 'as' => 'admin.payment_acc.create', 'uses' => 'PaymentBankController@getCreate']);
    Route::post('payment_acc/store',['middleware' => 'acl:new_payment_acc', 'as' => 'admin.payment_acc.store', 'uses' => 'PaymentBankController@postStore']);
    Route::get('payment_acc/{id}/edit',['middleware' => 'acl:edit_payment_acc', 'as' => 'admin.payment_acc.edit', 'uses' => 'PaymentBankController@getEdit']);
    Route::post('payment_acc/{id}/update',['middleware' => 'acl:edit_payment_acc', 'as' => 'admin.payment_acc.update', 'uses' => 'PaymentBankController@postUpdate']);


});

Route::group(['namespace' => 'Web', 'middleware' => ['auth']], function () {

    //Mail
    Route::get('mail/config', 'MailController@getIndex')->name('web.mail.index');
    Route::post('mail/env-update', 'MailController@env_key_update')->name('env_key_update.update');

    //About Us
    Route::get('about/us', 'MailController@getIndex')->name('web.mail.index');
    Route::post('mail/env-update', 'MailController@env_key_update')->name('env_key_update.update');

    // Newsletter
    Route::get('home/newsletter', ['middleware' => 'acl:view_newsletter', 'as' => 'web.home.newsletter', 'uses' => 'NewsletterController@getIndex']);

    //WEB ROUTE
    Route::get('home/slider', ['middleware' => 'acl:list_box', 'as' => 'web.home.slider', 'uses' => 'SliderController@getAllSlider']);
    Route::get('home/slider/create', ['middleware' => 'acl:list_box', 'as' => 'web.home.slider.create', 'uses' => 'SliderController@createSlider']);
    Route::get('home/slider/edit/{id}', ['middleware' => 'acl:list_box', 'as' => 'web.home.slider.edit', 'uses' => 'SliderController@getEdit']);
    Route::post('home/slider/store', ['middleware' => 'acl:list_box', 'as' => 'web.home.slider.store', 'uses' => 'SliderController@postStore']);
    Route::post('home/slider/{id}/update', ['middleware' => 'acl:edit_payment', 'as' => 'web.home.slider.update', 'uses' => 'SliderController@postUpdate']);
    Route::get('home/slider/{id}/delete', ['middleware' => 'acl:delete_payment', 'as' => 'web.home.slider.delete', 'uses' => 'SliderController@getDelete']);
    Route::post('web/slider/featureStatus', ['middleware' => 'acl:edit_payment', 'as' => 'web.home.slider.featureStatus', 'uses' => 'SliderController@changeFeatureStatus']);

    //WEB ARTICLE
    Route::get('web/blog/article', ['middleware' => 'acl:list_box', 'as' => 'web.blog.article', 'uses' => 'ArticleController@getAllArticle']);
    Route::get('web/blog/article/create', ['middleware' => 'acl:list_box', 'as' => 'web.blog.article.create', 'uses' => 'ArticleController@getCreate']);
    Route::post('web/blog/article/store', ['middleware' => 'acl:new_shipment_signature', 'as' => 'web.blog.article.store', 'uses' => 'ArticleController@postStore']);
    Route::get('web/blog/article/{id?}/edit', ['middleware' => 'acl:edit_shipment_signature', 'as' => 'web.blog.article.edit', 'uses' => 'ArticleController@getEdit']);
    Route::post('web/blog/article/{id}/update', ['middleware' => 'acl:edit_shipment_signature', 'as' => 'web.blog.article.update', 'uses' => 'ArticleController@postUpdate']);
    Route::get('web/blog/article/{id}/delete', ['middleware' => 'acl:delete_payment', 'as' => 'web.blog.article.delete', 'uses' => 'ArticleController@getDelete']);
    Route::post('ajax/text-editor/image-upload', ['middleware' => 'acl:edit_shipment_signature', 'as' => 'web.blog.text-editor.image', 'uses' => 'ArticleController@postEditorImageUpload']);


    Route::get('web/blog/category', ['middleware' => 'acl:list_box', 'as' => 'web.blog.category', 'uses' => 'BlogCategoryController@getAllCategory']);
    Route::get('web/blog/category/create', ['middleware' => 'acl:list_box', 'as' => 'web.blog.category.create', 'uses' => 'BlogCategoryController@getCreate']);
    Route::post('web/blog/category/store', ['middleware' => 'acl:new_shipment_signature', 'as' => 'web.blog.category.store', 'uses' => 'BlogCategoryController@postStore']);
    Route::get('web/blog/category/{id?}/edit', ['middleware' => 'acl:edit_shipment_signature', 'as' => 'web.blog.category.edit', 'uses' => 'BlogCategoryController@getEdit']);
    Route::post('web/blog/category/{id}/update', ['middleware' => 'acl:edit_shipment_signature', 'as' => 'web.blog.category.update', 'uses' => 'BlogCategoryController@postUpdate']);
    Route::get('web/blog/category/{id}/delete', ['middleware' => 'acl:delete_payment', 'as' => 'web.blog.category.delete', 'uses' => 'BlogCategoryController@getDelete']);

    //WEB PAGES
    Route::get('web/page', ['middleware' => 'acl:list_box', 'as' => 'web.page', 'uses' => 'PageController@getAllPage']);
    Route::get('web/page/create', ['middleware' => 'acl:list_box', 'as' => 'web.page.create', 'uses' => 'PageController@getCreate']);
    Route::post('web/page/store', ['middleware' => 'acl:new_page', 'as' => 'web.page.store', 'uses' => 'PageController@postStore']);
    Route::get('web/page/{id}/edit', ['middleware' => 'acl:edit_page', 'as' => 'web.page.edit', 'uses' => 'PageController@getEdit']);
    Route::post('web/page/{id}/update', ['middleware' => 'acl:edit_page', 'as' => 'web.page.update', 'uses' => 'PageController@postUpdate']);
    Route::get('web/page/{id}/delete', ['middleware' => 'acl:delete_page', 'as' => 'web.page.delete', 'uses' => 'PageController@getDelete']);
    // Route::post('ajax/text-editor/image-upload',['middleware' => 'acl:edit_spage', 'as' => 'web.blog.text-editor.image', 'uses' => 'PageController@postEditorImageUpload']);

    //WEB FAQ
    Route::get('web/faq', ['middleware' => 'acl:list_box', 'as' => 'web.faq', 'uses' => 'FaqController@getAllFaq']);
    Route::get('web/faq/create', ['middleware' => 'acl:list_box', 'as' => 'web.faq.create', 'uses' => 'FaqController@getCreate']);
    Route::post('web/faq/store', ['middleware' => 'acl:new_shipment_signature', 'as' => 'web.faq.store', 'uses' => 'FaqController@postStore']);
    Route::get('web/faq/{id?}/edit', ['middleware' => 'acl:edit_shipment_signature', 'as' => 'web.faq.edit', 'uses' => 'FaqController@getEdit']);
    Route::post('web/faq/{id}/update', ['middleware' => 'acl:edit_shipment_signature', 'as' => 'web.faq.update', 'uses' => 'FaqController@postUpdate']);
    Route::get('web/faq/{id}/delete', ['middleware' => 'acl:delete_payment', 'as' => 'web.faq.delete', 'uses' => 'FaqController@getDelete']);
    // Route::post('ajax/text-editor/image-upload',['middleware' => 'acl:edit_shipment_signature', 'as' => 'web.blog.text-editor.image', 'uses' => 'PageController@postEditorImageUpload']);

    //WEB ABOUT
    Route::get('web/about', ['middleware' => 'acl:list_box', 'as' => 'web.about', 'uses' => 'AboutController@getIndex']);
    Route::post('web/about/store', ['middleware' => 'acl:new_page', 'as' => 'web.about.store', 'uses' => 'AboutController@postStore']);





    // Ads
    Route::get('web/ads',['middleware' => 'acl:view_ads', 'as' => 'web.ads', 'uses' => 'AdsController@getIndex']);
    Route::get('web/ads/create',['middleware' => 'acl:add_ads', 'as' => 'web.ads.create', 'uses' => 'AdsController@createAd']);
    Route::post('web/ads/store',['middleware' => 'acl:add_ads', 'as' => 'web.ads.store', 'uses' => 'AdsController@storeAd']);
    Route::get('web/ads/{id}/edit',['middleware' => 'acl:edit_ads', 'as' => 'web.ads.edit', 'uses' => 'AdsController@editAd']);
    Route::post('web/ads/{id}/update',['middleware' => 'acl:edit_ads', 'as' => 'web.ads.update', 'uses' => 'AdsController@updateAd']);

    Route::get('web/ads/{id}/images',['middleware' => 'acl:view_ads_image', 'as' => 'web.ads.image', 'uses' => 'AdsController@getAdsImages']);
    Route::post('web/ads/{id}/images/store',['middleware' => 'acl:add_ads_image', 'as' => 'web.ads.image.store', 'uses' => 'AdsController@storeAdsImage']);
    Route::post('web/ads/{id}/images/update',['middleware' => 'acl:edit_ads_image', 'as' => 'web.ads.image.update', 'uses' => 'AdsController@updateAdsImage']);
    Route::get('web/ads/{id}/images/delete',['middleware' => 'acl:delete_ads_image', 'as' => 'web.ads.image.delete', 'uses' => 'AdsController@deleteAdsImage']);

    Route::get('web/ads_position',['middleware' => 'acl:view_ads_position', 'as' => 'web.ads_position', 'uses' => 'AdsController@getAdsPosition']);
    Route::get('web/ads_position/create',['middleware' => 'acl:add_ads_position', 'as' => 'web.ads_position.create', 'uses' => 'AdsController@createAdsPosition']);
    Route::post('web/ads_position/store',['middleware' => 'acl:add_ads_position', 'as' => 'web.ads_position.store', 'uses' => 'AdsController@storeAdsPosition']);
    Route::get('web/ads_position/{id}/edit',['middleware' => 'acl:edit_ads_position', 'as' => 'web.ads_position.edit', 'uses' => 'AdsController@editAdsPosition']);
    Route::post('web/ads_position/{id}/update',['middleware' => 'acl:edit_ads_position', 'as' => 'web.ads_position.update', 'uses' => 'AdsController@updateAdsPosition']);

    //web about us
    Route::get('about-us', ['middleware' => 'acl:list_box', 'as' => 'web.about.us', 'uses' => 'AboutUsController@getIndex']);
    Route::post('about-us/store', ['middleware' => 'acl:new_page', 'as' => 'web.about.us.store', 'uses' => 'AboutUsController@postStore']);

    //testimonial
    Route::get('testimonial', ['middleware' => 'acl:list_box', 'as' => 'web.testimonial', 'uses' => 'TestimonialController@getIndex']);
    Route::get('testimonial/create', ['middleware' => 'acl:new_page', 'as' => 'web.testimonial.create', 'uses' => 'TestimonialController@getCreate']);
    Route::post('testimonial/store', ['middleware' => 'acl:new_page', 'as' => 'web.testimonial.store', 'uses' => 'TestimonialController@postStore']);
    Route::get('testimonial/{id}/edit', ['middleware' => 'acl:new_page', 'as' => 'web.testimonial.edit', 'uses' => 'TestimonialController@getEdit']);
    Route::post('testimonial/{id}/update', ['middleware' => 'acl:new_page', 'as' => 'web.testimonial.update', 'uses' => 'TestimonialController@postUpdate']);
    Route::get('testimonial/{id}/delete', ['middleware' => 'acl:new_page', 'as' => 'web.testimonial.delete', 'uses' => 'TestimonialController@getDelete']);

    //team members
    Route::get('team_members', ['middleware' => 'acl:list_box', 'as' => 'web.team_members', 'uses' => 'TeamMemberController@getIndex']);
    Route::get('team_members/create', ['middleware' => 'acl:new_page', 'as' => 'web.team_members.create', 'uses' => 'TeamMemberController@getCreate']);
    Route::post('team_members/store', ['middleware' => 'acl:new_page', 'as' => 'web.team_members.store', 'uses' => 'TeamMemberController@postStore']);
    Route::get('team_members/{id}/edit', ['middleware' => 'acl:new_page', 'as' => 'web.team_members.edit', 'uses' => 'TeamMemberController@getEdit']);
    Route::post('team_members/{id}/update', ['middleware' => 'acl:new_page', 'as' => 'web.team_members.update', 'uses' => 'TeamMemberController@postUpdate']);
    Route::get('team_members/{id}/delete', ['middleware' => 'acl:new_page', 'as' => 'web.team_members.delete', 'uses' => 'TeamMemberController@getDelete']);

    //contact message
    Route::get('contact-message', ['middleware' => 'acl:contact_message', 'as' => 'web.contact_message', 'uses' => 'ContactFormController@getIndex']);


});
