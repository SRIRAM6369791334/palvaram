@if (count($deliveryPersons))
    @foreach ($deliveryPersons as $deliveryPerson)
        <option value="{{ $deliveryPerson->delivery_people_id }}" selected>{{ $deliveryPerson->deliveryPerson->name }}
        </option>
    @endforeach
@endif
