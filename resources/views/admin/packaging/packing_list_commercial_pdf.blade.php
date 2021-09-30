<html>
<head>
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
            margin-top: 10cm;
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
    $shipment_type = 'SEA';
    if( $shipment->IS_AIR_SHIPMENT == 1){
        $shipment_type = 'AIR';
    }
    ?>
    <body>
        <header>
            <table width="100%" style="border-collapse: collapse;margin-bottom: 10px;" cellpadding="5" cellspacing="0">
                <tr>
                    <td align="center"><strong>Shipping Type: &nbsp;&nbsp;</strong><u>{{ $shipment_type }} Freight to Malaysia (Commercial)</u></td>
                </tr>
            </table>
            <table width="100%" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">
                <tr>
                    <td valign="top" width="60%">
                        <div><strong>Shipper’s Name and Address:</strong></div>
                        @if($data['shipment_address'])
                            @foreach($data['shipment_address'] as $i => $addr )
                                @if($addr->ADDRESS_TYPE == 'From' )

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
                    <td valign="top" style="border:1px solid;">
                        <div><strong>Consignee’s Name and Address:</strong></div>

                        @if($data['shipment_address'])
                            @foreach($data['shipment_address'] as $i => $addr )
                                @if($addr->ADDRESS_TYPE == 'Ship_to' )

                                    <div style="text-transform: uppercase;">{{ $addr->NAME}}</div>
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
                    <hr />
                        <div style="color: red;">
                            <div><strong>Destination Agent:</strong></div>
                            @if($data['shipment_address'])
                                @foreach($data['shipment_address'] as $i => $addr )
                                    @if($addr->ADDRESS_TYPE == 'Receiving_agent' )
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
        </header>

        <main>
            <table border="1" class="tbl-list" width="100%" style="margin-bottom: 20px;" cellpadding="1" cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-center tbl-header">SL</th>
                        <th class="text-center tbl-header">Box</th>
                        <th class="text-center tbl-header">Width</th>
                        <th class="text-center tbl-header">Length</th>
                        <th class="text-center tbl-header">Height</th>
                        <th class="text-center tbl-header">Weight (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $total_weight = 0;
                    @endphp
                    @if(!empty($data['box_summary']))
                    @foreach($data['box_summary'] as $row)
                    <tr>
                        <td class="text-center">{{ $loop->iteration}}</td>
                        <td class="text-center">{{ $row->BOX_SERIAL_NO}}</td>
                        <td class="text-center">{{ $row->WIDTH_CM }} CM</td>
                        <td class="text-center">{{ $row->LENGTH_CM}} CM</td>
                        <td class="text-center">{{ $row->HEIGHT_CM}} CM</td>
                        <td style="text-align: right;padding-right:15px">{{ number_format($row->WEIGHT_KG,2,'.','') }}</td>
                    </tr>
                    @php
                    $total_weight += $row->WEIGHT_KG;
                    @endphp
                    @endforeach
                    @endif
                </tbody>
                <tr style="background-color: #e3e3e3;">
                    <td class="text-center" colspan="5" style="text-align: right;"><strong>Total = </strong></td>
                    <td style="text-align: right;padding-right:15px"><strong>{{ number_format($total_weight,2,'.','') }}</strong></td>
                </tr>
            </table>
        </main>
</body>
</head>
</html>
