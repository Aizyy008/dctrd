<div class="tab-pane mt-3 fade @if(empty($becomeInstructor) and (empty(request()->get('tab')))) active show @endif" id="general" role="tabpanel" aria-labelledby="general-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/users/{{ $user->id .'/update' }}" method="Post">
                {{ csrf_field() }}

                <div class="form-group">
                    <label>{{ trans('/admin/main.full_name') }}</label>
                    <input type="text" name="full_name"
                           class="form-control  @error('full_name') is-invalid @enderror"
                           value="{{ !empty($user) ? $user->full_name : old('full_name') }}"
                           placeholder="{{ trans('admin/main.create_field_full_name_placeholder') }}"/>
                    @error('full_name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                @can('admin_update_user_role_in_edit_page')
                    <div class="form-group">
                        <label>{{ trans('/admin/main.role_name') }}</label>
                        <select class="form-control @error('role_id') is-invalid @enderror" id="roleId" name="role_id">
                            <option disabled {{ empty($user) ? 'selected' : '' }}>{{ trans('admin/main.select_role') }}</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}"
                                        data-role-name="{{ $role->name }}"
                                        {{ (!empty($user) && $user->role_id == $role->id) ? 'selected' : '' }}>
                                    {{ $role->caption }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                @endcan
                {{-- ++++++++++++++++ Live Chat Widget : appear in organization or instructor ++++++++++++++++ --}}
                <div class="form-group chat-widget-group"
                    style="display: {{ ($user && ($user->role_name == 'organization' || $user->role_name == 'teacher')) ? 'block' : 'none' }};">
                    <label>Live Chat Widget</label>
                    <textarea name="chat_widget" rows="6"
                            class="form-control @error('chat_widget') is-invalid @enderror">{{ $user->chat_widget ?? '' }}</textarea>
                    @error('chat_widget')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="input-label">{{ trans('update.timezone') }}</label>
                    <select name="timezone" class="form-control select2" data-allow-clear="false">
                        <option value="" {{ empty($user->timezone) ? 'selected' : '' }} disabled>{{ trans('public.select') }}</option>
                        @foreach(getListOfTimezones() as $timezone)
                            <option value="{{ $timezone }}" @if(!empty($user) and $user->timezone == $timezone) selected @endif>{{ $timezone }}</option>
                        @endforeach
                    </select>
                    @error('timezone')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <div class="row">
                    <!-- New Fields for Backend Link and Frontend Link GR -->
                    <div class="form-group col-md-6">
                        <label for="frontend_link_gk">{{ trans('/admin/main.iframe connection (front-end)gr') }}</label>
                        <input type="text" name="frontend_link_gk" id="frontend_link_gk"
                            class="form-control @error('frontend_link_gk') is-invalid @enderror"
                            value="{{ old('frontend_link_gk', $user->frontend_link_gk ?? '') }}"
                            placeholder="Enter frontend link (optional)">
                        @error('frontend_link_gk')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Front Iframe Height -->
                    <div class="form-group col-md-4">
                        <label for="front_iframe_height_gk">{{ trans('/admin/main.Frontend Iframe Height gr') }}</label>
                        <input type="number" name="front_iframe_height_gk" id="front_iframe_height_gk"
                            class="form-control @error('front_iframe_height_gk') is-invalid @enderror"
                            value="{{ old('front_iframe_height_gk', $user->front_iframe_height_gk ?? '') }}"
                            placeholder="Enter frontend iframe height">
                        @error('front_iframe_height_gk')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>


                <div class="row">
                <div class="form-group col-md-6">
                    <label>{{ trans('admin/main.iframe connection (front-end)') }}</label>
                    <input type="text" name="frontend_link" class="form-control @error('frontend_link') is-invalid @enderror"
                           value="{{ !empty($user) ? $user->frontend_link : old('frontend_link') }}"
                           placeholder="Enter backend link"/>
                    @error('frontend_link')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <!-- Frontend Iframe Height -->
                <div class="form-group col-md-4">
                    <label>{{ trans('admin/main.Frontend Iframe Height') }}</label>
                    <input type="number" name="front_iframe_height" class="form-control @error('front_iframe_height') is-invalid @enderror"
                        value="{{ !empty($user) ? $user->front_iframe_height : old('front_iframe_height') }}"
                        placeholder="Enter frontend iframe height"/>
                    @error('front_iframe_height')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

                <div class="row">
                <div class="form-group col-md-6">
                    <label>{{ trans('admin/main.iframe connection (back-end)') }}</label>
                    <input type="text" name="backend_link" class="form-control @error('backend_link') is-invalid @enderror"
                           value="{{ !empty($user) ? $user->backend_link : old('backend_link') }}"
                           placeholder="Enter backend link"/>
                    @error('backend_link')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <!-- Backend Iframe Height -->
                <div class="form-group col-md-4">
                    <label>{{ trans('admin/main.Backend Iframe Height') }}</label>
                    <input type="number" name="back_iframe_height" class="form-control @error('back_iframe_height') is-invalid @enderror"
                        value="{{ !empty($user) ? $user->back_iframe_height : old('back_iframe_height') }}"
                        placeholder="Enter backend iframe height"/>
                    @error('back_iframe_height')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

                @if(!empty($currencies) and count($currencies))
                    @php
                        $userCurrency = currency($user);
                    @endphp

                    <div class="form-group">
                        <label class="input-label">{{ trans('update.currency') }}</label>
                        <select name="currency" class="form-control select2" data-allow-clear="false">
                            @foreach($currencies as $currencyItem)
                                <option value="{{ $currencyItem->currency }}" {{ ($userCurrency == $currencyItem->currency) ? 'selected' : '' }}>{{ currenciesLists($currencyItem->currency) }} ({{ currencySign($currencyItem->currency) }})</option>
                            @endforeach
                        </select>
                        @error('currency')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                @endif

                @if($user->isUser() || $user->isTeacher())
                    <div class="form-group">
                        <label class="input-label">{{ trans('admin/main.organization') }}</label>
                        <select name="organ_id" data-search-option="just_organization_role" class="form-control search-user-select2"
                                data-placeholder="{{ trans('admin/main.search') }} {{ trans('admin/main.organization') }}">

                            @if(!empty($user) and !empty($user->organization))
                                <option value="{{ $user->organization->id }}" selected>{{ $user->organization->full_name }}</option>
                            @endif
                        </select>
                    </div>
                @endif

                <div class="form-group">
                    <label for="username">{{ trans('admin/main.email') }}:</label>
                    <input name="email" type="text" id="username" value="{{ $user->email }}" class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username">{{ trans('admin/main.mobile') }}:</label>
                    <input name="mobile" type="text" value="{{ $user->mobile }}" class="form-control @error('mobile') is-invalid @enderror">
                    @error('mobile')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>{{ trans('admin/main.password') }}</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"/>
                    @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>{{ trans('admin/main.bio') }}</label>
                    <textarea name="bio" rows="3" class="form-control @error('bio') is-invalid @enderror">{{ $user->bio }}</textarea>
                    @error('bio')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>{{ trans('site.about') }}</label>
                    <textarea name="about" rows="6" class="form-control @error('about') is-invalid @enderror">{{ $user->about }}</textarea>
                    @error('about')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>


                <div class="form-group">
                    <label>{{ trans('update.certificate_additional') }}</label>
                    <input name="certificate_additional" value="{{ $user->certificate_additional }}" class="form-control @error('certificate_additional') is-invalid @enderror"/>
                    @error('certificate_additional')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>{{ trans('/admin/main.status') }}</label>
                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                        <option disabled {{ empty($user) ? 'selected' : '' }}>{{ trans('admin/main.select_status') }}</option>

                        @foreach (\App\User::$statuses as $status)
                            <option value="{{ $status }}" {{ !empty($user) && $user->status === $status ? 'selected' :''}}>{{  $status }}</option>
                        @endforeach
                    </select>
                    @error('status')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="input-label">{{ trans('auth.language') }}</label>
                    <select name="language" class="form-control">
                        <option value="">{{ trans('auth.language') }}</option>
                        @foreach($userLanguages as $lang => $language)
                            <option value="{{ $lang }}" @if(!empty($user) and mb_strtolower($user->language) == mb_strtolower($lang)) selected @endif>{{ $language }}</option>
                        @endforeach
                    </select>
                    @error('language')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group custom-switches-stacked mt-2">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="ban" value="0">
                        <input type="checkbox" name="ban" id="banSwitch" value="1" {{ (!empty($user) and $user->ban) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="banSwitch">{{ trans('admin/main.ban') }}</label>
                    </label>
                </div>

                <div class="row {{ (($user->ban) or (old('ban') == 'on')) ? '' : 'd-none' }}" id="banSection">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="input-label">{{ trans('public.from') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="dateInputGroupPrepend">
                                                                        <i class="fa fa-calendar-alt"></i>
                                                                    </span>
                                </div>
                                <input type="text" name="ban_start_at" class="form-control datepicker @error('ban_start_at') is-invalid @enderror" value="{{ !empty($user->ban_start_at) ? dateTimeFormat($user->ban_start_at,'Y/m/d') :'' }}"/>
                                @error('ban_start_at')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="input-label">{{ trans('public.to') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="dateInputGroupPrepend">
                                                                        <i class="fa fa-calendar-alt"></i>
                                                                    </span>
                                </div>
                                <input type="text" name="ban_end_at" class="form-control datepicker @error('ban_end_at') is-invalid @enderror" value="{{ !empty($user->ban_end_at) ? dateTimeFormat($user->ban_end_at,'Y/m/d') :'' }}"/>
                                @error('ban_end_at')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group custom-switches-stacked">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="verified" value="0">
                        <input type="checkbox" name="verified" id="verified" value="1" {{ (!empty($user) and $user->verified) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="verified">{{ trans('admin/main.enable_blue_badge') }}</label>
                    </label>
                </div>

                <div class="form-group custom-switches-stacked mt-2">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="affiliate" value="0">
                        <input type="checkbox" name="affiliate" id="affiliateSwitch" value="1" {{ (!empty($user) and $user->affiliate) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="affiliateSwitch">{{ trans('panel.affiliate') }}</label>
                    </label>
                </div>

                <div class="form-group custom-switches-stacked mt-2">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="can_create_store" value="0">
                        <input type="checkbox" name="can_create_store" id="canCreateStoreSwitch" value="1" {{ (!empty($user) and $user->can_create_store) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="canCreateStoreSwitch">{{ trans('update.store') }}</label>
                    </label>
                    <div class="text-muted text-small">{{ trans('update.admin_user_edit_can_create_store_hint') }}</div>
                </div>
                
                <div class="form-group custom-switches-stacked mt-2">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="cross_selling" value="0">
                        <input type="checkbox" name="cross_selling" id="can_cross_selling" value="1" {{ (!empty($user) and $user->cross_selling) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="can_cross_selling">{{ trans('update.Cross Selling') }}</label>
                    </label>
                </div>
                
                <div class="form-group custom-switches-stacked mt-2">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="up_selling" value="0">
                        <input type="checkbox" name="up_selling" id="can_up_selling" value="1" {{ (!empty($user) and $user->up_selling) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="can_up_selling">{{ trans('update.Up Selling') }}</label>
                    </label>
                </div>

                <div class="form-group custom-switches-stacked mt-2">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="access_content" value="1">
                        <input type="checkbox" name="access_content" id="contentAccessLimitationSwitch" value="0" {{ (!empty($user) and !$user->access_content) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="contentAccessLimitationSwitch">{{ trans('update.content_access_limitation') }}</label>
                    </label>
                    <div class="text-muted text-small">{{ trans('update.admin_user_edit_content_access_limitation_hint') }}</div>
                </div>

                @if(!empty($user) and !$user->isUser())
                    <div class="form-group custom-switches-stacked mt-2">
                        <label class="custom-switch pl-0">
                            <input type="hidden" name="enable_ai_content" value="0">
                            <input type="checkbox" name="enable_ai_content" id="aiContentLimitationSwitch" value="1" {{ (!empty($user) and $user->enable_ai_content) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                            <span class="custom-switch-indicator"></span>
                            <label class="custom-switch-description mb-0 cursor-pointer" for="aiContentLimitationSwitch">{{ trans('update.enable_ai_content') }}</label>
                        </label>
                        <div class="text-muted text-small">{{ trans('update.admin_user_edit_enable_ai_content_hint') }}</div>
                    </div>
                @endif

                <div class=" mt-4">
                    <button class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ++++++++++++++++ Live Chat Widget : appear in organization or instructor ++++++++++++++++
    document.addEventListener('DOMContentLoaded', function()
    {
        console.log("Script initialized");
        const roleSelect = document.getElementById('roleId');
        const chatWidgetGroup = document.querySelector('.chat-widget-group');
        if (!roleSelect || !chatWidgetGroup) {
            console.error("Required elements not found:", {
                roleSelect: !!roleSelect,
                chatWidgetGroup: !!chatWidgetGroup
            });
            return;
        }
        // Log initial state
        console.log("Initial display style:", chatWidgetGroup.style.display);
        function toggleChatWidget() {
            console.log("toggleChatWidget called");
            const selectedOption = roleSelect.options[roleSelect.selectedIndex];
            const roleName = selectedOption.getAttribute('data-role-name')?.toLowerCase() || '';
            console.log("Selected option:", selectedOption);
            console.log("Role name from data attribute:", roleName);
            console.log("Selected value:", roleSelect.value);
            const shouldShow = roleName === 'organization' || roleName === 'teacher';
            chatWidgetGroup.style.display = shouldShow ? 'block' : 'none';
            console.log("Should show:", shouldShow);
            console.log("New display style:", chatWidgetGroup.style.display);
        }
        // Call immediately
        toggleChatWidget();
        // Add event listener
        roleSelect.addEventListener('change', toggleChatWidget);
        console.log("Event listener added");
    });
</script>
