<!--Edit Account modal-->
<div class="modal fade text-left" id="addSourceModal" tabindex="-1" role="dialog" aria-labelledby="brand_name" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="source_name">Add New Bank Acc</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                {!! Form::open(['route'=>'account.store','method' => 'post', 'class' => 'form-horizontal',  'novalidate' ]) !!}
                    @csrf

                <div class="modal-body">
                    <div class="form-group {!! $errors->has('bank_name') ? 'error' : '' !!}">
                        <label>Bank Name<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('bank_name', null, [ 'class' => 'form-control mb-1 bank_name', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Bank Name', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('bank_name', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>

                    <div class="form-group {!! $errors->has('bank_acc_name') ? 'error' : '' !!}">
                        <label>Bank Acc Name<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('bank_acc_name', null, [ 'class' => 'form-control mb-1 bank_acc_name', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Bank Acc Name', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('bank_acc_name', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>

                    <div class="form-group {!! $errors->has('bank_acc_no') ? 'error' : '' !!}">
                        <label>Bank Acc Name<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('bank_acc_no', null, [ 'class' => 'form-control mb-1 bank_acc_no', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Bank Acc Number', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('bank_acc_no', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="reset" class="btn btn-secondary btn-sm" data-dismiss="modal" value="Close">
                    <input type="submit" class="btn btn-primary btn-sm submit-btn" value="Add">
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Account modal  html-->

<!--Edit Account modal-->
<div class="modal fade text-left" id="editSourceModal" tabindex="-1" role="dialog" aria-labelledby="brand_name" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="source_name">Add New Bank Acc</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                {!! Form::open(['method' => 'post', 'class' => 'form-horizontal',  'novalidate' , 'id' => 'bank_update_frm' ]) !!}
                    @csrf

                <div class="modal-body">
                    <div class="form-group {!! $errors->has('bank_name') ? 'error' : '' !!}">
                        <label>Bank Name<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('bank_name', null, [ 'class' => 'form-control mb-1 bank_name', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Bank Name', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('bank_name', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>

                    <div class="form-group {!! $errors->has('bank_acc_name') ? 'error' : '' !!}">
                        <label>Bank Acc Name<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('bank_acc_name', null, [ 'class' => 'form-control mb-1 bank_acc_name', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Bank Acc Name', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('bank_acc_name', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>

                    <div class="form-group {!! $errors->has('bank_acc_no') ? 'error' : '' !!}">
                        <label>Bank Acc Number<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('bank_acc_no', null, [ 'class' => 'form-control mb-1 bank_acc_no', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Bank Acc Number', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('bank_acc_no', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="reset" class="btn btn-secondary btn-sm" data-dismiss="modal" value="Close">
                    <input type="submit" class="btn btn-primary btn-sm submit-btn" value="Update">
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Account modal  html-->
