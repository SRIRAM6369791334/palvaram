<option value="">Choose Delivery Person</option>
@foreach ($deliveryPersons as $deliveryPerson)
    <option value="{{ $deliveryPerson->id }}">{{ $deliveryPerson->name }} | {{ $deliveryPerson->delivery_person_id }}
    </option>
@endforeach
