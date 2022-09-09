<div class="container-fluid mt-5">
	<div class="row">
		<div class="col-md-3 offset-md-1 text-sm-center">
			<a href="<?php echo base_url(); ?>" class="--xhr">
				<img src="<?php echo get_image('settings', get_setting('app_logo'), 'icon'); ?>" class="img-fluid mt-3" alt="..." style="opacity:.3" />
			</a>
		</div>
		<div class="col col-md-2">
			<ul class="nav flex-column row">
				<li class="nav-item">
					<a href="<?php echo base_url('pages/help'); ?>" class="nav-link --xhr">
						<?php echo phrase('help_center'); ?>
					</a>
				</li>
				<li class="nav-item">
					<a href="<?php echo base_url('pages/terms'); ?>" class="nav-link --xhr">
						<?php echo phrase('terms_and_conditions'); ?>
					</a>
				</li>
				<li class="nav-item">
					<a href="<?php echo base_url('pages/privacy'); ?>" class="nav-link --xhr">
						<?php echo phrase('privacy_policy'); ?>
					</a>
				</li>
				<li class="nav-item">
					<a href="<?php echo base_url('pages/faqs'); ?>" class="nav-link --xhr">
						<?php echo phrase('faqs'); ?>
					</a>
				</li>
			</ul>
		</div>
		<div class="col col-md-2">
			<ul class="nav flex-column row">
				<li class="nav-item">
					<a href="<?php echo base_url('pages/apis'); ?>" class="nav-link --xhr">
						<?php echo phrase('api_documentations'); ?>
					</a>
				</li>
				<li class="nav-item">
					<a href="<?php echo base_url('pages/feedback'); ?>" class="nav-link --xhr">
						<?php echo phrase('send_feedback'); ?>
					</a>
				</li>
				<li class="nav-item">
					<a href="<?php echo base_url('pages/sitemaps'); ?>" class="nav-link --xhr">
						<?php echo phrase('sitemaps'); ?>
					</a>
				</li>
			</ul>
		</div>
		<div class="col-md-3">
			<ul class="nav flex-column row">
				<?php if(get_setting('office_address')) { ?>
				<li class="nav-item">
					<a href="#" class="nav-link">
						<?php echo get_setting('office_address'); ?>
					</a>
				</li>
				<?php } ?>
				<?php if(get_setting('office_email')) { ?>
				<li class="nav-item">
					<a href="#" class="nav-link">
						<?php echo get_setting('office_email'); ?>
					</a>
				</li>
				<?php } ?>
				<?php if(get_setting('office_phone')) { ?>
				<li class="nav-item">
					<a href="#" class="nav-link">
						<?php echo get_setting('office_phone'); ?>
					</a>
				</li>
				<?php } ?>
				<?php if(get_setting('office_fax')) { ?>
				<li class="nav-item">
					<a href="#" class="nav-link">
						<?php echo get_setting('office_fax'); ?>
					</a>
				</li>
				<?php } ?>
				<?php if(get_setting('office_whatsapp')) { ?>
				<li class="nav-item">
					<a href="https://api.whatsapp.com/send?phone=<?php echo str_replace('+', '', get_setting('office_whatsapp')); ?>&text=Halo..." class="nav-link">
						<?php echo get_setting('office_whatsapp'); ?>
					</a>
				</li>
				<?php } ?>
				<?php if(get_setting('office_fanpage')) { ?>
				<li class="nav-item">
					<a href="https://www.facebook.com/pages/<?php echo get_setting('office_fanpage'); ?>" class="nav-link">
						<?php echo get_setting('office_fanpage'); ?>
					</a>
				</li>
				<?php } ?>
				<?php if(get_setting('office_twitter')) { ?>
				<li class="nav-item">
					<a href="https://www.twitter.com/<?php echo get_setting('office_twitter'); ?>" class="nav-link">
						<?php echo get_setting('office_twitter'); ?>
					</a>
				</li>
				<?php } ?>
				<?php if(get_setting('office_instagram')) { ?>
				<li class="nav-item">
					<a href="https://www.instagram.com/<?php echo get_setting('office_instagram'); ?>" class="nav-link">
						<?php echo get_setting('office_instagram'); ?>
					</a>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8 offset-md-2">
			<p class="text-center text-muted font-weight-light">
				<small>
					<?php echo get_setting('app_description'); ?>
				</small>
			</p>
		</div>
	</div>
	<p class="text-center">
		<small class="fw-bold">
			<?php echo phrase('copyright'); ?> &#169;<?php echo date('Y'); ?> - <?php echo get_setting('app_name'); ?>
		</small>
		<small>
			(Aksara <?php echo aksara('build_version'); ?>)
		</small>
	</p>
</div>
