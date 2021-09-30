<div class="row">
    <div class="col-md-12 col-sm-12">
        <a href="{{ route('admin.order_default.list') }}" class="btn btn-md btn-warning c-btn {{ request()->route()->getName() == 'admin.order_default.list' ? 'active' : ''}} " style="min-width:90px;">All default list </a>
        <a href="{{ route('admin.order_default_action.list') }}" class="btn btn-md btn-warning c-btn {{ request()->route()->getName() == 'admin.order_default_action.list' ? 'active' : ''}}" style="min-width:90px;">Awaiting Action</a>
        <a href="{{ route('admin.order_default_penalty.list') }}" class="btn btn-md btn-warning c-btn {{ request()->route()->getName() == 'admin.order_default_penalty.list' ? 'active' : ''}}" style="min-width:90px;">Default with grace period</a>
        <a href="{{ route('admin.order.cancelrequest') }}" class="btn btn-md btn-warning c-btn {{ request()->route()->getName() == 'admin.order.cancelrequest' ? 'active' : ''}}" style="min-width:90px;">Request For Cancellation</a>
        <a href="{{ route('admin.order.canceled') }}" class="btn btn-md btn-warning c-btn {{ request()->route()->getName() == 'admin.order.canceled' ? 'active' : ''}}" style="min-width:90px;">Cancelled Order</a>
      </div>
</div>
