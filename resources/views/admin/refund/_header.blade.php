<div class="row">
    <div class="col-md-12 col-sm-12">
        <a href="{{ route('admin.customer.refund') }}" class="btn btn-md btn-warning c-btn {{ request()->route()->getName() == 'admin.customer.refund' ? 'active' : ''}} " style="min-width:90px;">Customer/Reseller list </a>
        <a href="{{ route('admin.customer.refundrequest') }}" class="btn btn-md btn-warning c-btn {{ request()->route()->getName() == 'admin.customer.refundrequest' ? 'active' : ''}}" style="min-width:90px;">Request for Refund</a>
        <a href="{{ route('admin.customer.refunded') }}" class="btn btn-md btn-warning c-btn {{ request()->route()->getName() == 'admin.customer.refunded' ? 'active' : ''}}" style="min-width:90px;">Refunded</a>

      </div>
</div>
