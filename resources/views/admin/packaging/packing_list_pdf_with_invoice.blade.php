<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page {
            margin: 0cm 0cm;
        }
        @font-face {
            font-family: 'Helvetica';
            font-weight: normal;
            font-style: normal;
            font-variant: normal;
            src: url("font url");
        }

        /** Define the header rules **/
        header {
            position: fixed;
            top: 1cm;
            left: 1cm;
            right: 1cm;
            height: 10cm;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            margin-top: 7cm;
            margin-left: 1cm;
            margin-right: 1cm;
            margin-bottom: 5.1cm;
            font-family: Helvetica, sans-serif;
            font-size: 11px;
        }

        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 5cm;
            text-align: center;
            font-size: 11px;
        }

        .tbl-list {
            border-top: 1px solid black;
            border-bottom: 0;
            border-left: 1px solid black;
            border-right: 0;
            border-spacing: 0px;
            border-collapse: separate;
        }

        .tbl-list td, .tbl-list th {
            border-bottom: 1px solid black;
            border-right: 1px solid black;
            border-top: 0;
            border-left: 0;
        }
        .tbl-list tr td:last-child {
            border-right: 1px solid black;
        }

        .tbl-list tr td:first-child {
            border-left: 0;
        }

        .tbl-list tr:first-child {
            border-top: 0;
        }

        tfoot tr td{ font-weight: bold; }
        .page-break {
            page-break-after: always;
        }
        .tbl-header { background-color: #0073b7; color: #ffffff; }
        #tbl-signature {width: 70%; margin:10px auto;border-spacing: 30px 0; margin-top:10px; margin-bottom:10px;}

    </style>
    <?php
    $shipment = $data['shipment_info'];
    ?>
    <body>
        <header>
                        <table width="100%" style="border-collapse: collapse;" cellpadding="5" cellspacing="0">
                            <tr>
                                <td><img width="100" src="{{ asset('/assets/img/ukshop_logo_mini.jpg') }}" /></td>
                                <td>
                                    <div style="margin-bottom: 10px;"><strong>Packing List/Invoice:</strong> {{ $shipment->CODE ?? '' }}</div>
                                    <div><strong>AWB/BL:</strong> {{ $shipment->WAYBILL }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" width="70%">

                                    <div><strong>Ship To:</strong></div>
                                    @if($data['shipment_address'])
                                        @foreach($data['shipment_address'] as $i => $addr)
                                            @if($addr->ADDRESS_TYPE == 'Ship_to')
                                                <div style="text-transform: uppercase;">{{ $addr->NAME }}</div>

                                                @if( $addr->ADDRESS_LINE_1)
                                        <div>{{ $addr->ADDRESS_LINE_1 }}</div>
                                        @endif
                                        @if($addr->ADDRESS_LINE_2)
                                        <div>{{ $addr->ADDRESS_LINE_2 }}</div>
                                        @endif
                                        @if($addr->ADDRESS_LINE_3)
                                        <div>{{ $addr->ADDRESS_LINE_3 }}</div>
                                        @endif
                                        @if($addr->ADDRESS_LINE_4)
                                        <div>{{ $addr->ADDRESS_LINE_4 }}</div>
                                        @endif

                                        @if($addr->CITY)
                                            <div>{{ $addr->CITY }} @if($addr->POST_CODE)&nbsp;{{ $addr->POST_CODE }} @endif</div>
                                        @endif

                                        @if($addr->STATE)
                                            <div>{{ $addr->STATE}}@if($addr->COUNTRY),&nbsp;{{ $addr->COUNTRY }} @endif</div>
                                        @endif

                                        @if($addr->ATTENTION)
                                        <div>{{ $addr->ATTENTION }}</div>
                                        @endif

                                        @if($addr->TEL_NO)
                                        <div>{{ $addr->TEL_NO }}</div>
                                        @endif

                                        @if($addr->VAT_EORI_NO)
                                        <div>{{ $addr->VAT_EORI_NO }}</div>
                                        @endif


                                            @endif
                                        @endforeach
                                    @else
                                        <div> Not set </div>
                                    @endif

                                </td>
                                <td valign="top">
                                    <div><strong>Bill To:</strong></div>
                                    @if($data['shipment_address'])
                                        @foreach($data['shipment_address'] as $i => $addr)
                                            @if($addr->ADDRESS_TYPE == 'Bill_to')
                                                <div style="text-transform: uppercase;">{{ $addr->NAME }}</div>
                                                @if( $addr->ADDRESS_LINE_1)
                                        <div>{{ $addr->ADDRESS_LINE_1 }}</div>
                                        @endif
                                        @if($addr->ADDRESS_LINE_2)
                                        <div>{{ $addr->ADDRESS_LINE_2 }}</div>
                                        @endif
                                        @if($addr->ADDRESS_LINE_3)
                                        <div>{{ $addr->ADDRESS_LINE_3 }}</div>
                                        @endif
                                        @if($addr->ADDRESS_LINE_4)
                                        <div>{{ $addr->ADDRESS_LINE_4 }}</div>
                                        @endif

                                        @if($addr->CITY)
                                            <div>{{ $addr->CITY }} @if($addr->POST_CODE)&nbsp;{{ $addr->POST_CODE }} @endif</div>
                                        @endif

                                        @if($addr->STATE)
                                            <div>{{ $addr->STATE}}@if($addr->COUNTRY),&nbsp;{{ $addr->COUNTRY }} @endif</div>
                                        @endif

                                        @if($addr->ATTENTION)
                                        <div>{{ $addr->ATTENTION }}</div>
                                        @endif

                                        @if($addr->TEL_NO)
                                        <div>{{ $addr->TEL_NO }}</div>
                                        @endif

                                        @if($addr->VAT_EORI_NO)
                                        <div>{{ $addr->VAT_EORI_NO }}</div>
                                        @endif

                                            @endif
                                        @endforeach
                                    @else
                                        <div> Not set </div>
                                    @endif


                                </td>
                            </tr>
                        </table>
        </header>
        <footer>
            <div>I declared all informations contained on this Packing list/Invoice to be true and correct.</div>
            <table id="tbl-signature">

                <tr>

                    <td valign="bottom" style=" text-align: center;">

                        <table>
                            <tr>
                                <td style="width: 70%; text-align: center;">
                                    @if($shipment->signature)
                                    <img width="100" src="{{ asset($shipment->signature->IMG_PATH) }}" />
                                    @else
                                    Not Set
                                    @endif

                                </td>
                            </tr>
                            <tr>
                                <td style="border-top: 1px solid #000; width: 70%; text-align: center;">
                                    @if($shipment->signature)
                                        {{ $shipment->signature->NAME }}
                                    @else
                                    Not Set
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td valign="bottom">
                        <table>
                            <tr>
                                <td style="width: 70%; text-align: center;">
                                    <div>
                                        @if($data['shipment_address'])
                                            @foreach($data['shipment_address'] as $i => $addr)
                                                @if($addr->ADDRESS_TYPE == 'From')
                                                    <div style="text-transform: uppercase;">{{ $addr->NAME }}</div>
                                                    @if( $addr->ADDRESS_LINE_1)
                                        <div>{{ $addr->ADDRESS_LINE_1 }}</div>
                                        @endif
                                        @if($addr->ADDRESS_LINE_2)
                                        <div>{{ $addr->ADDRESS_LINE_2 }}</div>
                                        @endif
                                        @if($addr->ADDRESS_LINE_3)
                                        <div>{{ $addr->ADDRESS_LINE_3 }}</div>
                                        @endif
                                        @if($addr->ADDRESS_LINE_4)
                                        <div>{{ $addr->ADDRESS_LINE_4 }}</div>
                                        @endif

                                    @if($addr->CITY)
                                            <div>{{ $addr->CITY }} @if($addr->POST_CODE)&nbsp;{{ $addr->POST_CODE }} @endif</div>
                                        @endif

                                        @if($addr->STATE)
                                            <div>{{ $addr->STATE}}@if($addr->COUNTRY),&nbsp;{{ $addr->COUNTRY }} @endif</div>
                                        @endif

                                        @if($addr->ATTENTION)
                                        <div>{{ $addr->ATTENTION }}</div>
                                        @endif

                                        @if($addr->TEL_NO)
                                        <div>{{ $addr->TEL_NO }}</div>
                                        @endif

                                        @if($addr->VAT_EORI_NO)
                                        <div>{{ $addr->VAT_EORI_NO }}</div>
                                        @endif
                                                @endif
                                            @endforeach
                                        @else
                                            <div> Not set </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                        </table>
                    </td>

                    <td valign="bottom">
                        <table>
                            <tr>
                                <td style="width: 70%; text-align: center;">
                                    @if($shipment->PACKING_PROCESS_DATE)
                                        {{ date('d-m-Y',strtotime($shipment->PACKING_PROCESS_DATE)) }}
                                    @else
                                        Not Set
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="border-top: 1px solid #000; width: 70%; text-align: center;">Date</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <script type="text/php">
                if (isset($pdf)) {
                    $text = "page {PAGE_NUM} / {PAGE_COUNT}";
                    $size = 10;
                    $font = $fontMetrics->getFont("Verdana");
                    $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
                    $x = ($pdf->get_width() - $width) / 1;
                    $y = $pdf->get_height() - 35;
                    $pdf->page_text($x, $y, $text, $font, $size);
                }
            </script>
        </footer>
        <?php

$grand_total_qty = 0;
$grand_total_amt = 0;

        ?>
        <main>
            @if(isset($data['box_summary']) && count($data['box_summary']) > 0 )
            @foreach($data['box_summary'] as $rk => $box )
            <?php
            $last_itaration = count($data['box_summary'])-1;
            $total_box_qnty = 0;
            $total_box_amount = 0;
            ?>

            <table width="100%" class="tbl-list" style="margin-bottom: 30px;" cellpadding="3" cellspacing="0">
                <tr><td colspan="5" style="text-align: center;"><strong>BOX No - {{ $box->BOX_SERIAL_NO }}</strong></td></tr>
                <thead>
                    <tr>
                        <th class="tbl-header">HS CODE</th>
                        <th class="tbl-header">Description</th>
                        <th class="tbl-header" align="center">Qty</th>
                        <th class="tbl-header" align="center">Unit Price (£)</th>
                        <th class="tbl-header" align="center">Total (£)</th>
                    </tr>
                </thead>

                @if(isset($data['data']) && count($data['data']) > 0 )
                    @foreach($data['data'] as $ik => $item )
                        @if($item->BOX_SERIAL_NO == $box->BOX_SERIAL_NO)

                        <?php

                        $total_box_qnty   += $item->QTY;
                        $total_box_amount += $item->QTY*$item->UNIT_PRICE;

                        $grand_total_qty += $item->QTY;
                        $grand_total_amt += $item->QTY*$item->UNIT_PRICE;
                        ?>

                <tr>
                    <td width="9%" align="left">{{ $item->HS_CODE }}</td>
                    <td><strong>{{ $item->SUBCATEGORY_NAME }} - </strong>{{ $item->PRC_INV_NAME }}</td>
                    <td width="7%" align="right">{{ $item->QTY }}</td>
                    <td width="11%" align="right">{{ number_format($item->UNIT_PRICE,4,".","") }}</td>
                    <td width="9%" align="right">{{ number_format(($item->QTY*$item->UNIT_PRICE),2,".","") }}</td>
                </tr>
                @endif
                @endforeach
                @endif

                <tfoot>
                    <tr style="background-color: #e3e3e3;">
                        <td colspan="2" align="right"><strong> Sub Total = </strong></td>
                        <td align="right"><strong>{{ $total_box_qnty }}</strong></td>
                        <td align="right"></td>
                        <td align="right"><strong>{{ number_format($total_box_amount,2,".","") }}</strong></td>
                    </tr>
                    <tr style="background-color: #e3e3e3;">
                        <td colspan="5" >
                        <span style="font-weight: 200 !important; font-size: 12px;"> {{ $box->INVOICE_DETAILS }} </span>
                        </td>

                    </tr>

                    @if($last_itaration == $rk )
                    <tr style="background-color: #e3e3e3;">
                    <td colspan="2" align="right"><strong>Grand Total = </strong></td>
                    <td align="right"><strong>{{ $grand_total_qty }}</strong></td>
                    <td align="right"></td>
                    <td align="right"><strong>{{ number_format($grand_total_amt,2,".","") }}</strong></td>
                    </tr>
                    @endif

                </tfoot>


            </table>
            @endforeach
            @endif



        <div class="page-break"></div>
        <?php
        $box_total_qty 		= 0 ;
        $box_total_price 	= 0 ;

        ?>
        <table class="tbl-list" border="1" width="50%" style="margin-bottom: 30px; margin:auto;" cellpadding="3" cellspacing="0">
            <thead>
                <tr><th colspan="3" style="text-align: center;">Box Summary</th></tr>
                <tr>
                    <th class="tbl-header" style="text-align: center;">Box No</th>
                    <th class="tbl-header" style="text-align: center;">Quantity</th>
                    <th class="tbl-header" style="text-align: center;">TOTAL (£)</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($data['box_summary']) && count($data['box_summary']) > 0 )
                @foreach($data['box_summary'] as $bs)
                <?php
                $box_total_qty += $bs->BOX_QTY ;
                $box_total_price += $bs->BOX_TOTAL_PRICE ;
                ?>
                <tr>
                    <td align="center">{{ $bs->BOX_SERIAL_NO }}</td>
                    <td align="right">{{ $bs->BOX_QTY }}</td>
                    <td align="right">{{ number_format($bs->BOX_TOTAL_PRICE, 2) }}</td>

                </tr>
                @endforeach
                @endif

            </tbody>
            <tfoot>
                <tr style="background-color: #e3e3e3;">
                    <td align="right">Total = </td>
                    <td align="right">{{ $box_total_qty }}</td>
                    <td align="right">{{ number_format($box_total_price, 2) }}</td>
                </tr>
            </tfoot>
        </table>
        <div class="page-break"></div>
        <?php
        $hs_total_qty 		= 0 ;
        $hs_total_price 	= 0 ;

        ?>
        <table class="tbl-list" border="1" width="50%" style="margin-bottom: 20px; margin:auto;" cellpadding="3" cellspacing="0">
            <thead>
                <tr><th colspan="4" style="text-align: center;">HS CODE Summary</th></tr>
                <tr>
                    <th class="tbl-header" style="text-align: center;">HS CODE</th>
                    <th class="tbl-header" style="text-align: center;">Category</th>
                    <th class="tbl-header" style="text-align: center;">Quantity</th>
                    <th class="tbl-header" style="text-align: center;">TOTAL (£)</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($data['hscode_summary']) && count($data['hscode_summary']) > 0 )
                @foreach($data['hscode_summary'] as $hs)
                <?php
                    $hs_total_qty 		+= $hs->BOX_QTY ;
                    $hs_total_price 	+= $hs->BOX_TOTAL_PRICE ;

                ?>

                <tr>
                    <td align="left">{{ $hs->HS_CODE }}</td>
                    <td align="center">{{ $hs->UNIQUE_SUBCATEGORY_NAME }}</td>
                    <td align="right">{{ $hs->BOX_QTY }}</td>
                    <td align="right">{{ number_format($hs->BOX_TOTAL_PRICE, 2) }}</td>
                </tr>
                @endforeach
                @endif

            </tbody>
            <tfoot>
                <tr style="background-color: #e3e3e3;">
                    <td align="right">Total = </td>
                    <td align="center"></td>
                    <td align="right">{{ $hs_total_qty ?? 0 }}</td>
                    <td align="right">{{ number_format($hs_total_price, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </main>
</body>
</head>
</html>
