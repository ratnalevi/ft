<!-- Favicon -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<link rel="icon" href="{{ asset('assets/img/brand/favicon.png') }}" type="image/x-icon"/>

<!-- Favicon -->
<link rel="icon" href="{{ asset('assets/img/brand/favicon.png') }}" type="image/x-icon"/>

<!-- Internal Morris Css-->
<link href="{{ asset('assets/plugins/morris.js/morris.css') }}" rel="stylesheet">

<!-- Icons css -->
<link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">

<!--  Right-sidemenu css -->
<link href="{{ asset('assets/plugins/sidebar/sidebar.css') }}" rel="stylesheet">

<!-- P-scroll bar css-->
<link href="{{ asset('assets/plugins/perfect-scrollbar/p-scrollbar.css') }}" rel="stylesheet"/>

<!--  Left-Sidebar css -->
<link rel="stylesheet" href="{{ asset('assets/css/sidemenu.css') }}">

<!--- Style css --->
<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

<!--- Dark-mode css --->
<link href="{{ asset('assets/css/style-dark.css') }}" rel="stylesheet">

<!---Skinmodes css-->
<link href="{{ asset('assets/css/skin-modes.css') }}" rel="stylesheet"/>

<!--- Animations css-->
<link href="{{ asset('assets/css/animate.css') }}" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<style>
    .table thead th,
    .table thead td {
        color: #37374e;
        font-weight: 700;
        font-size: 14px;
        letter-spacing: .5px;
        text-transform: uppercase;
        border-bottom-width: 1px;
        border-top-width: 0;
        padding: 0 15px 5px;
    }

    .side-menu__label {
        white-space: nowrap;
        -webkit-box-flex: 1;
        -ms-flex: 1 1 auto;
        flex: 1 1 auto;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        color: #ffffff;
        position: relative;
        font-size: 15.5px;
        line-height: 1;
        vertical-align: middle;
        font-weight: 400;
    }

    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    table.dataTable thead th, table.dataTable thead td {
        padding-right: 30px;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 7px;
    }

    table.dataTable tbody td.sorting_1 {
        background-color: #ffffff;
    }

    .app-sidebar {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        background: red;
        color: #14112d;
        width: 240px;
        max-height: 100%;
        z-index: 1024;
        background: #282727;
        -webkit-box-shadow: 0px 8px 14.72px 1.28px rgb(229 228 230 / 50%);
        box-shadow: 0px 8px 14.72px 1.28px rgb(229 228 230 / 50%);
        border-right: 1px solid #e3e3e3;
        -webkit-transition: left 0.3s ease, width 0.3s ease;
        transition: left 0.3s ease, width 0.3s ease;
    }

    .side-menu__icon {
        font-size: 23px;
        line-height: 0;
        margin-right: 14px;
        width: 29px;
        height: 31px;
        line-height: 34px;
        border-radius: 3px;
        text-align: center;
        color: #f1f1f1;
        fill: #5b6e88;
    }

    .main-sidemenu {
        margin-top: 33px;
        height: 90%;
    }

    .dataTables_info {
        margin-left: 1%;
    }

    .paginate_button {
        cursor: pointer;
    }

    .dataTables_length {
        margin-left: 1%;
    }

    .dataTables_filter {
        margin-right: 1%;
    }

    input.btn.btn-secondary.ml-4 {
        border-radius: 21px;
        width: 100px;
    }
</style>
