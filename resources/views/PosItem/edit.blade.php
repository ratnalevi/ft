@extends('layouts.master')

@section('template_title')
    {{ __('Update') }} Pos Item
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Pos Item</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ '/PosItem/' . $posItem->POSItemsID . '/update' }}" role="form"
                              enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('PosItem.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
