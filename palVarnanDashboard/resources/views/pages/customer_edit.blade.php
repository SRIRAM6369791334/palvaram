@extends('layouts.master')
@section('title')
     Pal Varnan
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/css/customer_page.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
@endsection

@section('content')
    <button class="btn btn-primary backBtn">Back</button>

    <div class="row">
        <div class="card col-lg-4 py-1 px-3">
            <div class="profile-card mx-auto profoile_border_container">
                <div class="image">
                    <img src="{{ env('MOBILE_API_URL') }}images/{{ $user->profile_image}}" alt=""
                        class="profile-img" />
                </div>

                <div class="text-data">
                    <span class="name">{{ $user->name }}</span>
                    @php $phone = $user->phone_number ?: $user->phone; @endphp
                    @if($phone)
                    <a href="tel:+91{{ $phone }}">
                        <span class="job">+91 {{ strlen($phone) >= 5 ? substr_replace($phone, '-', 5, 0) : $phone }}</span>
                    </a>
                    @endif
                </div>
                <div class="buttons">
                    {{-- <button class="button">Update Image</button> --}}
                    {{-- <button class="button" data-bs-toggle="modal" data-bs-target="#updatePasswordModal">Change
                        Password</button> --}}
                </div>


            </div>
            {{-- <div class="card-body">
                <!--  Modal content for the above example -->
                <div class="row">
                    <div class="m-auto text-center">
                        <div class="profileview-img1">
                            <img class="profile_img"
                                src=" https://dashboardsdj.freshmindz.in/assets/images/sdj_img/profile.png " width="200">
                        </div>
                        <h6>User</h6>
                        <p class="text-muted font-size-sm">User Id</p>
                    </div>
                </div>
            </div> --}}
        </div>

        <div class="col-lg-8">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                        type="button" role="tab" aria-controls="nav-home" aria-selected="true">Basic Details</button>
                    <button class="nav-link address_tab" id="nav-profile-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile"
                        aria-selected="false" style="margin-left:10px">Address</button>
                    {{-- <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact"
                        type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Contact</button> --}}
                </div>
            </nav>
            <div class="tab-content  p-lg-3" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <form class="needs-validation mt-3" id="editBasicProfileForm" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="first_name">First Name*</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                        value="{{ $user->name }}" placeholder="User name" required readonly>
                                </div>
                            </div>
                            <!--<div class="col-md-6">-->
                            <!--    <div class="mb-3">-->
                            <!--        <label class="form-label" for="last_name">Last Name</label>-->
                            <!--        <input type="text" class="form-control" id="last_name" placeholder="Last Name"-->
                            <!--            value="{{ $user->last_name }}" name="last_name" required readonly>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email*</label>
                                    <input type="email" class="form-control" id="email" placeholder="Email"
                                        value="{{ $user->email }}" name="email" required readonly>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 full_name_container">
                                <div class="mb-3">
                                    <label class="form-label" for="full_name">Username*</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name"
                                        {{ $user->name }} placeholder="User name" required readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <label class="form-label" for="phone_number">Phone Number*</label>
                                    <div class="input-group">

                                        {{-- <span class="input-group-text" id="inputGroupPrepend">{{ $user->phone_code }}</span> --}}
                                        <input type="tel" maxlength="10" pattern="^(?!^91)[6-9]\d{9}$"
                                            class="form-control" id="phone_number" name="phone_number" readonly
                                            placeholder="Phone Number" value="{{ $user->phone_number ?: $user->phone }}" required>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="gender_select">Choose Gender*</label>
                                    <select class="form-select" name="gender" id="gender_select" required>
                                        <option value="" disabled selected>Select Gender</option>
                                        @foreach ($genderTypes as $genderType)
                                            <option value="{{ $genderType->id }}"
                                                @if ($user->gender == $genderType->id) selected @endif>
                                                {{ $genderType->gender_name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div> --}}
                        </div>
                        {{-- <div class="text-center">
                            <button class="btn btn-primary mt-3 basic_profile_submit_btn" type="submit">Update</button>
                        </div> --}}
                    </form>
                </div>
                <div class="tab-pane fade p-lg-3" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="form-label">Customer Delivery Address</h4>
                        {{-- <div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">Add
                                New Address</button>
                        </div> --}}
                    </div>
                    <div class="mb-3 address_row" >

                        @if (count($user->user_addresses))
                            @foreach ($user->user_addresses as $user_address)
                                <div class="px-2 address_container">
                                    <div class="d-flex py-2 px-0  flex-column">
                                        <div>Address</div>
                                        <div>
                                            <p>{{ $user_address->address_first_name}} {{ $user_address->address_last_name}}</p>
                                            <p>{{ $user_address->address_line_one }}</p>
                                            <p>{{ $user_address->address_line_two }},{{ $user_address->area_name }}</p>

                                            <p>{{ $user_address->city }} - {{ $user_address->pincode }}</p>
                                            <p></p>
                                            <p>{{ $user_address->state }}</p>
                                            {{ $user_address->country->name ?? 'N/A' }}
                                            <div class="d-flex">
                                                <div>Ph.NO:</div>
                                                <div>{{ $user_address->phone_code }} {{ $user->phone_number }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- @if ($user->user_default_address_id == $user_address->id)
                                        <div class="default_address_container">
                                            <span class="address_container_span badge badge-primary">Default</span>
                                        </div>
                                    @endif --}}
                                    {{-- <input type="radio" class="address_radio_input" name="address"
                                    value="{{ $user_address->id }}" @if ($user->user_default_address_id == $user_address->id) checked @endif> --}}

                                    {{-- <div class="d-flex justify-content-end mb-3 mt-2">

                                        @if ($user->user_default_address_id != $user_address->id)
                                            <button class="btn btn-primary address_radio_input mx-2"
                                                data-addressid="{{ $user_address->id }}">Set As Default</button>
                                        @endif


                                        <button class="btn btn-danger address_delete_btn"
                                            data-addressid="{{ $user_address->id }}">
                                            Delete
                                        </button>
                                    </div> --}}
                                </div>
                            @endforeach
                        @elseif($user->door_no || $user->street || $user->city)
                            <div class="px-2 address_container">
                                <div class="d-flex py-2 px-0  flex-column">
                                    <div>Address</div>
                                    <div>
                                        <p>{{ $user->first_name }} {{ $user->last_name }}</p>
                                        <p>{{ $user->door_no }}</p>
                                        <p>{{ $user->street }}, {{ $user->area }}</p>

                                        <p>{{ $user->city }} - {{ $user->pincode }}</p>
                                        <div class="d-flex">
                                            <div>Ph.NO:</div>
                                            <div>+91 {{ $user->phone_number ?: $user->phone }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-3">
                                <h5 class="h5 text-center">No Delivery Address Found...</h5>
                            </div>
                        @endif




                    </div>

                </div>
                {{-- <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">...</div> --}}
            </div>
        </div>
    </div>

    {{-- addAddressModal --}}
    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAddressModalLabel">Add New Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addAddressForm">
                        {{--
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="">Enter delivery area / building*</label>
                                    <input type="text" class="form-control" id="" name="deliveryPerson_name"
                                        placeholder="DeliveryPerson name" required>
                                </div>
                            </div>
                        </div>
                        --}}


                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="address_line_one">Door No /
                                        Address Line 1*</label>
                                    <input type="text" class="form-control" id="address_line_one"
                                        name="address_line_one" placeholder="Enter Address" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="address_line_two">Street Name/ Address Line 2*</label>
                                    <input type="text" class="form-control" id="address_line_two"
                                        name="address_line_two" placeholder="Enter Address" required>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="landmark">Landmark (optional)</label>
                                    <input type="text" class="form-control" id="landmark" name="landmark"
                                        placeholder="Enter Landmark(optional)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="area_select">Select Area*</label>
                                    <select class="form-select" name="area_id" id="area_select" required>
                                        <option value="" disabled selected>Select Area</option>
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="address_type_select">Select Address Type*</label>
                                    <select class="form-select" name="address_type_id" id="address_type_select" required>
                                        <option value="" disabled selected>Select Address Type</option>
                                        <option value="1">Home</option>
                                        <option value="2">Work</option>
                                        <option value="3">Others</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 others_container">
                                <div class="mb-3">
                                    <label class="form-label" for="other_adress_type">Others Addres Type*</label>
                                    <input type="text" class="form-control" id="other_adress_type"
                                        name="address_type_others_name" placeholder="Enter other address type">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="city_input">City*</label>
                                    <input type="text" class="form-control" id="city_input" name="city"
                                        placeholder="Select City" required>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="row">

                        </div> --}}
                        <div class="text-center">
                            <button class="btn btn-primary add_submit_btn  mt-3" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- Update Password Modal --}}

    <div class="modal fade" id="updatePasswordModal" tabindex="-1" aria-labelledby="updatePasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updatePasswordModalLabel">Change New Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="udpatePasswordForm">
                        <input type="hidden" name="phone_number" value="{{ $user->phone_number }}">
                        <input type="hidden" name="user_id" value="{{  $user->user_id }}">
                        <div class="col-md-12">
                            <div class="mb-3">


                                <div class="input-group">
                                    <input class="form-control" id="edit_password" placeholder="Update New  Password" name="password"
                                        required type="password">
                                    <span class="input-group-text btn btn-outline-secondary" id="inputGroupAppend"
                                        type="button">
                                        <i class="bx bx-hide icon"></i>
                                        <i class="bx bx-show icon"></i>
                                    </span>
                                </div>

                            </div>
                        </div>


                        <div class="text-center">
                            <button class="btn btn-primary mt-3 editpass_submit_btn" type="submit">Update</button>
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
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>

    <script>
        window.apiUrl = "{{ env('MOBILE_API_URL') }}";
        window.userId = "{{ $user->user_id }}";
    </script>
    <script src="{{ URL::asset('assets/js/app/CustomerEditPage.js') }}"></script>
@endsection
