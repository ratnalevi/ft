@extends('layouts.master')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
@section('title', 'Edit - Account')
@section('content')

    <div class="col-lg-12">
        <div class="card">
            <div class="m-3">
                <h4>
                    <i class="fa fa-cogs" aria-hidden="true"></i>
                    <span class="ml-2">Administration</span>
                </h4>
                <hr>
                <h5>
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                    <span class="ml-3">Edit - Account</span>
                </h5>
            </div>

            <form method="post" action="/update-account" class="m-3 pl-4">

                @if (Session::has('success'))
                    <div class="alert alert-success">
                        {{ Session::get('success') }}
                        @php
                            Session::forget('success');
                        @endphp
                    </div>
                @endif

                @csrf
                <div class="m-1">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                @php
                                    $name = Session::get('userID');
                                @endphp
                                <label for="">Account Name</label>
                                <input type="text" maxlength="64" value="{{ $account->AccountName }}"
                                       onkeypress="return /[0-9a-zA-Z\s]/i.test(event.key)" class="form-control"
                                       name="accountName" required>
                                <input type="hidden" class="form-control" value="{{ $name->UserID }}" name="userID">
                                <input type="hidden" class="form-control" value="{{ $account->AccountID }}"
                                       name="AccountID">
                                <input type="hidden" class="form-control" value="{{ $account->LocationID }}"
                                       name="locationID">
                            </div>
                        </div>

                        <div class="col-lg-1">
                        </div>

                        {{-- <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Location</label>
                                <select onchange="getLocationDate(this.value)" name="locationName" id="locationName"
                                    class="form-control">
                                    <option value="">Select Location</option>
                                    @foreach ($locations as $item)
                                        <option value="{{ $item->LocationID }}"
                                            @if ($item->LocationID == $account->LocationID) selected @endif>
                                            {{ $item->LocationName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Contact Name</label>
                                <input type="text" maxlength="64" value="{{ $account->EmailTechnical }}"
                                       onkeypress="return /[0-9a-zA-Z\s]/i.test(event.key)" class="form-control"
                                       name="EmailTechnical">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-1">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Address 1</label>
                                <input type="text" maxlength="255" value="{{ $account->Address1 }}"
                                       onkeypress="return /[0-9a-zA-Z\s]/i.test(event.key)" class="form-control"
                                       name="address1">
                            </div>
                        </div>

                        <div class="col-lg-1">
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Contact Phone Number</label>
                                <input value="{{ $account->PhonePrimary }}" type="tel" id="phone"
                                       class="form-control" name="phoneNumber">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-1">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Address Line 2</label>
                                <input value="{{ $account->Address2 }}" maxlength="255" type="text"
                                       onkeypress="return /[0-9a-zA-Z\s]/i.test(event.key)" class="form-control"
                                       name="address2">
                            </div>
                        </div>

                        <div class="col-lg-1">
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Contact Email</label>
                                <input value="{{ $account->Email }}" type="email" id="email" class="form-control"
                                       name="email">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-1">
                    <div class="row">

                        <div class="col-lg-4 ">
                            <div class="row d-flex">
                                <div class="col-lg-5">
                                    <label for="">City</label>
                                    <input type="text" maxlength="64" value="{{ $account->City }}"
                                           onkeypress="return /[A-Z\s]/i.test(event.key)" class="form-control"
                                           name="city">
                                </div>
                                <div class="col-lg-3">
                                    <label for="">State</label>
                                    <input type="text" value="{{ $account->State }}"
                                           onkeypress="return restrictInput(event)" class="form-control" name="state">
                                </div>
                                <div class="col-lg-4">
                                    <label for="">Zip Code</label>
                                    <input type="text" value="{{ $account->PostalCode }}"
                                           onkeypress="return /[0-9]/i.test(event.key)" maxlength="5"
                                           class="form-control"
                                           name="zip">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1">
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mt-5">
                                        <label>Account is Active</label>
                                        @if ($account->IsActive == 1)
                                            <input type="checkbox" checked name="record_status" id="record_status">
                                        @else
                                            <input type="checkbox" name="record_status" id="record_status">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="m-1">
                    <div class="row">
                        <span id="form_result"></span>
                        <div class="col-lg-10">
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <input type="submit" class="btn btn-secondary ml-4 mr-4" value="Update">
                            </div>
                        </div>

                    </div>
                </div>

            </form>

        </div>
    </div>

    <script>
        function getLocationDate(id) {
            $('input[name="city"]').val(location.City);
            $('input[name="state"]').val('');
            $('input[name="zip"]').val('');
            $('input[name="email"]').val('');
            $('input[name="address1"]').val('');
            $('input[name="address2"]').val('');
            $('input[name="phoneNumber"]').val('');
            $.ajax({
                url: "{{ url('/get/locationData') }}/" + id,
                method: "POST",
                success: function (data) {
                    var location = data.location;
                    $('input[name="city"]').val(location.City);
                    $('input[name="state"]').val(location.State);
                    $('input[name="zip"]').val(location.PostalCode);
                    $('input[name="email"]').val(location.Email);
                    $('input[name="address1"]').val(location.Address1);
                    $('input[name="address2"]').val(location.Address2);
                    $('input[name="phoneNumber"]').val(location.PhonePrimary);
                }
            });
        }

        function restrictInput(event) {
            var key = event.key;

            if (/[A-Za-z]/.test(key) && event.target.value.length < 2) {
                return true; // Allow the key
            } else {
                return false; // Prevent the key
            }
        }

        $(document).ready(function () {

        });
    </script>

@endsection
