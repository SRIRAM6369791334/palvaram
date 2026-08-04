@extends('layouts.master')
@section('title')
    Pal Varnan - Gift Color
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Home @endslot
        @slot('title') Gift Color @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="modal-button mt-2">
                        <div class="row align-items-start">
                            <div class="col-sm">
                                <a href="{{ url('/giftproducts') }}" class="btn btn-light btn-sm mb-4" style="background: #0c716a; color: #fff;">
                                        <i class="mdi mdi-arrow-left"></i> Back
                                    </a>
                                <button type="button" class="btn btn-success mb-4"
                                    data-bs-toggle="modal" data-bs-target="#addGiftColorModal">
                                    <i class="mdi mdi-plus me-1"></i> Add Gift Color
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="table-gridjs"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ADD MODAL --}}
    <div class="modal fade" id="addGiftColorModal" tabindex="-1" aria-labelledby="addGiftColorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGiftColorModalLabel">Add Gift Color</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="addGiftColorForm" novalidate>
                        @csrf
                        @can('Country.selection')
                        <div class="mb-3">
                            <label class="form-label">Country <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="add_country_id" name="country_id[]" multiple>
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">
                                        {{ $country->name }} ({{ $country->currency }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endcan
                        
                        <div class="mb-3">
                            <label class="form-label" for="add_color">Gift Color <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_color" name="color"
                                placeholder="e.g. red, blue, black" required>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary add_submit_btn mt-2" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div class="modal fade" id="editGiftColorModal" tabindex="-1" aria-labelledby="editGiftColorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGiftColorModalLabel">Edit Gift Color</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="editGiftColorForm" novalidate>
                        @csrf
                        <input type="hidden" id="edit_gift_id">
                        <input type="hidden" id="edit_country_id_hidden" name="country_id">
                        @can('Country.selection')
                        <div class="mb-3">
                            <label class="form-label">Country <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="edit_country_id" name="country_id[]" multiple>
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">
                                        {{ $country->name }} ({{ $country->currency }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endcan
                        
                        <div class="mb-3">
                            <label class="form-label" for="edit_color">Gift Color <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_color" name="color"
                                placeholder="e.g. red, blue, black" required>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary edit_submit_btn mt-2" type="submit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
    <script>
        window.gifts             = @json($gifts);
        window.countries         = @json($countries);
        window.allowedCountryIds = @json($allowedCountryIds);
        window.giftUrls = {
            store:   "{{ route('gift-color.store') }}",
            update:  "{{ url('gift-color/update') }}",
            destroy: "{{ url('gift-color/destroy') }}",
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ URL::asset('assets/js/app/GiftColorPage.js') }}"></script>
    <script>
        // Add modal — init select2
        $('#addGiftColorModal').on('shown.bs.modal', function () {
            if ($('#add_country_id').hasClass("select2-hidden-accessible")) {
                $('#add_country_id').select2('destroy');
            }
            $('#add_country_id').select2({
                placeholder: "Select Countries",
                width: '100%',
                dropdownParent: $('#addGiftColorModal')
            });
        });

        // Edit modal — init select2
        $('#editGiftColorModal').on('shown.bs.modal', function () {
            if ($('#edit_country_id').hasClass("select2-hidden-accessible")) {
                $('#edit_country_id').select2('destroy');
            }
            $('#edit_country_id').select2({
                placeholder: "Select Countries",
                width: '100%',
                dropdownParent: $('#editGiftColorModal')
            });
        });
    </script>
@endsection