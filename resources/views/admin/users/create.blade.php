@extends('admin.layouts.app')

@push('libraries_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{!empty($user) ?trans('/admin/main.edit'): trans('admin/main.new') }} {{ trans('admin/main.user') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item"><a>{{ trans('admin/main.users') }}</a>
                </div>
                <div class="breadcrumb-item">{{!empty($user) ?trans('/admin/main.edit'): trans('admin/main.new') }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 ">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-6">
                                    <form action="{{ getAdminPanelUrl() }}/users/store" method="Post">
                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <label>{{ trans('/admin/main.full_name') }}</label>
                                            <input type="text" name="full_name"
                                                   class="form-control  @error('full_name') is-invalid @enderror"
                                                   value="{{ old('full_name') }}"
                                                   placeholder="{{ trans('admin/main.create_field_full_name_placeholder') }}"/>
                                            @error('full_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="username">{{ trans('auth.email_or_mobile') }}:</label>
                                            <input name="username" type="text" class="form-control @error('email') is-invalid @enderror @error('mobile') is-invalid @enderror" id="username" value="{{ old('email') }}" aria-describedby="emailHelp">
                                            @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                            @error('mobile')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="input-label">{{ trans('admin/main.password') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span type="button" class="input-group-text">
                                                        <i class="fa fa-lock"></i>
                                                    </span>
                                                </div>
                                                <input type="password" name="password"
                                                       class="form-control @error('password') is-invalid @enderror"/>
                                                @error('password')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- ++++++++++++++++ user role ++++++++++++++++ --}}
                                        <div class="form-group">
                                            <label>{{ trans('/admin/main.role_name') }}</label>
                                            <select class="form-control select2 @error('role_id') is-invalid @enderror" id="roleId" name="role_id">
                                                <option disabled selected>{{ trans('admin/main.select_role') }}</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}" {{ old('role_id') === $role->id ? 'selected' :''}}>{{ $role->name }} - {{ $role->caption }}</option>
                                                @endforeach
                                            </select>
                                            @error('role_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        {{-- ++++++++++++++++ Live Chat Widget : appear in organization or instructor ++++++++++++++++ --}}
                                        <div class="form-group chat-widget-group" style="display: none;">
                                            <label>Live Chat Widget</label>
                                            <textarea name="chat_widget" rows="6" class="form-control @error('chat_widget') is-invalid @enderror"></textarea>
                                            @error('chat_widget')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group" id="groupSelect">
                                            <label class="input-label d-block">{{ trans('admin/main.group') }}</label>
                                            <select name="group_id" class="form-control select2 @error('group_id') is-invalid @enderror">
                                                <option value="" selected disabled></option>

                                                @foreach($userGroups as $userGroup)
                                                    <option value="{{ $userGroup->id }}" @if(!empty($notification) and !empty($notification->group) and $notification->group->id == $userGroup->id) selected @endif>{{ $userGroup->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">@error('group_id') {{ $message }} @enderror</div>
                                        </div>

                                        <div class="form-group">
                                            <label>{{ trans('/admin/main.status') }}</label>
                                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                                <option disabled selected>{{ trans('admin/main.select_status') }}</option>
                                                @foreach (\App\User::$statuses as $status)
                                                    <option
                                                        value="{{ $status }}" {{ old('status') === $status ? 'selected' :''}}>{{  $status }}</option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>


                                        <div class="row">
                                        <!-- New Fields for Backend Link and Frontend Link gr -->
                                        <div class="form-group col-md-6">
                                            <label for="frontend_link_gk">{{ trans('/admin/main.iframe connection (front-end)gr') }}</label>
                                            <input type="text" name="frontend_link_gk" id="frontend_link_gk"
                                                class="form-control @error('frontend_link_gk') is-invalid @enderror"
                                                value="{{ old('frontend_link_gk') }}"
                                                placeholder="Enter frontend link (optional)">
                                            @error('frontend_link_gk')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <!-- Front Iframe Height -->
                                        <div class="form-group  col-md-4">
                                            <label for="front_iframe_height_gk">{{ trans('/admin/main.Frontend Iframe Height gr') }}</label>
                                            <input type="number" name="front_iframe_height_gk" id="front_iframe_height_gk"
                                                class="form-control @error('front_iframe_height_gk') is-invalid @enderror"
                                                value="{{ old('front_iframe_height_gk') }}"
                                                placeholder="Enter frontend iframe height">
                                            @error('front_iframe_height_gk')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                        <div class="row">
                                        <!-- New Fields for Backend Link and Frontend Link En -->
                                        <div class="form-group col-md-6">
                                            <label for="frontend_link">{{ trans('/admin/main.iframe connection (front-end)en') }}</label>
                                            <input type="text" name="frontend_link" id="frontend_link"
                                                class="form-control @error('frontend_link') is-invalid @enderror"
                                                value="{{ old('frontend_link') }}"
                                                placeholder="Enter frontend link (optional)">
                                            @error('frontend_link')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <!-- Front Iframe Height -->
                                        <div class="form-group  col-md-4">
                                            <label for="front_iframe_height">{{ trans('/admin/main.Frontend Iframe Height') }}</label>
                                            <input type="number" name="front_iframe_height" id="front_iframe_height"
                                                class="form-control @error('front_iframe_height') is-invalid @enderror"
                                                value="{{ old('front_iframe_height') }}"
                                                placeholder="Enter frontend iframe height">
                                            @error('front_iframe_height')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="backend_link">{{ trans('/admin/main.iframe connection (back-end)') }}</label>
                                            <input type="text" name="backend_link" id="backend_link"
                                                class="form-control @error('backend_link') is-invalid @enderror"
                                                value="{{ old('backend_link') }}"
                                                placeholder="Enter backend link (optional)">
                                            @error('backend_link')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <!-- Back Iframe Height -->
                                        <div class="form-group col-md-4">
                                            <label for="back_iframe_height">{{ trans('/admin/main.Backend Iframe Height') }}</label>
                                            <input type="number" name="back_iframe_height" id="back_iframe_height"
                                                class="form-control @error('back_iframe_height') is-invalid @enderror"
                                                value="{{ old('back_iframe_height') }}"
                                                placeholder="Enter backend iframe height">
                                            @error('back_iframe_height')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                        <div class="text-right mt-4">
                                            <button class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script>
        // ++++++++++++++++ Live Chat Widget : appear in organization or instructor ++++++++++++++++
        document.addEventListener('DOMContentLoaded', function()
        {
            console.log("Script initialized");
            const roleSelect = document.getElementById('roleId');
            const chatWidgetGroup = document.querySelector('.chat-widget-group');
            if (!roleSelect || !chatWidgetGroup)
            {
                console.error("Required elements not found");
                return;
            }
            function toggleChatWidget()
            {
                console.log("toggleChatWidget called");
                // Get the selected value from Select2
                const selectedValue = $('#roleId').val();
                if (!selectedValue)
                {
                    console.log("No value selected");
                    chatWidgetGroup.style.display = 'none';
                    return;
                }
                // Get the option element using the value
                const selectedOption = roleSelect.querySelector(`option[value="${selectedValue}"]`);
                const roleName = selectedOption ?
                    selectedOption.text.split(' - ')[0].toLowerCase() : '';
                console.log("Selected value:", selectedValue);
                console.log("Selected role:", roleName);
                chatWidgetGroup.style.display =
                    (roleName === 'organization' || roleName === 'teacher') ?
                    'block' : 'none';
            }
            // Initial call after Select2 is initialized
            setTimeout(() => {
                toggleChatWidget();
            }, 500); // Wait for Select2 initialization
            // Listen for Select2 change event
            $('#roleId').on('select2:select', toggleChatWidget);
            // Fallback for regular change event
            roleSelect.addEventListener('change', toggleChatWidget);
        });
    </script>
@endpush
