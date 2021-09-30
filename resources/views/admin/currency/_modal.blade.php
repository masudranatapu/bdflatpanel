<!--Edit Currency modal-->
<div class="modal fade text-left" id="EditCode" tabindex="-1" role="dialog" aria-labelledby="brand_name" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="">Update currency</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                {!! Form::open(['method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate' , 'id' => 'currncy_update_frm' ]) !!}
                    @csrf

                {!! Form::hidden('type', null, [ 'class' => 'form-control mb-1 type', 'data-validation-required-message' => 'This field is required' ]) !!}

                <div class="modal-body">
                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                        <label id="input_title"><span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('currency_value', null, [ 'class' => 'form-control mb-1 currency_value', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter ', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
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
</div>
<!--End Currency modal  html-->


<!--Add Currency html-->
<div class="modal fade text-left" id="AddCurrency" tabindex="-1" role="dialog" aria-labelledby="method" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="">Add New Currency</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                {!! Form::open(['method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate' , 'id' => 'currncy_store_frm' ]) !!}
                    @csrf

                {!! Form::hidden('name', null, [ 'class' => 'form-control mb-1 source_id', 'data-validation-required-message' => 'This field is required' ]) !!}

                <div class="modal-body">
                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                        <label>Enter Currenct Code<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('code', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Currency Code', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                        <label>Enter Currency Name<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('name', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Currency Name', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                        <label>Enter Currency Exchange Rate<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('rate', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Currency Exchange Rate', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="reset" class="btn btn-secondary btn-sm" data-dismiss="modal" value="Close">
                    <input type="submit" class="btn btn-primary btn-sm submit-btn" value="Submit">
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
</div>
<!--End Add Currency  html-->
