<!--Edit Box modal-->
<div class="modal fade text-left" id="EditBoxLabel" tabindex="-1" role="dialog" aria-labelledby="brand_name" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="heading"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                {!! Form::open(['method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate' , 'id' => 'box_label_update_frm' ]) !!}
                    @csrf

                {!! Form::hidden('id', null, [ 'class' => 'form-control mb-1 box_id', 'data-validation-required-message' => 'This field is required' ]) !!}

                <div class="modal-body">
                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                        <label>{{trans('form.name')}}<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('box_label', null, [ 'class' => 'form-control mb-1 box_label', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Box Label', 'tabindex' => 1 ]) !!}
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
<!--End Box modal  html-->
