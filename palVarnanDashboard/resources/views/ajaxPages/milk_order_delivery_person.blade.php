@isset($delvieryPersons)
    @if (count($delvieryPersons))
        <option value="" seleced readonly>Select Delivery Person</option>
    @else
        <option value="" selected readonly>Assign Delivery Person first</option>
    @endif
    @foreach ($delvieryPersons as $deliveryPerson)
        <option value="{{ $deliveryPerson->deliveryPerson->delivery_person_id }}">
            {{ $deliveryPerson->deliveryPerson->delivery_person_id }} | {{ $deliveryPerson->deliveryPerson->name }}

        </option>
    @endforeach
@endisset
