<ul class="navigation navigation-main mt-1" id="main-menu-navigation" data-menu="menu-navigation">
    <li class=" nav-item @yield('dashboard')">
        <a href="{{ route('admin.dashboard')}}"><i class="la la-dashboard"></i><span class="menu-title"
                                                                                     data-i18n="@lang('left_menu.dashboard')">@lang('left_menu.dashboard')</span></a>
    </li>

    @if(hasAccessAbility('view_property', $roles))
        <li class=" nav-item @yield('Property Management')">
            <a href="#"><i class="fas fa-box-open"></i></i><span class="menu-title"
                                                                 data-i18n="@lang('left_menu.product')">Properties</span></a>
            <ul class="menu-content">
                @if(hasAccessAbility('view_property', $roles))
                    <li class="nav-item @yield('property_list')"><a class="menu-item"
                                                                    href="{{ route('admin.product.list') }}"><i></i><span
                                data-i18n="@yield('property_list')">Properties</span></a></li>
                @endif


                {{--
                @if(hasAccessAbility('view_category', $roles))
                <li class="@yield('product category')"><a class="menu-item" href="{{route('product.category.list')}}"><i></i><span data-i18n="@yield('product category')">Category-Subcategory</span></a></li>
                @endif

                @if(hasAccessAbility('view_brand', $roles))
                <li class="@yield('product brand')"><a class="menu-item" href="{{route('product.brand.list')}}"><i></i><span data-i18n="@lang('left_menu.brand')">@lang('left_menu.product_brand')</span></a></li>
                @endif
                @if(hasAccessAbility('view_product', $roles))
                <li class="@yield('product sub-category')"><a class="menu-item" href="{{route('admin.sub_category.list')}}"><span data-i18n="Basic">@lang('left_menu.sub_category')</span></a></li>
                @endif --}}
            </ul>
        </li>
    @endif
    @if(hasAccessAbility('view_seeker', $roles))
        <li class="nav-item  @yield('Property Seekers')"><a href="#"><i class="fas fa-users-cog"></i><span
                    class="menu-title" data-i18n="@lang('left_menu.customer')">Property Seekers</span></a>
            <ul class="menu-content">
                @if(hasAccessAbility('view_seeker', $roles))
                    <li class="@yield('seeker_list')"><a class="menu-item" href="{{route('admin.seeker.list')}}"><i></i><span
                                data-i18n="@lang('left_menu.seeker_list')">Seeker List</span></a></li>
                @endif


            </ul>
        </li>
    @endif

    @if(hasAccessAbility('view_owner', $roles))
        <li class="nav-item  @yield('Property Owner')"><a href="#"><i class="fas fa-users-cog"></i><span
                    class="menu-title" data-i18n="@lang('left_menu.customer')">Property Owners</span></a>
            <ul class="menu-content">

                @if(hasAccessAbility('view_owner', $roles))
                    <li class="@yield('owner_list')"><a class="menu-item"
                                                        href="{{route('admin.owner.list')}}"><i></i><span
                                data-i18n="owner_list">Owner List</span></a></li>
                @endif

            </ul>
        </li>
    @endif

    @if(hasAccessAbility('view_agent', $roles))
        <li class="nav-item  @yield('Agent Management')"><a href="#"><i class="fas fa-users-cog"></i><span
                    class="menu-title" data-i18n="@lang('left_menu.customer')">BDFLAT Agents</span></a>
            <ul class="menu-content">

                @if(hasAccessAbility('view_agent', $roles))
                    <li class="@yield('agent_list')"><a class="menu-item"
                                                        href="{{route('admin.agents.list')}}"><i></i><span
                                data-i18n="@lang('left_menu.agent_list')">Agent List</span></a>
                    </li>
                @endif

            </ul>
        </li>
    @endif



    @if(hasAccessAbility('view_payment_section', $roles))
        <li class="nav-item @yield('Payment')">
            <a href="#"><i class="la la-paypal"></i><span class="menu-title"
                                                          data-i18n="Calendars">@lang('left_menu.payment')</span></a>
            <ul class="menu-content">
                @if(hasAccessAbility('view_transaction', $roles))
                    <li class="nav-item @yield('transaction_list')"><a class="menu-item"
                                                                       href="{{ route('admin.transaction.list') }}"><i></i><span
                                data-i18n="Basic">Transactions</span></a></li>
                @endif
                @if(hasAccessAbility('view_refund_request', $roles))
                    <li class="nav-item @yield('refund_request')"><a class="menu-item"
                                                                     href="{{ route('admin.refund_request') }}"><i></i><span
                                data-i18n="Basic">Refund Request</span></a>
                    </li>
                @endif

                @if(hasAccessAbility('view_recharge_request', $roles))
                    <li class=" nav-item @yield('recharge_request')"><a class="menu-item"
                                                                        href="{{ route('admin.recharge_request') }}"><i></i><span
                                data-i18n="Basic">Recharge Request</span></a></li>
                @endif
                @if(hasAccessAbility('view_agent_commission', $roles))
                    <li class="@yield('agent_commission')"><a class="menu-item"
                                                              href="{{route('admin.agent_commission')}}"><i></i><span
                                data-i18n="@yield('agent_commission')">Agent Commission Request</span></a></li>
                @endif
                {{-- @if(hasAccessAbility('view_bank_to_bank', $roles))
                <li class="@yield('bank_to_bank_xfer')"><a class="menu-item" href="{{route('admin.account_to_bank_list.view')}}"><i></i><span data-i18n="@yield('bank_to_bank_xfer')">Internal Transfer</span></a></li>
                @endif
                @if(hasAccessAbility('view_refund', $roles))
                <li class="nav-item @yield('view_refund')"><a class="menu-item" href="{{ route('admin.customer.refund') }}"><i></i><span data-i18n="Basic">Refund</span></a></li>
                @endif --}}
            </ul>
        </li>
    @endif

    {{-- @if(hasAccessAbility('view_order', $roles))
    <li class=" nav-item @yield('Order Management')"><a href="#"><i class="fas fa-cart-plus"></i><span class="menu-title" data-i18n="@lang('left_menu.order_management')">Orders</span></a>
        <ul class="menu-content">
            @if(hasAccessAbility('new_search_booking', $roles))
            <li class="@yield('new_search_booking')"><a class="menu-item" href="{{route('admin.booking.search_create')}}"><i></i><span data-i18n="@yield('new_search_booking')">Search & Book</span></a></li>
            @endif
            @if(hasAccessAbility('view_booking', $roles))
            <li class="@yield('booking_list')"><a class="menu-item" href="{{ route('admin.booking.list') }}"><span data-i18n="@yield('booking_list')">@lang('left_menu.booking_list')</span></a></li>
            @endif
            @if(hasAccessAbility('view_order', $roles))
            <li class="@yield('list_order')"><a class="menu-item" href="{{route('admin.order.list')}}"><i></i><span data-i18n="@yield('list_order')">Order List</span></a></li>
            @endif
            @if(hasAccessAbility('view_order', $roles))
            <li class="@yield('list_altered_order')"><a class="menu-item" href="{{route('admin.order_alter.list')}}"><i></i><span data-i18n="@yield('list_order')">Awaiting Approval</span></a></li>
            @endif
            @if(hasAccessAbility('view_order', $roles))
            <li class="@yield('list_default_order')"><a class="menu-item" href="{{route('admin.order_default.list')}}"><i></i><span data-i18n="@yield('list_default_order')">Cancel Order</span></a></li>
            @endif
        </ul>
    </li>
    @endif --}}

    {{-- @if(hasAccessAbility('view_invoice', $roles))
    <li class=" nav-item @yield('Procurement')">
        <a href="#"><i class="far fa-clipboard"></i>
            <span class="menu-title" data-i18n="@yield('Procurement')">@lang('left_menu.procurement')</span>
        </a>
        <ul class="menu-content">
            <li class="@yield('vendor')">
                <a class="menu-item" href="{{route('admin.vendor')}}"><i></i>
                    <span data-i18n="@yield('vendor')">@lang('left_menu.vendor')</span>
                </a>
            </li>
            <li class="@yield('invoice')">
                <a class="menu-item" href="{{route('admin.invoice')}}"><i></i>
                <span data-i18n="@yield('invoice')">@lang('left_menu.invoice')</span>
                </a>
            </li>
            <li class="@yield('stock_processing')">
                <a class="menu-item" href="{{route('admin.invoice_processing')}}"><i></i>
                <span data-i18n="@yield('stock_processing')">@lang('left_menu.stock_processing')</span>
                </a>
            </li>
            <li class="@yield('vat_processing')">
                <a class="menu-item" href="{{route('admin.vat_processing')}}"><i></i>
                <span data-i18n="@yield('vat_processing')">@lang('left_menu.vat_processing')</span>
                </a>
            </li>
            <li class=" nav-item @yield('payment_processing')"><a class="menu-item" href="{{ route('admin.payment_processing.list') }}"><i></i><span data-i18n="Basic">@lang('left_menu.payment_processing')</span></a></li>
        </ul>
    </li>
    @endif --}}

    {{-- @if(hasAccessAbility('view_warehouse_section', $roles))
    <li class="nav-item  @yield('Warehouse Operation')"><a href="#"><i class="fas fa-tasks"></i><span class="menu-title" data-i18n="@lang('left_menu.customer')">Warehouse</span></a>

        <ul class="menu-content">
            @if(hasAccessAbility('view_warehouse_stock', $roles))
            <li class="@yield('product_list_')">
                <a class="menu-item" href="{{route('admin.all_product.list')}}"><i></i>
                    <span data-i18n="">@lang('left_menu.product_list_')</span>
                </a>
            </li>
            @endif
            @if(hasAccessAbility('view_warehouse_unshelved', $roles))
            <li class="@yield('unshelved_list')">
                <a class="menu-item" href="{{route('admin.unshelved.list')}}"><i></i>
                    <span data-i18n="">@lang('left_menu.unshelved_list')</span>
                </a>
            </li>
            @endif
            @if(hasAccessAbility('view_warehouse_shelved', $roles))
            <li class="@yield('shelve_list')">
                <a class="menu-item" href="{{route('admin.shelve.list')}}"><i></i>
                    <span data-i18n="">@lang('left_menu.shelve_list')</span>
                </a>
            </li>
            @endif
            @if(hasAccessAbility('view_warehouse_boxed', $roles))
            <li class="@yield('box_list')">
                <a class="menu-item" href="{{route('admin.box.list')}}"><i></i>
                    <span data-i18n="">@lang('left_menu.box_list')</span>
                </a>
            </li>
            @endif
            @if(hasAccessAbility('view_box_type', $roles))
            <li class="@yield('box_type_list')">
                <a class="menu-item" href="{{route('admin.box_type.list')}}"><i></i>
                    <span data-i18n="">Box Type</span>
                </a>
            </li>
            @endif
            @if(hasAccessAbility('view_warehouse_notboxed', $roles))
            <li class="@yield('not_box_list')">
                <a class="menu-item" href="{{route('admin.not_box.list')}}"><i></i>
                    <span data-i18n="">@lang('left_menu.not_box_list')</span>
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif --}}
    {{-- @if(hasAccessAbility('view_shipment_section', $roles))
    <li class=" nav-item @yield('Shipping')">
        <a href="#"><i class="fas fa-shipping-fast"></i></i>
            <span class="menu-title" data-i18n="@yield('Shipping')">@lang('left_menu.shipping')</span>
        </a>
        <ul class="menu-content">

           <li class="@yield('add_shipping')">
                <a class="menu-item" href="{{route('admin.shipment.create')}}"><i></i>
                    <span data-i18n="">@lang('left_menu.add_shipping')</span>
                </a>
            </li>
            <li class="@yield('list_shipping')">
                <a class="menu-item" href="{{route('admin.shipment.list')}}"><i></i>
                    <span data-i18n="">@lang('left_menu.list_shipping')</span>
                </a>
            </li>
            <li class="@yield('processing_shipping')">
                <a class="menu-item" href="{{route('admin.shipment.processing')}}"><i></i>
                    <span data-i18n="">@lang('left_menu.processing_shipping')</span>
                </a>
            </li>
            <li class="@yield('shipping_address')">
                <a class="menu-item" href="{{route('admin.shipping-address.list')}}"><i></i>
                    <span data-i18n="">@lang('left_menu.shipping_address')</span>
                </a>
            </li>
            <li class="@yield('shipment_sign')">
                <a class="menu-item" href="{{ route('admin.shipment-signature.list') }}"><i></i>
                    <span data-i18n="">@lang('left_menu.shipment_sign')</span>
                </a>
            </li>
        </ul>
    </li>
    @endif --}}




    {{-- @if(hasAccessAbility('view_dispatch_management', $roles))
    <li class=" nav-item @yield('Dispatch Management')"><a href="#"><i class="la la-truck"></i><span class="menu-title" data-i18n="@lang('left_menu.dispatch_management')">Dispatch</span></a>
        <ul class="menu-content">
            @if(hasAccessAbility('view_dispatch', $roles))
            <li class="@yield('list_dispatch')"><a class="menu-item" href="{{route('admin.dispatch.list',['dispatch' => 'rts'])}}"><i></i><span data-i18n="@yield('list_dispatch')">Dispatch List</span></a></li>
            @endif
            @if(hasAccessAbility('view_notify_sms', $roles))
            <li class="@yield('notify_sms')"><a class="menu-item" href="{{route('admin.notify_sms.list')}}"><span data-i18n="@yield('notify_sms')">Notification SMS</span></a></li>
            @endif
            @if(hasAccessAbility('view_batch_collected', $roles))
            <li class="@yield('view_batch_list_collected')"><a class="menu-item" href="{{route('admin.batch_collected.list')}}"><span data-i18n="@yield('view_batch_list_collected')">RTS Batch List</span></a></li>
            @endif
            @if(hasAccessAbility('view_pending_app_dispach', $roles))
            <li class="@yield('view_pending_app_dispach')"><a class="menu-item" href="{{route('admin.pending_by_app.dispatch-list')}}"><span data-i18n="@yield('view_pending_app_dispach')">Pending App Dispatch</span></a></li>
            @endif
            @if(hasAccessAbility('view_dispatched', $roles))
            <li class="@yield('dispatched_list')"><a class="menu-item" href="{{route('admin.dispatched.list')}}"><span data-i18n="@yield('dispatched_list')">Dispatched List</span></a></li>
            @endif
           @if(hasAccessAbility('view_order_collect', $roles))
            <li class="@yield('order_collect_list')"><a class="menu-item" href="{{route('admin.order_collect.list')}}"><span data-i18n="@yield('order_collect_list')">Order Collect List</span></a></li>
            @endif
             @if(hasAccessAbility('view_batch_collect', $roles))
            <li class="@yield('view_batch_list')"><a class="menu-item" href="{{route('admin.batch_collect.list')}}"><span data-i18n="@yield('view_batch_list')">RTS Batch List</span></a></li>
            @endif
        </ul>
    </li>
    @endif --}}
    {{-- <li class=" nav-item"><a href="{{route('product.inventory.list')}}"><i class="la la-calendar"></i><span class="menu-title" data-i18n="Calendars">@lang('left_menu.inventory')</span></a>
    </li> --}}

    <li class="nav-item @yield('Pages')">
        <a href="#">
            <i class="la la-sticky-note"></i>
            <span class="menu-title" data-i18n="Pages">Pages</span>
        </a>
        <ul class="menu-content">
            @if(hasAccessAbility('view_pages', $roles))
                <li class="@yield('pages-list')">
                    <a href="{{ route('admin.pages.list') }}">
                        <span class="menu-title" data-i18n="Pages List">Pages List</span>
                    </a>
                </li>
            @endif
            @if(hasAccessAbility('view_pages_category', $roles))
                <li class="@yield('pages-category')">
                    <a href="{{ route('admin.pages-category.list') }}">
                        <span class="menu-title" data-i18n="Pages List">Pages Category</span>
                    </a>
                </li>
            @endif
        </ul>
    </li>

    @if(hasAccessAbility('view_web_ads', $roles))
        <li class=" nav-item @yield('web_ads')">
            <a href="#"><i class="la la-cog"></i>
                <span class="menu-title" data-i18n="@yield('web_ads')">Web Ads</span>
            </a>
            <ul class="menu-content">
                @if(hasAccessAbility('view_web_ads', $roles))
                <li class="@yield('ads')">
                    <a class="menu-item" href="{{route('web.ads')}}"><i></i>
                        <span data-i18n="">Ads</span>
                    </a>
                </li>
                @endif

                @if(hasAccessAbility('view_ads_position', $roles))
                <li class="@yield('ads_position')">
                    <a class="menu-item" href="{{route('web.ads_position')}}"><i></i>
                        <span data-i18n="">Ad Position</span>
                    </a>
                </li>
                @endif
            </ul>

        </li>
    @endif

    {{-- <li class=" nav-item @yield('dashboard')">
        <a href="{{ route('admin.dashboard')}}"><i class="la la-dashboard"></i><span class="menu-title" data-i18n="@lang('left_menu.dashboard')">Banners</span></a>
    </li>
    <li class=" nav-item @yield('dashboard')">
        <a href="{{ route('admin.dashboard')}}"><i class="la la-dashboard"></i><span class="menu-title" data-i18n="@lang('left_menu.dashboard')">Pages</span></a>
    </li> --}}
    {{-- @if(hasAccessAbility('view_sales_report_section', $roles))
        <li class="nav-item  @yield('Sales Report')"><a href="#"><i class="ft-bar-chart"></i><span class="menu-title" data-i18n="@lang('left_menu.customer')">Reports</span></a>
            <ul class="menu-content">
                @if(hasAccessAbility('view_sales_report', $roles))
                    <li class="@yield('sales_report')">
                        <a class="menu-item" href="{{route('admin.sales_report.list')}}"><i></i>
                            <span data-i18n="">Sales Comission</span>
                        </a>
                    </li>
                @endif
                @if(hasAccessAbility('view_collection_list', $roles))
                    <li class="@yield('view_bank_collection')"><a class="menu-item" href="{{route('admin.collection.list')}}"><span
                                data-i18n="@yield('view_bank_collection')">COD payment Position</span></a></li>
                @endif
                @if(hasAccessAbility('yet_to_ship', $roles))
                    <li class="@yield('yet_to_ship')"><a class="menu-item" href="{{route('admin.yet_to_ship.list')}}"><span
                                data-i18n="@yield('yet_to_ship')">Yet to Ship</span></a></li>
                @endif
            </ul>
        </li>
    @endif --}}

    {{-- @if(hasAccessAbility('view_account_name', $roles))
        <li class=" nav-item @yield('Accounts')">
            <a href="#"><i class="fas fa-money-check"></i><span class="menu-title" data-i18n="Calendars">@lang('left_menu.account')</span></a>
            <ul class="menu-content">
                <li class=" nav-item @yield('Payment Management')"><a class="menu-item" href="{{route('admin.account.list')}}"><i></i><span data-i18n="Basic">Bank Acc List</span></a>
                </li>

                <li class=" nav-item @yield('payment_bank')"><a class="menu-item" href="{{route('admin.payment_bank.list')}}"><i></i><span
                            data-i18n="Basic">@lang('left_menu.payment_bank')</span></a></li>

                <li><a class="menu-item" href="#"><i></i><span data-i18n="Basic">@lang('left_menu.others')</span></a>
                    <ul class="menu-content">
                        <li class="@yield('vat')"><a class="menu-item" href="#!"><i></i><span data-i18n="Basic">@lang('left_menu.Vat')</span></a></li>
                        <li class="@yield('Account Name')"><a class="menu-item" href="#!"><i></i><span data-i18n="Basic">@lang('left_menu.Name')</span></a></li>
                        <li class="@yield('product model')"><a class="menu-item" href="#!"><i></i><span data-i18n="Extra">@lang('left_menu.Method')</span></a></li>
                    </ul>
                </li>
            </ul>
        </li>
    @endif --}}

    <li class=" navigation-header"><span data-i18n="Settings">Settings</span><i class="la la-ellipsis-h" data-toggle="tooltip" data-placement="right" data-original-title="Settings"></i></li>

    @if(hasAccessAbility('view_admin_user', $roles))
        <li class=" nav-item @yield('Admin Mangement')">
            <a href="#">
                <i class="ft-users"></i>
                <span class="menu-title" data-i18n="@lang('left_menu.admin_management')">Admin Users</span>
            </a>
            <ul class="menu-content">
                @if(hasAccessAbility('view_admin_user', $roles))
                    <li class="@yield('admin-user')">
                        <a href="{{ route('admin.admin-user') }}">
                            <span class="menu-title"
                                  data-i18n="@lang('left_menu.admin_user')">@lang('left_menu.admin_user')</span>
                        </a>
                    </li>
                @endif
                @if(hasAccessAbility('view_user_group', $roles))
                    <li class=" nav-item @yield('user-group')">
                        <a href="{{ route('admin.user-group') }}">
                            <span class="menu-title"
                                  data-i18n="@lang('left_menu.user_category')">@lang('left_menu.user_category')</span>
                        </a>
                    </li>
                @endif

            </ul>
        </li>
    @endif

    @if(hasAccessAbility('view_role', $roles))
        <li class=" nav-item @yield('Role Management')">
            <a href="#">
                <i class="la la-user-plus"></i>
                <span class="menu-title"
                      data-i18n="@lang('left_menu.role_management')">@lang('left_menu.role_management')</span>
            </a>
            <ul class="menu-content">
                @if(hasAccessAbility('view_role', $roles))
                    <li class="@yield('role')">
                        <a class="menu-item" href="{{ route('admin.role') }}">
                            <i></i>
                            <span data-i18n="@lang('left_menu.role')">@lang('left_menu.role')</span>
                        </a>
                    </li>
                @endif
                @if(hasAccessAbility('view_menu', $roles))
                    <li class="@yield('permission-group')">
                        <a class="menu-item" href="{{ route('admin.permission-group') }}">
                            <i></i>
                            <span data-i18n="@lang('left_menu.menus')">@lang('left_menu.menus')</span>
                        </a>
                    </li>
                @endif
                @if(hasAccessAbility('view_action', $roles))
                    <li class="@yield('permission')"><a class="menu-item" href="{{ route('admin.permission') }}"><i></i><span data-i18n="@lang('left_menu.actions')">@lang('left_menu.actions')</span></a>
                    </li>
                @endif

            </ul>
        </li>
    @endif

    @if(hasAccessAbility('view_system_settings', $roles))
        <li class=" nav-item @yield('System Settings')">
            <a href="#"><i class="la la-cogs"></i><span class="menu-title" data-i18n="@lang('left_menu.system_settings')">@lang('left_menu.system_settings')</span></a>
            <ul class="menu-content">

                <li class="@yield('property_category')"><a class="menu-item" href="{{route('admin.property.category')}}"><i></i><span data-i18n="Basic">Property Categories</span></a></li>
                <li class="@yield('property_condition')"><a class="menu-item" href="{{route('admin.property.condition')}}"><i></i><span data-i18n="Basic">Condition</span></a>
                </li>
                <li class="@yield('features')"><a class="menu-item" href="{{route('admin.property.features')}}"><i></i><span data-i18n="Basic">Features</span></a></li>
                <li class="@yield('nearBy')"><a class="menu-item" href="{{route('admin.nearby.area')}}"><i></i><span data-i18n="Basic">NearBy</span></a></li>
                <li class="@yield('city_list')"><a class="menu-item" href="{{route('admin.city.list')}}"><i></i><span data-i18n="Basic">City or Division</span></a></li>
                <li class="@yield('area_list')"><a class="menu-item" href="{{route('admin.area.list')}}"><i></i><span data-i18n="Basic">Area</span></a></li>

{{--                <li class="@yield('bedroom')"><a class="menu-item" href="{{route('admin.address_type.city_list_')}}"><i></i><span data-i18n="Basic">Bedroom</span></a> </li>--}}

{{--                <li class="@yield('city_list')"><a class="menu-item" href="{{route('admin.address_type.city_list_')}}"><i></i><span data-i18n="Basic">Bathroom</span></a></li>--}}
{{--                <li class="@yield('city_list')"><a class="menu-item" href="{{route('admin.address_type.city_list_')}}"><i></i><span data-i18n="Basic">Refund Reason for Lead</span></a></li>--}}
{{--                <li class="@yield('city_list')"><a class="menu-item" href="{{route('admin.address_type.city_list_')}}"><i></i><span data-i18n="Basic">Refund Reason for property </span></a></li>--}}
                <li class="@yield('floor_list')"><a class="menu-item" href="{{route('admin.property.floor')}}"><i></i><span data-i18n="Basic">Floor</span></a></li>
                <li class="@yield('facing_list')"><a class="menu-item" href="{{route('admin.property.facing')}}"><i></i><span data-i18n="Basic">Facing</span></a></li>
                <li class="@yield('listing_price')"><a class="menu-item" href="{{route('admin.listing_price.list')}}"><i></i><span data-i18n="Basic">Pricing</span></a></li>
{{--                <li class="@yield('city_list')"><a class="menu-item" href="{{route('admin.address_type.city_list_')}}"><i></i><span data-i18n="Basic">City List</span></a></li>--}}

{{--                @if(hasAccessAbility('view_address_type', $roles))--}}
{{--                    <li class="@yield('address_type')"><a class="menu-item" href="{{route('admin.address_type.list')}}"><i></i><span data-i18n="@lang('left_menu.address_type')">@lang('left_menu.address_type')</span></a>--}}
{{--                    </li>--}}
{{--                @endif--}}


                <li class="@yield('payment_method')">
                    <a class="menu-item" href="{{route('admin.payment_method.list')}}"><i></i>
                        <span data-i18n="@yield('payment_method')">Payment Method</span>
                    </a>
                </li>
                <li class="@yield('payment_account')">
                    <a class="menu-item" href="{{route('admin.payment_acc.list')}}"><i></i>
                        <span data-i18n="@yield('payment_account')">Payment Account</span>
                    </a>
                </li>

            </ul>
        </li>
    @endif


    @if(hasAccessAbility('view_web_settings', $roles))
        <li class=" nav-item @yield('Web Settings')">
            <a href="#"><i class="la la-cogs"></i><span class="menu-title" data-i18n="@lang('left_menu.web_settings')">Web Settings</span></a>
            <ul class="menu-content">
                <li class="@yield('Web Info')"><a class="menu-item" href="{{route('admin.generalinfo')}}"><i></i><span data-i18n="Basic">General</span></a></li>

                <li class="@yield('Contact Message')"><a class="menu-item" href="{{route('web.contact_message')}}"><i></i><span data-i18n="Contact Message">Contact Message</span></a></li>

                <li class="@yield('About Us')"><a class="menu-item" href="{{route('web.about.us')}}"><i></i><span data-i18n="About Us">About Us</span></a></li>

                <li class="@yield('Testimonial')"><a class="menu-item" href="{{route('web.testimonial')}}"><i></i><span data-i18n="Testimonial">Testimonial</span></a></li>

                <li class="@yield('Team Members')"><a class="menu-item" href="{{route('web.team_members')}}"><i></i><span data-i18n="Team Members">Team Members</span></a></li>

                <li class="@yield('slider')"><a class="menu-item" href="{{route('web.home.slider')}}"><i></i><span data-i18n="">Home Slider</span></a>
                </li>

                <li class="@yield('newsletter')"><a class="menu-item" href="{{route('web.home.newsletter')}}"><i></i><span data-i18n="">Newsletter</span></a>
                </li>


                <li class="@yield('page_category')"><a class="menu-item" href="{{route('admin.address_type.city_list_')}}"><i></i><span data-i18n="Basic">Page Category</span></a></li>
                <li class="@yield('web_page')">
                    <a class="menu-item" href="{{route('web.page')}}"><i></i>
                        <span data-i18n="@yield('web_page')">Pages</span>
                    </a>
                </li>

                <li class="@yield('blog-category')">
                    <a class="menu-item" href="{{route('web.blog.category')}}"><i></i>
                        <span data-i18n="@yield('blog-category')">Blog Category</span>
                    </a>
                </li>
                <li class="@yield('blog-article')">
                    <a class="menu-item" href="{{route('web.blog.article')}}"><i></i>
                        <span data-i18n="@yield('blog-article')">Blog Article</span>
                    </a>
                </li>

                <li class="@yield('faq')">
                    <a class="menu-item" href="{{route('web.faq')}}"><i></i>
                        <span data-i18n="@yield('faq')">Faq</span>
                    </a>
                </li>
                <li class="@yield('about')">
                    <a class="menu-item" href="{{route('web.about')}}"><i></i>
                        <span data-i18n="@yield('about')">About</span>
                    </a>
                </li>


            </ul>
        </li>
    @endif





</ul>
