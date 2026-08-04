@if (isset($areas) && count($areas))
    <option value="" selected disabled>Select Area</option>
    @foreach ($areas as $area)
        <option value="{{ $area->officename }}">{{ $area->officename }}</option>
    @endforeach
    <option value="others">Others</option>
@else
    <option value="" selected disabled>No Areas Found</option>
@endif
