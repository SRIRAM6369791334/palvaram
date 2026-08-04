<option value="" disabled selected>Select Product</option>
@foreach ($productsver as $productsve)
    <option value="{{ $productsve->id }}">
        {{ $productsve->value }}
        @if ($productsve->varient == 1)
            l
        @elseif ($productsve->varient == 2)
            ml
        @elseif ($productsve->varient == 3)
            g
        @elseif ($productsve->varient == 4)
            kg
        @elseif ($productsve->varient == 5)
            Nos
        @endif
    </option>
@endforeach
