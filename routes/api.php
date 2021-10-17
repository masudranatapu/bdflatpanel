<?php
Route::prefix('v1')->group(function () {
    Route::post('login', ['uses' => 'Api\AuthController@postLogin']);

	Route::group(['middleware' => ['chckt'], 'namespace' => 'Api'], function ($router) {
        Route::get('get-user', ['middleware' => 'api-acl', 'uses' => 'UserController@getUserList']);
        // Route::post('get-master-products', ['middleware' => 'api-acl', 'uses' => 'ProductApiController@getProductList']);
        Route::post('get-variant', ['middleware' => 'api-acl', 'uses' => 'ProductApiController@getVariantList']);
        Route::post('get-checkEntry', ['middleware' => 'api-acl', 'uses' => 'ProductApiController@getAllVariantList']);
        Route::post('get-stockSearchList', ['middleware' => 'api-acl', 'uses' => 'ProductApiController@getStockSearchList']);
        Route::post('get-variant-img', ['middleware' => 'api-acl', 'uses' => 'ProductApiController@getVariantImg']);
        Route::get('get-products/{id}', ['middleware' => 'api-acl', 'uses' => 'ProductApiController@getProductListSingle']);
        Route::post('product-item-details-list', ['middleware' => 'api-acl', 'uses' => 'ProductApiController@postProductDetailsList']);
        Route::post('product-search', ['middleware' => 'api-acl', 'uses' => 'ProductApiController@postProductSearchList']);
        Route::post('product-search-my', ['middleware' => 'api-acl', 'uses' => 'ProductApiController@postProductSearchListMy']);
        Route::post('product-search-details-my', ['middleware' => 'api-acl', 'uses' => 'ProductApiController@postProductSearchListDetailsMy']);
        Route::post('product-box-location', ['middleware' => 'api-acl', 'uses' => 'ProductApiController@postProductBoxLocation']);

        // Boxing
        Route::post('get-box-dimension', ['middleware' => 'api-acl', 'uses' => 'BoxingApiController@getBoxDimention']);
        Route::post('boxing', ['middleware' => 'api-acl', 'uses' => 'BoxingApiController@getProductBox']);
        Route::post('boxlist', ['middleware' => 'api-acl', 'uses' => 'BoxingApiController@postBoxList']);
        Route::post('reboxing', ['middleware' => 'api-acl', 'uses' => 'BoxingApiController@getRebox']);
        Route::post('unboxing-list', ['middleware' => 'api-acl', 'uses' => 'BoxingApiController@getUnboxList']);
        Route::post('unboxing-box-list', ['middleware' => 'api-acl', 'uses' => 'BoxingApiController@getUnboxingBoxList']);
        Route::post('priority-unbox-list', ['middleware' => 'api-acl', 'uses' => 'BoxingApiController@getPriorityUnboxList']);
        Route::post('priority-unboxing-item-list', ['middleware' => 'api-acl', 'uses' => 'BoxingApiController@priorityUnboxListItem']);
        Route::post('unboxing', ['middleware' => 'api-acl', 'uses' => 'BoxingApiController@getUnbox']);
        Route::post('boxed-details-item-list', ['middleware' => 'api-acl', 'uses' => 'BoxingApiController@postBoxListDetails']);
        Route::post('yet-to-box', ['middleware' => 'api-acl', 'uses' => 'BoxingApiController@postYetToBox']);
        Route::post('land-area-item-list', ['middleware' => 'api-acl', 'uses' => 'BoxingApiController@postUnboxListItem']);
        Route::post('update-box-label', ['middleware' => 'api-acl', 'uses' => 'BoxingApiController@postBoxLabelUpdate']);
        Route::post('reboxing-add-box-open', ['middleware' => 'api-acl', 'uses' => 'BoxingApiController@postBoxLabelExists']);

        //Shipment
        Route::post('shipment', ['middleware' => 'api-acl', 'uses' => 'ShipmentApiController@ShipmentPost']);
        Route::post('shipment-receive', ['middleware' => 'api-acl', 'uses' => 'ShipmentApiController@shipmentReceived']);
        Route::post('shipment-list', ['middleware' => 'api-acl', 'uses' => 'ShipmentApiController@shipmentList']);

        // Shelving
        Route::post('shelving', ['middleware' => 'api-acl', 'uses' => 'ShelvingApiController@postShelving']);
        Route::post('shelving-post-item', ['middleware' => 'api-acl', 'uses' => 'ShelvingApiController@postShelvingList']);
        Route::post('rts-shelve-checkout', ['middleware' => 'api-acl', 'uses' => 'ShelvingApiController@postRtsShelveCheckout']);

        // cod-rtc-dispatch-list
        Route::post('cod-rtc-dispatch-list', ['middleware' => 'api-acl', 'uses' => 'DispatchApiController@postCodRtcDispatchList']);
        Route::post('cod-rtc-dispatch-item', ['middleware' => 'api-acl', 'uses' => 'DispatchApiController@postCodRtcDispatchItem']);
        Route::post('cod-rtc-shelve-transfer', ['middleware' => 'api-acl', 'uses' => 'DispatchApiController@postCodRtcDispatchTransfer']);
        Route::post('cod-rtc-boxed-item-transfer', ['middleware' => 'api-acl','uses' => 'DispatchApiController@postCodRtcBoxItemTransfer']);
        Route::post('cod-rtc-acknowledgement', ['middleware' => 'api-acl', 'uses' => 'DispatchApiController@postCodRtcAcknowledge']);
        Route::post('cod-rtc-order-list', ['middleware' => 'api-acl', 'uses' => 'DispatchApiController@postCodRtcOrderList']);
        Route::post('cod-rtc-dispatch-zone-item', ['middleware' => 'api-acl', 'uses' => 'DispatchApiController@postCodRtsZone']);
        Route::post('cod-rtc-dispatch', ['middleware' => 'api-acl', 'uses' => 'DispatchApiController@postCodRtcDispatch']);
        Route::post('rts-dispatch-list', ['middleware' => 'api-acl', 'uses' => 'DispatchApiController@postRtsDispatchList']);
        Route::post('rts-batch-list', ['middleware' => 'api-acl', 'uses' => 'DispatchApiController@postRtsBatchList']);
        Route::post('dispatch-area-batch-list', ['middleware' => 'api-acl', 'uses' => 'DispatchApiController@postRtsDispatchedList']);
        Route::post('rts-dispatch-area-item', ['middleware' => 'api-acl', 'uses' => 'DispatchApiController@postRtsDispatchedItemList']);
        Route::post('consignment-list', ['middleware' => 'api-acl', 'uses' => 'DispatchApiController@postCOnsignmentList']);
        Route::post('get-consignment-item', ['middleware' => 'api-acl', 'uses' => 'DispatchApiController@postProductOfTrackingNo']);
        Route::post('dispatch', ['middleware' => 'api-acl', 'uses' => 'DispatchApiController@postDispatch']);
    });
    Route::group(['namespace' => 'Api'], function ($router) {
        Route::post('check-session', ['uses' => 'UserController@checkValidity']);
        Route::post('shelve-item-list', ['uses' => 'ShelvingApiController@postAllShelveList']);
        Route::post('get-master-products', ['uses' => 'ProductApiController@getProductList']);
    });
});


Route::group(['namespace' => 'Api'], function ($router) {
    Route::get('update-status', ['uses' => 'StatusApiController@checkStatus']);
    Route::get('notification/all_notify_sms/send',['uses' => 'NotifyApiController@getSendAllSms']);
    Route::get('order-default',['uses' => 'NotifyApiController@getOrderDefault']);
});
