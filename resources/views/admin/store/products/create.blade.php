@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="/assets/default/vendors/bootstrap-tagsinput/bootstrap-tagsinput.min.css">
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a
                        href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item">{{ $pageTitle }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12 ">
                    <div class="card">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="text-danger">
                                    {{ trans('update.please_fix_the_error_fields_that_are_specified') }}</div>
                            @endif

                            <form id="productForm" method="post"
                                action="{{ getAdminPanelUrl() }}/store/products/{{ !empty($product) ? $product->id . '/update' : 'store' }}"
                                class="webinar-form">
                                {{ csrf_field() }}

                                @include('admin.store.products.create.basic_information')

                                @if (!empty($product))
                                    @include('admin.store.products.create.extra_information')

                                    @include('admin.store.products.create.image_and_files')

                                    @include('admin.store.products.create.category_and_specification')


                                    <section class="mt-3">
                                        <h2 class="section-title after-line">{{ trans('public.message_to_reviewer') }}</h2>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group mt-15">
                                                    <textarea name="message_for_reviewer" rows="10" class="form-control">{{ (!empty($product) and $product->message_for_reviewer) ? $product->message_for_reviewer : old('message_for_reviewer') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                @endif

                                <div class="row">
                                    <div class="col-12">
                                        <input type="hidden" id="productStatusInput" name="status"
                                            value="{{ \App\Models\Product::$draft }}">

                                        <button type="button" id="saveAndPublish"
                                            class="btn btn-success">{{ !empty($product) ? trans('admin/main.save_and_publish') : trans('admin/main.save_and_continue') }}</button>

                                        @if (!empty($product))
                                            <button type="button" id="saveReject"
                                                class="btn btn-warning">{{ trans('public.reject') }}</button>

                                            @include('admin.includes.delete_button', [
                                                'url' =>
                                                    getAdminPanelUrl() .
                                                    '/store/products/' .
                                                    $product->id .
                                                    '/delete',
                                                'btnText' => trans('public.delete'),
                                                'hideDefaultClass' => true,
                                                'btnClass' => 'btn btn-danger',
                                            ])
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('admin.store.products.create.modals.file_description_modal')
    @include('admin.store.products.create.modals.file_modal')
@endsection

@push('scripts_bottom')
    <script>
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        var requestFailedLang = '{{ trans('public.request_failed') }}';
        var maxFourImageCanSelect = '{{ trans('update.max_four_image_can_select') }}';
    </script>

    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>

    <script src="/assets/default/vendors/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>

    <script src="/assets/default/js/admin/new_product.min.js"></script>


    {{-- Variant --}}
    <script>
        $(window).on("load", function() { // Ensures script runs only after everything is loaded
            let selectedVariants = {};

            $(".variant-selector").select2({
                tags: true,
                createTag: function(params) {
                    return {
                        id: params.term,
                        text: params.term,
                        newOption: true
                    };
                }
            });

            $(document).on("change", ".variant-selector", function() {
                let selectElement = $(this);
                let variant_id = selectElement.data('variant_id');
                let values = selectElement.val() || [];

                if (values.length === 0) return;

                selectedVariants[variant_id] = values;

                saveNewVariant(variant_id, values);
                appendNewVariants();
            });

            function saveNewVariant(variantId, values) {
                return $.ajax({
                    url: "{{ route('save_new_value_variant') }}",
                    type: "POST",
                    data: {
                        variant_id: variantId,
                        values: values,
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(response) {
                        console.log("New Variant Saved:", values);
                    },
                    error: function(xhr) {
                        console.error("Error saving variant:", xhr.responseText);
                    }
                });
            }

            function appendNewVariants() {
                let allVariants = generateCombinations(Object.values(selectedVariants));

                let tableBody = $("#variantTable tbody");
                // tableBody.empty(); // Clear old variants to prevent duplication

                if (allVariants.length === 0) {
                    appendVariantRow("variant_1", "Default Variant"); // Ensure at least one row
                }

                allVariants.forEach((variantName, index) => {
                    let variantId = `variant_${index + 1}`;
                    appendVariantRow(variantId, variantName);
                });
            }

            function generateCombinations(arrays) {
                if (arrays.length === 0) return [];
                return arrays.reduce((acc, current) => {
                    let result = [];
                    acc.forEach(a => {
                        current.forEach(b => {
                            result.push(a && b ? `${a}-${b}` : b);
                        });
                    });
                    return result;
                }, [""]);
            }

            function appendVariantRow(variantId, variantName) {
                let tableBody = $("#variantTable tbody");

                let row = `
                    <tr data-variant-id="${variantId}">
                        <td><input type="text" name="variant_name[]" class="form-control" value="${variantName}"></td>
                        <td><input type="number" name="variant_price[]" class="form-control"></td>
                        <td><input type="number" name="variant_stock[]" class="form-control"></td>
                        <td><input type="number" name="variant_initial_price[]" class="form-control"></td>
                        <td><input type="number" name="variant_discount[]" class="form-control"></td>
                        <td><input type="text" name="variant_sku[]" class="form-control"></td>
                        <td>
                            <div class="input-group-prepend">
                                <button type="button" class="input-group-text admin-file-manager" data-input="variant_image_${variantId}" data-preview="holder">
                                    <i class="fa fa-upload"></i>
                                </button>
                            </div>
                            <input type="text" name="variant_image[]" id="variant_image_${variantId}" class="form-control" placeholder="Variant Image"/>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger delete-variant">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                tableBody.append(row);
            }

            $(document).on("click", ".delete-variant", function() {
                $(this).closest("tr").remove();
            });

        });
    </script>

    {{-- Specifications --}}
    <script>
        $(document).ready(function() {
            $('#categories').on('change', function() {
                let categoryId = $(this).val();
                var specification_url = "{{ route('get_category_specifications', ['id' => '#id']) }}";
                specification_url = specification_url.replace('#id', categoryId);

                if (categoryId) {
                    $.ajax({
                        url: specification_url,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            let specificationsHtml = '';

                            response.forEach(specification => {
                                if (specification.input_type === 'multi_value') {
                                    let optionsHtml = '';

                                    if (specification.multi_values) {
                                        specification.multi_values.forEach(
                                            multiValue => {
                                                optionsHtml +=
                                                    `<option value="${multiValue.title}">${multiValue.title}</option>`;
                                            });
                                    }

                                    specificationsHtml += `
                                <div class="w-100">
                                    <label for="">${specification.title}</label>
                                    <div class="form-group js-multi-values-input multi_value">
                                        <select name="${specification.title}"
                                            class="js-ajax-multi_values form-control select-multi-values-select2 variant-selector"
                                            multiple
                                            data-placeholder="Select Specification"
                                            data-allow-clear="false"
                                            data-search="false"
                                            data-tags="true"
                                            data-variant_id="${specification.id}">
                                            ${optionsHtml}
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            `;
                                }
                            });

                            $('#specificationsList').html(specificationsHtml);

                            // Reinitialize Select2 after appending new elements
                            $('.select-multi-values-select2').select2({
                                width: '100%',
                                placeholder: "Select Specification",
                                allowClear: false
                            });
                        }
                    });
                } else {
                    $('#specificationsList').html('');
                }
            });
        });
    </script>

@endpush
