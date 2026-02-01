@extends('admin.layouts.app')

@push('libraries_top')

@endpush

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

        <div class="section-body">

            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-comment-dots"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{trans('update.total_forums')}}</h4>
                            </div>
                            <div class="card-body">
                                {{ $totalForums }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-comment-alt"></i>
                        </div>

                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{trans('update.total_topics')}}</h4>
                            </div>
                            <div class="card-body">
                                {{ $totalTopics }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-comment"></i>
                        </div>

                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{trans('update.total_posts')}}</h4>
                            </div>
                            <div class="card-body">
                                {{ $postsCount }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="fas fa-comments"></i>
                        </div>

                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{trans('update.active_members')}}</h4>
                            </div>
                            <div class="card-body">
                                {{ $membersCount }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <a href="{{route('admin.forums.export')}}" class="btn btn-success mb-3">{{trans('admin/main.bulk_export')}}</a>
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th>{{ trans('admin/main.icon') }}</th>
                                        <th class="text-left">{{ trans('admin/main.title') }}</th>
                                        @if(empty(request()->get('subForums')))
                                            <th>{{ trans('update.sub_forums') }}</th>
                                        @endif
                                        <th>{{ trans('update.topics') }}</th>
                                        <th>{{ trans('site.posts') }}</th>
                                        <th>{{ trans('admin/main.status') }}</th>
                                        <th>{{ trans('admin/main.closed') }}</th>
                                        <th>{{ trans('admin/main.action') }}</th>
                                    </tr>
                                    @foreach($forums as $forum)

                                        <tr>
                                            <td>
                                                <img src="{{ $forum->icon }}" width="30" alt="">
                                            </td>
                                            <td class="text-left">
                                                @if(!empty($forum->subForums) and count($forum->subForums))
                                                    <a href="{{ getAdminPanelUrl() }}/forums?subForums={{ $forum->id }}">{{ $forum->title }}</a>
                                                @else
                                                    <a href="{{ getAdminPanelUrl() }}/forums/{{ $forum->id }}/topics">{{ $forum->title }}</a>
                                                @endif
                                            </td>
                                            @if(empty(request()->get('subForums')))
                                                <td>
                                                    @if(!empty($forum->subForums))
                                                        {{ count($forum->subForums) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endif
                                            <td>{{ $forum->topics_count }}</td>
                                            <td>{{ $forum->posts_count }}</td>
                                            <td>
                                                {{ trans('admin/main.'.$forum->status) }}
                                            </td>
                                            <td>
                                                @if($forum->close)
                                                    {{ trans('admin/main.yes') }}
                                                @else
                                                    {{ trans('admin/main.no') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($forum->subForums) and count($forum->subForums))
                                                    <a href="{{ getAdminPanelUrl() }}/forums?subForums={{ $forum->id }}"
                                                       class="btn-transparent btn-sm text-primary mr-1"
                                                       data-toggle="tooltip" data-placement="top" title="{{ trans('update.forums') }}"
                                                    >
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                @else
                                                    @can('admin_forum_topics_lists')
                                                        <a href="{{ getAdminPanelUrl() }}/forums/{{ $forum->id }}/topics"
                                                           class="btn-transparent btn-sm text-primary mr-1"
                                                           data-toggle="tooltip" data-placement="top" title="{{ trans('update.topics') }}"
                                                        >
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    @endcan
                                                @endif

                                                @can('admin_forum_edit')
                                                    <a href="{{ getAdminPanelUrl() }}/forums/{{ $forum->id }}/edit"
                                                       class="btn-transparent btn-sm text-primary">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('admin_forum_delete')
                                                    @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/forums/'.$forum->id.'/delete'])
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $forums->appends(request()->input())->links() }}
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
                    <form action="{{ route('admin.forums.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>{{ trans('admin/main.upload_excel') }}</label>
                            <div class="d-flex align-items-center">
                                <input type="file" name="excel_file" class="form-control col-md-6 mr-2" required>
                                <button type="submit" class="btn btn-primary">{{ trans('admin/main.upload') }}</button>
                            </div>
                        </div>
                        <div class="mt-5">
                            <a href="{{ route('admin.forums.download-template') }}" class="btn btn-success">{{ trans('admin/main.download_template') }}</a>
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
                                    {{ trans('admin/main.title') }}
                                    <span class="badge badge-success">{{ trans('admin/main.required') }}</span>
                                </td>
                                <td>{{ trans('admin/main.forum_title_description') }}</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>
                                    {{ trans('admin/main.description') }}
                                    <span class="badge badge-success">{{ trans('admin/main.required') }}</span>
                                </td>
                                <td>{{ trans('admin/main.forum_description') }}</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>
                                    {{ trans('admin/main.icon') }}
                                    <span class="badge badge-info">{{ trans('admin/main.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/main.icon_description') }}</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>
                                    {{ trans('admin/main.role_id') }}
                                    <span class="badge badge-info">{{ trans('admin/main.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/main.role_id_description') }}</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>
                                    {{ trans('admin/main.group_id') }}
                                    <span class="badge badge-info">{{ trans('admin/main.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/main.group_id_description') }}</td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>
                                    {{ trans('admin/main.closed') }}
                                    <span class="badge badge-success">{{ trans('admin/main.required') }}</span>
                                </td>
                                <td>{{ trans('admin/main.forum_closed_description') }}</td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>
                                    {{ trans('admin/main.status') }}
                                    <span class="badge badge-success">{{ trans('admin/main.required') }}</span>
                                </td>
                                <td>{{ trans('admin/main.forum_status_description') }}</td>
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
