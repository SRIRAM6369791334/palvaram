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
            Coupon Management
        @endslot
    @endcomponent

    <div class="container">
        <h2>Coupon Management</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('coupons.store') }}" method="POST" class="mb-4">
            @csrf
            <div class="row">
                <div class="col-md-2">
                    <input name="code" class="form-control" placeholder="Code" required>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-control select2">
                        <option value="fixed">₹ Fixed</option>
                        <option value="percentage">% Percentage</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input name="value" class="form-control" placeholder="Value" required type="number" step="0.01">
                </div>
                <div class="col-md-2">
                    <input name="min_order_amount" class="form-control" placeholder="Min Order ₹" type="number">
                </div>
                <div class="col-md-2">
                    <input name="start_date" class="form-control" type="date">
                </div>
                <div class="col-md-2">
                    <input name="end_date" class="form-control" type="date">
                </div>
                <div class="col-md-12 mt-2">
                    <button class="btn btn-primary">Create Coupon</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Min Order</th>
                    <th>Valid From</th>
                    <th>Valid Till</th>
                    <th>Status</th>
                    <th>Toggle</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($coupons as $coupon)
                    <tr>
                        <td>{{ $coupon->code }}</td>
                        <td>{{ ucfirst($coupon->type) }}</td>
                        <td>{{ $coupon->value }}</td>
                        <td>{{ $coupon->min_order_amount }}</td>
                        <td>{{ $coupon->start_date }}</td>
                        <td>{{ $coupon->end_date }}</td>
                        <td>
                            <span class="badge {{ $coupon->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('coupons.toggle', $coupon->id) }}" method="POST">
                                @csrf
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" onchange="this.form.submit()"
                                        {{ $coupon->is_active ? 'checked' : '' }}>
                                </div>
                            </form>

                        </td>
                        <td>
                            <!-- Actions -->
                            <button type="button" class="btn btn-sm btn-info"
                                onclick="editCoupon({{ $coupon->id }})">Edit</button>
                            <button type="button" class="btn btn-sm btn-danger"
                                onclick="confirmDelete({{ $coupon->id }})">Delete</button>

                            <form id="delete-form-{{ $coupon->id }}"
                                action="{{ route('newcoupons.destroy', $coupon->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editCouponModal" tabindex="-1" aria-labelledby="editCouponModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="editCouponForm">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Coupon</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-6 mb-2">
                            <label>Code</label>
                            <input name="code" class="form-control" id="edit_code" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Type</label>
                            <select name="type" class="form-control select2" id="edit_type">
                                <option value="fixed">₹ Fixed</option>
                                <option value="percentage">% Percentage</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Value</label>
                            <input name="value" class="form-control" id="edit_value" type="number" step="0.01">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Min Order</label>
                            <input name="min_order_amount" class="form-control" id="edit_min_order_amount" type="number">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Start Date</label>
                            <input name="start_date" class="form-control" id="edit_start_date" type="date">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>End Date</label>
                            <input name="end_date" class="form-control" id="edit_end_date" type="date">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
    <script>
        function editCoupon(id) {
            fetch(`/admin/coupons/${id}/edit`)
                .then(res => res.json())
                .then(data => {
                    const form = document.getElementById('editCouponForm');
                    form.action = `/admin/coupons/${id}/update`;
                    document.getElementById('edit_code').value = data.code;
                    document.getElementById('edit_type').value = data.type;
                    document.getElementById('edit_value').value = data.value;
                    document.getElementById('edit_min_order_amount').value = data.min_order_amount;
                    document.getElementById('edit_start_date').value = data.start_date;
                    document.getElementById('edit_end_date').value = data.end_date;

                    new bootstrap.Modal(document.getElementById('editCouponModal')).show();
                });
        }
    </script>

    <script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the coupon.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection
