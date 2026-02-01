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

            <section class="card">
                <div class="card-body">
                    <form method="get" class="mb-0">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.search')}}</label>
                                    <input name="full_name" type="text" class="form-control" value="{{ request()->get('full_name') }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.role') }}</label>
                                    <select name="role_id" class="form-control">
                                        <option value="">{{ trans('public.all') }}</option>
                                        @foreach($staffsRoles as $role)
                                            <option value="{{ $role->id }}" @if(!empty(request()->get('role_id')) and request()->get('role_id') == $role->id) selected @endif>{{ $role->caption }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label mb-4"> </label>
                                    <input type="submit" class="text-center btn btn-primary w-100" value="{{trans('admin/main.show_results')}}">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        {{-- ++++++++++++++++++++ export_excel ++++++++++++++++++++ --}}
                        <div class="card-header">
                            @can('admin_users_export_excel')
                                <a href="{{ route('staffs.export') }}" class="btn btn-primary">
                                    {{ trans('admin/main.export_xls') }}
                                </a>
                            @endcan
                            <div class="h-10"></div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th>{{ trans('admin/main.id') }}</th>
                                        <th class="text-left">{{ trans('admin/main.name') }}</th>
                                        <th>{{ trans('admin/main.role_name') }}</th>
                                        <th>{{ trans('admin/main.register_date') }}</th>
                                        <th>{{ trans('admin/main.status') }}</th>
                                        <th width="120">{{ trans('admin/main.actions') }}</th>
                                    </tr>

                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td class="text-left">
                                                <div class="d-flex align-items-center">
                                                    <figure class="avatar mr-2">
                                                        <img src="{{ $user->getAvatar() }}" alt="{{ $user->full_name }}">
                                                    </figure>
                                                    <div class="media-body ml-1">
                                                        <div class="mt-0 mb-1 font-weight-bold">{{ $user->full_name }}</div>

                                                        @if($user->mobile)
                                                            <div class="text-primary text-small font-600-bold">{{ $user->mobile }}</div>
                                                        @endif

                                                        @if($user->email)
                                                            <div class="text-primary text-small font-600-bold">{{ $user->email }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="text-center">{{ $user->role->caption }}</td>
                                            <td>{{ dateTimeFormat($user->created_at, 'j M Y | H:i') }}</td>

                                            <td>
                                                <div class="media-body">
                                                    @if($user->ban and !empty($user->ban_end_at) and $user->ban_end_at > time())
                                                        <div class="mt-0 mb-1 font-weight-bold text-danger">{{ trans('admin/main.ban') }}</div>
                                                        <div class="text-small font-600-bold">Until {{ dateTimeFormat($user->ban_end_at, 'Y/m/j') }}</div>
                                                    @else
                                                        <div class="mt-0 mb-1 font-weight-bold {{ ($user->status == 'active') ? 'text-success' : 'text-warning' }}">{{ trans('admin/main.'.$user->status) }}</div>
                                                        <div class="text-small font-600-bold {{ ($user->verified ? ' text-success ' : ' text-warning ') }}">({{ trans('public.'.($user->verified ? 'verified' : 'not_verified')) }})</div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center mb-2" width="120">

                                                @can('admin_users_edit')
                                                    <a href="{{ getAdminPanelUrl() }}/users/{{ $user->id }}/edit" class="btn-transparent  text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('admin_users_delete')
                                                    @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/users/'.$user->id.'/delete' , 'btnClass' => '', 'deleteConfirmMsg' => trans('update.user_delete_confirm_msg')])
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $users->appends(request()->input())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- +++++++++++++++++++++ alerts +++++++++++++++++ --}}
    @if (session('success'))
        <div class="alert alert-success my-25 text-white bg-success" style="background-color: #47c363 !important;">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger text-white">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{-- +++++++++++++++++++++ import Excel +++++++++++++++++ --}}
    <!-- Separate Form for Excel Import -->
    <section class="card">
        <div class="row">
            <!-- Form and Download Button -->
            <div class="card-body px-5">
                <form method="post" action="{{ route('staffs.import') }}" enctype="multipart/form-data" class="mt-3" id="excelImportForm">
                    {{ csrf_field() }}
                    <h6 class="section-title">{{ trans('admin/main.upload_excel') }}</h6>
                    <div class="row">
                        <div class="col-8">
                            <div class="form-group mt-15">
                                <label>{{ trans('admin/main.upload_excel') }}</label>
                                <input type="file" name="excel_file" class="form-control @error('excel_file') is-invalid @enderror" accept=".xlsx,.xls">
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary" style="margin-top:42px !important;">
                                {{ trans('admin/main.upload') }}
                            </button>
                        </div>
                    </div>
                    <!-- Download Template Button -->
                    <div class="mt-3">
                        <a href="{{ route('staff.download.template') }}" class="btn btn-success btn-sm">
                            {{ trans('admin/main.download_template') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
    {{-- +++++++++++++++++++++ download Excel template +++++++++++++++++ --}}
    @include('admin.users.users_excel_instructions')
@endsection

@push('scripts_bottom')

@endpush
