@extends('admin.layouts.app')

@push('styles_top')

@endpush

@section('content')


    <section class="section">
        <div class="section-header">
            <h1>{{trans('admin/main.reviews')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{trans('admin/main.reviews')}}</div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.total_reviews')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalReviews }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-eye"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.published_reviews')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $publishedReviews }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-calculator"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.rates_average')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $ratesAverage }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-comment-slash"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('update.products_without_review')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $productsWithoutReview }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-body">
            <section class="card">
                <div class="card-body">
                    <form method="get" class="mb-0">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.search')}}</label>
                                    <input type="text" class="form-control" name="search" placeholder="" value="{{ request()->get('search') }}">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.start_date')}}</label>
                                    <div class="input-group">
                                        <input type="date" id="fsdate" class="text-center form-control" name="from" value="{{ request()->get('from') }}" placeholder="Start Date">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.end_date')}}</label>
                                    <div class="input-group">
                                        <input type="date" id="lsdate" class="text-center form-control" name="to" value="{{ request()->get('to') }}" placeholder="End Date">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="input-label">{{trans('update.products')}}</label>
                                    <select name="product_ids[]" multiple="multiple" class="form-control search-product-select2"
                                            data-placeholder="{{trans('update.search_product')}}">

                                        @if(!empty($products) and $products->count() > 0)
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" selected>{{ $product->title }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.status')}}</label>
                                    <select name="status" class="form-control populate">
                                        <option value="">{{trans('admin/main.all_status')}}</option>
                                        <option value="active" @if(request()->get('status') == 'active') selected @endif>{{trans('admin/main.published')}}</option>
                                        <option value="pending" @if(request()->get('status') == 'pending') selected @endif>{{trans('admin/main.hidden')}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group mt-1">
                                    <label class="input-label mb-4"> </label>
                                    <input type="submit" class="text-center btn btn-primary w-100" value="{{trans('admin/main.show_results')}}">
                                </div>
                            </div>

                        </div>
                    </form>
                    <a href="{{route('admin.products_reviews.export')}}" class="btn btn-success">{{trans('admin/main.bulk_export')}}</a>
                </div>
            </section>

            <section class="card">
                <div class="card-body">
                    <table class="table table-striped font-14" id="datatable-details">

                        <tr>
                            <th class="text-left">{{trans('update.product')}}</th>
                            <th class="text-left">{{trans('update.customer')}}</th>
                            <th class="">{{trans('admin/main.comment')}}</th>
                            <th class="">{{trans('admin/main.reply')}}</th>
                            <th class="">{{trans('admin/main.rate')}} (5)</th>
                            <th class="">{{trans('admin/main.created_at')}}</th>
                            <th class="">{{trans('admin/main.status')}}</th>
                            <th class="">{{trans('admin/main.actions')}}</th>
                        </tr>

                        @foreach($reviews as $review)
                            <tr>
                                <td class="text-left">
                                    <a href="{{ $review->product->getUrl() }}" target="_blank">{{ $review->product->title }}</a>
                                </td>

                                <td class="text-left">{{ $review->creator->full_name }}</td>

                                <td>
                                    <button type="button" class="js-show-description btn btn-outline-primary">{{ trans('admin/main.show') }}</button>
                                    <input type="hidden" value="{{ nl2br($review->description) }}">
                                </td>

                                <td class="">{{ $review->comments_count }}</td>

                                <td class="">{{ $review->rates }}</td>

                                <td class="">{{ dateTimeFormat($review->created_at,'j M Y | H:i') }}</td>

                                <td class="">
                                    @if($review->status == 'active')
                                        <b class="f-w-b text-success">{{trans('admin/main.published')}}</b>
                                    @else
                                        <b class="f-w-b text-warning">{{trans('admin/main.hidden')}}</b>
                                    @endif
                                </td>
                                <td class="" width="50">
                                    @can('admin_store_products_reviews_status_toggle')
                                        <a href="{{ getAdminPanelUrl() }}/store/reviews/{{ $review->id }}/toggleStatus" class="btn-transparent text-primary mr-1" data-toggle="tooltip" data-placement="top" title="{{ ($review->status == 'active') ? 'Hidden' : 'Publish' }}">
                                            @if($review->status == 'active')
                                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                            @else
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            @endif
                                        </a>
                                    @endcan

                                    @can('admin_store_products_reviews_detail_show')
                                        <input type="hidden" class="js-product_quality" value="{{ $review->product_quality }}">
                                        <input type="hidden" class="js-purchase_worth" value="{{ $review->purchase_worth }}">
                                        <input type="hidden" class="js-delivery_quality" value="{{ $review->delivery_quality }}">
                                        <input type="hidden" class="js-seller_quality" value="{{ $review->seller_quality }}">


                                        <button type="button" class="js-show-product-review-details btn-transparent text-primary mr-1" data-toggle="tooltip" data-placement="top" title="Rate Detail">
                                            <i class="fa fa-star" aria-hidden="true"></i>
                                        </button>
                                    @endcan

                                    @can('admin_reviews_reply')
                                        <a href="{{ getAdminPanelUrl("/store/reviews/{$review->id}/reply") }}" target="_blank" class="btn-transparent text-primary mr-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.reply') }}">
                                            <i class="fa fa-reply"></i>
                                        </a>
                                    @endcan

                                    @can('admin_store_products_reviews_delete')
                                        @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/store/reviews/'. $review->id.'/delete','btnClass' => ''])
                                    @endcan
                                </td>
                            </tr>
                        @endforeach

                    </table>
                </div>
            </section>
        </div>
    </section>

    <div class="modal fade" id="reviewRateDetail" tabindex="-1" aria-labelledby="contactMessageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactMessageLabel">{{trans('admin/main.view_rates_details')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                        <span class="font-weight-bold">{{ trans('update.product') }}:</span>
                        <span class="js-product_quality"></span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                        <span class="font-weight-bold">{{ trans('product.purchase_worth') }}:</span>
                        <span class="js-purchase_worth"></span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                        <span class="font-weight-bold">{{ trans('update.delivery') }}:</span>
                        <span class="js-delivery_quality"></span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                        <span class="font-weight-bold">{{ trans('update.seller') }}:</span>
                        <span class="js-seller_quality"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('admin/main.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="contactMessage" tabindex="-1" aria-labelledby="contactMessageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactMessageLabel">{{ trans('admin/main.message') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('admin/main.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

    <section class="section">
        <div class="row">
            <!-- Form and Download Button -->
            <div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ trans('admin/main.upload_excel') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.products_reviews.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>{{ trans('admin/main.upload_excel') }}</label>
                                <div class="d-flex align-items-center">
                                    <input type="file" name="excel_file" class="form-control col-md-6 mr-2" required>
                                    <button type="submit" class="btn btn-primary">{{ trans('admin/main.upload') }}</button>
                                </div>
                            </div>
                            <div class="mt-5">
                                <a href="{{ route('admin.products_reviews.download-template') }}" class="btn btn-success">{{ trans('admin/main.download_template') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


            <!-- Instructions Table -->
            <div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ trans('admin/main.instructions') }}</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ trans('admin/main.column_number') }}</th>
                                    <th>{{ trans('admin/main.column_name') }}</th>
                                    <th>{{ trans('admin/main.instructions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>{{ trans('admin/main.product') }}
                                        <span class="badge badge-success">{{ trans('admin/main.required') }}</span>
                                    </td>
                                    <td>{{ trans('admin/main.product_id_description') }}</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>{{ trans('admin/main.customer') }}
                                        <span class="badge badge-success">{{ trans('admin/main.required') }}</span>
                                    </td>
                                    <td>{{ trans('admin/main.customer_id_description') }}</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>{{ trans('admin/main.comment') }}
                                        <span class="badge badge-info">{{ trans('admin/main.optional') }}</span>
                                    </td>
                                    <td>{{ trans('admin/main.comment_description') }}</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>{{ trans('admin/main.rate') }}
                                        <span class="badge badge-success">{{ trans('admin/main.required') }}</span>
                                    </td>
                                    <td>{{ trans('admin/main.rate_description') }}</td>
                                </tr>

                                <tr>
                                    <td>5</td>
                                    <td>{{ trans('admin/main.status') }}
                                        <span class="badge badge-success">{{ trans('admin/main.required') }}</span>
                                    </td>
                                    <td>{{ trans('admin/main.status_description_two') }}</td>
                                </tr>
                            </tbody>



                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/admin/reviews.min.js"></script>
@endpush
