<style>
    .badge-secondary { background-color: #3abaf4 !important; }
    .badge-success { background-color: #47c363 !important; }
</style>
<!-- Instructions Section -->
<section class="card">
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card">
                <div class="mx-4 mt-4">
                    <div>
                        <h3>{{ trans('admin/pages/users.excel_upload_instructions') }}</h3>
                    </div>
                    <div class="mt-1">
                        <p>{{ trans('admin/pages/users.follow_instructions') }}</p>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Instructions Table -->
                    <table class="table table-striped font-14">
                        <thead>
                            <tr>
                                <th>{{ trans('admin/pages/users.column_number') }}</th>
                                <th>{{ trans('admin/pages/users.column_name') }}</th>
                                <th>{{ trans('admin/pages/users.instruction') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>
                                    {{ trans('admin/pages/users.full_name') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.full_name_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>
                                    {{ trans('admin/pages/users.email') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.email_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>
                                    {{ trans('admin/pages/users.mobile') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.mobile_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>{{ trans('admin/pages/users.backend_link') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.backend_link_instruction_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>{{ trans('admin/pages/users.back_iframe_height') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.back_iframe_height_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td>{{ trans('admin/pages/users.frontend_link') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.frontend_link_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>9</td>
                                <td>{{ trans('admin/pages/users.front_iframe_height') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.front_iframe_height_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>10</td>
                                <td>{{ trans('admin/pages/users.bio') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.bio_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>11</td>
                                <td>
                                    {{ trans('admin/pages/users.password') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.password_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>12</td>
                                <td>
                                    {{ trans('admin/pages/users.google_id') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.google_id_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>13</td>
                                <td>
                                    {{ trans('admin/pages/users.facebook_id') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.facebook_id_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>14</td>
                                <td>
                                    {{ trans('admin/pages/users.logged_count') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.logged_count_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>15</td>
                                <td>
                                    {{ trans('admin/pages/users.financial_approval') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.financial_approval_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>16</td>
                                <td>
                                    {{ trans('admin/pages/users.installment_approval') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.installment_approval_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>17</td>
                                <td>
                                    {{ trans('admin/pages/users.enable_installments') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.enable_installments_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>18</td>
                                <td>
                                    {{ trans('admin/pages/users.disable_cashback') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.disable_cashback_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>19</td>
                                <td>
                                    {{ trans('admin/pages/users.enable_registration_bonus') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.enable_registration_bonus_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>20</td>
                                <td>
                                    {{ trans('admin/pages/users.registration_bonus_amount') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.registration_bonus_amount_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>21</td>
                                <td>
                                    {{ trans('admin/pages/users.avatar_settings') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.avatar_settings_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>22</td>
                                <td>
                                    {{ trans('admin/pages/users.cover_img') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.cover_img_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>23</td>
                                <td>
                                    {{ trans('admin/pages/users.headline') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.headline_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>24</td>
                                <td>
                                    {{ trans('admin/pages/users.about') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.about_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>25</td>
                                <td>
                                    {{ trans('admin/pages/users.address') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.address_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>26</td>
                                <td>
                                    {{ trans('admin/pages/users.level_of_training') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.level_of_training_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>27</td>
                                <td>
                                    {{ trans('admin/pages/users.status') }}
                                    <span class="badge badge-success">{{ trans('admin/pages/users.required') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.status_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>28</td>
                                <td>
                                    {{ trans('admin/pages/users.access_content') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.access_content_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>29</td>
                                <td>
                                    {{ trans('admin/pages/users.enable_ai_content') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.enable_ai_content_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>30</td>
                                <td>
                                    {{ trans('admin/pages/users.language') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.language_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>31</td>
                                <td>
                                    {{ trans('admin/pages/users.currency') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.currency_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>32</td>
                                <td>
                                    {{ trans('admin/pages/users.timezone') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.timezone_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>33</td>
                                <td>
                                    {{ trans('admin/pages/users.newletter') }}
                                    <span class="badge badge-success">{{ trans('admin/pages/users.required') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.newletter_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>34</td>
                                <td>
                                    {{ trans('admin/pages/users.public_message') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.public_message_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>35</td>
                                <td>
                                    {{ trans('admin/pages/users.identity_scan') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.identity_scan_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>36</td>
                                <td>
                                    {{ trans('admin/pages/users.certificate') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.certificate_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>37</td>
                                <td>
                                    {{ trans('admin/pages/users.affiliate') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.affiliate_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>38</td>
                                <td>
                                    {{ trans('admin/pages/users.can_create_store') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.can_create_store_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>39</td>
                                <td>
                                    {{ trans('admin/pages/users.ban') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.ban_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>40</td>
                                <td>
                                    {{ trans('admin/pages/users.bas_start_at') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.bas_start_at_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>41</td>
                                <td>
                                    {{ trans('admin/pages/users.ban_end_at') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.ban_end_at_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>42</td>
                                <td>
                                    {{ trans('admin/pages/users.offline') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.offline_instruction') }}</td>
                            </tr>
                            <tr>
                                <td>43</td>
                                <td>
                                    {{ trans('admin/pages/users.offline_messsage') }}
                                    <span class="badge badge-secondary">{{ trans('admin/pages/users.optional') }}</span>
                                </td>
                                <td>{{ trans('admin/pages/users.offline_messsage_instruction') }}</td>
                            </tr>
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>
</section>
