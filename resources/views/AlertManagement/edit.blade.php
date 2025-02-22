@extends('layouts.master')

@section('title', 'Edit Alert')
@section('content')
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" /> --}}
    <style>
        .form-control {
            display: block;
            width: 100%;
            padding: 0.422rem 0.875rem;
            font-size: 0.9375rem;
            font-weight: 400;
            line-height: 1.5;
            color: #6f6b7d;
            background-color: #fff;
            background-clip: padding-box;
            border: 1.2px solid #dbdade;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: 0.375rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

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
        <h5 class="fw-bold py-3 mb-3">
            <span class="text-muted fw-light">Administration/ <a href="/alert-management">Alert Management/</a></span>
            <u>Edit - Alert</u>
        </h5>
        <div class="card">
            <div
                class="card-header sticky-element bg-label-secondary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
                <h4><i class="fa fa-bell" aria-hidden="true"></i>
                    <span class="ml-2">Edit - Alert</span>
                </h4>
            </div>
            <hr class="mt-0">

            {{-- <form class="m-3" id="save_alert_form"> --}}
            <form method="post" action="/save/alert" class="m-3">
                <span id="form_result"></span>
                @csrf
                <div class="m-1">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Alert Name</label>
                                <input type="text" maxlength="64" onkeypress="return /[0-9a-zA-Z\s]/i.test(event.key)"
                                       class="form-control" value="{{ $alert->AlertName }}" name="alert_name" required>
                                <input type="text" class="form-control" hidden value="{{ $alert->AlertID }}"
                                       name="alert_id" required>
                            </div>
                        </div>


                        <div class="col-lg-8">
                            <div class="form-group">
                                <label for="">Alert Description</label>
                                <input type="text" maxlength="64" class="form-control"
                                       value="{{ $alert->AlertDescription }}"
                                       name="alert_description" id="alert_description" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="m-1">
                    <div class="row">
                        <div class="col-lg-10"></div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <input style="width: 100px" type="submit" class="btn btn-secondary ml-4 mr-4"
                                       value="Update">
                            </div>
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>

    <script>
        $('#save_alert_form').on('submit', function (event) {
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            event.preventDefault();

            $.ajax({
                url: "{{ url('/save/alert') }}",
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
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error(
                        "An error occurred. Please try again later."); // Generic error message for Ajax request failure
                }
            });

        });
    </script>

@endsection
