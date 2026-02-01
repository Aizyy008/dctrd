<section>
    <h2 class="section-title after-line mt-2 mb-4">{{ trans('public.category') }}</h2>

    <div class="row">
        <div class="col-12 col-md-6 mt-30">

            <div class="form-group">
                <label class="input-label">{{ trans('public.category') }}</label>

                <select id="categories" class="custom-select @error('category_id')  is-invalid @enderror"
                    name="category_id" required>
                    <option {{ (!empty($product) and !empty($product->category_id)) ? '' : 'selected' }} disabled>
                        {{ trans('public.choose_category') }}</option>
                    @foreach ($productCategories as $productCategory)
                        @if (!empty($productCategory->subCategories) and $productCategory->subCategories->count() > 0)
                            <optgroup label="{{ $productCategory->title }}">
                                @foreach ($productCategory->subCategories as $subCategory)
                                    <option value="{{ $subCategory->id }}"
                                        {{ (!empty($product) and $product->category_id == $subCategory->id or old('category_id') == $subCategory->id) ? 'selected' : '' }}>
                                        {{ $subCategory->title }}</option>
                                @endforeach
                            </optgroup>
                        @else
                            <option value="{{ $productCategory->id }}"
                                {{ (!empty($product) and $product->category_id == $productCategory->id or old('category_id') == $productCategory->id) ? 'selected' : '' }}>
                                {{ $productCategory->title }}</option>
                        @endif
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="col-12 mt-20">
            <div class="form-group {{ (!empty($productCategoryFilters) and count($productCategoryFilters)) ? '' : 'd-none' }}"
                id="categoriesFiltersContainer">
                <span class="input-label d-block">{{ trans('public.category_filters') }}</span>
                <div id="categoriesFiltersCard" class="row">

                    @if (!empty($productCategoryFilters) and count($productCategoryFilters))
                        @foreach ($productCategoryFilters as $filter)
                            <div class="col-12 col-md-3 mt-3">
                                <div class="webinar-category-filters">
                                    <strong class="category-filter-title d-block">{{ $filter->title }}</strong>
                                    <div class="py-2"></div>

                                    @php
                                        $productFilterOptions = $product->selectedFilterOptions
                                            ->pluck('filter_option_id')
                                            ->toArray();

                                        if (!empty(old('filters'))) {
                                            $productFilterOptions = array_merge($productFilterOptions, old('filters'));
                                        }
                                    @endphp

                                    @foreach ($filter->options as $option)
                                        <div class="form-group d-flex align-items-center justify-content-between">
                                            <label class="cursor-pointer font-14 text-gray"
                                                for="filterOptions{{ $option->id }}">{{ $option->title }}</label>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="filters[]" value="{{ $option->id }}"
                                                    {{ !empty($productFilterOptions) && in_array($option->id, $productFilterOptions) ? 'checked' : '' }}
                                                    class="custom-control-input" id="filterOptions{{ $option->id }}">
                                                <label class="custom-control-label"
                                                    for="filterOptions{{ $option->id }}"></label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif

                </div>
            </div>
        </div>

        {{-- Related Course --}}
        <div class="col-12 mt-20">
            @include('admin.webinars.relatedCourse.add_related_course', [
                'relatedCourseItemId' => $product->id,
                'relatedCourseItemType' => 'product',
                'relatedCourses' => $product->relatedCourses,
            ])
        </div>

        {{-- <div class="col-12 mt-20">
            <div id="specificationsList"></div>
        </div> --}}


        {{-- <div class="col-12 mt-20">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="section-title after-line">{{ trans('update.specifications') }}</h2>

                <div class="px-2 mt-3">
                    <button type="button" id="productAddSpecification" class="btn btn-primary btn-sm">{{ trans('update.new_specification') }}</button>
                </div>
            </div>

            <div class="accordion-content-wrapper mt-15" id="specificationsAccordion" role="tablist" aria-multiselectable="true">
                @if (!empty($product->selectedSpecifications) and count($product->selectedSpecifications))
                    <div>
                        @foreach ($product->selectedSpecifications as $selectedSpecificationRow)
                            @include('admin.store.products.create.accordions.specification',['selectedSpecification' => $selectedSpecificationRow])
                        @endforeach
                    </div>
                @else
                    @include(getTemplate() . '.includes.no-result',[
                        'file_name' => 'files.png',
                        'title' => trans('update.specifications_no_result'),
                        'hint' => trans('update.specifications_no_result_hint'),
                    ])
                @endif
            </div>

            <div id="newSpecificationForm" class="d-none">
                @include('admin.store.products.create.accordions.specification')
            </div>
        </div> --}}

        <div class="col-12 mt-20">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="section-title after-line">{{ trans('update.Specifications') }}</h2>
            </div>

            <div class="accordion-content-wrapper mt-15" id="variationsAccordion" role="tablist"
                aria-multiselectable="true">
                @if ($product->category_id != null)
                <div class="d-flex justify-content-between" style="gap:10px;">
                    @foreach (getProductSpecifications($product->category_id) as $productSpecification)
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
                                        @php
                                            $selectedSpecification = \App\Models\ProductSelectedSpecification::where('product_id',$product->id)->pluck('product_specification_id')->toArray();
                                        @endphp
                                        @if (!empty($productSpecification) and !empty($productSpecification->multiValues))
                                            @foreach ($productSpecification->multiValues as $multiValue)
                                                <option value="{{ $multiValue->title }}" @if(in_array($multiValue->id,$selectedSpecification))  selected @endif>{{ $multiValue->title }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                @else
                <div id="specificationsList" style="display: flex ; gap: 15px;justify-content:center">
                    @include('admin.includes.no-result', [
                        'file_name' => 'comment.png',
                        'title' => trans('update.No Specifications'),
                        'hint' => trans('update.Please Select Category At The First'),
                    ])
                </div>
                @endif                
            </div>

        </div>

        <div class="col-12 mt-20">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="section-title after-line">{{ trans('update.variations') }}</h2>
            </div>

            <div class="accordion-content-wrapper mt-15" id="variationsAccordion" role="tablist"
                aria-multiselectable="true">
                @if (!empty($productSpecifications))
                    <div>
                        @include('admin.store.products.create.accordions.variation', [
                            'productSpecifications' => $productSpecifications,
                        ])
                    </div>
                @else
                    @include(getTemplate() . '.includes.no-result', [
                        'file_name' => 'files.png',
                        'title' => trans('update.variations_no_result'),
                        'hint' => trans('update.variations_no_result_hint'),
                    ])
                @endif
            </div>

            <div id="newSpecificationForm" class="d-none">
                @include('admin.store.products.create.accordions.variation')
            </div>
        </div>

        <div class="col-12 mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="section-title after-line">{{ trans('public.faq') }}</h2>

                <div class="px-2 mt-3">
                    <button type="button" id="productAddFAQ"
                        class="btn btn-primary btn-sm mt-10">{{ trans('webinars.add_new_faqs') }}</button>
                </div>
            </div>

            <div class="accordion-content-wrapper mt-15" id="faqsAccordion" role="tablist" aria-multiselectable="true">
                @if (!empty($product->faqs) and count($product->faqs))
                    <div>
                        @foreach ($product->faqs as $faqRow)
                            @include('admin.store.products.create.accordions.faq', ['faq' => $faqRow])
                        @endforeach
                    </div>
                @else
                    @include(getTemplate() . '.includes.no-result', [
                        'file_name' => 'faq.png',
                        'title' => trans('update.product_faq_no_result'),
                        'hint' => trans('update.product_faq_no_result_hint'),
                    ])
                @endif
            </div>

            <div id="newFaqForm" class="d-none">
                @include('admin.store.products.create.accordions.faq')
            </div>
        </div>
    </div>
</section>
