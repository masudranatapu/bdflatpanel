<div class="box-body table-responsive">
    <table class="table table-bordered table-striped table-hover thead-light" id="manage_payment_table">
        <thead>
            <tr>
                <th style="width: 50px; " class="text-center">SL#</th>
                <th style="width: 100px; ">Order ID</th>
                <th>Order Note</th>
                <th style="width: 150px; ">Order Date</th>
                <th style="width: 150px; ">Original amount (RM)</th>
                <th style="width: 120px; ">Penalty Amount</th>
                <th style="width: 126px; ">Due Amount</th>
                <th style="width: 150px; ">Payment</th>
            </tr>
        </thead>

        <tbody>
            <tr class="row_class null ">
                <td class="text-center">1</td>
                <td class="text-center"><a href="http://dev.ukshop.my/admin/order/6400" class"link"=""
                        target="_blank">ORD-6399</a><input type="hidden" name="order_id[]" value="6400"></td>
                <td></td>
                <td class="text-center">2020-11-09</td>
                <td class="text-right"><span>1798.00</span></td>
                <td class="text-right"><span>0.00</span></td>
                <td class="text-right"><span>1798.00</span><input type="hidden" value="1798.00" class="each_due_amount">
                </td>
                <td style="with:120px;"><input type="text" class="form-control each_order_value text-right number-only"
                        value="" name="split_pay[]" max_amount="1798.00"></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td class="text-center empty_info text-danger" colspan="8"></td>
            </tr>

            <tr>
                <td style="border:0px" colspan="6"></td>
                <td style="border:0px" colspan="2">
                    <div class="form-group">
                        <label for="amount_to_apply">Amount to Apply</label>
                        <input type="text" class="form-control text-right" id="amount_to_apply" name="amount_to_apply"
                            value="" title="" placeholder="0.00" readonly="" tabindex="">
                    </div>
                </td>

            </tr>

            <tr>
                <td style="border:0px" colspan="6"></td>
                <td style="border:0px" colspan="2">
                    <div class="form-group">
                        <label for="amount_to_credit">Amount to Credit</label>
                        <input type="text" class="form-control text-right" id="amount_to_credit" name="amount_to_credit"
                            value="" title="" placeholder="0.00" tabindex="">
                    </div>
                </td>

            </tr>
            <tr>
                <td style="border:0px" colspan="6"></td>
                <td style="border:0px">
                    <button type="submit" class="btn bg-purple btn-block" id="submit_payment">Save</button>
                </td>
                <td style="border:0px">
                    <a href="#" class="btn btn-danger btn-block">Cancel</a>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
