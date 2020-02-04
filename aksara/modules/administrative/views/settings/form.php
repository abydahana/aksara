<div class="container-fluid pt-3 pb-3">
	<div class="row">
		<div class="col-md-8">
			<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
				<nav class="nav nav-pills nav-fill btn-group btn-group-sm sticky-top bg-white no-wrap" style="top:105px">
					<a href="#pills-setting" data-toggle="pill" id="pills-setting-tab" class="nav-item nav-link btn btn-outline-primary active" role="tab" aria-controls="pills-setting" aria-selected="true">
						<i class="mdi mdi-cogs"></i>
						<?php echo phrase('configuration'); ?>
					</a>
					<a href="#pills-membership" data-toggle="pill" id="pills-membership-tab" class="nav-item nav-link btn btn-outline-primary" role="tab" aria-controls="pills-membership" aria-selected="false">
						<i class="mdi mdi-account-group-outline"></i>
						<?php echo phrase('membership'); ?>
					</a>
					<a href="#pills-apis" data-toggle="pill" id="pills-apis-tab" class="nav-item nav-link btn btn-outline-primary" role="tab" aria-controls="pills-apis" aria-selected="false">
						<i class="mdi mdi-code-braces"></i>
						<?php echo phrase('apis'); ?>
					</a>
					<a href="#pills-simda" data-toggle="pill" id="pills-simda-tab" class="nav-item nav-link btn btn-outline-primary" role="tab" aria-controls="pills-simda" aria-selected="false">
						<i class="mdi mdi-alpha-s-circle-outline"></i>
						SIMDA
					</a>
				</nav>
				<br />
				<div class="tab-content" id="pills-tabContent">
					<div class="tab-pane fade show active" id="pills-setting" role="tabpanel" aria-labelledby="pills-setting-tab">
						<div class="form-group">
							<label class="text-muted d-block" for="app_name_input">
								<?php echo $results->form_data->app_name->label; ?>
								<?php echo ($results->form_data->app_name->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
							</label>
							<?php echo $results->form_data->app_name->content; ?>
						</div>
						<div class="form-group">
							<label class="text-muted d-block" for="app_description_input">
								<?php echo $results->form_data->app_description->label; ?>
								<?php echo ($results->form_data->app_description->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
							</label>
							<?php echo $results->form_data->app_description->content; ?>
						</div>
						<div class="row form-group">
							<div class="col-sm-6">
								<label class="text-muted d-block" for="office_email_input">
									<?php echo $results->form_data->office_email->label; ?>
									<?php echo ($results->form_data->office_email->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
								</label>
								<?php echo $results->form_data->office_email->content; ?>
							</div>
							<div class="col-sm-6">
								<label class="text-muted d-block" for="office_phone_input">
									<?php echo $results->form_data->office_phone->label; ?>
									<?php echo ($results->form_data->office_phone->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
								</label>
								<?php echo $results->form_data->office_phone->content; ?>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-6">
								<label class="text-muted d-block" for="office_fax_input">
									<?php echo $results->form_data->office_fax->label; ?>
									<?php echo ($results->form_data->office_fax->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
								</label>
								<?php echo $results->form_data->office_fax->content; ?>
							</div>
							<div class="col-sm-6">
								<label class="text-muted d-block" for="whatsapp_number_input">
									<?php echo $results->form_data->whatsapp_number->label; ?>
									<?php echo ($results->form_data->whatsapp_number->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
								</label>
								<?php echo $results->form_data->whatsapp_number->content; ?>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-6">
								<label class="text-muted d-block" for="instagram_username_input">
									<?php echo $results->form_data->instagram_username->label; ?>
									<?php echo ($results->form_data->instagram_username->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
								</label>
								<?php echo $results->form_data->instagram_username->content; ?>
							</div>
							<div class="col-sm-6">
								<label class="text-muted d-block" for="twitter_username_input">
									<?php echo $results->form_data->twitter_username->label; ?>
									<?php echo ($results->form_data->twitter_username->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
								</label>
								<?php echo $results->form_data->twitter_username->content; ?>
							</div>
						</div>
						<div class="form-group">
							<label class="text-muted d-block" for="office_address_input">
								<?php echo $results->form_data->office_address->label; ?>
								<?php echo ($results->form_data->office_address->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
							</label>
							<?php echo $results->form_data->office_address->content; ?>
						</div>
						<div class="form-group">
							<label class="text-muted d-block" for="office_map_input">
								<?php echo $results->form_data->office_map->label; ?>
								<?php echo ($results->form_data->office_map->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
							</label>
							<?php echo $results->form_data->office_map->content; ?>
						</div>
						<div class="row">
							<div class="col-6 col-md-3">
								<div class="form-group text-center">
									<label class="text-muted d-block" for="app_logo_input">
										<?php echo $results->form_data->app_logo->label; ?>
									</label>
									<div class="row">
										<div class="col-md-8 offset-md-2">
											<?php echo $results->form_data->app_logo->content; ?>
										</div>
									</div>
								</div>
							</div>
							<div class="col-6 col-md-3">
								<div class="form-group text-center">
									<label class="text-muted d-block" for="app_icon_input">
										<?php echo $results->form_data->app_icon->label; ?>
									</label>
									<div class="row">
										<div class="col-md-8 offset-md-2">
											<?php echo $results->form_data->app_icon->content; ?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label class="text-muted d-block" for="app_language_input">
										<?php echo $results->form_data->app_language->label; ?>
										<?php echo ($results->form_data->app_language->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
									</label>
									<?php echo $results->form_data->app_language->content; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="pills-membership" role="tabpanel" aria-labelledby="pills-membership-tab">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label class="text-muted d-block">
										<?php echo $results->form_data->login_annually->label; ?>
									</label>
									<?php echo $results->form_data->login_annually->content; ?>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label class="text-muted d-block">
										<?php echo $results->form_data->one_device_login->label; ?>
									</label>
									<?php echo $results->form_data->one_device_login->content; ?>
								</div>
							</div>
						</div>
						<hr />
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="text-muted d-block" for="default_membership_group_input">
										<?php echo $results->form_data->enable_frontend_registration->label; ?>
									</label>
									<?php echo $results->form_data->enable_frontend_registration->content; ?>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="text-muted d-block" for="default_membership_group_input">
										<?php echo $results->form_data->default_membership_group->label; ?>
									</label>
									<?php echo $results->form_data->default_membership_group->content; ?>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="text-muted d-block" for="default_membership_group_input">
										<?php echo $results->form_data->auto_active_registration->label; ?>
										<i class="mdi mdi-help-circle-outline" data-toggle="tooltip" title="<?php echo phrase('activate_user_after_registration'); ?>"></i>
									</label>
									<?php echo $results->form_data->auto_active_registration->content; ?>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="text-muted d-block" for="enable_username_changes_input">
										<?php echo $results->form_data->enable_username_changes->label; ?>
									</label>
									<?php echo $results->form_data->enable_username_changes->content; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="pills-apis" role="tabpanel" aria-labelledby="pills-apis-tab">
						<div class="row">
							<div class="col-md-5">
								<div class="form-group">
									<label class="text-muted d-block" for="maps_provider_input">
										<?php echo $results->form_data->maps_provider->label; ?>
										<?php echo ($results->form_data->maps_provider->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
									</label>
									<?php echo $results->form_data->maps_provider->content; ?>
								</div>
							</div>
							<div class="col-md-7">
								<div class="form-group">
									<label class="text-muted d-block" for="google_maps_api_key_input">
										<?php echo $results->form_data->google_maps_api_key->label; ?>
										<?php echo ($results->form_data->google_maps_api_key->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
									</label>
									<?php echo $results->form_data->google_maps_api_key->content; ?>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-5">
								<div class="form-group">
									<label class="text-muted d-block" for="openlayers_search_provider_input">
										<?php echo $results->form_data->openlayers_search_provider->label; ?>
										<?php echo ($results->form_data->openlayers_search_provider->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
									</label>
									<?php echo $results->form_data->openlayers_search_provider->content; ?>
								</div>
							</div>
							<div class="col-md-7">
								<div class="form-group">
									<label class="text-muted d-block" for="openlayers_search_key_input">
										<?php echo $results->form_data->openlayers_search_key->label; ?>
										<?php echo ($results->form_data->openlayers_search_key->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
									</label>
									<?php echo $results->form_data->openlayers_search_key->content; ?>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-5">
								<div class="form-group">
									<label class="text-muted d-block" for="google_analytics_key_input">
										<?php echo $results->form_data->google_analytics_key->label; ?>
										<?php echo ($results->form_data->google_analytics_key->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
									</label>
									<?php echo $results->form_data->google_analytics_key->content; ?>
								</div>
							</div>
							<div class="col-md-7">
								<div class="form-group">
									<label class="text-muted d-block" for="facebook_fanpage_input">
										<?php echo $results->form_data->facebook_fanpage->label; ?>
										<?php echo ($results->form_data->facebook_fanpage->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
									</label>
									<?php echo $results->form_data->facebook_fanpage->content; ?>
								</div>
							</div>
						</div>
						<hr />
						<h5>
							OAUTH
						</h5>
						<div class="row">
							<div class="col-md-5">
								<div class="form-group">
									<label class="text-muted d-block" for="facebook_app_id_input">
										<?php echo $results->form_data->facebook_app_id->label; ?>
										<?php echo ($results->form_data->facebook_app_id->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
									</label>
									<?php echo $results->form_data->facebook_app_id->content; ?>
								</div>
							</div>
							<div class="col-md-7">
								<div class="form-group">
									<label class="text-muted d-block" for="facebook_app_secret_input">
										<?php echo $results->form_data->facebook_app_secret->label; ?>
										<?php echo ($results->form_data->facebook_app_secret->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
									</label>
									<?php echo $results->form_data->facebook_app_secret->content; ?>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-7">
								<div class="form-group">
									<label class="text-muted d-block" for="google_client_id_input">
										<?php echo $results->form_data->google_client_id->label; ?>
										<?php echo ($results->form_data->google_client_id->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
									</label>
									<?php echo $results->form_data->google_client_id->content; ?>
								</div>
							</div>
							<div class="col-md-5">
								<div class="form-group">
									<label class="text-muted d-block" for="google_client_secret_input">
										<?php echo $results->form_data->google_client_secret->label; ?>
										<?php echo ($results->form_data->google_client_secret->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
									</label>
									<?php echo $results->form_data->google_client_secret->content; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="pills-simda" role="tabpanel" aria-labelledby="pills-simda-tab">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="text-muted d-block" for="nama_pemda_input">
										<?php echo $results->form_data->nama_pemda->label; ?>
										<?php echo ($results->form_data->nama_pemda->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
									</label>
									<?php echo $results->form_data->nama_pemda->content; ?>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="text-muted d-block" for="nama_daerah_input">
										<?php echo $results->form_data->nama_daerah->label; ?>
										<?php echo ($results->form_data->nama_daerah->required ? '<span class="float-right text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
									</label>
									<?php echo $results->form_data->nama_daerah->content; ?>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="text-muted d-block">
								<?php echo $results->form_data->versi_simda->label; ?>
							</label>
							<?php echo $results->form_data->versi_simda->content; ?>
						</div>
						<div class="row">
							<div class="col-6 col-md-3">
								<div class="form-group text-center">
									<label class="text-muted d-block" for="reports_logo_input">
										<?php echo $results->form_data->reports_logo->label; ?>
									</label>
									<div class="row">
										<div class="col-md-8 offset-md-2">
											<?php echo $results->form_data->reports_logo->content; ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="--validation-callback mb-0"></div>
					
				</div>
				<div class="opt-btn-overlap-fix"></div><!-- fix the overlap -->
				<div class="opt-btn">
					<input type="hidden" name="token" value="<?php echo $token; ?>" />
					<a href="<?php echo base_url('administrative'); ?>" class="btn btn-light --xhr">
						<i class="mdi mdi-arrow-left"></i>
						&nbsp;
						<?php echo phrase('administrative'); ?>
					</a>
					<button type="submit" class="btn btn-primary float-right">
						<i class="mdi mdi-check"></i>
						<?php echo phrase('update'); ?>
						<em class="text-sm">(ctrl+s)</em>
					</button>
				</div>
			</form>
		</div>
	</div>
</div>