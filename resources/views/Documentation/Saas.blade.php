@extends('layouts.master')

@section('title', 'Saas License')
@section('content')

    <style>
        i.fa.fa-plus,
        i.fa.fa-pencil-square-o {
            font-size: 22px;
        }
    </style>

    <div class="col-lg-12 m-3">
        <div class="card m-3">
            <div class="m-3">
                <h4>
                    <i class="fa fa-book" aria-hidden="true"></i>
                    <span class="ml-2">Documentation</span>
                </h4>
            </div>

            <div class="row m-3">
                <iframe src="https://drive.google.com/file/d/1VEzoqy_lGFZkVONSCnzk57DJN_dgntG5/preview" width="100%"
                        height="600px"></iframe>
            </div>
        </div>
    </div>

@endsection
