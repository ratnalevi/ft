@extends('layouts.master')

@section('template_title')
    {{ $posItem->ItemName ?? __('Show') . " " . __('Pos Item') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Pos Item</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('PosItem.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">

                                <div class="form-group mb-2 mb20">
                                    <strong>Positemsid:</strong>
                                    {{ $posItem->POSItemsID }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Accountid:</strong>
                                    {{ $posItem->AccountID }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Locationid:</strong>
                                    {{ $posItem->LocationID }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Beerbrandid:</strong>
                                    {{ $posItem->BeerBrandID }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Ounces:</strong>
                                    {{ $posItem->Ounces }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Itemnum:</strong>
                                    {{ $posItem->ItemNUM }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Itemname:</strong>
                                    {{ $posItem->ItemName }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Itemdesc:</strong>
                                    {{ $posItem->ItemDESC }}
                                </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
