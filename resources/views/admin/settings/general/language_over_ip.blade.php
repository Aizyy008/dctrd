@php
    if (!empty($itemValue) and !is_array($itemValue)) {
        $itemValue = json_delanguage($itemValue, true);
    }
@endphp

<div class="tab-pane mt-3 fade" id="language_over_ip" role="tabpanel" aria-labelledby="basic-tab">
    <div class="row">
        <div class="col-12">
            <form action="{{ getAdminPanelUrl() }}/settings/language_over_ip/store" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="page" value="general">
                <input type="hidden" name="language_over_ip" value="language_over_ip">

                {{-- ++++++++++++++++++ Enable Language Over IP ++++++++++++++++++  --}}
                <div class="form-group custom-switches-stacked">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="value[enable_language_over_ip]" value="0">
                        <input type="checkbox" name="value[enable_language_over_ip]" id="enable_language_over_ipSwitch" value="1" {{ (!empty($itemValue) and !empty($itemValue['enable_language_over_ip']) and $itemValue['enable_language_over_ip']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="enable_language_over_ipSwitch">{{ trans('admin/main.enable_language_over_ip') }}</label>
                    </label>
                    @error('value.enable_language_over_ip')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row language-over-ip-section" style="display: {{ (!empty($itemValue) and !empty($itemValue['enable_language_over_ip']) and $itemValue['enable_language_over_ip']) ? 'block' : 'none' }};">
                    {{-- ================== Existing Mappings Table ================== --}}
                    <div class="row mt-4">
                        <div class="col-md-6 col-6">
                            <h5>{{ trans('admin/main.country_mappings') }}</h5>
                            <p>{{ trans('admin/main.country_mappings_description') }}</p>
                        </div>
                        <div class="col-md-6 col-6">
                            <br><hr>
                            <p>{{ trans('admin/main.update_mapping_note') }}</p>
                            <div class="mt-4">
                                <div id="mappingsTable">
                                    {{-- {{ dd(\App\Models\LanguageOverIpCountryMappingSetting::all()) }} --}}
                                    @if(\App\Models\LanguageOverIpCountryMappingSetting::count() > 0)
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('admin/main.country') }}</th>
                                                    <th>{{ trans('admin/main.language') }}</th>
                                                    <th>{{ trans('admin/main.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="mappingsBody">
                                                @foreach(\App\Models\LanguageOverIpCountryMappingSetting::all() as $mapping)
                                                    <tr data-mapping-id="{{ $mapping->id }}">
                                                        <td>{{ $mapping->country->title ?? 'Unknown' }}</td>
                                                        <td>{{ $languages[$mapping->language] ?? $mapping->language }}</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-danger delete-mapping" data-id="{{ $mapping->id }}">{{ trans('admin/main.delete') }}</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p id="noMappings">{{ trans('admin/main.no_mappings') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- ================== Create Mapping ================== --}}
                    <div class="row">
                        <div class="col-md-6 col-6">
                            <h5>{{ trans('admin/main.create_mapping') }}</h5>
                            <p>{{ trans('admin/main.create_mapping_description') }}</p>
                        </div>
                        <div class="col-md-6 col-6">
                            <br><hr>
                            {{-- ========= Countries Dropdown ========== --}}
                            @php
                                $countries = App\Models\Region::where('type','country')->pluck('id','title');
                            @endphp
                            <div class="form-group">
                                <label class="input-label d-block">{{ trans('admin/main.country') }}</label>
                                <select id="country_id" class="form-control select2" data-placeholder="{{ trans('admin/main.select_country') }}">
                                    <option value=""></option>
                                    @foreach($countries as $title => $id)
                                        <option value="{{ $id }}">{{ $title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- ========= Languages Dropdown ========= --}}
                            <div class="form-group">
                                <label class="input-label d-block">{{ trans('admin/main.language') }}</label>
                                <select id="language" name="language_code" class="form-control select2" data-placeholder="{{ trans('admin/main.select_language') }}">
                                    <option value="">{{ trans('admin/main.select_language') }}</option>
                                    @foreach(getUserLanguagesLists() as $code => $language)
                                        <option value="{{ $code }}" data-language="{{ $language }}">{{ $language }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- ===== save button ===== --}}
                            <button type="button" id="createMapping" class="btn btn-primary">{{ trans('admin/main.create_mapping') }}</button>
                        </div>
                    </div>
                    {{-- ================== PopUp Notifications Accordion ================== --}}
                    <div class="row mt-4">
                        {{-- ========= left ========= --}}
                        <div class="col-md-6 col-6">
                            <h5>{{ trans('admin/main.popup_text') }}</h5>
                            <p>{{ trans('admin/main.popup_text_description') }}</p>
                        </div>
                        {{-- ========= right ========= --}}
                        <div class="col-md-6 col-6">
                            <br><hr>
                            <div class="accordion" id="popupSettingsAccordion">
                                @foreach(getUserLanguagesLists() as $code => $language)
                                    @php
                                        $setting = $popupSettings[$language] ?? null;
                                    @endphp
                                    <div class="card">
                                        <div class="card-header" id="heading_{{ $language }}" style="background-color: #f0f0f1">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse_{{ $language }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse_{{ $language }}">
                                                    {{ $language }}
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapse_{{ $language }}" class="collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading_{{ $language }}" data-parent="#popupSettingsAccordion">
                                            <div class="card-body">
                                                <div class="row">
                                                    {{-- Notification Title --}}
                                                    <div class="col-md-12">
                                                        <label for="notification_title_{{ $language }}">{{ trans('admin/main.notification_title') }}</label>
                                                        <input type="text" class="form-control" id="notification_title_{{ $language }}" value="{{ $setting->notification_title ?? '' }}">
                                                    </div>
                                                    {{-- Notification Text --}}
                                                    <div class="col-md-12">
                                                        <label for="notification_text_{{ $language }}">{{ trans('admin/main.notification_text') }}</label>
                                                        <input type="text" class="form-control" id="notification_text_{{ $language }}" value="{{ $setting->notification_text ?? '' }}">
                                                    </div>
                                                    {{-- Confirm Button Text --}}
                                                    <div class="col-md-12">
                                                        <label for="confirm_button_text_{{ $language }}">{{ trans('admin/main.confirm_button_text') }}</label>
                                                        <input type="text" class="form-control" id="confirm_button_text_{{ $language }}" value="{{ $setting->confirm_button_text ?? '' }}">
                                                    </div>
                                                    {{-- Cancel Button Text --}}
                                                    <div class="col-md-12">
                                                        <label for="cancel_button_text_{{ $language }}">{{ trans('admin/main.cancel_button_text') }}</label>
                                                        <input type="text" class="form-control" id="cancel_button_text_{{ $language }}" value="{{ $setting->cancel_button_text ?? '' }}">
                                                    </div>
                                                    {{-- Action Type --}}
                                                    <div class="col-md-12 my-2">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6>{{ trans('admin/main.action_type') }}</h6>
                                                                <p>{{ trans('admin/main.action_type_description') }}</p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input type="radio" name="action_type_{{ $language }}" id="action_type_popup_{{ $language }}" value="1" class="form-check-input" {{ (!$setting || $setting->action_type == 1) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="action_type_popup_{{ $language }}">{{ trans('admin/main.action_type_popup') }}</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input type="radio" name="action_type_{{ $language }}" id="action_type_redirect_{{ $language }}" value="0" class="form-check-input" {{ $setting && $setting->action_type == 0 ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="action_type_redirect_{{ $language }}">{{ trans('admin/main.action_type_redirect') }}</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-primary create-popup-language mt-3" data-language="{{ $language }}">{{ trans('admin/main.save') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                {{-- +++++++++ submit form button +++++++++ --}}
                <button type="submit" class="btn btn-primary">{{ trans('admin/main.save_change') }}</button>
            </form>
        </div>
    </div>
</div>
@push('scripts_bottom')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function()
        {
            // ---------------------------- "Country Mapping" Settings ----------------------------
            // ============= Store ============= 
            // 1- Store "Country Mapping" without reload page : "AJAX endpoint"
            $('#createMapping').click(function()
            {
                let country_id = $('#country_id').val();
                let language = $('#language').find('option:selected').data('language') || '';
                let language_code = $('#language').val();
                if (!country_id || !language) 
                {
                    alert('Please select both a country and a language.');
                    return;
                }
                // ajax request
                $.ajax({
                    url: '{{ getAdminPanelUrl() }}/settings/language_over_ip/store-mapping',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        country_id: country_id,
                        language: language,
                        language_code: language_code,
                    },
                    success: function(response)
                    {
                        if (response.success)
                        {
                            console.log(response);
                            let tableBody = $('#mappingsBody');
                            let noMappings = $('#noMappings');

                            // Remove "no mappings" message and create table if needed
                            if (noMappings.length)
                            {
                                noMappings.remove();
                                $('#mappingsTable').html(`
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ trans('admin/main.country') }}</th>
                                                <th>{{ trans('admin/main.language') }}</th>
                                                <th>{{ trans('admin/main.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="mappingsBody"></tbody>
                                    </table>
                                `);
                                tableBody = $('#mappingsBody');
                            }

                            // Append new mapping
                            tableBody.append(`
                                <tr data-mapping-id="${response.mapping.id}">
                                    <td>${response.mapping.country}</td>
                                    <td>${response.mapping.language}</td>
                                    <td>
                                        <a href="{{ getAdminPanelUrl() }}/settings/language_over_ip/${response.id}/delete" class="btn btn-sm btn-danger" onclick="return confirm('{{ trans('admin/main.confirm_delete') }}')">{{ trans('admin/main.delete') }}</a>
                                    </td>
                                </tr>
                            `);

                            // Clear dropdowns
                            $('#country_id').val('').trigger('change');
                            $('#language').val('').trigger('change');

                            alert('Mapping created successfully!');
                        }
                    },
                    error: function() {
                        alert('Error creating mapping.');
                    }
                });
            });
            // ============= Delete =============
            // 2- Delete "Country Mapping" without reload page : "AJAX endpoint"
            $(document).on('click', '.delete-mapping', function() {
                if (!confirm('{{ trans('admin/main.confirm_delete') }}')) return;

                let mappingId = $(this).data('id');
                let row = $(`tr[data-mapping-id="${mappingId}"]`);

                $.ajax({
                    url: '{{ getAdminPanelUrl() }}/settings/language_over_ip/' + mappingId + '/delete',
                    method: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            row.remove();
                            if ($('#mappingsBody tr').length === 0) {
                                $('#mappingsTable').html('<p id="noMappings">{{ trans('admin/main.no_mappings') }}</p>');
                            }
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Error deleting mapping.');
                    }
                });
            });
            // ---------------------------- "Popup" Settings ----------------------------
            // ============= Store ============= 
            // Save Popup Settings
            $(document).on('click', '.create-popup-language', function(e)
            {
                    e.preventDefault();
                    let language = $(this).data('language');
                    let notificationTitle = $(`#notification_title_${language}`).val();
                    let notificationText = $(`#notification_text_${language}`).val();
                    let confirmButtonText = $(`#confirm_button_text_${language}`).val();
                    let cancelButtonText = $(`#cancel_button_text_${language}`).val();
                    let actionType = $(`input[name="action_type_${language}"]:checked`).val();

                    $.ajax({
                        url: '{{ getAdminPanelUrl() }}/settings/language_over_ip/store-popup',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            language: language,
                            notification_title: notificationTitle,
                            notification_text: notificationText,
                            confirm_button_text: confirmButtonText,
                            cancel_button_text: cancelButtonText,
                            action_type: actionType
                        },
                        success: function(response) 
                        {
                            if (response.success) 
                            {
                                alert(response.message);
                            }
                        },
                        error: function() 
                        {
                            alert('Error saving popup settings.');
                        }
                    });
                });
            });
            // +++++++++++++++++++ enable_language_over_ip Switch +++++++++++++++++++
            // Toggle visibility of language over IP sections
            function toggleLanguageSections()
            {
                let isEnabled = $('#enable_language_over_ipSwitch').is(':checked');
                $('.language-over-ip-section').css('display', isEnabled ? 'block' : 'none');
            }
            // Initial toggle based on checkbox state
            toggleLanguageSections();
            // Toggle on checkbox change
            $('#enable_language_over_ipSwitch').change(function() 
            {
                toggleLanguageSections();
            });
    </script>
@endpush
