@extends('admin.layouts.app')

@push('libraries_top')

@endpush

@section('content')

    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.testimonials') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.testimonials') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            @can('admin_testimonials_create')
                                <a href="{{ getAdminPanelUrl() }}/testimonials/create" class="btn btn-primary">
                                    {{ trans('admin/main.add_new') }}
                                </a>
                            @endcan

                            <!-- Bulk Export Button -->
                            @can('admin_testimonials_create')
                                <a href="{{ route('admin.testimonials.export') }}" class="btn btn-success ml-2">
                                    {{ trans('admin/main.bulk_export') }}
                                </a>
                            @endcan
                        </div>


                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('admin/main.user_name') }}</th>
                                        <th>{{ trans('admin/main.rate') }}</th>
                                        <th class="text-center">{{ trans('admin/main.content') }}</th>
                                        <th class="text-center">{{ trans('admin/main.status') }}</th>
                                        <th>{{ trans('admin/main.created_at') }}</th>
                                        <th>{{ trans('admin/main.action') }}</th>
                                    </tr>
                                    @foreach($testimonials as $testimonial)
                                        <tr>
                                            <td>
                                                <img src="{{ $testimonial->user_avatar }}" alt="" width="56" height="56" class="rounded-circle">
                                            </td>
                                            <td>{{ $testimonial->user_name }}</td>
                                            <td>{{ $testimonial->rate }}</td>
                                            <td class="text-center" width="30%">{{ nl2br(truncate($testimonial->comment, 150, true)) }}</td>

                                            <td class="text-center">
                                                @if($testimonial->status == 'active')
                                                    <span class="text-success">{{ trans('admin/main.active') }}</span>
                                                @else
                                                    <span class="text-warning">{{ trans('admin/main.disable') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ dateTimeFormat($testimonial->created_at, 'j M Y | H:i') }}</td>
                                            <td width="150px">

                                                @can('admin_supports_reply')
                                                    <a href="{{ getAdminPanelUrl() }}/testimonials/{{ $testimonial->id }}/edit" class="btn-transparent text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('admin_supports_delete')
                                                    @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/testimonials/'.$testimonial->id.'/delete' , 'btnClass' => ''])
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $testimonials->appends(request()->input())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

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
                        <form action="{{ route('admin.testimonials.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>{{ trans('admin/main.upload_excel') }}</label>
                                <div class="d-flex align-items-center">
                                    <input type="file" name="excel_file" class="form-control col-md-6 mr-2" required>
                                    <button type="submit" class="btn btn-primary">{{ trans('admin/main.upload') }}</button>
                                </div>
                            </div>
                            <div class="mt-5">
                                <a href="{{ route('admin.testimonials.download-template') }}" class="btn btn-success">{{ trans('admin/main.download_template') }}</a>
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
                                    <td>{{ trans('admin/main.user_avatar') }}
                                        <span class="badge badge-success">{{ trans('admin/main.required') }}</span>
                                    </td>
                                    <td>{{ trans('admin/main.user_avatar_description') }}</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>{{ trans('admin/main.user_name') }}
                                        <span class="badge badge-success">{{ trans('admin/main.required') }}</span>
                                    </td>
                                    <td>{{ trans('admin/main.user_name_description') }}</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>{{ trans('admin/main.job_title') }}
                                        <span class="badge badge-success">{{ trans('admin/main.required') }}</span>
                                    </td>
                                    <td>{{ trans('admin/main.job_itle_description') }}</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>{{ trans('admin/main.rate') }}
                                        <span class="badge badge-info">{{ trans('admin/main.optional') }}</span>
                                    </td>
                                    <td>{{ trans('admin/main.rate_description') }}</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>{{ trans('admin/main.comment') }}
                                        <span class="badge badge-success">{{ trans('admin/main.required') }}</span>
                                    </td>
                                    <td>{{ trans('admin/main.comment_description') }}</td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>{{ trans('admin/main.status') }}
                                        <span class="badge badge-info">{{ trans('admin/main.optional') }}</span>
                                    </td>
                                    <td>{{ trans('admin/main.status_description') }}</td>
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

@endpush
