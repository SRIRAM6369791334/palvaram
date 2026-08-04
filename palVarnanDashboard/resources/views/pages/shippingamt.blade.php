@extends('layouts.master')
@section('title')
    Pal Varnan
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
@endsection


@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Home
        @endslot
        @slot('title')
            Shipping Amount
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Shipping Amount</h4>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="shippingTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="india-tab" data-bs-toggle="tab" data-bs-target="#india"
                                type="button" role="tab" aria-controls="india" aria-selected="true">
                                India
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="international-tab" data-bs-toggle="tab"
                                data-bs-target="#international" type="button" role="tab" aria-controls="international"
                                aria-selected="false">
                                International
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="shippingTabContent">
                        {{-- India Tab --}}
                        <div class="tab-pane fade show active" id="india" role="tabpanel" aria-labelledby="india-tab">
                            <form action="{{ route('admin.updateShippingIndia') }}" method="POST">
                                @csrf
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>State</th>
                                            <th>Priority Shipping (₹)</th>
                                            <th>Express Shipping (₹)</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($states as $index => $state)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><input type="text" name="shipping[{{ $state->id }}][name]"
                                                        class="form-control" value="{{ $state->name }}"></td>
                                                <td>
                                                    <input type="number" name="shipping[{{ $state->id }}][standard]"
                                                        class="form-control" step="0.01"
                                                        value="{{ $state->shipping_charge }}">
                                                </td>
                                                <td>
                                                    <input type="number" name="shipping[{{ $state->id }}][express]"
                                                        class="form-control" step="0.01"
                                                        value="{{ $state->express_shipping_charge }}">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="confirmDelete({{ $state->id }})">Delete</button>
                                                </td>


                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button type="submit" class="btn btn-primary mt-2">Update India Shipping Charges</button>
                            </form>
                            @foreach ($states as $state)
    <form id="delete-form-{{ $state->id }}"
        action="{{ route('admin.deleteShippingState', $state->id) }}"
        method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endforeach

                            <hr class="my-4">
                            <h5>Add New State</h5>
                            <form action="{{ route('admin.addNewState') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <label for="country_id" class="form-label">Country</label>
                                        <select name="country_idss" id="country_id" class="form-control" disabled>
                                            <option value="101" selected>India</option>
                                        </select>
                                        <input type="hidden" name="country_id" value="101">
                                    </div>

                                    <div class="col-md-3 mb-2">
                                        <label for="state_name" class="form-label">State Name</label>
                                        <input type="text" name="state_name" id="state_name" class="form-control"
                                            required>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label for="priority_charge" class="form-label">Priority Shipping (₹)</label>
                                        <input type="number" step="0.01" name="priority_charge" id="priority_charge"
                                            class="form-control" required>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label for="express_charge" class="form-label">Express Shipping (₹)</label>
                                        <input type="number" step="0.01" name="express_charge" id="express_charge"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">Add State</button>
                            </form>
                        </div>


                        {{-- International Tab --}}
                        <div class="tab-pane fade" id="international" role="tabpanel" aria-labelledby="international-tab">
                            <form action="{{ route('admin.updateShippingInternational') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="international_charge" class="form-label">Shipping Charge for All
                                        International Countries (₹)</label>
                                    <input type="number" step="0.01" class="form-control"
                                        name="international_charge" id="international_charge"
                                        value="{{ $international_charge }}">
                                </div>
                                <button type="submit" class="btn btn-primary">Update International Shipping</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
    <script>
    function confirmDelete(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to delete this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection
