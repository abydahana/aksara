<div class="jumbotron jumbotron-fluid bg-light mb-0">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2 col-md-1 offset-md-1 text-sm-center">
				<img src="<?php echo get_image('users', $userdata->photo, 'icon'); ?>" class="img-fluid rounded" alt="..." />
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
						<a class="nav-link no-wrap --xhr" href="<?php echo base_url('user/' . $userdata->username . '/about'); ?>">
							<i class="mdi mdi-information-outline"></i>
							<?php echo phrase('about'); ?>
						</a>
					</li>
					<li class="nav-item text-center">
						<a class="nav-link active no-wrap --xhr" href="<?php echo base_url('user/' . $userdata->username . '/portfolio'); ?>">
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
		<div class="col-md-10 offset-md-1">
			placeholder
			
			<?php echo $this->template->pagination($pagination); ?>
		</div>
	</div>
</div>