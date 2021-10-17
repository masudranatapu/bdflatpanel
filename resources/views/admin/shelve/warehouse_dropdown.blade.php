<select id="warehouse_type" class="form-control mb-1 select2">
    <option value="0">Select Warehouse</option>
@foreach ($data as $item)
    <option value="{{ $item->NAME }}">{{ $item->NAME }}</option>
@endforeach
</select>
