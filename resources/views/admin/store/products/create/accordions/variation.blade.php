
{{-- <div class="d-flex justify-content-between" style="gap:10px;">
    @foreach ($productSpecifications as $productSpecification)
        @if ($productSpecification->input_type == 'multi_value')
            <div class="w-100">
                <label for="">{{ $productSpecification->title }}</label>
                <div class="form-group js-multi-values-input multi_value">
                    <select name="{{ $productSpecification->title }}"
                        class="js-ajax-multi_values form-control select-multi-values-select2 variant-selector"
                        multiple
                        data-placeholder="{{ trans('update.select_specification_params') }}"
                        data-allow-clear="false"
                        data-search="false"
                        data-tags="true"
                        data-search="false" data-tags="true" data-variant_id="{{ $productSpecification->id }}">
                        @if (!empty($productSpecification) and !empty($productSpecification->multiValues))
                            @foreach ($productSpecification->multiValues as $multiValue)
                                <option value="{{ $multiValue->title }}">{{ $multiValue->title }}</option>
                            @endforeach
                        @endif
                    </select>

                    <div class="invalid-feedback"></div>
                </div>
            </div>
        @endif
    @endforeach
</div> --}}

<!-- Table for Variants -->
<div class="table-responsive">
    <table class="table table-bordered mt-4" id="variantTable">
        <thead>
            <tr>
                <th>Variant</th>
                <th>Variant Price</th>
                <th>Stock</th>
                <th>Initial Price</th>
                <th>Discount %</th>
                <th>SKU No</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($product->variants as $variant)
                <tr data-variant-id="{{ $variant->id }}">
                    <td>
                        <input type="text" name="variant_name[]" class="form-control" value="{{ $variant->name }}">
                    </td>
                    <td><input type="number" name="variant_price[]" class="form-control" value="{{ $variant->price }}">
                    </td>
                    <td><input type="number" name="variant_stock[]" class="form-control" value="{{ $variant->stock }}">
                    </td>
                    <td><input type="number" name="variant_initial_price[]" class="form-control"
                            value="{{ $variant->initial_price }}"></td>
                    <td><input type="number" name="variant_discount[]" class="form-control"
                            value="{{ $variant->discount }}"></td>
                    <td><input type="text" name="variant_sku[]" class="form-control" value="{{ $variant->sku }}">
                    </td>
                    <td>
                        <div class="input-group-prepend">
                            <button type="button" class="input-group-text admin-file-manager"
                                data-input="variant_image_{{ $variant->id }}" data-preview="holder">
                                <i class="fa fa-upload"></i>
                            </button>
                        </div>
                        <input type="text" name="variant_image[]" id="variant_image_{{ $variant->id }}"
                            class="form-control" value="{{ $variant->image }}" placeholder="Variant Image" />
                    </td>
                    <td>
                        <input type="hidden" name="variant_id[]" value="{{ $variant->id }}">
                        <a href="{{ route('delete_product_variants',$variant->id) }}" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
</div>
