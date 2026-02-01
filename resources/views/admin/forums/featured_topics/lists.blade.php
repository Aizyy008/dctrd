@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ $pageTitle }}</div>
            </div>
        </div>

        <a href="{{route('admin.featured-topics.export')}}" class="btn btn-success mb-3">{{trans('admin/main.bulk_export')}}</a>
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th>{{ trans('admin/main.icon') }}</th>
                                        <th>{{ trans('public.topic') }}</th>
                                        <th class="text-center">{{ trans('public.date') }}</th>
                                        <th>{{ trans('admin/main.actions') }}</th>
                                    </tr>

                                    @foreach($featuredTopics as $feature)

                                        <tr>
                                            <td>
                                                <img src="{{ $feature->icon }}" alt="" width="48" height="48" class="">
                                            </td>

                                            <td class="text-center">{{ $feature->topic->title }}</td>

                                            <td class="text-center">{{ dateTimeFormat($feature->created_at, 'j M Y | H:i') }}</td>

                                            <td width="150">

                                                @can('admin_featured_topics_edit')
                                                    <a href="{{ getAdminPanelUrl() }}/featured-topics/{{ $feature->id }}/edit" class="btn-sm" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('admin_featured_topics_delete')
                                                    @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/featured-topics/'. $feature->id .'/delete','btnClass' => 'btn-sm','icon' => true])
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach

                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $featuredTopics->appends(request()->input())->links() }}
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
                    <form action="{{ route('admin.featured-topics.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>{{ trans('admin/main.upload_excel') }}</label>
                            <div class="d-flex align-items-center">
                                <input type="file" name="excel_file" class="form-control col-md-6 mr-2" required>
                                <button type="submit" class="btn btn-primary">{{ trans('admin/main.upload') }}</button>
                            </div>
                        </div>
                        <div class="mt-5">
                            <a href="{{ route('admin.featured-topics.download-template') }}" class="btn btn-success">
                                {{ trans('admin/main.download_template') }}
                            </a>
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
                                <td>
                                    {{ trans('admin/main.icon') }}
                                    <span class="badge badge-success">{{ trans('admin/main.required') }}</span>
                                </td>
                                <td>{{ trans('admin/main.icon_description') }}</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>
                                    {{ trans('admin/main.topic_id') }}
                                    <span class="badge badge-success">{{ trans('admin/main.required') }}</span>
                                </td>
                                <td>{{ trans('admin/main.topic_id_description') }}</td>
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
