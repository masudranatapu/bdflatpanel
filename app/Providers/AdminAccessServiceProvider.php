<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AdminAccessServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }

    public function registerBindings()
    {
        $repos = [
            'Account',
            'Agent',
            'Booking',
            'Owner',
            'CustomerAddress',
            'AccountMethod',
            'Bank',
            'UserGroup',
            'PermissionGroup',
            'Permission',
            'Role',
            'Auth',
            'Dashboard',
            'AdminUser',
            'ProductModel',
            'Product',
            'Vendor',
            'Invoice',
            'InvoiceDetails',
            'Brand',
            'Color',
            'ProductSize',
            'Category',
            'SubCategory',
            'Hscode',
            'Customer',
            'Order',
            'Shipment',
            'Address',
            'Box',
            'Shelve',
            'Slider',
            'Datatable',
            'Currency',
            'Payment',
            'Offer',
            'OfferType',
            'Packaging',
            'ShippingAddress',
            'ShipmentSign',
            'OfferPrimary',
            'OfferSecondary',
            'PaymentBank',
            'Faulty',
            'Dispatch',
            'BankState',
            'NotifySms',
            'SalesReport',
            'Poslazu',
            'SearchBooking',
            'Shopcategory',
            'WebInfo',
            'PropertyCategory',
            'PropertyCondition',
            'Earnings',
            'Ads',
            'Pages',
            'Area',
            'City',
            'PropertyFeatures'
        ];

        foreach ($repos as $repo) {
            $this->app->bind("App\Repositories\Admin\\{$repo}\\{$repo}Interface", "App\Repositories\Admin\\{$repo}\\{$repo}Abstract");
        }
    }

}
