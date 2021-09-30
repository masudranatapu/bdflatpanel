<!--Edit Product Subcategory  html-->
<div class="modal fade text-left" id="_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Assign Product Item To</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                {!! Form::open(['route' => 'admin.order_item.assign', 'method' => 'post', 'class' => 'form-horizontal', 'files' => false , 'novalidate' ]) !!}
                    {{-- @csrf --}}

                {!! Form::hidden('batch_id', null, [ 'class' => 'form-control mb-1', 'id' => 'batch_id' , 'data-validation-required-message' => 'This field is required' ]) !!}
                {!! Form::hidden('sku_id', null, [ 'class' => 'form-control mb-1', 'id' => 'sku_id' , 'data-validation-required-message' => 'This field is required' ]) !!}
                <?php
                $user = \App\Models\AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')
                                    ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                                    ->select('SA_USER.PK_NO','USERNAME')
                                    ->where('F_ROLE_NO',20)
                                    ->get();
                ?>
                <div class="modal-body">
                    <div class="form-group {!! $errors->has('logistic_user') ? 'error' : '' !!}">
                        <label>Logistic Users<span class="text-danger">*</span></label>
                        <div class="controls">
                            {{-- {!! Form::select('logistic_user', (array)$user, null, ['class'=>'form-control mb-1 select2', 'id' => 'logistic_user']) !!} --}}
                            <select class="form-control select2" name="logistic_user" id="logistic_user" data-validation-required-message="This field is required" tabindex="1" >
                                <option value="">--select User--</option>
                                @foreach ($user as $item)
                                <option value="{{ $item->PK_NO }}"> {{ $item->USERNAME }} </option>
                                @endforeach
                                <option value="0">Unassign User</option>
                            </select>
                            {!! $errors->first('logistic_user', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="reset" class="btn btn-secondary btn-sm" data-dismiss="modal" value="Close">
                    <input type="submit" class="btn btn-primary btn-sm submit-btn" value="Send">
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
