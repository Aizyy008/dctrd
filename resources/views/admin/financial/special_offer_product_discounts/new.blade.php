@extends('admin.layouts.app')


@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active">
                    <a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                {{-- <div class="breadcrumb-item">{{ trans('admin/main.product_discount') }}</div> --}}
                <div class="breadcrumb-item">{{ $pageTitle}}</div>
            </div>
        </div>


        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-8 col-lg-6">
                            <form action="{{ getAdminPanelUrl() }}/financial/special_offer_product_discounts/{{ !empty($productDiscounts) ? 
                                            $productDiscounts->id.'/update' : 'store' }}"
                                  method="Post">
                                {{ csrf_field() }}
                                {{-- ++++++++++++++++ Name ++++++++++++++++ --}}
                                <div class="form-group">
                                    <label>{{ trans('admin/main.name') }}</label>
                                    <input type="text" name="name"
                                           class="form-control  @error('name') is-invalid @enderror"
                                           value="{{ !empty($productDiscounts) ? $productDiscounts->name : old('name') }}"
                                           placeholder="{{ trans('admin/main.name_placeholder') }}"/>
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                {{-- ++++++++++++++++++ start : product_type ++++++++++++++++++ --}}
                                <div class="form-group js-products-input">
                                    <label class="input-label d-block">{{ trans('update.product_type') }}</label>
                                    <select name="product_type" id="product_type" class="form-control">
                                        <option value="">{{ trans('select') }}</option>
                                        <option value="physical" {{ (!empty($productDiscounts) && $productDiscounts->product->type == 'physical') ? 'selected' : '' }}>
                                            {{ trans('update.physical') }}
                                        </option>
                                        <option value="virtual" {{ (!empty($productDiscounts) && $productDiscounts->product->type == 'virtual') ? 'selected' : '' }}>
                                            {{ trans('update.virtual') }}
                                        </option>
                                    </select>
                                    @error('product_type')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                {{-- ++++++++++++++++++ end : product_type ++++++++++++++++++ --}}
                                {{-- ++++++++++++++++++ start : products ++++++++++++++++++ --}}
                                <div id="products_dropdown_container" class="js-course-field form-group" style="display: none;">
                                    <label>{{ trans('update.products') }}</label>
                                    <select name="product_id" id="product_id" class="form-control @error('product_id') is-invalid @enderror"
                                            data-selected="{{ $productDiscounts->product_id ?? '' }}">
                                        <option value="">select</option>
                                        @if(!empty($productDiscounts) && !empty($productDiscounts->product_id))
                                            <option value="{{ $productDiscounts->product->id }}" selected>
                                                {{ $productDiscounts->product->title }}
                                            </option>
                                        @endif
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                {{-- ++++++++++++++++++ end : products ++++++++++++++++++ --}}
                                {{-- ++++++++++++++++++++++ percentage ++++++++++++++++++++++ --}}
                                <div class="form-group ">
                                    <label>{{ trans('admin/main.discount_percentage') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-percentage"></i>
                                            </div>
                                        </div>
                                        <input type="number"
                                               name="percent" class="spinner-input form-control text-center  @error('percent') is-invalid @enderror"
                                               value="{{ !empty($productDiscounts) ? $productDiscounts->percent : old('percent') }}"
                                               maxlength="3" min="0" max="100">
                                        @error('percent')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- ++++++++++++++++++++++ from_date ++++++++++++++++++++++ --}}
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.from_date') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="dateRangeLabel">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="from_date" class="form-control text-center datetimepicker"
                                               aria-describedby="dateRangeLabel" autocomplete="off"
                                               value="{{ !empty($productDiscounts) ? dateTimeFormat($productDiscounts->from_date,'Y-m-d H:i',false) : old('from_date') }}"/>
                                        @error('from_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror

                                    </div>
                                </div>
                                {{-- ++++++++++++++++++++++ to_date ++++++++++++++++++++++ --}}
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.to_date') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="dateRangeLabel">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="to_date" class="form-control text-center datetimepicker"
                                               aria-describedby="dateRangeLabel" autocomplete="off"
                                               value="{{ !empty($productDiscounts) ? dateTimeFormat($productDiscounts->to_date,'Y-m-d H:i',false) : old('to_date') }}"/>
                                        @error('to_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- ++++++++++++++++++++++ status ++++++++++++++++++++++ --}}
                                {{-- {{ dd($productDiscounts->status ) }} --}}
                                <div class="form-group">
                                    <label>{{ trans('admin/main.status') }}</label>
                                    <select name="status" class="form-control custom-select @error('status') is-invalid @enderror">
                                        <option value="active" {{ isset($productDiscounts) && $productDiscounts->status == \App\Models\SpecialOffer::$active ? 'selected' : '' }}>
                                            {{ trans('panel.active') }}
                                        </option>
                                        <option value="inactive" {{ isset($productDiscounts) && $productDiscounts->status == \App\Models\SpecialOffer::$inactive ? 'selected' : '' }}>
                                            {{ trans('panel.inactive') }}
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class=" mt-4">
                                    <button class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/admin/special_offers.min.js"></script>
    {{-- =========== products dropdown =============== --}}
    <script>
        $(document).ready(function ()
        {
            $('#product_type').on('change', function () {
                let productType = $(this).val();
                let productDropdownContainer = $('#products_dropdown_container');
                let productDropdown = $('#product_id');
                let selectedProductId = productDropdown.attr('data-selected'); // Get selected product ID

                if (productType) {
                    productDropdownContainer.show();
                } else {
                    productDropdownContainer.hide();
                    // productDropdown.empty();
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.getProductsByType') }}",
                    method: 'GET',
                    data: { product_type: productType },
                    success: function (response) {
                        console.log(response);
                        productDropdown.empty().append('<option value="">select</option>');
                        
                        $.each(response.products, function (index, product) 
                        {
                            let isSelected = product.id == selectedProductId ? 'selected' : '';
                            productDropdown.append('<option value="' + product.id + '" ' + isSelected + '>' + product.title + '</option>');
                        });
                    },
                    error: function () {
                        console.log("Error fetching products.");
                    }
                });
            });
            // Trigger change event on page load if `product_type` is pre-selected
            $('#product_type').trigger('change');
        });

    </script>
@endpush
