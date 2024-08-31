<div class="container-fluid">
    <form action="<?= current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
        <div class="row border-bottom bg-white sticky-top" style="overflow-x:auto">
            <ul class="nav nav-pills" style="flex-wrap: nowrap">
                <li class="nav-item">
                    <a href="#pills-setting" data-bs-toggle="pill" id="pills-setting-tab" class="nav-link rounded-0 no-wrap --xhr active">
                        <i class="mdi mdi-cogs"></i>
                        <?= phrase('Configuration'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#pills-membership" data-bs-toggle="pill" id="pills-membership-tab" class="nav-link rounded-0 no-wrap --xhr">
                        <i class="mdi mdi-account-group-outline"></i>
                        <?= phrase('Membership'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#pills-apis" data-bs-toggle="pill" id="pills-apis-tab" class="nav-link rounded-0 no-wrap --xhr">
                        <i class="mdi mdi-code-braces"></i>
                        <?= phrase('APIs'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#pills-oauth" data-bs-toggle="pill" id="pills-oauth-tab" class="nav-link rounded-0 no-wrap --xhr">
                        <i class="mdi mdi-shield-lock-outline"></i>
                        <?= phrase('OAuth'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#pills-notifier" data-bs-toggle="pill" id="pills-notifier-tab" class="nav-link rounded-0 no-wrap --xhr">
                        <i class="mdi mdi-bullhorn"></i>
                        <?= phrase('Notifier'); ?>
                    </a>
                </li>
            </ul>
        </div>
        <div class="py-3">
            <div class="row">
                <div class="col-md-8">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-setting">
                            <h5>
                                <?= phrase('Application Identity'); ?>
                            </h5>

                            <?= form_input($results->field_data->app_name); ?>
                            <?= form_input($results->field_data->app_description); ?>

                            <div class="row align-items-center">
                                <div class="col-6 col-md-3">
                                    <?= form_input($results->field_data->app_logo); ?>
                                </div>
                                <div class="col-6 col-md-3">
                                    <?= form_input($results->field_data->app_icon); ?>
                                </div>
                                <div class="col-12 col-md-6">
                                    <?= form_input($results->field_data->app_language); ?>
                                </div>
                            </div>
                            
                            <hr class="border-secondary" />
                            
                            <h5>
                                <?= phrase('Contact Information'); ?>
                            </h5>
                            
                            <?= form_input($results->field_data->office_name); ?>

                            <div class="row">
                                <div class="col-sm-6">
                                    <?= form_input($results->field_data->office_email); ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= form_input($results->field_data->office_phone); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <?= form_input($results->field_data->office_fax); ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= form_input($results->field_data->whatsapp_number); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <?= form_input($results->field_data->instagram_username); ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= form_input($results->field_data->twitter_username); ?>
                                </div>
                            </div>
                            
                            <?= form_input($results->field_data->office_address); ?>
                            
                            <?= form_input($results->field_data->office_map); ?>
                        </div>
                        <div class="tab-pane fade" id="pills-membership">
                            <h5>
                                <?= phrase('Membership'); ?>
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->field_data->frontend_registration); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= form_input($results->field_data->default_membership_group); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->field_data->auto_active_registration); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= form_input($results->field_data->username_change); ?>
                                </div>
                            </div>
                            <hr class="border-secondary" />
                            <div class="row">
                                <div class="col-sm-3">
                                    <?= form_input($results->field_data->login_attempt); ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= form_input($results->field_data->blocking_time); ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= form_input($results->field_data->one_device_login); ?>
                                </div>
                            </div>
                            <hr class="border-secondary" />
                            <div class="row">
                                <div class="col-sm-3">
                                    <?= form_input($results->field_data->account_age_restriction); ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= form_input($results->field_data->spam_timer); ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-apis">
                            <h5>
                                <?= phrase('APIs'); ?>
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->field_data->openlayers_search_provider); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= form_input($results->field_data->openlayers_search_key); ?>
                                </div>
                            </div>

                            <?= form_input($results->field_data->default_map_tile); ?>

                            <hr class="border-secondary" />

                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->field_data->google_analytics_key); ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-oauth">
                            <h5>
                                <?= phrase('Third Party Authentication'); ?>
                            </h5>
                            <div class="row">
                                <div class="col-md-5">
                                        <?= form_input($results->field_data->facebook_app_id); ?>
                                </div>
                                <div class="col-md-7">
                                    <?= form_input($results->field_data->facebook_app_secret); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <?= form_input($results->field_data->google_client_id); ?>
                                </div>
                                <div class="col-md-7">
                                    <?= form_input($results->field_data->google_client_secret); ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-notifier">
                            <h5>
                                <?= phrase('Application Notifier'); ?>
                            </h5>
                            <div class="row">
                                <div class="col-sm-6">
                                    <?= form_input($results->field_data->action_sound); ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= form_input($results->field_data->update_check); ?>
                                </div>
                            </div>
                            <hr class="border-secondary" />
                            <h5>
                                <?= phrase('Email Notifier'); ?>
                            </h5>
                            <div class="row">
                                <div class="col-sm-8">
                                    <?= form_input($results->field_data->smtp_hostname); ?>
                                </div>
                                <div class="col-sm-4">
                                    <?= form_input($results->field_data->smtp_port); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <?= form_input($results->field_data->smtp_username); ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= form_input($results->field_data->smtp_password); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div role="validation-callback"></div>
            <div class="opt-btn-overlap-fix"></div>
            <div class="row opt-btn">
                <div class="col-md-8">
                    <a href="<?= current_page('../'); ?>" class="btn btn-link --xhr">
                        <i class="mdi mdi-arrow-left"></i>
                        <?= phrase('Administrative'); ?>
                    </a>
                    <button type="submit" class="btn btn-primary float-end">
                        <i class="mdi mdi-check"></i>
                        <?= phrase('Update'); ?>
                        <em class="text-sm">(ctrl+s)</em>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
