@if(isset($rows) && count($rows) > 0 )
    @foreach($rows as $k => $row )

    <?php
$cleanStr = preg_replace('/[^A-Za-z0-9 ]/', '',$search_param['name_search']);
$cleanDes = preg_replace('/[^A-Za-z0-9 ]/', '',$row->NARRATION);

$names = explode(' ', trim($cleanStr));

$description = preg_replace('/'.implode('|', $names).'/i', "<b class='active_txt'>$0</b>", $cleanDes);

?>

        <tr class="bs_row">
            <td  style="width: 100px;" class="text-center <?php if ($search_param['pay_date'] == $row->TXN_DATE ){ echo "active_txt"; } ?>" >
                <span class="bs_pk_no" style="display: none;">{{ $row->PK_NO }}</span>
                {{ date('d-M-Y',strtotime($row->TXN_DATE)) }}
            </td>
            <td>
                <div style="line-height:20px;"><?php echo $description; ?></div>
            </td>
            <td  style="width: 100px;"  class="text-right <?php if ($search_param['pay_amount'] == $row->DR_AMOUNT ){ echo "active_txt"; } ?> ">
                {{ number_format($row->DR_AMOUNT,2) }}
            </td>
            <td  style="width: 100px;"  class="text-right <?php if ($search_param['pay_amount'] == $row->CR_AMOUNT ){ echo "active_txt"; } ?> ">
                {{ number_format($row->CR_AMOUNT,2) }}
            </td>
            <td  style="width: 100px;"  class="text-center <?php if ($search_param['bank_id'] == $row->F_ACC_BANK_PAYMENT_NO ){ echo "active_txt"; } ?>" >
                {{ $row->BANK_NAME }}
                <span style="display: block; font-size:10px;">
                    ({{ $row->BANK_ACC_NAME }})
                </span>
            </td>
        </tr>
    @endforeach

    @else
    <tr>
        <td colspan="5" class="text-center text-success ">No data found</td>
    </tr>
@endif
