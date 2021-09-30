<!--Edit Product Model  html-->
<div class="modal fade text-left" id="addEditModelModal" tabindex="-1" role="dialog" aria-labelledby="brand_name" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="brand_name"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                {!! Form::open(['method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate' , 'id' => 'model_update_frm' ]) !!}
                    @csrf

                {!! Form::hidden('brand', null, [ 'class' => 'form-control mb-1 brand_id', 'data-validation-required-message' => 'This field is required' ]) !!}

                <div class="modal-body">
                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                        <label>{{trans('form.name')}}<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('name', null, [ 'class' => 'form-control mb-1 model_name', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter model name', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                    {{-- <div class="form-group {!! $errors->has('code') ? 'error' : '' !!}">
                        <label>{{trans('form.code')}}<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('code', null, [ 'class' => 'form-control mb-1 model_code', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter model code', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('code', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div> --}}
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
<!--End Edit Product Model  html-->



<!--Add Edit Product Color html-->
<div class="modal fade text-left" id="addEditColorModal" tabindex="-1" role="dialog" aria-labelledby="brand_name_c" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="brand_name_c"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                {!! Form::open(['method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate' , 'id' => 'color_add_edit_frm' ]) !!}
                    @csrf

                {!! Form::hidden('brand', null, [ 'class' => 'form-control mb-1 brand_id', 'data-validation-required-message' => 'This field is required' ]) !!}

                <div class="modal-body">
                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                        <label>{{trans('form.name')}}<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('name', null, [ 'class' => 'form-control mb-1 color_name', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter color name', 'tabindex' => 1 ]) !!}
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
<!--End Add Edit Product Color html-->



<!--Add Edit Product Color html-->
<div class="modal fade text-left" id="addEditSizeModal" tabindex="-1" role="dialog" aria-labelledby="brand_name_s" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="brand_name_s"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                {!! Form::open(['method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate' , 'id' => 'size_add_edit_frm' ]) !!}
                    @csrf

                {!! Form::hidden('brand', null, [ 'class' => 'form-control mb-1 brand_id', 'data-validation-required-message' => 'This field is required' ]) !!}

                <div class="modal-body">
                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                        <label>{{trans('form.name')}}<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('name', null, [ 'class' => 'form-control mb-1 size_name', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter size name', 'tabindex' => 1 ]) !!}
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
<!--End Add Edit Product Color html-->


