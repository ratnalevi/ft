@extends('layouts.master')

@section('title', 'Home')
@section('content')
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" /> --}}
    <style>
        a {
            color: #0d6efd;
            text-decoration: none;
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
                    <span class="ml-3">Add - Brand</span>
                </h5>
            </div>

            {{-- <form class="m-3" id="save_brand_form">  --}}
            <form method="post" action="/save/brand" class="m-3">

                <span id="form_result"></span>
                @csrf
                <div class="m-1">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Name</label>
                                <input id="brandName" type="text" minlength="6" maxlength="128"
                                       onkeypress="return /[0-9a-zA-Z\s]/i.test(event.key)" class="form-control"
                                       name="brand_name" required>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">ABV</label> <span style="font-size: 10px;color: red;">max value for
                                    this field is 9.999</span>
                                <input type="number" id="abv" class="form-control" step="0.001" name="abv"
                                       required>

                                <span id="validationMessage"></span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Brewer</label>
                                <input type="text" onkeypress="return /[0-9a-zA-Z\s]/i.test(event.key)" maxlength="128"
                                       id="comments" class="form-control" name="comments" required>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Select Beer Type</label>
                                <select class="form-control locationLOca" name="beer_type_id" id="beer_type_id"
                                        required>
                                    @foreach ($beertypes as $items)
                                        <option value="{{ $items->BeerTypeID }}">{{ $items->Description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="m-1">
                    <div class="row">
                        <div class="col-lg-10"></div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <input type="submit" id="submit" class="btn btn-secondary ml-4 mr-4" value="Save">
                            </div>
                        </div>

                    </div>
                </div>

            </form>

        </div>
    </div>
    <script>
        $(document).ready(function () {

            function callValidation() {
                var passwordInput = $(this);
                var passwordValidity = passwordInput.prop('validity');
                if (passwordValidity.tooShort) {
                    passwordInput.addClass('is-invalid');
                    $('#password-error').text('Password is too short. Minimum length: ' +
                        passwordInput.prop('minLength'));
                } else if (passwordValidity.tooLong) {
                    passwordInput.addClass('is-invalid');
                    $('#password-error').text('Password is too long. Maximum length: ' +
                        passwordInput.prop('maxLength'));
                } else {
                    passwordInput.removeClass('is-invalid');
                    $('#password-error').text('');
                }
            }

            $('#brandName').on('keyup', function () {
                callValidation()
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            var minLength = 6; // Minimum allowed length
            var maxLength = 128; // Maximum allowed length

            $('#comments').attr('minlength', minLength);
            $('#comments').attr('maxlength', maxLength);

            $('#abv').on('input', function () {
                var textField = $(this);
                var value = textField.val();
                var isValid = /^\d{1}\.\d{3}$/.test(value);

                if (isValid) {
                    textField.removeClass('is-invalid');
                    $('#validationMessage').text('');
                    $('#submit').prop('disabled', false);
                } else {
                    $('#submit').prop('disabled', true);
                    textField.addClass('is-invalid');
                    $('#validationMessage').text('Please enter in D.DDD format.');
                }
            });
        });

        $('#save_brand_form').on('submit', function (event) {
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            event.preventDefault();

            $.ajax({
                url: "{{ url('/save/brand') }}",
                method: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                dataType: "json",
                success: function (data) {
                    var html = '';
                    if (data.errors) {
                        for (var count = 0; count < data.errors.length; count++) {
                            toastr.warning(data.errors[count]);
                        }
                    }
                    if (data.success) {
                        toastr.success(data.success);
                        $('#save_brand_form')[0].reset();
                    }
                    $('#form_result').html(html);
                },
                error: function (xhr, status, error) {
                    toastr.error(
                        "An error occurred. Please try again later."
                    ); // Generic error message for Ajax request failure
                }
            });

        });
    </script>

@endsection
