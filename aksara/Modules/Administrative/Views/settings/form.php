<?php

/**
 * @var mixed $results
 */
?>
<div class="container-fluid">
    <form action="<?= current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
        <div class="sticky-top bg-body overflow-x-auto py-1 px-3 mx--3 border-bottom">
            <ul class="nav nav-pills nav-pills-dark flex-nowrap">
                <li class="nav-item">
                    <a href="#pills-setting" data-bs-toggle="pill" class="nav-link rounded-pill active no-wrap --xhr">
                        <i class="mdi mdi-cogs"></i> <?= phrase('Configuration'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#pills-membership" data-bs-toggle="pill" class="nav-link rounded-pill no-wrap --xhr">
                        <i class="mdi mdi-account-group-outline"></i> <?= phrase('Membership'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#pills-storage" data-bs-toggle="pill" class="nav-link rounded-pill no-wrap --xhr">
                        <i class="mdi mdi-cloud-outline"></i> <?= phrase('Storage'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#pills-apis" data-bs-toggle="pill" class="nav-link rounded-pill no-wrap --xhr">
                        <i class="mdi mdi-code-braces"></i> <?= phrase('API'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#pills-ai" data-bs-toggle="pill" class="nav-link rounded-pill no-wrap --xhr">
                        <i class="mdi mdi-creation"></i> <?= phrase('AI'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#pills-oauth" data-bs-toggle="pill" class="nav-link rounded-pill no-wrap --xhr">
                        <i class="mdi mdi-shield-lock-outline"></i> <?= phrase('OAuth'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#pills-notifier" data-bs-toggle="pill" class="nav-link rounded-pill no-wrap --xhr">
                        <i class="mdi mdi-bullhorn"></i> <?= phrase('Notifier'); ?>
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

                            <?= form_input($results->fieldData->app_name); ?>
                            <?= form_input($results->fieldData->app_description); ?>

                            <div class="row align-items-center">
                                <div class="col-6 col-sm-4">
                                    <?= form_input($results->fieldData->app_logo); ?>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <?= form_input($results->fieldData->app_icon); ?>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <?= form_input($results->fieldData->reports_icon); ?>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->app_language); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->force_system_language); ?>
                                </div>
                            </div>

                            <hr class="border-secondary" />

                            <h5>
                                <?= phrase('Contact Information'); ?>
                            </h5>

                            <?= form_input($results->fieldData->office_name); ?>

                            <div class="row">
                                <div class="col-sm-6">
                                    <?= form_input($results->fieldData->office_email); ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= form_input($results->fieldData->office_phone); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <?= form_input($results->fieldData->office_fax); ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= form_input($results->fieldData->whatsapp_number); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <?= form_input($results->fieldData->instagram_username); ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= form_input($results->fieldData->twitter_username); ?>
                                </div>
                            </div>

                            <?= form_input($results->fieldData->office_address); ?>

                            <?= form_input($results->fieldData->office_map); ?>
                        </div>
                        <div class="tab-pane fade" id="pills-membership">
                            <h5>
                                <?= phrase('Membership'); ?>
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->frontend_registration); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->default_membership_group); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->auto_active_registration); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->username_change); ?>
                                </div>
                            </div>
                            <hr class="border-secondary" />
                            <div class="row">
                                <div class="col-sm-3">
                                    <?= form_input($results->fieldData->login_attempt); ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= form_input($results->fieldData->blocking_time); ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= form_input($results->fieldData->one_device_login); ?>
                                </div>
                            </div>
                            <hr class="border-secondary" />
                            <div class="row">
                                <div class="col-sm-3">
                                    <?= form_input($results->fieldData->account_age_restriction); ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= form_input($results->fieldData->spam_timer); ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-storage">
                            <h5>
                                <?= phrase('Storage'); ?>
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->provider); ?>
                                </div>
                            </div>
                            <?= form_input($results->fieldData->endpoint); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->region); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->bucket); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->access_key); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->secret_key); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->sync_existing_uploads); ?>
                                </div>
                            </div>
                            <div class="alert alert-warning callout">
                                <?= phrase('If you check Sync Existing Uploads, existing files will be transferred and overwritten when needed.'); ?>
                                <div class="text-danger fw-bold">
                                    <?= phrase('Saving may take longer because files will be transferred during this process.'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-apis">
                            <h5>
                                <?= phrase('APIs'); ?>
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->openlayers_search_provider); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->openlayers_search_key); ?>
                                </div>
                            </div>

                            <?= form_input($results->fieldData->default_map_tile); ?>

                            <hr class="border-secondary" />

                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->google_analytics_key); ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-ai">
                            <h5>
                                <?= phrase('Artificial Intelligence'); ?>
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->ai_enabled); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->ai_provider); ?>
                                </div>
                            </div>

                            <?= form_input($results->fieldData->ai_base_url); ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->ai_api_key); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->ai_model); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->ai_image_enabled); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->ai_image_model); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->ai_temperature); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= form_input($results->fieldData->ai_max_tokens); ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-oauth">
                            <h5>
                                <?= phrase('Third Party Authentication'); ?>
                            </h5>
                            <div class="row">
                                <div class="col-md-5">
                                    <?= form_input($results->fieldData->facebook_app_id); ?>
                                </div>
                                <div class="col-md-7">
                                    <?= form_input($results->fieldData->facebook_app_secret); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <?= form_input($results->fieldData->google_client_id); ?>
                                </div>
                                <div class="col-md-7">
                                    <?= form_input($results->fieldData->google_client_secret); ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-notifier">
                            <h5>
                                <?= phrase('Application Notifier'); ?>
                            </h5>
                            <div class="row">
                                <div class="col-sm-6">
                                    <?= form_input($results->fieldData->action_sound); ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= form_input($results->fieldData->update_check); ?>
                                </div>
                            </div>
                            <hr class="border-secondary" />
                            <h5>
                                <?= phrase('Email Notifier'); ?>
                            </h5>
                            <div class="row">
                                <div class="col-sm-8">
                                    <?= form_input($results->fieldData->smtp_hostname); ?>
                                </div>
                                <div class="col-sm-4">
                                    <?= form_input($results->fieldData->smtp_port); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <?= form_input($results->fieldData->smtp_username); ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= form_input($results->fieldData->smtp_password); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div data-role="validation-callback"></div>
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

<script>
(function() {
    window.aksaraStorageProviderChanged = function(provider) {
        if (! provider) {
            return;
        }

        const scope = provider.closest('form') || document;
        const formData = new FormData();

        formData.append('fetch', 'storageProvider');
        formData.append('provider', provider.value);

        fetch(scope.action || window.location.href, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then((response) => response.json())
        .then((response) => {
            if (! response || 200 !== response.status || ! response.results) {
                return;
            }

            Object.keys(response.results).forEach((key) => {
                const field = scope.querySelector('[name="' + key + '"][type="checkbox"], [name="' + key + '"]');

                if (! field || field === provider) {
                    return;
                }

                if ('checkbox' === field.type) {
                    field.checked = '1' == response.results[key] || 1 === response.results[key] || true === response.results[key];
                } else {
                    field.value = response.results[key] || '';

                    if (window.jQuery) {
                        window.jQuery(field).trigger('change').trigger('change.select2');
                    }
                }
            });
        });
    };

    document.addEventListener('change', function(event) {
        const provider = event.target.closest('[name="provider"]');

        if (! provider) {
            return;
        }

        window.aksaraStorageProviderChanged(provider);
    });
})();
</script>
