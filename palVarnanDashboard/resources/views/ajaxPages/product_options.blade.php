<option value="" disabled selected>Select Product</option>
@foreach ($products as $product)
    <option value="{{ $product->id }}">{{ $product->product_name }}</option>
@endforeach
