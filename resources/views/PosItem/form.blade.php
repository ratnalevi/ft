<div class="row padding-1 p-1">
    <div class="col-md-12">

        <div class="form-group mb-2 mb20">
            <label for="beer_brand_i_d" class="form-label">{{ __('Beer Brand') }}</label>
            <select style="border-radius: 36px;" name="BeerBrandID" id="BeerBrandID" class="form-control @error('BeerBrandID') is-invalid @enderror" placeholder="Beer Brand" value="{{ old('BeerBrandID', $posItem?->BeerBrandID) }}">
                @foreach ($allBrands as $brand)
                    @if ($posItem->BeerBrandID == $brand->BeerBrandsID)
                        <option value="{{ $brand->BeerBrandsID }}" selected>{{ $brand->Brand }}
                    @else
                        <option value="{{ $brand->BeerBrandsID }}">{{ $brand->Brand }}
                    @endif
                @endforeach
            </select>
            {!! $errors->first('BeerBrandID', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="ounces" class="form-label">{{ __('Ounces') }}</label>
            <select style="border-radius: 36px;" name="Ounces" id="Ounces" class="form-control @error('Ounces') is-invalid @enderror" placeholder="Beer Brand" value="{{ old('Ounces', $posItem?->Ounces) }}">
                <option value="16">16</option>
                <option value="30">30</option>
                <option value="64">64</option>
            </select>

            {!! $errors->first('Ounces', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="item_n_u_m" class="form-label">{{ __('Item Number') }}</label>
            <input type="text" name="ItemNUM" class="form-control @error('ItemNUM') is-invalid @enderror" value="{{ old('ItemNUM', $posItem?->ItemNUM) }}" id="item_n_u_m" placeholder="Itemnum">
            {!! $errors->first('ItemNUM', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="item_name" class="form-label">{{ __('Item Name') }}</label>
            <input type="text" name="ItemName" class="form-control @error('ItemName') is-invalid @enderror" value="{{ old('ItemName', $posItem?->ItemName) }}" id="item_name" placeholder="Itemname">
            {!! $errors->first('ItemName', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="item_d_e_s_c" class="form-label">{{ __('Item DESC') }}</label>
            <input type="text" name="ItemDESC" class="form-control @error('ItemDESC') is-invalid @enderror" value="{{ old('ItemDESC', $posItem?->ItemDESC) }}" id="item_d_e_s_c" placeholder="Itemdesc">
            {!! $errors->first('ItemDESC', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
{{--        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>--}}
    </div>
</div>
