@extends('layouts.master')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
{{--
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script> --}}
@section('title', 'Add - Location')
@section('content')
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" /> --}}



    <style>
        a {
            color: #0d6efd;
            text-decoration: none;
        }

        .btn-primary {
            color: #fff;
            background-color: #b0b0b0;
            border-color: #b0b0b0;
            padding: 12px 50px;
            color: black;
        }
    </style>
    <style>
        i.fa.fa-plus,
        i.fa.fa-pencil-square-o {
            font-size: 22px;
        }

        #chart {
            margin: 0 auto;
        }

        g#SvgjsG1055 {
            display: none;
        }

        .choices__inner {
            display: inline-block;
            vertical-align: top;
            width: 100%;
            background-color: #f9f9f9;
            padding: 7.5px 7.5px 3.75px;
            border: 1px solid #ddd;
            border-radius: 22px;
            font-size: 14px;
            min-height: 30vh;
            overflow: hidden;
        }

        .choices__input {
            display: inline-block;
            vertical-align: baseline;
            background-color: #f9f9f9;
            font-size: 14px;
            margin-bottom: 0px;
            border: 0;
            border-radius: 0;
            max-width: 100%;
            padding: 4px 0 4px 2px;
        }
    </style>

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
                    <span class="ml-3">Add - Location</span>
                </h5>
            </div>

            <form method="post" action="/save/location" class="m-3 pl-4">

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
                                <label for="">Location Name</label>
                                <input type="text" minlength="6" maxlength="64"
                                       {{-- onkeypress="return /[0-9a-zA-Z\s]/i.test(event.key)"  --}}
                                       class="form-control"
                                       name="LocationName" required>
                                <input type="hidden" class="form-control" value="{{ $name->UserID }}" name="userID">
                            </div>
                        </div>

                        <div class="col-lg-1">
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Account</label>
                                <select name="AccountID" id="AccountID" class="form-control">
                                    <option value="">Select Account</option>
                                    @foreach ($accounts as $item)
                                        <option value="{{ $item->AccountID }}">{{ $item->AccountName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Location Contact Name</label>
                                <input type="text" onkeypress="return /[0-9a-zA-Z\s]/i.test(event.key)"
                                    class="form-control" name="contactName">
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="m-1">
                    <div class="row">

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Contact Name</label>
                                <input type="text" onkeypress="return /[0-9a-zA-Z\s]/i.test(event.key)" id="contactName"
                                       class="form-control" name="contactName">
                            </div>
                        </div>

                        <div class="col-lg-1">
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Contact Phone Number</label>
                                <input type="tel" id="phone" class="form-control" name="phoneNumber">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-1">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Address 1</label>
                                <input type="text" maxlength="255" onkeypress="return /[0-9a-zA-Z\s]/i.test(event.key)"
                                       class="form-control" name="address1">
                            </div>
                        </div>
                        <div class="col-lg-1">
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Address 2</label>
                                <input type="text" maxlength="255" onkeypress="return /[0-9a-zA-Z\s]/i.test(event.key)"
                                       class="form-control" name="address2">
                            </div>
                        </div>


                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Contact Email</label>
                                <input type="email" id="email" class="form-control" name="email">
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
                                    <input type="text" maxlength="64"
                                           onkeypress="return /[A-Z\s]/i.test(event.key)" class="form-control"
                                           name="city">
                                </div>
                                <div class="col-lg-3">
                                    <label for="">State</label>
                                    <input type="text" onkeypress="return restrictInput(event)" class="form-control"
                                           name="state">
                                </div>
                                <div class="col-lg-4">
                                    <label for="">Zip Code</label>
                                    <input type="text" onkeypress="return /[0-9]/i.test(event.key)" maxlength="5"
                                           class="form-control" name="zip">
                                </div>
                            </div>
                            <div class="row d-flex  mt-4">
                                <div class="col">
                                    <label for="">Daily Allowable Hours for Pouring</label>
                                </div>
                            </div>
                            <div class="row d-flex mb-4">
                                <div class="col-lg-6">
                                    From<input type="time" class="form-control" required name="from">
                                </div>
                                <div class="col-lg-6">
                                    Hours
                                    <select name="totalHours" id="totalHours" class="form-control" required>
                                        <option value="">Select Hours</option>

                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                        <option value="13">13</option>
                                        <option value="14">14</option>
                                        <option value="15">15</option>
                                        <option value="16">16</option>
                                        <option value="17">17</option>
                                        <option value="18">18</option>
                                        <option value="19">19</option>
                                        <option value="20">20</option>
                                        <option value="21">21</option>
                                        <option value="22">22</option>
                                        <option value="23">23</option>
                                        <option value="24">24</option>


                                    </select>
                                    {{-- Until<input type="time" class="form-control" required name="until"> --}}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1">
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <!-- Device(s) at this Location field -->
                                <label for="brands">Device(s) at this Location</label>
                                <select multiple name="brands[]" id="brands" class="form-control brands">
                                    @foreach ($beerBrand as $item)
                                        <option value="{{ $item->DevicesID }}">{{ $item->Name }}</option>
                                    @endforeach
                                </select>
                                <span id="brandsError" class="text-danger"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="m-1">
                    <div class="row">
                        <span id="form_result"></span>
                        <div class="col-lg-10">
                            {{-- <div class="form-group">
                                <input type="reset" class="btn btn-primary" value="Cancel">
                            </div> --}}
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <input type="submit" class="btn btn-secondary ml-4 mr-4" value="Save">
                            </div>
                        </div>

                    </div>
                </div>

            </form>

        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('form').on('submit', function (event) {
                //                event.preventDefault(); // Prevent form submission

                var selectedBrands = $('#brands').val(); // Get the selected values
                //now we can add location without device
                //                if (selectedBrands === null || selectedBrands.length === 0) {
                // Show error message
                //                    $('#brandsError').text('Please select at least one device.');
                //                } else {
                // Submit the form
                this.submit();
                //                }
            });
        });

        function restrictInput(event) {
            var key = event.key;

            if (/[A-Za-z]/.test(key) && event.target.value.length < 2) {
                return true; // Allow the key
            } else {
                return false; // Prevent the key
            }
        }

        $(document).ready(function () {
            var multipleCancelButton = new Choices('#brands', {
                removeItemButton: true,
                maxItemCount: 100,
                searchResultLimit: 100,
                renderChoiceLimit: 100
            });

        });
        $('#save_location_form').on('submit', function (event) {
            $('#form_result').html('');
            event.preventDefault();

            $.ajax({
                url: "{{ url('/save/location') }}",
                method: "POST",
                data: new FormData(this),
                success: function (data) {
                    var html = '';
                    if (data.errors) {
                        $('#form_result').html('');
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        $('#form_result').html('');
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#save_location_form')[0].reset();
                    }
                    $('#form_result').html(html);
                }
            });

        });
    </script>

@endsection
