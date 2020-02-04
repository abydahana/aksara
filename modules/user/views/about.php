<div class="jumbotron jumbotron-fluid bg-light mb-0">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2 col-md-1 offset-md-1 text-sm-center">
				<img src="<?php echo get_image('users', $userdata->photo, 'thumb'); ?>" class="img-fluid rounded" alt="..." />
			</div>
			<div class="col-sm-10 col-md-8 text-sm-center">
				<h4 class="mb-0">
					<?php echo $userdata->first_name . ' ' . $userdata->last_name; ?>
				</h4>
				<p>
					<span class="badge badge-secondary">
						<?php echo $userdata->group_name; ?>
					</span>
				</p>
			</div>
		</div>
	</div>
</div>
<div class="container-fluid sticky-top bg-white border-bottom pt-2" style="top:56px;z-index:1021">
	<div class="">
		<div class="row">
			<div class="col-md-8 offset-md-1 mb-2">
				<ul class="nav nav-pills">
					<li class="nav-item text-center">
						<a class="nav-link no-wrap --xhr" href="<?php echo base_url('user/' . $userdata->username); ?>">
							<i class="mdi mdi-account"></i>
							<?php echo phrase('profile'); ?>
						</a>
					</li>
					<li class="nav-item text-center">
						<a class="nav-link active no-wrap --xhr" href="<?php echo base_url('user/' . $userdata->username . '/about'); ?>">
							<i class="mdi mdi-information-outline"></i>
							<?php echo phrase('about'); ?>
						</a>
					</li>
					<li class="nav-item text-center">
						<a class="nav-link no-wrap --xhr" href="<?php echo base_url('user/' . $userdata->username . '/portfolio'); ?>">
							<i class="mdi mdi-briefcase-account-outline"></i>
							<?php echo phrase('portfolio'); ?>
						</a>
					</li>
					<?php if($userdata->username == get_userdata('username')) { ?>
					<li class="nav-item text-center">
						<a class="nav-link no-wrap --xhr" href="<?php echo base_url('user/account'); ?>">
							<i class="mdi mdi-account-edit"></i>
							<?php echo phrase('account'); ?>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
			<div class="col-md-2 mb-2">
				<button type="button" class="btn btn-outline-primary btn-block">
					<i class="mdi mdi-download"></i>
					<?php echo phrase('download_cv'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<div class="container-fluid pt-3">
	<div class="row">
		<div class="col-md-3 offset-md-1">
			<div class="sticky-top mb-5" style="top:130px">
				<div class="nav flex-column nav-pills nav-pills-light" id="v-pills-tab" role="tablist" aria-orientation="vertical">
					<a href="#v-pills-general" class="nav-link active" data-toggle="pill" aria-selected="true">
						<i class="mdi mdi-information-outline"></i>
						<?php echo phrase('general'); ?>
					</a>
					<a href="#v-pills-contact" class="nav-link" data-toggle="pill">
						<i class="mdi mdi-phone"></i>
						<?php echo phrase('contacts'); ?>
					</a>
					<a href="#v-pills-education" class="nav-link" data-toggle="pill">
						<i class="mdi mdi-school"></i>
						<?php echo phrase('educations'); ?>
					</a>
					<a href="#v-pills-workplace" class="nav-link" data-toggle="pill">
						<i class="mdi mdi-city"></i>
						<?php echo phrase('workplaces'); ?>
					</a>
					<a href="#v-pills-skill" class="nav-link" data-toggle="pill">
						<i class="mdi mdi-account-tie"></i>
						<?php echo phrase('professional_skills'); ?>
					</a>
				</div>
			</div>
		</div>
		<div class="col-md-7">
			<div class="tab-content">
				<div class="tab-pane fade show active pb-3" id="v-pills-general">
					<h6>
						<?php echo phrase('general_information'); ?>
					</h6>
					<hr />
					<div class="form-group">
						<label class="text-muted d-block mb-0">
							<?php echo phrase('full_name'); ?>
						</label>
						<p>
							<?php echo $userdata->first_name . ' ' . $userdata->last_name; ?>
						</p>
					</div>
					<div class="form-group">
						<label class="text-muted d-block mb-0">
							<?php echo phrase('mention'); ?>
						</label>
						<p>
							@<?php echo $userdata->username; ?>
						</p>
					</div>
					<div class="form-group">
						<label class="text-muted d-block mb-0">
							<?php echo phrase('bio'); ?>
						</label>
						<p>
							<?php echo $userdata->bio; ?>
						</p>
					</div>
					<div class="form-group">
						<label class="text-muted d-block mb-0">
							<?php echo phrase('email'); ?>
						</label>
						<p>
							<?php echo $userdata->email; ?>
						</p>
					</div>
				</div>
				<div class="tab-pane fade pb-3" id="v-pills-contact">
					<h6>
						<?php echo phrase('contacts'); ?>
					</h6>
					<hr />
					
					<?php
						if($contacts)
						{
							foreach($contacts as $key => $val)
							{
								echo '
									<div class="form-group" id="contact_' . $val->id . '">
										' . ($userdata->username == get_userdata('username') ? '<div class="btn-group btn-group-sm float-right">
											<button type="button" data-href="' . base_url('user/account/contact', array('id' => $val->id)) . '" class="btn btn-outline-secondary btn-sm pt-0 pb-0 btn-edit" data-toggle="tooltip" title="' . phrase('update') . '" data-parent="#v-pills-contact" data-content-type="' . $val->type . '" data-content-value="' . $val->value . '">
												<i class="mdi mdi-square-edit-outline"></i>
											</button>
											<a href="' . base_url('user/account/contact', array('id' => $val->id, 'delete' => true)) . '" class="btn btn-outline-secondary btn-sm pt-0 pb-0 --open-delete-confirm" data-toggle="tooltip" title="' . phrase('remove') . '">
												<i class="mdi mdi-trash-can-outline"></i>
											</a>
										</div>' : null) . '
										<label class="text-muted d-block mb-0">
											' . (1 == $val->type ? phrase('mobile') : (2 == $val->type ? phrase('email') : (3 == $val->type ? phrase('website') : (4 == $val->type ? phrase('messenger') : phrase('address'))))) . '
										</label>
										<p class="text-word-wrap">
											' . (in_array($val->type, array(3, 4)) ? auto_link($val->value, null, true) : $val->value) . '
										</p>
									</div>
								';
							}
						}
					?>
					
					<?php if($userdata->username == get_userdata('username')) { ?>
					<form action="<?php echo base_url('user/account/contact'); ?>" data-original-action="<?php echo base_url('user/account/contact'); ?>" method="POST" class="--live-submit">
						<div class="form-input d-none">
							<?php echo ($contacts ? '<hr />' : null); ?>
							<label class="text-muted d-block">
								<?php echo phrase('add_contact'); ?>
							</label>
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<select name="type" class="form-control" id="contact type_input">
											<option value="1" data-icon="mdi mdi-phone">
												<?php echo phrase('mobile'); ?>
											</option>
											<option value="2" data-icon="mdi mdi-at">
												<?php echo phrase('email'); ?>
											</option>
											<option value="3" data-icon="mdi mdi-link-variant">
												<?php echo phrase('website'); ?>
											</option>
											<option value="4" data-icon="mdi mdi-whatsapp">
												<?php echo phrase('messenger'); ?>
											</option>
											<option value="5" data-icon="mdi mdi-map-marker-outline">
												<?php echo phrase('address'); ?>
											</option>
										</select>
										<div class="invalid-feedback d-block"><?php echo phrase('required'); ?></div>
									</div>
								</div>
								<div class="col-sm-8">
									<div class="form-group" style="position:relative">
										<input type="text" name="value" class="form-control" id="value_input" placeholder="Type the contact detail" />
										<div class="invalid-feedback d-block"><?php echo phrase('required'); ?></div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-6 col-sm-4 col-md-4">
								<button type="button" class="btn btn-light btn-block btn-add">
									<i class="btn-icon mdi mdi-plus"></i>
									<span class="btn-label">
										<?php echo phrase('add'); ?>
									</span>
								</button>
							</div>
							<div class="col-6 col-sm-8 col-md-4">
								<button type="submit" class="btn btn-primary btn-block d-none" disabled>
									<i class="mdi mdi-check"></i>
									<?php echo phrase('submit'); ?>
								</button>
							</div>
						</div>
					</form>
					<?php } ?>
				</div>
				<div class="tab-pane fade pb-3" id="v-pills-education">
					<h6>
						<?php echo phrase('educations'); ?>
					</h6>
					<hr />
					
					<?php
						if($educations)
						{
							foreach($educations as $key => $val)
							{
								echo '
									<div class="form-group" id="education_' . $val->id . '">
										<div class="row">
											<div class="col-2 col-sm-1 pt-2">
												<i class="mdi ' . (1 == $val->type ? 'mdi-chair-school' : (2 == $val->type ? 'mdi-school' : 'mdi-account-tie')) . ' mdi-2x text-muted"></i>
											</div>
											<div class="col-10 col-sm-11">
												' . ($userdata->username == get_userdata('username') ? '<div class="btn-group btn-group-sm float-right">
													<button type="button" data-href="' . base_url('user/account/education', array('id' => $val->id)) . '" class="btn btn-outline-secondary btn-sm pt-0 pb-0 btn-edit" data-toggle="tooltip" title="' . phrase('update') . '" data-parent="#v-pills-education" data-content-type="' . $val->type . '" data-content-value="' . $val->value . '" data-content-start="' . $val->start . '" data-content-end="' . $val->end . '" data-content-present="' . $val->present . '" data-content-description="' . htmlspecialchars($val->description) . '">
														<i class="mdi mdi-square-edit-outline"></i>
													</button>
													<a href="' . base_url('user/account/education', array('id' => $val->id, 'delete' => true)) . '" class="btn btn-outline-secondary btn-sm pt-0 pb-0 --open-delete-confirm" data-toggle="tooltip" title="' . phrase('remove') . '">
														<i class="mdi mdi-trash-can-outline"></i>
													</a>
												</div>' : null) . '
												<label class="d-block mb-0">
													' . phrase('went_to') . ' <b>' . $val->value . '</b>
												</label>
												<label class="text-muted d-block mb-0">
													' . $val->start . ' ' . phrase('up_to') . ' ' . ($val->present ? phrase('present') : $val->end) . '
												</label>
												<p class="text-sm text-word-wrap">
													' . $val->description . '
												</p>
											</div>
										</div>
									</div>
								';
							}
						}
					?>
					
					
					<?php if($userdata->username == get_userdata('username')) { ?>
					<form action="<?php echo base_url('user/account/education'); ?>" data-original-action="<?php echo base_url('user/account/education'); ?>" method="POST" class="--live-submit">
						<div class="form-input d-none">
							<?php echo ($educations ? '<hr />' : null); ?>
							<label class="text-muted d-block">
								<?php echo phrase('add_graduation'); ?>
							</label>
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<select name="type" class="form-control" id="education type_input">
											<option value="1" data-icon="mdi mdi-chair-school">
												<?php echo phrase('high_school'); ?>
											</option>
											<option value="2" data-icon="mdi mdi-school">
												<?php echo phrase('college'); ?>
											</option>
											<option value="3" data-icon="mdi mdi-account-tie">
												<?php echo phrase('professional'); ?>
											</option>
										</select>
										<div class="invalid-feedback d-block"><?php echo phrase('required'); ?></div>
									</div>
								</div>
								<div class="col-sm-8">
									<div class="form-group" style="position:relative">
										<input type="text" name="value" class="form-control education-input" id="value_input" placeholder="Type the education detail" />
										<div class="invalid-feedback d-block"><?php echo phrase('required'); ?></div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-6 col-sm-4">
									<div class="form-group">
										<input type="text" name="start" class="form-control" role="datepicker" placeholder="<?php echo phrase('start_date'); ?>" id="start_input" />
										<div class="invalid-feedback d-block"><?php echo phrase('required'); ?></div>
									</div>
								</div>
								<div class="col-6 col-sm-4">
									<div class="form-group">
										<input type="text" name="end" class="form-control" role="datepicker" placeholder="<?php echo phrase('end_date'); ?>" id="end_input" />
										<div class="invalid-feedback d-block"><?php echo phrase('required'); ?></div>
									</div>
								</div>
								<div class="col-12 col-sm-4">
									<div class="form-group">
										<label>
											<input type="checkbox" name="present" value="1" onchange="jExec((this.checked?$(this).closest('form').find('#end_input').next('.invalid-feedback').removeClass('d-block').addClass('d-none'):$(this).closest('form').find('#end_input').next('.invalid-feedback').removeClass('d-none').addClass('d-block')))" />
											<?php echo phrase('present'); ?>
										</label>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12 col-md-8">
									<div class="form-group">
										<textarea name="description" class="form-control" placeholder="<?php echo phrase('description'); ?>" id="description_input" rows="1"></textarea>
										<div class="invalid-feedback d-block"><?php echo phrase('required'); ?></div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-6 col-sm-4 col-md-4">
								<button type="button" class="btn btn-light btn-block btn-add">
									<i class="btn-icon mdi mdi-plus"></i>
									<span class="btn-label">
										<?php echo phrase('add'); ?>
									</span>
								</button>
							</div>
							<div class="col-6 col-sm-8 col-md-4">
								<button type="submit" class="btn btn-primary btn-block d-none" disabled>
									<i class="mdi mdi-check"></i>
									<?php echo phrase('submit'); ?>
								</button>
							</div>
						</div>
					</form>
					<?php } ?>
				</div>
				<div class="tab-pane fade pb-3" id="v-pills-workplace">
					<h6>
						<?php echo phrase('workplaces'); ?>
					</h6>
					<hr />
					
					<?php
						if($workplaces)
						{
							foreach($workplaces as $key => $val)
							{
								echo '
									<div class="form-group" id="workplace_' . $val->id . '">
										<div class="row">
											<div class="col-2 col-sm-1 pt-2">
												<i class="mdi mdi-account-multiple-outline mdi-2x text-muted"></i>
											</div>
											<div class="col-10 col-sm-11">
												' . ($userdata->username == get_userdata('username') ? '<div class="btn-group btn-group-sm float-right">
													<button type="button" data-href="' . base_url('user/account/workplace', array('id' => $val->id)) . '" class="btn btn-outline-secondary btn-sm pt-0 pb-0 btn-edit" data-toggle="tooltip" title="' . phrase('update') . '" data-parent="#v-pills-workplace" data-content-position="' . $val->position . '" data-content-value="' . $val->value . '" data-content-start="' . $val->start . '" data-content-end="' . $val->end . '" data-content-present="' . $val->present . '" data-content-description="' . htmlspecialchars($val->description) . '">
														<i class="mdi mdi-square-edit-outline"></i>
													</button>
													<a href="' . base_url('user/account/workplace', array('id' => $val->id, 'delete' => true)) . '" class="btn btn-outline-secondary btn-sm pt-0 pb-0 --open-delete-confirm" data-toggle="tooltip" title="' . phrase('remove') . '">
														<i class="mdi mdi-trash-can-outline"></i>
													</a>
												</div>' : null) . '
												<label class="d-block mb-0">
													' . $val->position . ' ' . phrase('at') . ' <b>' . $val->value . '</b>
												</label>
												<label class="text-muted d-block mb-0">
													' . $val->start . ' ' . phrase('up_to') . ' ' . ($val->present ? phrase('present') : $val->end) . '
												</label>
												<p class="text-sm text-word-wrap">
													' . $val->description . '
												</p>
											</div>
										</div>
									</div>
								';
							}
						}
					?>
					
					<?php if($userdata->username == get_userdata('username')) { ?>
					<form action="<?php echo base_url('user/account/workplace'); ?>" data-original-action="<?php echo base_url('user/account/workplace'); ?>" method="POST" class="--live-submit">
						<div class="form-input d-none">
							<?php echo ($workplaces ? '<hr />' : null); ?>
							<label class="text-muted d-block">
								<?php echo phrase('add_workplace'); ?>
							</label>
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<input type="text" name="position" class="form-control" placeholder="<?php echo phrase('position'); ?>" />
										<div class="invalid-feedback d-block"><?php echo phrase('required'); ?></div>
									</div>
								</div>
								<div class="col-sm-8">
									<div class="form-group" style="position:relative">
										<input type="text" name="value" class="form-control working-experience-input" id="value_input" placeholder="Company" />
										<div class="invalid-feedback d-block"><?php echo phrase('required'); ?></div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-6 col-sm-4">
									<div class="form-group">
										<input type="text" name="start" class="form-control" role="datepicker" placeholder="<?php echo phrase('start_date'); ?>" id="start_input" />
										<div class="invalid-feedback d-block"><?php echo phrase('required'); ?></div>
									</div>
								</div>
								<div class="col-6 col-sm-4">
									<div class="form-group">
										<input type="text" name="end" class="form-control" role="datepicker" placeholder="<?php echo phrase('end_date'); ?>" id="end_input" />
										<div class="invalid-feedback d-block"><?php echo phrase('required'); ?></div>
									</div>
								</div>
								<div class="col-12 col-sm-4">
									<div class="form-group">
										<label>
											<input type="checkbox" name="present" value="1" onchange="jExec((this.checked?$(this).closest('form').find('#end_input').next('.invalid-feedback').removeClass('d-block').addClass('d-none'):$(this).closest('form').find('#end_input').next('.invalid-feedback').removeClass('d-none').addClass('d-block')))" />
											<?php echo phrase('currently_working'); ?>
										</label>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12 col-md-8">
									<div class="form-group">
										<textarea name="description" class="form-control" placeholder="<?php echo phrase('description'); ?>" id="description_input" rows="1"></textarea>
										<div class="invalid-feedback d-block"><?php echo phrase('required'); ?></div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-6 col-sm-4 col-md-4">
								<button type="button" class="btn btn-light btn-block btn-add">
									<i class="btn-icon mdi mdi-plus"></i>
									<span class="btn-label">
										<?php echo phrase('add'); ?>
									</span>
								</button>
							</div>
							<div class="col-6 col-sm-8 col-md-4">
								<button type="submit" class="btn btn-primary btn-block d-none" disabled>
									<i class="mdi mdi-check"></i>
									<?php echo phrase('submit'); ?>
								</button>
							</div>
						</div>
					</form>
					<?php } ?>
				</div>
				<div class="tab-pane fade pb-3" id="v-pills-skill">
					<h6>
						<?php echo phrase('professional_skills'); ?>
					</h6>
					<hr />
					
					<?php
						if($skills)
						{
							foreach($skills as $key => $val)
							{
								echo '
									<div class="form-group" id="skill_' . $val->id . '">
										<div class="row">
											<div class="col-2 col-sm-1 pt-2">
												<i class="mdi mdi-progress-check mdi-2x text-muted"></i>
											</div>
											<div class="col-10 col-sm-11">
												' . ($userdata->username == get_userdata('username') ? '<div class="btn-group btn-group-sm float-right">
													<button type="button" data-href="' . base_url('user/account/skill', array('id' => $val->id)) . '" class="btn btn-outline-secondary btn-sm pt-0 pb-0 btn-edit" data-toggle="tooltip" title="' . phrase('update') . '" data-parent="#v-pills-skill" data-content-value="' . $val->value . '" data-content-description="' . htmlspecialchars($val->description) . '" data-content-level-achieved="' . $val->level_achieved . '">
														<i class="mdi mdi-square-edit-outline"></i>
													</button>
													<a href="' . base_url('user/account/skill', array('id' => $val->id, 'delete' => true)) . '" class="btn btn-outline-secondary btn-sm pt-0 pb-0 --open-delete-confirm" data-toggle="tooltip" title="' . phrase('remove') . '">
														<i class="mdi mdi-trash-can-outline"></i>
													</a>
												</div>' : null) . '
												<label class="d-block mb-0">
													' . phrase('achieved') . ' <b>' . $val->value . '</b>
												</label>
												<label class="text-muted d-block mb-0">
													' . (1 == $val->level_achieved ? phrase('beginner') : (2 == $val->level_achieved ? phrase('intermediate') : (3 == $val->level_achieved ? phrase('advanced') : phrase('expert')))) . '
												</label>
												<p class="text-sm text-word-wrap">
													' . $val->description . '
												</p>
											</div>
										</div>
									</div>
								';
							}
						}
					?>
					
					<?php if($userdata->username == get_userdata('username')) { ?>
					<form action="<?php echo base_url('user/account/skill'); ?>" data-original-action="<?php echo base_url('user/account/skill'); ?>" method="POST" class="--live-submit">
						<div class="form-input d-none">
							<?php echo ($skills ? '<hr />' : null); ?>
							<label class="text-muted d-block">
								<?php echo phrase('add_skills'); ?>
							</label>
							<div class="row">
								<div class="col-md-8">
									<div class="form-group" style="position:relative">
										<input type="text" name="value" class="form-control" id="value_input" placeholder="Type the skill detail" />
										<div class="invalid-feedback d-block"><?php echo phrase('required'); ?></div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-8">
									<div class="form-group" style="position:relative">
										<textarea name="description" class="form-control" placeholder="<?php echo phrase('description'); ?>" id="description_input" rows="1"></textarea>
										<div class="invalid-feedback d-block"><?php echo phrase('required'); ?></div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="text-muted d-block">
									<?php echo phrase('level_achieved'); ?>
								</label>
								<label class="d-block">
									<input type="radio" name="level_achieved" value="1" />
									<?php echo phrase('beginner'); ?>
								</label>
								<label class="d-block">
									<input type="radio" name="level_achieved" value="2" />
									<?php echo phrase('intermediate'); ?>
								</label>
								<label class="d-block">
									<input type="radio" name="level_achieved" value="3" />
									<?php echo phrase('advanced'); ?>
								</label>
								<label class="d-block">
									<input type="radio" name="level_achieved" value="4" />
									<?php echo phrase('expert'); ?>
								</label>
							</div>
						</div>
						<div class="row">
							<div class="col-6 col-sm-4 col-md-4">
								<button type="button" class="btn btn-light btn-block btn-add">
									<i class="btn-icon mdi mdi-plus"></i>
									<span class="btn-label">
										<?php echo phrase('add'); ?>
									</span>
								</button>
							</div>
							<div class="col-6 col-sm-8 col-md-4">
								<button type="submit" class="btn btn-primary btn-block d-none" disabled>
									<i class="mdi mdi-check"></i>
									<?php echo phrase('submit'); ?>
								</button>
							</div>
						</div>
					</form>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>